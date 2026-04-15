<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Http\Requests\VendorDemandPricingRequest;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Botble\CarRentals\Services\DemandPricingRecommendationService;
use Botble\CarRentals\Services\VendorDemandPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VendorDemandPricingController extends BaseController
{
    public function __construct(
        protected DemandPricingRecommendationService $recommendationService,
        protected VendorDemandPricingService $vendorService,
    ) {}

    /**
     * List recommendations (main page)
     * Default view: by car
     */
    public function index(Request $request)
    {
        $vendorId = auth('customer')->id();
        $view = $request->get('view', 'car'); // 'car' or 'date'
        $carId = $request->get('car_id', null);
        $status = $request->get('status', 'pending'); // 'pending', 'applied', 'dismissed'

        $pendingCount = $this->vendorService->getPendingCount($vendorId);
        $appliedCount = DemandPricingRecommendation::query()
            ->whereHas('car', fn ($q) => $q->where('vendor_id', $vendorId))
            ->where('status', 'applied')
            ->count();

        if ($view === 'date') {
            $from = $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now();
            $to = $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now()->addDays(30);

            $recommendations = $this->vendorService->getRecommendationsByDate(
                $vendorId,
                $from,
                $to,
                $status
            );
        } else {
            // Car view (default)
            $recommendations = $this->vendorService->getRecommendationsByCar($vendorId, $carId, $status);
        }

        return view('plugins/car-rentals::themes.vendor-dashboard.pricing.recommendations', [
            'vendorId' => $vendorId,
            'recommendations' => $recommendations,
            'view' => $view,
            'status' => $status,
            'carId' => $carId,
            'pendingCount' => $pendingCount,
            'appliedCount' => $appliedCount,
            'cars' => Car::where('vendor_id', $vendorId)->get(),
        ]);
    }

    /**
     * Calendar view of recommendations (by date)
     */
    public function calendar(Request $request)
    {
        $vendorId = auth('customer')->id();
        $from = $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now();
        $to = $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now()->addDays(30);

        $recommendations = $this->vendorService->getRecommendationsByDate($vendorId, $from, $to, 'pending');

        return view('plugins/car-rentals::themes.vendor-dashboard.pricing.calendar-view', [
            'recommendations' => $recommendations,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * View recommendations for a specific car
     */
    public function byCar(Car $car)
    {
        abort_unless($car->vendor_id === auth('customer')->id(), 403);

        $vendorId = auth('customer')->id();
        $recommendations = $this->vendorService->getRecommendationsByCar($vendorId, $car->id);

        return view('plugins/car-rentals::themes.vendor-dashboard.pricing.car-view', [
            'car' => $car,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Apply recommendation (accept the recommended price)
     */
    public function apply(DemandPricingRecommendation $recommendation, Request $request)
    {
        abort_unless($recommendation->car->vendor_id === auth('customer')->id(), 403);

        try {
            $this->recommendationService->applyRecommendation($recommendation, null);

            return redirect()->back()->with('success_msg', sprintf(
                'Recommendation applied successfully! Price updated to $%.2f',
                $recommendation->recommended_value,
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error_msg', 'Failed to apply recommendation: ' . $e->getMessage());
        }
    }

    /**
     * Dismiss recommendation (reject it)
     */
    public function dismiss(DemandPricingRecommendation $recommendation, VendorDemandPricingRequest $request)
    {
        abort_unless($recommendation->car->vendor_id === auth('customer')->id(), 403);

        $recommendation->update([
            'status' => 'dismissed',
            'rejected_reason' => $request->get('rejected_reason'),
            'vendor_notes' => $request->get('vendor_notes'),
            'applied_at' => now(),
        ]);

        return redirect()->back()->with('success_msg', 'Recommendation dismissed. Your feedback helps us improve future suggestions!');
    }

    /**
     * Adjust and apply recommendation (vendor tweaks price)
     */
    public function adjust(DemandPricingRecommendation $recommendation, VendorDemandPricingRequest $request)
    {
        abort_unless($recommendation->car->vendor_id === auth('customer')->id(), 403);

        $adjustedPrice = (float) $request->get('adjusted_price');
        $priceDelta = $adjustedPrice - $recommendation->recommended_value;

        // Validate adjustment is within ±10%
        $maxAdjustment = $recommendation->recommended_value * 0.10;
        if (abs($priceDelta) > $maxAdjustment) {
            return redirect()->back()->with('error_msg', sprintf(
                'Price adjustment limited to ±10%% ($%.2f to $%.2f)',
                $recommendation->recommended_value * 0.90,
                $recommendation->recommended_value * 1.10,
            ));
        }

        try {
            // Create the car_date with adjusted price
            $this->recommendationService->applyRecommendation($recommendation, null);

            // Update the recommendation with adjustment tracking
            $recommendation->update([
                'adjustment_applied' => $priceDelta,
                'vendor_adjustment_notes' => $request->get('adjustment_notes'),
            ]);

            return redirect()->back()->with('success_msg', sprintf(
                'Recommendation applied with adjustment! Final price: $%.2f (original: $%.2f)',
                $adjustedPrice,
                $recommendation->recommended_value,
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error_msg', 'Failed to apply recommendation: ' . $e->getMessage());
        }
    }

    /**
     * Historical performance report
     */
    public function performance(Request $request)
    {
        $vendorId = auth('customer')->id();
        $from = $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now()->subDays(30);
        $to = $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now();

        $performanceHistory = $this->vendorService->getPerformanceHistory($vendorId, $from, $to);

        // Calculate summary stats
        $stats = [
            'total_applied' => $performanceHistory->count(),
            'total_revenue_impact' => $performanceHistory->sum('estimated_revenue_impact'),
            'avg_confidence' => $performanceHistory->avg('confidence_score'),
            'distribution_by_car' => $performanceHistory->groupBy('car_id')->map(fn ($c) => $c->count()),
        ];

        return view('plugins/car-rentals::themes.vendor-dashboard.pricing.performance', [
            'history' => $performanceHistory,
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * AJAX: Get recommendations grouped by car
     */
    public function ajaxRecommendationsByCar(Request $request)
    {
        $vendorId = auth('customer')->id();
        $carId = $request->get('car_id');
        $status = $request->get('status', 'pending');

        $recommendations = $this->vendorService->getRecommendationsByCar($vendorId, $carId, $status);

        return Response::json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * AJAX: Get recommendations grouped by date
     */
    public function ajaxRecommendationsByDate(Request $request)
    {
        $vendorId = auth('customer')->id();
        $from = $request->get('from') ? Carbon::parse($request->get('from')) : Carbon::now();
        $to = $request->get('to') ? Carbon::parse($request->get('to')) : Carbon::now()->addDays(30);
        $status = $request->get('status', 'pending');

        $recommendations = $this->vendorService->getRecommendationsByDate($vendorId, $from, $to, $status);

        return Response::json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }
}
