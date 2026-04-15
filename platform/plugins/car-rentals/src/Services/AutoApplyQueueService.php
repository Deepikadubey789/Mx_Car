<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoApplyQueueService
{
    /**
     * Auto-apply recommendations based on confidence thresholds and settings
     */
    public function autoApplyRecommendations(): array
    {
        $applied = 0;
        $skipped = 0;
        $errors = 0;

        // Check global pause (emergency kill switch)
        if (CarRentalsHelper::getSetting('auto_apply_globally_paused')) {
            Log::info('Auto-apply skipped: globally paused');
            return ['applied' => 0, 'skipped' => 0, 'errors' => 0, 'reason' => 'global_pause'];
        }

        // Get all pending recommendations for cars with auto-apply enabled
        $recommendations = DemandPricingRecommendation::query()
            ->where('status', 'pending')
            ->where('expires_at', '>', now()) // Only non-expired
            ->with(['car.pricingPolicy'])
            ->whereHas('car.pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_enabled', true)
                    ->where(function ($query): void {
                        // Not paused, or pause has expired
                        $query->whereNull('demand_auto_apply_paused_until')
                            ->orWhere('demand_auto_apply_paused_until', '<', now());
                    });
            })
            ->get();

        foreach ($recommendations as $recommendation) {
            try {
                $policy = $recommendation->car->pricingPolicy;

                // Check confidence threshold
                if ($recommendation->confidence_score < $policy->demand_auto_apply_min_confidence) {
                    $skipped++;
                    continue;
                }

                // Apply via existing service (reuse Phase 1 logic)
                app(DemandPricingRecommendationService::class)
                    ->applyRecommendation($recommendation, $this->getSystemActorId());

                $applied++;

                // Update policy tracking
                $policy->update([
                    'demand_auto_apply_last_applied_at' => now(),
                    'demand_auto_apply_count' => DB::raw('demand_auto_apply_count + 1'),
                ]);

                Log::info('Auto-applied recommendation', [
                    'recommendation_id' => $recommendation->id,
                    'car_id' => $recommendation->car_id,
                    'price' => $recommendation->recommended_value,
                    'confidence' => $recommendation->confidence_score,
                ]);

            } catch (\Exception $e) {
                $errors++;
                Log::error('Auto-apply failed for recommendation ' . $recommendation->id, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $result = [
            'applied' => $applied,
            'skipped' => $skipped,
            'errors' => $errors,
        ];

        Log::info('Auto-apply job completed', $result);

        return $result;
    }

    /**
     * Pause auto-apply for a specific car
     */
    public function pauseAutoApply(Car $car, ?int $hours = null): void
    {
        $pausedUntil = $hours ? now()->addHours($hours) : now()->addDay();

        $car->pricingPolicy->update([
            'demand_auto_apply_paused_until' => $pausedUntil,
        ]);

        Log::info('Auto-apply paused for car', [
            'car_id' => $car->id,
            'paused_until' => $pausedUntil,
        ]);
    }

    /**
     * Resume auto-apply for a specific car
     */
    public function resumeAutoApply(Car $car): void
    {
        $car->pricingPolicy->update([
            'demand_auto_apply_paused_until' => null,
        ]);

        Log::info('Auto-apply resumed for car', [
            'car_id' => $car->id,
        ]);
    }

    /**
     * Check if auto-apply is enabled and active for a car
     */
    public function isAutoApplyActive(Car $car): bool
    {
        $policy = $car->pricingPolicy;

        if (!$policy || !$policy->demand_auto_apply_enabled) {
            return false;
        }

        // Check if paused
        if ($policy->demand_auto_apply_paused_until && $policy->demand_auto_apply_paused_until > now()) {
            return false;
        }

        // Check global pause
        if (CarRentalsHelper::getSetting('auto_apply_globally_paused')) {
            return false;
        }

        return true;
    }

    /**
     * Get system actor ID for audit trail
     * Returns admin ID if running from command, or null for system
     */
    protected function getSystemActorId(): ?int
    {
        // If running from queue/command, use system ID
        // If running from admin UI, use authenticated admin ID
        if (auth('admin')->check()) {
            return auth('admin')->id();
        }

        // Use special system actor ID (0 indicates auto-apply)
        return 0;
    }

    /**
     * Get auto-apply metrics for a date range
     */
    public function getMetrics(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): array
    {
        $appliedCount = DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0) // Auto-applied (system actor)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->count();

        $avgConfidence = DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0)
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->avg('confidence_score') ?? 0;

        $carsWithAutoApply = Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_enabled', true);
            })
            ->count();

        $carsWithPause = Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_auto_apply_paused_until', '>', now());
            })
            ->count();

        return [
            'applied_count' => $appliedCount,
            'avg_confidence' => round($avgConfidence, 4),
            'cars_with_auto_apply' => $carsWithAutoApply,
            'cars_paused' => $carsWithPause,
        ];
    }
}
