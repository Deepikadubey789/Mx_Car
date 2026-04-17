<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\WhatsAppConfig;
use Botble\CarRentals\Services\WhatsApp\WhatsAppSentMessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppRemindersCommand extends Command
{
    protected $signature = 'whatsapp:send-reminders {--dry-run : Preview reminders without sending}';
    protected $description = 'Send WhatsApp pickup and return reminders 24 hours before scheduled times';

    protected WhatsAppSentMessageService $whatsAppSentMessageService;

    public function __construct(WhatsAppSentMessageService $whatsAppSentMessageService)
    {
        parent::__construct();
        $this->whatsAppSentMessageService = $whatsAppSentMessageService;
    }

    public function handle(): int
    {
        // Check if WhatsApp is enabled
        if (!WhatsAppConfig::where('enabled', true)->exists()) {
            $this->info('WhatsApp is not enabled. Skipping reminders.');
            return 0;
        }

        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? 'DRY RUN MODE - No messages will be sent' : 'Sending WhatsApp reminders...');

        // Send pickup reminders
        $this->sendPickupReminders($dryRun);

        // Send return reminders
        $this->sendReturnReminders($dryRun);

        $this->info('Reminders completed.');
        return 0;
    }

    /**
     * Send pickup reminders for bookings starting tomorrow
     */
    protected function sendPickupReminders(bool $dryRun): void
    {
        try {
            $tomorrow = now()->addDay()->startOfDay();
            $tomorrowEnd = $tomorrow->copy()->endOfDay();

            // Find bookings starting tomorrow that haven't had pickup reminder sent today
            $bookings = Booking::whereDate('start_date', $tomorrow->toDateString())
                ->whereIn('status', ['confirmed', 'ongoing'])
                ->with('customer')
                ->get();

            $count = 0;
            foreach ($bookings as $booking) {
                if (!$booking->customer || !$booking->customer->whatsapp) {
                    continue;
                }

                // Check if reminder already sent today
                if ($this->reminderAlreadySent($booking, 'pickup_reminder')) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("DRY RUN: Would send pickup reminder for booking {$booking->booking_number}");
                } else {
                    $carDetails = $booking->car;
                    $data = [
                        'booking_reference' => $booking->booking_number,
                        'car_name' => $carDetails->name ?? 'Car',
                        'pickup_time' => $booking->start_date?->format('h:i A'),
                        'pickup_location' => $carDetails->car_addresses?->first()?->address ?? 'TBD',
                        'pickup_address' => $carDetails->car_addresses?->first()?->address ?? 'TBD',
                    ];

                    $result = $this->whatsAppSentMessageService->sendFromTemplate(
                        $booking->customer,
                        'pickup_reminder',
                        $data,
                        'pickup_reminder',
                        $booking
                    );

                    if ($result['success']) {
                        $this->line("✓ Pickup reminder sent: {$booking->booking_number}");
                        $count++;
                    } else {
                        $this->warn("✗ Pickup reminder failed: {$booking->booking_number} - {$result['error']}");
                    }
                }
            }

            $this->info("Pickup reminders: $count sent");
        } catch (\Exception $e) {
            Log::error('Error sending pickup reminders', ['error' => $e->getMessage()]);
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Send return reminders for bookings ending tomorrow
     */
    protected function sendReturnReminders(bool $dryRun): void
    {
        try {
            $tomorrow = now()->addDay()->startOfDay();

            // Find bookings ending tomorrow that haven't had return reminder sent today
            $bookings = Booking::whereDate('end_date', $tomorrow->toDateString())
                ->whereIn('status', ['confirmed', 'ongoing'])
                ->with('customer')
                ->get();

            $count = 0;
            foreach ($bookings as $booking) {
                if (!$booking->customer || !$booking->customer->whatsapp) {
                    continue;
                }

                // Check if reminder already sent today
                if ($this->reminderAlreadySent($booking, 'return_reminder')) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("DRY RUN: Would send return reminder for booking {$booking->booking_number}");
                } else {
                    $carDetails = $booking->car;
                    $data = [
                        'booking_reference' => $booking->booking_number,
                        'return_time' => $booking->end_date?->format('h:i A'),
                        'return_location' => $carDetails->car_addresses?->first()?->address ?? 'TBD',
                        'return_address' => $carDetails->car_addresses?->first()?->address ?? 'TBD',
                    ];

                    $result = $this->whatsAppSentMessageService->sendFromTemplate(
                        $booking->customer,
                        'return_reminder',
                        $data,
                        'return_reminder',
                        $booking
                    );

                    if ($result['success']) {
                        $this->line("✓ Return reminder sent: {$booking->booking_number}");
                        $count++;
                    } else {
                        $this->warn("✗ Return reminder failed: {$booking->booking_number} - {$result['error']}");
                    }
                }
            }

            $this->info("Return reminders: $count sent");
        } catch (\Exception $e) {
            Log::error('Error sending return reminders', ['error' => $e->getMessage()]);
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Check if reminder was already sent today for this booking
     */
    protected function reminderAlreadySent(Booking $booking, string $eventType): bool
    {
        try {
            return \Botble\CarRentals\Models\WhatsAppSentMessage::where('booking_id', $booking->id)
                ->where('event_type', $eventType)
                ->whereDate('created_at', now()->toDateString())
                ->exists();
        } catch (\Exception) {
            return false;
        }
    }
}
