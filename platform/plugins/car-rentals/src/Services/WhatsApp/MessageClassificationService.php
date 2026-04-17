<?php

namespace Botble\CarRentals\Services\WhatsApp;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Models\Customer;
use Illuminate\Support\Facades\Log;

class MessageClassificationService
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Classify an incoming message to determine its context
     *
     * @param Customer $customer Customer who sent the message
     * @param string $messageContent Message content
     * @param array $metadata Message metadata (type, timestamp, phone, etc.)
     * @return array Classification result with keys:
     *   - context_type: 'booking' | 'claim' | 'none'
     *   - context_id: int|null ID of booking or claim
     *   - category: 'confirmation' | 'cancellation' | 'dispute' | 'general'
     *   - confidence: float 0-1
     *   - reason: string explanation
     */
    public function classifyMessage(
        Customer $customer,
        string $messageContent,
        array $metadata = []
    ): array {
        try {
            // Start with low confidence assumption
            $result = [
                'context_type' => 'none',
                'context_id' => null,
                'category' => 'general',
                'confidence' => 0,
                'reason' => 'No context matched',
            ];

            // Categorize the message content
            $category = $this->whatsAppService->categorizeMessage($messageContent);
            $result['category'] = $category;

            // Step 1: Check if booking ID is explicitly mentioned in content
            $explicitBookingId = $this->whatsAppService->extractBookingIdFromContent($messageContent);
            if ($explicitBookingId) {
                $booking = Booking::find($explicitBookingId);
                if ($booking && $booking->customer_id === $customer->id) {
                    $result['context_type'] = 'booking';
                    $result['context_id'] = $explicitBookingId;
                    $result['confidence'] = 0.95;
                    $result['reason'] = 'Booking ID explicitly found in message';
                    return $result;
                }
            }

            // Step 2: For dispute messages, find recent open claim
            if ($category === 'dispute') {
                $claim = $this->findRecentClaim($customer);
                if ($claim) {
                    $result['context_type'] = 'claim';
                    $result['context_id'] = $claim->id;
                    $result['confidence'] = 0.85;
                    $result['reason'] = 'Dispute keywords matched with recent open claim';
                    return $result;
                }
            }

            // Step 3: For confirmation/cancellation messages, find recent active booking
            if (in_array($category, ['booking_confirmation', 'cancellation'])) {
                $booking = $this->findRecentBooking($customer, $category);
                if ($booking) {
                    $result['context_type'] = 'booking';
                    $result['context_id'] = $booking->id;
                    $result['confidence'] = 0.80;
                    $result['reason'] = 'Message category matches recent booking status';
                    return $result;
                }
            }

            // Step 4: If no explicit match, try to find any recent active booking (general case)
            $booking = $this->findRecentBooking($customer, 'any');
            if ($booking) {
                $result['context_type'] = 'booking';
                $result['context_id'] = $booking->id;
                $result['confidence'] = 0.50;
                $result['reason'] = 'Linked to most recent active booking (low confidence)';
                return $result;
            }

            // No context found
            $result['context_type'] = 'none';
            $result['context_id'] = null;
            $result['confidence'] = 0;
            $result['reason'] = 'No matching booking or claim found for customer';

            return $result;
        } catch (\Exception $e) {
            Log::warning('Message classification failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'context_type' => 'none',
                'context_id' => null,
                'category' => 'general',
                'confidence' => 0,
                'reason' => 'Classification service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Find the most recent active booking for a customer
     *
     * @param Customer $customer
     * @param string $category 'any', 'booking_confirmation', 'cancellation'
     * @return Booking|null
     */
    protected function findRecentBooking(Customer $customer, string $category = 'any'): ?Booking
    {
        try {
            $query = Booking::where('customer_id', $customer->id)
                ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
                ->orderBy('created_at', 'desc');

            // Additional filtering based on category if needed
            if ($category === 'booking_confirmation') {
                // Look for recently created or pending confirmation
                $query->where('status', 'pending');
            } elseif ($category === 'cancellation') {
                // Look for active bookings that can be cancelled
                $query->whereIn('status', ['pending', 'confirmed']);
            }

            // Within last 7 days
            $query->where('created_at', '>=', now()->subDays(7));

            return $query->first();
        } catch (\Exception $e) {
            Log::warning('Recent booking search failed', [
                'customer_id' => $customer->id,
                'category' => $category,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find the most recent open claim for a customer
     *
     * @param Customer $customer
     * @return BookingClaim|null
     */
    protected function findRecentClaim(Customer $customer): ?BookingClaim
    {
        try {
            return BookingClaim::whereHas('booking', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->whereIn('status', ['open', 'under_review', 'awaiting_docs'])
            ->orderBy('created_at', 'desc')
            ->first();
        } catch (\Exception $e) {
            Log::warning('Recent claim search failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
