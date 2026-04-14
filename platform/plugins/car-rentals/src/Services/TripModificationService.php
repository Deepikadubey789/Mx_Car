<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TripModificationService
{
    public function __construct(protected PricingQuoteService $pricingQuoteService)
    {
    }

    // EXTEND TRIP — Pending Admin Approval
    public function extendTrip(Booking $booking, string $newEndDate, ?string $reason = ''): array
    {
        $newEnd = Carbon::parse($newEndDate);
        $currentEnd = Carbon::parse($booking->car->rental_end_date);
        $startDate = Carbon::parse($booking->car->rental_start_date);

        if ($newEnd->lte($currentEnd)) {
            return ['success' => false, 'message' => 'New end date must be after current end date.'];
        }

        // Check if already pending modification
        if ($booking->modification_status === 'pending') {
            return ['success' => false, 'message' => 'You already have a pending modification request.'];
        }

        $car = $booking->car->car;
        $serviceIds = $booking->services ? $booking->services->pluck('id')->toArray() : [];

        $newQuote = $this->pricingQuoteService->buildQuote($car, $startDate, $newEnd, $serviceIds);

        $oldAmount = (float) $booking->sub_total;
        $newAmount = (float) ($newQuote['subtotal'] ?? $newQuote['sub_total'] ?? 0);
        $extraCharge = round(max(0, $newAmount - $oldAmount), 2);
        $extraDays = $currentEnd->diffInDays($newEnd);

        return DB::transaction(function () use ($booking, $newEnd, $extraDays, $extraCharge, $reason, $currentEnd) {
            if (! $booking->original_end_date) {
                $booking->original_end_date = $currentEnd;
            }

            $booking->modification_type = 'extend';
            $booking->modification_status = 'pending';
            $booking->modification_reason = $reason;
            $booking->modified_at = now();
            // Store requested date in reason for admin reference
            $booking->modification_reason = '[Requested End: ' . $newEnd->format('Y-m-d H:i') . '] ' . $reason;
            $booking->save();

            DB::table('admin_notifications')->insert([
                'title' => 'Trip Extend Request #' . $booking->booking_number,
                'description' => $booking->customer_name . ' requested to extend trip by ' . $extraDays . ' day(s) until ' . $newEnd->format('M d, Y') . '. Extra: $' . $extraCharge,
                'action_label' => 'Review Request',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Extension request submitted! Waiting for admin approval.',
                'extra_days' => $extraDays,
                'extra_charge' => $extraCharge,
                'new_end_date' => $newEnd->format('Y-m-d H:i:s'),
                'status' => 'pending',
            ];
        });
    }

    // SHORTEN TRIP — Pending Admin Approval
    public function shortenTrip(Booking $booking, string $newEndDate, ?string $reason = ''): array
    {
        $newEnd = Carbon::parse($newEndDate);
        $currentEnd = Carbon::parse($booking->car->rental_end_date);
        $startDate = Carbon::parse($booking->car->rental_start_date);

        if ($newEnd->gte($currentEnd)) {
            return ['success' => false, 'message' => 'New end date must be before current end date.'];
        }

        if ($newEnd->lte($startDate)) {
            return ['success' => false, 'message' => 'New end date must be after start date.'];
        }

        // Check if already pending modification
        if ($booking->modification_status === 'pending') {
            return ['success' => false, 'message' => 'You already have a pending modification request.'];
        }

        $car = $booking->car->car;
        $serviceIds = $booking->services ? $booking->services->pluck('id')->toArray() : [];

        $newQuote = $this->pricingQuoteService->buildQuote($car, $startDate, $newEnd, $serviceIds);

        $oldAmount = (float) $booking->sub_total;
        $newAmount = (float) ($newQuote['subtotal'] ?? $newQuote['sub_total'] ?? 0);
        $refundAmount = round(max(0, $oldAmount - $newAmount), 2);
        $savedDays = $newEnd->diffInDays($currentEnd);

        return DB::transaction(function () use ($booking, $newEnd, $savedDays, $refundAmount, $reason, $currentEnd) {
            if (! $booking->original_end_date) {
                $booking->original_end_date = $currentEnd;
            }

            $booking->modification_type = 'shorten';
            $booking->modification_status = 'pending';
            $booking->modification_reason = '[Requested End: ' . $newEnd->format('Y-m-d H:i') . '] [Refund: $' . $refundAmount . '] ' . $reason;
            $booking->modified_at = now();
            $booking->save();

            DB::table('admin_notifications')->insert([
                'title' => 'Trip Shorten Request #' . $booking->booking_number,
                'description' => $booking->customer_name . ' requested to shorten trip by ' . $savedDays . ' day(s) until ' . $newEnd->format('M d, Y') . '. Refund: $' . $refundAmount,
                'action_label' => 'Review Request',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Shorten request submitted! Waiting for admin approval.',
                'saved_days' => $savedDays,
                'refund_amount' => $refundAmount,
                'new_end_date' => $newEnd->format('Y-m-d H:i:s'),
                'status' => 'pending',
            ];
        });
    }

    // EARLY RETURN — Direct (no approval needed)
       public function earlyReturn(Booking $booking, ?string $reason = ''): array 
    {
        $now = Carbon::now();
        $currentEnd = Carbon::parse($booking->car->rental_end_date);

        if ($now->gte($currentEnd)) {
            return ['success' => false, 'message' => 'Trip already ended.'];
        }

        // Early return apply directly
        $newEnd = $now;
        $startDate = Carbon::parse($booking->car->rental_start_date);
        $car = $booking->car->car;
        $serviceIds = $booking->services ? $booking->services->pluck('id')->toArray() : [];

        $newQuote = $this->pricingQuoteService->buildQuote($car, $startDate, $newEnd, $serviceIds);
        $oldAmount = (float) $booking->sub_total;
        $newAmount = (float) ($newQuote['subtotal'] ?? $newQuote['sub_total'] ?? 0);
        $refundAmount = round(max(0, $oldAmount - $newAmount), 2);
        $savedDays = $newEnd->diffInDays($currentEnd);

        return DB::transaction(function () use ($booking, $newEnd, $savedDays, $refundAmount, $reason, $currentEnd) {
            if (! $booking->original_end_date) {
                $booking->original_end_date = $currentEnd;
            }

            $booking->modification_type = 'early_return';
            $booking->modification_status = 'approved';
            $booking->modification_reason = $reason ?: 'Early return by customer';
            $booking->modified_at = now();
            $booking->refund_amount = $refundAmount;
            $booking->amount = max(0, round((float) $booking->amount - $refundAmount, 2));
            $booking->save();

            $booking->car->update([
                'rental_end_date' => $newEnd,
                'number_of_days' => max(1, (int) $booking->car->number_of_days - $savedDays),
            ]);

            $this->syncInvoiceAmount($booking, $refundAmount, 'subtract');

            DB::table('admin_notifications')->insert([
                'title' => 'Early Return #' . $booking->booking_number,
                'description' => $booking->customer_name . ' returned car early. Refund: $' . $refundAmount,
                'action_label' => 'View Booking',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Early return confirmed. Refund: $' . $refundAmount,
                'saved_days' => $savedDays,
                'refund_amount' => $refundAmount,
                'new_end_date' => $newEnd->format('Y-m-d H:i:s'),
                'new_total' => $booking->fresh()->amount,
            ];
        });
    }

    // APPROVE MODIFICATION — Admin action
    public function approveModification(Booking $booking): array
    {
        if ($booking->modification_status !== 'pending') {
            return ['success' => false, 'message' => 'No pending modification found.'];
        }

        preg_match('/\[Requested End: ([^\]]+)\]/', $booking->modification_reason, $matches);
        if (empty($matches[1])) {
            return ['success' => false, 'message' => 'Requested end date not found.'];
        }

        $newEnd = Carbon::parse($matches[1]);
        $currentEnd = Carbon::parse($booking->car->rental_end_date);

        return DB::transaction(function () use ($booking, $newEnd, $currentEnd) {
            $type = $booking->modification_type;

            if ($type === 'extend') {
                $extraDays = $currentEnd->diffInDays($newEnd);
                $car = $booking->car->car;
                $startDate = Carbon::parse($booking->car->rental_start_date);
                $serviceIds = $booking->services ? $booking->services->pluck('id')->toArray() : [];
                $newQuote = $this->pricingQuoteService->buildQuote($car, $startDate, $newEnd, $serviceIds);
                $oldAmount = (float) $booking->sub_total;
                $newAmount = (float) ($newQuote['subtotal'] ?? $newQuote['sub_total'] ?? 0);
                $extraCharge = round(max(0, $newAmount - $oldAmount), 2);

                $booking->amount = round((float) $booking->amount + $extraCharge, 2);
                $booking->car->update([
                    'rental_end_date' => $newEnd,
                    'number_of_days' => (int) $booking->car->number_of_days + $extraDays,
                ]);
                $this->syncInvoiceAmount($booking, $extraCharge, 'add');

            } elseif ($type === 'shorten') {
                $savedDays = $newEnd->diffInDays($currentEnd);
                preg_match('/\[Refund: \$([^\]]+)\]/', $booking->modification_reason, $refundMatches);
                $refundAmount = (float) ($refundMatches[1] ?? 0);

                $booking->refund_amount = $refundAmount;
                $booking->amount = max(0, round((float) $booking->amount - $refundAmount, 2));
                $booking->car->update([
                    'rental_end_date' => $newEnd,
                    'number_of_days' => max(1, (int) $booking->car->number_of_days - $savedDays),
                ]);
                $this->syncInvoiceAmount($booking, $refundAmount, 'subtract');
            }

            $booking->modification_status = 'approved';
            $booking->save();

            DB::table('admin_notifications')->insert([
                'title' => 'Modification Approved #' . $booking->booking_number,
                'description' => 'Admin approved ' . $type . ' request for ' . $booking->customer_name,
                'action_label' => 'View Booking',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Modification approved successfully.',
                'type' => $type,
                'new_end_date' => $newEnd->format('Y-m-d H:i:s'),
            ];
        });
    }

    // REJECT MODIFICATION — Admin action
    public function rejectModification(Booking $booking, string $reason = ''): array
    {
        if ($booking->modification_status !== 'pending') {
            return ['success' => false, 'message' => 'No pending modification found.'];
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->modification_status = 'rejected';
            $booking->modification_reason = $booking->modification_reason . ' [Rejected: ' . $reason . ']';
            $booking->save();

            DB::table('admin_notifications')->insert([
                'title' => 'Modification Rejected #' . $booking->booking_number,
                'description' => 'Admin rejected ' . $booking->modification_type . ' request for ' . $booking->customer_name,
                'action_label' => 'View Booking',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Modification rejected.',
            ];
        });
    }

    // CANCEL TRIP — Policy Logic
    public function cancelTrip(Booking $booking, ?string $reason = '', ?string $cancelledBy = 'customer'): array
    {
        if ($booking->status == BookingStatusEnum::CANCELLED) {
            return ['success' => false, 'message' => 'Booking already cancelled.'];
        }

        if ($booking->status == BookingStatusEnum::COMPLETED) {
            return ['success' => false, 'message' => 'Completed booking cannot be cancelled.'];
        }

        $startDate = Carbon::parse($booking->car->rental_start_date);
        $now = Carbon::now();

        if ($now->gte($startDate)) {
            $policy = 'no_refund';
            $refundAmount = 0;
        } else {
            $hoursUntilStart = $now->diffInHours($startDate, false);

            if ($hoursUntilStart > 48) {
                $policy = 'free';
                $refundAmount = (float) $booking->amount;
            } elseif ($hoursUntilStart > 24) {
                $policy = 'partial';
                $refundAmount = round((float) $booking->amount * 0.5, 2);
            } else {
                $policy = 'no_refund';
                $refundAmount = 0;
            }
        }

        if ($cancelledBy === 'host') {
            $policy = 'free';
            $refundAmount = (float) $booking->amount;
        }

        return DB::transaction(function () use ($booking, $policy, $refundAmount, $reason, $cancelledBy) {
            $booking->status = BookingStatusEnum::CANCELLED;
            $booking->cancellation_policy = $policy;
            $booking->refund_amount = $refundAmount;
            $booking->cancellation_reason = $reason;
            $booking->cancelled_at = now();
            $booking->modification_type = 'cancelled';
            $booking->save();

            DB::table('admin_notifications')->insert([
                'title' => 'Booking Cancelled #' . $booking->booking_number,
                'description' => ucfirst($cancelledBy) . ' cancelled booking. Policy: ' . $policy . '. Refund: $' . $refundAmount,
                'action_label' => 'View Booking',
                'action_url' => route('car-rentals.bookings.edit', $booking->id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Booking cancelled.',
                'cancellation_policy' => $policy,
                'refund_amount' => $refundAmount,
                'cancelled_by' => $cancelledBy,
            ];
        });
    }

    // SYNC INVOICE (auto recalculation)
    protected function syncInvoiceAmount(Booking $booking, float $delta, string $operation): void
    {
        if (! method_exists($booking, 'invoice') || ! $booking->invoice()->exists()) {
            return;
        }

        $invoice = $booking->invoice;

        if ($operation === 'add') {
            $invoice->sub_total = round((float) $invoice->sub_total + $delta, 2);
            $invoice->amount = round((float) $invoice->amount + $delta, 2);
        } else {
            $invoice->sub_total = max(0, round((float) $invoice->sub_total - $delta, 2));
            $invoice->amount = max(0, round((float) $invoice->amount - $delta, 2));
        }

        $invoice->save();
    }
}