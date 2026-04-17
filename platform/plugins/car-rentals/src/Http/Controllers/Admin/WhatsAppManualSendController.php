<?php

namespace Botble\CarRentals\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\WhatsAppMessageTemplate;
use Botble\CarRentals\Models\WhatsAppSentMessage;
use Botble\CarRentals\Services\WhatsApp\WhatsAppSentMessageService;
use Botble\CarRentals\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppManualSendController extends BaseController
{
    protected WhatsAppSentMessageService $whatsAppSentMessageService;
    protected WhatsAppService $whatsAppService;

    public function __construct(
        WhatsAppSentMessageService $whatsAppSentMessageService,
        WhatsAppService $whatsAppService
    )
    {
        $this->whatsAppSentMessageService = $whatsAppSentMessageService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show form to compose and send manual WhatsApp message
     */
    public function create(): View
    {
        $customers = Customer::where('whatsapp', '!=', null)
            ->where('whatsapp', '!=', '')
            ->orderBy('name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'label' => "{$customer->name} ({$customer->whatsapp})",
                ];
            });

        return view('plugins/car-rentals::admin.whatsapp.send', [
            'customers' => $customers,
            'templates' => WhatsAppMessageTemplate::query()
                ->active()
                ->orderBy('label')
                ->get(['name', 'label']),
        ]);
    }

    /**
     * Fetch bookings for a customer (AJAX)
     */
    public function getCustomerBookings(Request $request)
    {
        $customerId = $request->get('customer_id');

        if (!$customerId) {
            return response()->json([]);
        }

        $bookings = Booking::where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($booking) {
                $carName = $booking->car?->name ?? 'N/A';
                return [
                    'id' => $booking->id,
                    'label' => "{$booking->booking_number} - {$carName} ({$booking->get_status_name()})",
                ];
            });

        return response()->json($bookings);
    }

    /**
     * Fetch claims for a customer (AJAX)
     */
    public function getCustomerClaims(Request $request)
    {
        $customerId = $request->get('customer_id');

        if (!$customerId) {
            return response()->json([]);
        }

        $claims = BookingClaim::whereHas('booking', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
            ->with('booking')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($claim) {
                return [
                    'id' => $claim->id,
                    'label' => "Claim #{$claim->id} - Booking {$claim->booking?->booking_number} ({$claim->status})",
                ];
            });

        return response()->json($claims);
    }

    /**
     * Send manual message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:cr_customers,id',
            'send_mode' => 'required|in:text,template',
            'message' => 'required_if:send_mode,text|nullable|string|min:1|max:1000',
            'template_name' => 'required_if:send_mode,template|nullable|string|max:100',
            'template_language' => 'nullable|string|max:10',
            'booking_id' => 'nullable|exists:cr_bookings,id',
            'claim_id' => 'nullable|exists:cr_booking_claims,id',
        ]);

        try {
            $customer = Customer::findOrFail($validated['customer_id']);

            if (!$customer->whatsapp) {
                return back()
                    ->withError('This customer does not have a WhatsApp number.');
            }

            if (! $this->whatsAppService->normalizePhoneNumber($customer->whatsapp)) {
                return back()
                    ->withError('Customer WhatsApp number must be in E.164 format (example: +918302786768).');
            }

            $booking = $validated['booking_id'] ? Booking::find($validated['booking_id']) : null;
            $claim = $validated['claim_id'] ? BookingClaim::find($validated['claim_id']) : null;

            if ($validated['send_mode'] === 'template') {
                $result = $this->whatsAppSentMessageService->sendTemplateDirect(
                    $customer,
                    trim((string) $validated['template_name']),
                    (string) ($validated['template_language'] ?? 'en_US'),
                    'manual_template',
                    $booking,
                    $claim
                );
            } else {
                $result = $this->whatsAppSentMessageService->sendCustom(
                    $customer,
                    (string) $validated['message'],
                    'manual',
                    $booking,
                    $claim
                );
            }

            if ($result['success']) {
                return back()
                    ->withSuccess("Message accepted by WhatsApp API. Message ID: {$result['message_id']}. Final status updates (sent/delivered/read/failed) will appear in history.");
            } else {
                if ((int) ($result['error_code'] ?? 0) === 131047) {
                    return back()
                        ->with(
                            'error',
                            'Message was rejected by WhatsApp because the 24-hour customer service window has expired (error 131047). Use an approved template message or ask customer to reply first.'
                        )
                        ->withInput();
                }

                return back()
                    ->with('error', "Failed to send message: {$result['error']}")
                    ->withInput();
            }
        } catch (\Exception $e) {
            return back()
                ->withError('Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show sent message history for a customer
     */
    public function history(Customer $customer): View
    {
        $sentMessages = WhatsAppSentMessage::where('customer_id', $customer->id)
            ->with('booking', 'claim')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('plugins/car-rentals::admin.whatsapp.history', [
            'customer' => $customer,
            'sentMessages' => $sentMessages,
        ]);
    }

    /**
     * Show statistics dashboard
     */
    public function dashboard(): View
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'total_messages' => WhatsAppSentMessage::count(),
            'accepted_today' => WhatsAppSentMessage::where('status', 'accepted')
                ->whereDate('created_at', $today)
                ->count(),
            'delivered_today' => WhatsAppSentMessage::whereIn('status', ['delivered', 'read'])
                ->whereDate('status_updated_at', '>=', $today)
                ->count(),
            'failed_today' => WhatsAppSentMessage::where('status', 'failed')
                ->whereDate('status_updated_at', '>=', $today)
                ->count(),
            'delivered_this_month' => WhatsAppSentMessage::whereIn('status', ['delivered', 'read'])
                ->whereDate('status_updated_at', '>=', $thisMonth)
                ->count(),
            'by_event_type' => WhatsAppSentMessage::selectRaw('event_type, COUNT(*) as count, SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as accepted_count, SUM(CASE WHEN status IN ("delivered", "read") THEN 1 ELSE 0 END) as delivered_count, SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count')
                ->groupBy('event_type')
                ->orderByDesc('count')
                ->get(),
        ];

        return view('plugins/car-rentals::admin.whatsapp.dashboard', [
            'stats' => $stats,
        ]);
    }
}
