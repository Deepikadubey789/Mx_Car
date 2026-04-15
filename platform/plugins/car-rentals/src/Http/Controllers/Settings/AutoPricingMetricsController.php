<?php

namespace Botble\CarRentals\Http\Controllers\Settings;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Services\AutoPricingMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AutoPricingMetricsController extends BaseController
{
    public function index(Request $request, AutoPricingMetricsService $metricsService)
    {
        $this->pageTitle('Auto-Pricing Metrics');

        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(7);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        $metrics = $metricsService->getMetrics($startDate, $endDate);
        $perCarMetrics = $metricsService->getPerCarMetrics($startDate, $endDate);

        $latestApplications = \Botble\CarRentals\Models\DemandPricingRecommendation::query()
            ->where('status', 'applied')
            ->where('applied_by', 0) // Auto-applied
            ->whereBetween('applied_at', [$startDate, $endDate])
            ->with(['car'])
            ->orderBy('applied_at', 'desc')
            ->limit(15)
            ->get();

        $globalPaused = (bool) CarRentalsHelper::getSetting('auto_apply_globally_paused');

        return view('plugins/car-rentals::auto-pricing-metrics', [
            'metrics' => $metrics,
            'perCarMetrics' => $perCarMetrics,
            'latestApplications' => $latestApplications,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'globalPaused' => $globalPaused,
        ]);
    }

    public function toggleGlobalPause(Request $request)
    {
        $this->authorize('update.settings');

        $paused = (bool) $request->input('paused');
        CarRentalsHelper::setSetting('auto_apply_globally_paused', $paused);

        $message = $paused 
            ? 'Auto-pricing globally paused - no recommendations will be auto-applied'
            : 'Auto-pricing resumed - recommendations will be auto-applied normally';

        return back()->with('success', $message);
    }

    public function updateSettings(Request $request)
    {
        $this->authorize('update.settings');

        $validated = $request->validate([
            'auto_apply_default_confidence' => ['required', 'numeric', 'min:0', 'max:1'],
            'auto_apply_default_max_change' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        CarRentalsHelper::setSetting('auto_apply_default_confidence', $validated['auto_apply_default_confidence']);
        if ($validated['auto_apply_default_max_change']) {
            CarRentalsHelper::setSetting('auto_apply_default_max_change', $validated['auto_apply_default_max_change']);
        }

        return back()->with('success', 'Auto-pricing settings updated successfully');
    }
}
