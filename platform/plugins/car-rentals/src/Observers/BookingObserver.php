<?php

namespace Botble\CarRentals\Observers;

use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\WhatsAppConfig;
use Botble\CarRentals\Services\WhatsApp\WhatsAppSentMessageService;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    protected WhatsAppSentMessageService $whatsAppSentMessageService;

    public function __construct(WhatsAppSentMessageService $whatsAppSentMessageService)
    {
        $this->whatsAppSentMessageService = $whatsAppSentMessageService;
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Only if WhatsApp is enabled
        if (!WhatsAppConfig::where('enabled', true)->exists()) {
            return;
        }

        try {
            $customer = $booking->customer;
            if (!$customer || !$customer->whatsapp) {
                return;
            }

            // Check for status changes
            $currentStatus = $booking->status instanceof BookingStatusEnum
                ? $booking->status->getValue()
                : (string) $booking->status;
            $originalStatus = (string) $booking->getRawOriginal('status');
            $confirmedStatuses = [
                BookingStatusEnum::PROCESSING,
                'confirmed',
            ];

            // Treat processing as "confirmed" because vendor approval updates status to processing.
            $wasConfirmed = in_array($originalStatus, $confirmedStatuses, true);
            $isConfirmed = in_array($currentStatus, $confirmedStatuses, true);

            // Status changed to confirmed/processing
            if (! $wasConfirmed && $isConfirmed) {
                $this->sendBookingConfirmed($booking, $customer);
            }

            // Status changed to cancelled
            if ($originalStatus !== BookingStatusEnum::CANCELLED && $currentStatus === BookingStatusEnum::CANCELLED) {
                $this->sendBookingCancelled($booking, $customer);
            }
        } catch (\Exception $e) {
            Log::error('BookingObserver error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send booking confirmed message
     */
    protected function sendBookingConfirmed(Booking $booking, $customer): void
    {
        try {
            $carDetails = $booking->car;
            $data = [
                'booking_reference' => $booking->booking_number,
                'car_name' => $carDetails->name ?? 'Car',
                'car_year' => $carDetails->year ?? '',
                'pickup_date' => $booking->start_date?->format('M d, Y'),
                'pickup_time' => $booking->start_date?->format('h:i A'),
                'return_date' => $booking->end_date?->format('M d, Y'),
                'return_time' => $booking->end_date?->format('h:i A'),
                'pickup_location' => $carDetails->car_addresses?->first()?->address ?? 'TBD',
                'total_amount' => '$' . number_format($booking->amount, 2),
            ];

            $this->whatsAppSentMessageService->sendFromTemplate(
                $customer,
                'booking_confirmed',
                $data,
                'booking_confirmed',
                $booking
            );

            Log::info('Booking confirmed WhatsApp sent', ['booking_id' => $booking->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmed message', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send booking cancelled message
     */
    protected function sendBookingCancelled(Booking $booking, $customer): void
    {
        try {
            $data = [
                'booking_reference' => $booking->booking_number,
                'cancellation_reason' => $booking->cancellation_reason ?? 'Customer requested',
                'refund_amount' => '$' . number_format($booking->refund_amount ?? 0, 2),
            ];

            $this->whatsAppSentMessageService->sendFromTemplate(
                $customer,
                'booking_cancelled',
                $data,
                'booking_cancelled',
                $booking
            );

            Log::info('Booking cancelled WhatsApp sent', ['booking_id' => $booking->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancelled message', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
