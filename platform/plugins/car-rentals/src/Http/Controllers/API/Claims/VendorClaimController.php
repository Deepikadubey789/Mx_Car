<?php

namespace Botble\CarRentals\Http\Controllers\API\Claims;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\ClaimResource;
use Botble\CarRentals\Http\Resources\ClaimTimelineResource;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Support\Facades\Auth;

class VendorClaimController extends BaseApiController
{
    public function index(int $booking)
    {
        $booking = $this->resolveBooking($booking);

        return $this
            ->httpResponse()
            ->setData(ClaimResource::collection(
                $booking->claims()->with('booking')->latest('id')->get()
            ))
            ->toApiResponse();
    }

    public function show(int $booking, int $claim)
    {
        $booking = $this->resolveBooking($booking);
        $claim = $this->resolveClaim($booking, $claim);

        return $this
            ->httpResponse()
            ->setData(new ClaimResource($claim->loadMissing('booking')))
            ->toApiResponse();
    }

    public function timeline(int $booking)
    {
        $booking = $this->resolveBooking($booking);

        return $this
            ->httpResponse()
            ->setData(ClaimTimelineResource::collection($this->buildPublicTimelineRows($booking)))
            ->toApiResponse();
    }

    protected function resolveBooking(int $bookingId): Booking
    {
        $vendor = Auth::guard('sanctum')->user();

        $booking = Booking::query()
            ->whereKey($bookingId)
            ->where('vendor_id', $vendor?->id)
            ->with('claims.booking')
            ->first();

        if (! $booking) {
            abort(404, 'Booking not found');
        }

        return $booking;
    }

    protected function resolveClaim(Booking $booking, int $claimId): BookingClaim
    {
        $claim = $booking->claims->firstWhere('id', $claimId);

        if (! $claim) {
            abort(404, 'Claim not found');
        }

        return $claim;
    }

    protected function buildPublicTimelineRows(Booking $booking): array
    {
        $rows = [];

        foreach ($booking->claims as $claim) {
            $rows[] = [
                'occurred_at' => optional($claim->created_at)?->toIso8601String(),
                'category' => 'claim',
                'title' => 'Claim opened',
                'summary' => sprintf('Claim opened for %s.', (string) ($claim->category ?: 'general')),
                'metadata' => [
                    'claim_id' => $claim->id,
                    'status' => $claim->status,
                    'category' => $claim->category,
                ],
                'actor' => null,
                'source' => 'claim:' . $claim->id . ':opened',
            ];

            if ($claim->requires_additional_docs) {
                $rows[] = [
                    'occurred_at' => optional($claim->updated_at)?->toIso8601String(),
                    'category' => 'claim',
                    'title' => 'Documents requested',
                    'summary' => 'Additional documents have been requested for this claim.',
                    'metadata' => [
                        'claim_id' => $claim->id,
                        'status' => $claim->status,
                    ],
                    'actor' => null,
                    'source' => 'claim:' . $claim->id . ':docs_requested',
                ];
            }

            if ($claim->updated_at && $claim->updated_at->ne($claim->created_at)) {
                $rows[] = [
                    'occurred_at' => $claim->updated_at->toIso8601String(),
                    'category' => 'claim',
                    'title' => 'Claim updated',
                    'summary' => sprintf('Claim status is now %s.', str_replace('_', ' ', (string) $claim->status)),
                    'metadata' => [
                        'claim_id' => $claim->id,
                        'status' => $claim->status,
                    ],
                    'actor' => null,
                    'source' => 'claim:' . $claim->id . ':updated',
                ];
            }

            if ($claim->resolved_at) {
                $rows[] = [
                    'occurred_at' => $claim->resolved_at->toIso8601String(),
                    'category' => 'claim',
                    'title' => 'Claim closed',
                    'summary' => sprintf('Claim closed with status %s.', str_replace('_', ' ', (string) $claim->status)),
                    'metadata' => [
                        'claim_id' => $claim->id,
                        'status' => $claim->status,
                        'approved_amount' => $claim->approved_amount,
                    ],
                    'actor' => null,
                    'source' => 'claim:' . $claim->id . ':closed',
                ];
            }
        }

        usort($rows, fn (array $a, array $b): int => strcmp((string) ($a['occurred_at'] ?? ''), (string) ($b['occurred_at'] ?? '')));

        return $rows;
    }
}
