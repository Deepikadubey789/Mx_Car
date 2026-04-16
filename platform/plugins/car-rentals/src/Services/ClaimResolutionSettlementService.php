<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Support\Arr;

class ClaimResolutionSettlementService
{
    /**
     * @return array{status: string, reference: ?string, message: string, metadata: array<string, mixed>}
     */
    public function settle(Booking $booking, BookingClaim $claim): array
    {
        $action = (string) ($claim->outcome_action ?: 'manual_only');
        $approvedAmount = (float) ($claim->approved_amount ?? 0);

        // Idempotency: repeated execution for an already-succeeded claim returns same result.
        if (
            in_array($claim->settlement_status, ['completed', 'manual'], true)
            && $claim->settlement_completed_at
            && Arr::get($claim->settlement_metadata, 'outcome_action') === $action
        ) {
            return [
                'status' => (string) $claim->settlement_status,
                'reference' => $claim->settlement_reference,
                'message' => 'Settlement already finalized for this action.',
                'metadata' => (array) ($claim->settlement_metadata ?? []),
            ];
        }

        $reference = sprintf('claim-%d-%s-%d', $claim->id, $action, now()->timestamp);
        $metadata = [
            'outcome_action' => $action,
            'approved_amount' => $approvedAmount,
            'booking_id' => $booking->id,
        ];

        switch ($action) {
            case 'capture_deposit':
                $booking->deposit_captured_amount = $approvedAmount;
                $booking->save();
                $metadata['booking_field_updated'] = 'deposit_captured_amount';

                return [
                    'status' => 'completed',
                    'reference' => $reference,
                    'message' => 'Deposit capture recorded.',
                    'metadata' => $metadata,
                ];

            case 'release_deposit':
                $booking->deposit_released_amount = $approvedAmount;
                $booking->save();
                $metadata['booking_field_updated'] = 'deposit_released_amount';

                return [
                    'status' => 'completed',
                    'reference' => $reference,
                    'message' => 'Deposit release recorded.',
                    'metadata' => $metadata,
                ];

            case 'partial_refund':
                $metadata['refund_amount'] = $approvedAmount;

                return [
                    'status' => 'completed',
                    'reference' => $reference,
                    'message' => 'Partial refund recorded for manual processing.',
                    'metadata' => $metadata,
                ];

            case 'manual_only':
            default:
                return [
                    'status' => 'manual',
                    'reference' => $reference,
                    'message' => 'No automated settlement action applied.',
                    'metadata' => $metadata,
                ];
        }
    }
}
