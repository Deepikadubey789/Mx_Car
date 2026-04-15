<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarAutoApplySettings;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoPricingApiController extends BaseApiController
{
    /**
     * Get auto-pricing configuration for a car
     *
     * @group Car Rentals - Vendor
     */
    public function show(Car $car)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(401)
                ->toApiResponse();
        }

        // Verify car belongs to vendor
        if ($car->vendor_id !== $customer->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(403)
                ->toApiResponse();
        }

        // Get or create auto-apply settings
        $settings = CarAutoApplySettings::firstOrCreate(
            ['car_id' => $car->id],
            [
                'is_auto_apply_enabled' => false,
                'auto_apply_min_confidence' => 70,
                'paused_until' => null,
            ]
        );

        $data = [
            'car_id' => $car->id,
            'car_name' => $car->name,
            'is_auto_apply_enabled' => (bool) $settings->is_auto_apply_enabled,
            'auto_apply_min_confidence' => (int) $settings->auto_apply_min_confidence,
            'is_paused' => $settings->paused_until && $settings->paused_until > now(),
            'paused_until' => $settings->paused_until,
            'pause_remaining_hours' => $settings->paused_until && $settings->paused_until > now()
                ? round($settings->paused_until->diffInHours(now()), 1)
                : null,
        ];

        return $this
            ->httpResponse()
            ->setData($data)
            ->toApiResponse();
    }

    /**
     * Update auto-apply settings for a car
     *
     * @group Car Rentals - Vendor
     */
    public function update(Car $car, Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(401)
                ->toApiResponse();
        }

        // Verify car belongs to vendor
        if ($car->vendor_id !== $customer->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(403)
                ->toApiResponse();
        }

        // Validate request
        $request->validate([
            'is_auto_apply_enabled' => ['required', 'boolean'],
            'auto_apply_min_confidence' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Get or create auto-apply settings
        $settings = CarAutoApplySettings::firstOrCreate(
            ['car_id' => $car->id],
            [
                'is_auto_apply_enabled' => false,
                'auto_apply_min_confidence' => 70,
                'paused_until' => null,
            ]
        );

        // Update settings
        $settings->update([
            'is_auto_apply_enabled' => $request->boolean('is_auto_apply_enabled'),
            'auto_apply_min_confidence' => $request->integer('auto_apply_min_confidence'),
        ]);

        $data = [
            'car_id' => $car->id,
            'car_name' => $car->name,
            'is_auto_apply_enabled' => (bool) $settings->is_auto_apply_enabled,
            'auto_apply_min_confidence' => (int) $settings->auto_apply_min_confidence,
            'is_paused' => $settings->paused_until && $settings->paused_until > now(),
            'paused_until' => $settings->paused_until,
        ];

        return $this
            ->httpResponse()
            ->setData($data)
            ->setMessage('Auto-pricing settings updated successfully')
            ->toApiResponse();
    }

    /**
     * Pause auto-apply for N hours (1-336 hours = 2 weeks)
     *
     * @group Car Rentals - Vendor
     */
    public function pause(Car $car, Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(401)
                ->toApiResponse();
        }

        // Verify car belongs to vendor
        if ($car->vendor_id !== $customer->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(403)
                ->toApiResponse();
        }

        // Validate hours
        $request->validate([
            'hours' => ['required', 'integer', 'min:1', 'max:336'],
        ], [
            'hours.required' => 'Hours is required',
            'hours.min' => 'Pause duration must be at least 1 hour',
            'hours.max' => 'Pause duration cannot exceed 336 hours (2 weeks)',
        ]);

        // Get or create auto-apply settings
        $settings = CarAutoApplySettings::firstOrCreate(
            ['car_id' => $car->id],
            [
                'is_auto_apply_enabled' => false,
                'auto_apply_min_confidence' => 70,
                'paused_until' => null,
            ]
        );

        $hours = $request->integer('hours');
        $pausedUntil = now()->addHours($hours);

        $settings->update(['paused_until' => $pausedUntil]);

        $data = [
            'car_id' => $car->id,
            'car_name' => $car->name,
            'is_paused' => true,
            'paused_until' => $pausedUntil,
            'pause_duration_hours' => $hours,
            'pause_end_time' => $pausedUntil->format('Y-m-d H:i:s'),
        ];

        return $this
            ->httpResponse()
            ->setData($data)
            ->setMessage("Auto-pricing paused for {$hours} hours")
            ->toApiResponse();
    }

    /**
     * Resume auto-apply (clear pause)
     *
     * @group Car Rentals - Vendor
     */
    public function resume(Car $car)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(401)
                ->toApiResponse();
        }

        // Verify car belongs to vendor
        if ($car->vendor_id !== $customer->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(403)
                ->toApiResponse();
        }

        // Get or create auto-apply settings
        $settings = CarAutoApplySettings::firstOrCreate(
            ['car_id' => $car->id],
            [
                'is_auto_apply_enabled' => false,
                'auto_apply_min_confidence' => 70,
                'paused_until' => null,
            ]
        );

        // Check if already resumed
        if (!$settings->paused_until || $settings->paused_until <= now()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Auto-pricing is not currently paused')
                ->setStatusCode(400)
                ->toApiResponse();
        }

        $settings->update(['paused_until' => null]);

        $data = [
            'car_id' => $car->id,
            'car_name' => $car->name,
            'is_paused' => false,
            'paused_until' => null,
        ];

        return $this
            ->httpResponse()
            ->setData($data)
            ->setMessage('Auto-pricing resumed successfully')
            ->toApiResponse();
    }

    /**
     * Get applied recommendations history
     *
     * @group Car Rentals - Vendor
     */
    public function history(Car $car, Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(401)
                ->toApiResponse();
        }

        // Verify car belongs to vendor
        if ($car->vendor_id !== $customer->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Unauthorized')
                ->setStatusCode(403)
                ->toApiResponse();
        }

        // Validate filters
        $perPage = min($request->integer('per_page', 15), 100);
        $page = max($request->integer('page', 1), 1);

        // Get applied recommendations history
        $history = DemandPricingRecommendation::query()
            ->where('car_id', $car->id)
            ->where('status', 'applied')
            ->orderBy('applied_at', 'desc')
            ->select([
                'id',
                'car_id',
                'recommendation_date',
                'recommended_value',
                'confidence_score',
                'reason_codes',
                'adjustment_applied',
                'applied_at',
                'created_at',
            ])
            ->paginate($perPage, ['*'], 'page', $page);

        // Format history
        $formattedHistory = $history->map(function ($item) {
            return [
                'id' => $item->id,
                'recommendation_date' => $item->recommendation_date?->format('Y-m-d'),
                'recommended_price' => round($item->recommended_value, 2),
                'confidence_score' => round($item->confidence_score * 100, 2) . '%',
                'reasons' => $item->reason_codes ?? [],
                'adjustment_applied' => $item->adjustment_applied ? $item->adjustment_applied . '%' : null,
                'applied_at' => $item->applied_at?->format('Y-m-d H:i:s'),
            ];
        });

        return $this
            ->httpResponse()
            ->setData([
                'car_id' => $car->id,
                'car_name' => $car->name,
                'total_applied' => $history->total(),
                'history' => $formattedHistory,
                'pagination' => [
                    'current_page' => $history->currentPage(),
                    'per_page' => $history->perPage(),
                    'total' => $history->total(),
                    'last_page' => $history->lastPage(),
                ],
            ])
            ->toApiResponse();
    }
}
