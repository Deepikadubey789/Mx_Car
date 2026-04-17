<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\BookingResource;
use Botble\CarRentals\Http\Resources\CustomerResource;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\CarReview;
use Illuminate\Support\Facades\Auth;

class OverviewController extends BaseApiController
{
    /**
     * Get the authenticated customer's account overview.
     *
     * Returns the same data as the /overview web page:
     *   - Customer profile snapshot
     *   - Stats  (total bookings, total reviews, member since)
     *   - Account status (account type, vendor status, KYC)
     *   - Recent bookings (last 3)
     *
     * @group Car Rentals - Customer
     */
    public function index()
    {
        /** @var \Botble\CarRentals\Models\Customer $customer */
        $customer = Auth::guard('sanctum')->user();

        // --- Stats ---
        $totalBookings = Booking::query()
            ->where('customer_id', $customer->id)
            ->count();

        $totalReviews = CarReview::query()
            ->where('customer_id', $customer->id)
            ->count();

        // --- Recent bookings (last 3, same as the web page) ---
        $recentBookings = Booking::query()
            ->where('customer_id', $customer->id)
            ->with(['car.car.make'])
            ->latest('id')
            ->limit(3)
            ->get();

        // --- KYC display state ---
        $kycDisplay = $this->resolveKycDisplay((string) $customer->kyc_status);

        // --- Account status ---
        $accountStatus = [
            'account_type'   => 'Standard',
            'is_vendor'      => (bool) $customer->is_vendor,
            'vendor_status'  => $customer->is_vendor ? 'active' : 'not_active',
            'kyc_status'     => (string) $customer->kyc_status,
            'kyc_label'      => $kycDisplay['label'],
            'kyc_note'       => $kycDisplay['note'],
            'show_kyc_verify' => $kycDisplay['show_verify'],
        ];

        return $this
            ->httpResponse()
            ->setData([
                'customer'        => new CustomerResource($customer),
                'stats'           => [
                    'total_bookings'  => $totalBookings,
                    'total_reviews'   => $totalReviews,
                    'member_since'    => $customer->created_at->toISOString(),
                    'member_since_human' => $customer->created_at->diffForHumans(null, true),
                ],
                'account_status'  => $accountStatus,
                'recent_bookings' => BookingResource::collection($recentBookings),
            ])
            ->toApiResponse();
    }

    /**
     * Resolve the KYC display state for the given status string.
     * Mirrors the logic in overview.blade.php and PublicController::getKycDisplayState().
     */
    private function resolveKycDisplay(string $kycStatus): array
    {
        return match ($kycStatus) {
            'verified' => [
                'label'       => 'Verified',
                'note'        => 'Your identity has been verified and your KYC is complete.',
                'show_verify' => false,
            ],
            'pending', 'manual_review' => [
                'label'       => 'Under review',
                'note'        => 'Your KYC request is in review. You will be notified after approval.',
                'show_verify' => false,
            ],
            'failed' => [
                'label'       => 'Verification failed',
                'note'        => 'Please verify again with clear license and selfie images.',
                'show_verify' => true,
            ],
            default => [
                'label'       => 'Not started',
                'note'        => 'Verify your account to complete KYC and unlock access.',
                'show_verify' => true,
            ],
        };
    }
}
