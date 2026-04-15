<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Botble\CarRentals\Notifications\VendorHighConfidenceRecommendationNotification;
use Botble\CarRentals\Jobs\SendVendorRecommendationNotificationJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class VendorRecommendationNotificationService
{
    protected VendorDemandPricingService $vendorService;

    public function __construct(VendorDemandPricingService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * Send notifications for high-confidence recommendations generated
     * Batches notifications: one per vendor, showing top recommendations
     */
    public function notifyHighConfidenceRecommendations(float $confidenceThreshold = 0.80): void
    {
        // Get all recommendations with confidence >= threshold created today (not yet notified)
        $recommendations = DemandPricingRecommendation::query()
            ->where('confidence_score', '>=', $confidenceThreshold)
            ->where('status', 'pending')
            ->whereDate('generated_at', today())
            ->orderBy('confidence_score', 'desc')
            ->get();

        if ($recommendations->isEmpty()) {
            return;
        }

        // Group by vendor
        $byVendor = $recommendations->groupBy(fn ($rec) => $rec->car->vendor_id);

        // Send batched notification per vendor
        foreach ($byVendor as $vendorId => $vendorRecommendations) {
            $this->notifyVendor($vendorId, $vendorRecommendations);
        }
    }

    /**
     * Notify a specific vendor about their high-confidence recommendations
     */
    public function notifyVendor(int $vendorId, Collection $recommendations): void
    {
        $vendor = Customer::query()->find($vendorId);

        if (! $vendor || ! $vendor->is_vendor) {
            return;
        }

        // Take top 5 by confidence
        $topRecommendations = $recommendations
            ->sortByDesc('confidence_score')
            ->take(5)
            ->values()
            ->map(fn ($rec) => $this->vendorService->formatForVendor($rec));

        // Dispatch notification job (queued)
        SendVendorRecommendationNotificationJob::dispatch($vendor, $topRecommendations);
    }

    /**
     * Batch notifications for all vendors with pending recommendations
     * (called separately, not part of generation cycle)
     */
    public function notifyAllVendorsWithPending(): void
    {
        // Get all vendors with pending recommendations
        $vendorIds = DemandPricingRecommendation::query()
            ->where('status', 'pending')
            ->distinct('car_id')
            ->get()
            ->pluck('car.vendor_id')
            ->filter()
            ->unique();

        foreach ($vendorIds as $vendorId) {
            $pending = $this->vendorService->getTopRecommendations($vendorId, 5);
            $this->notifyVendor($vendorId, $pending);
        }
    }

    /**
     * Send urgent notification for expiring recommendations (within 24 hours)
     */
    public function notifyExpiringRecommendations(int $hoursThreshold = 24): void
    {
        // Get all vendors
        $vendors = Customer::where('is_vendor', true)->get();

        foreach ($vendors as $vendor) {
            $expiringRecs = $this->vendorService->getExpiringRecommendations($vendor->id, $hoursThreshold);

            if ($expiringRecs->isNotEmpty()) {
                Notification::send($vendor, new VendorHighConfidenceRecommendationNotification(
                    $expiringRecs,
                    'expiring'
                ));
            }
        }
    }

    /**
     * Send notification via multiple channels
     */
    public function sendViaChannels(Customer $vendor, Collection $recommendations, array $channels = ['mail', 'database']): void
    {
        $notification = new VendorHighConfidenceRecommendationNotification($recommendations);

        foreach ($channels as $channel) {
            $notification->via(null); // Will use notification settings
        }

        Notification::send($vendor, $notification);
    }
}
