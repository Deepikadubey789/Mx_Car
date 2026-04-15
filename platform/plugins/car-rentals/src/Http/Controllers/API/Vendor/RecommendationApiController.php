<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Requests\API\GetRecommendationsRequest;
use Botble\CarRentals\Http\Resources\DemandPricingRecommendationResource;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationApiController extends BaseApiController
{
    /**
     * List recommendations for vendor's cars with filters
     *
     * @group Car Rentals - Vendor
     */
    public function index(GetRecommendationsRequest $request)
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

        $query = DemandPricingRecommendation::query()
            ->whereHas('car', function ($q) use ($customer) {
                $q->where('vendor_id', $customer->id);
            })
            ->with('car');

        // Filter by status
        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filter by car_id
        if ($request->has('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }

        // Verify car belongs to vendor
        if ($request->has('car_id')) {
            $car = Car::findOrFail($request->input('car_id'));
            if ($car->vendor_id !== $customer->id) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Unauthorized')
                    ->setStatusCode(403)
                    ->toApiResponse();
            }
        }

        $perPage = min($request->integer('per_page', 15), 100);
        $page = max($request->integer('page', 1), 1);

        $recommendations = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this
            ->httpResponse()
            ->setData(DemandPricingRecommendationResource::collection($recommendations))
            ->toApiResponse();
    }

    /**
     * Get single recommendation details
     *
     * @group Car Rentals - Vendor
     */
    public function show(Car $car, DemandPricingRecommendation $recommendation)
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

        // Verify recommendation belongs to car
        if ($recommendation->car_id !== $car->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Not Found')
                ->setStatusCode(404)
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData(new DemandPricingRecommendationResource($recommendation))
            ->toApiResponse();
    }

    /**
     * Accept and apply recommendation as-is
     *
     * @group Car Rentals - Vendor
     */
    public function apply(Car $car, DemandPricingRecommendation $recommendation, Request $request)
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

        // Verify recommendation belongs to car
        if ($recommendation->car_id !== $car->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Not Found')
                ->setStatusCode(404)
                ->toApiResponse();
        }

        // Check if already applied or dismissed
        if ($recommendation->status !== 'pending') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('This recommendation has already been ' . $recommendation->status)
                ->setStatusCode(400)
                ->toApiResponse();
        }

        // Update car rental rate and mark recommendation as applied
        $car->update(['rental_rate' => $recommendation->recommended_value]);
        $recommendation->update([
            'status' => 'applied',
            'applied_at' => now(),
            'applied_by' => $customer->id,
        ]);

        return $this
            ->httpResponse()
            ->setData(new DemandPricingRecommendationResource($recommendation))
            ->setMessage('Recommendation applied successfully')
            ->toApiResponse();
    }

    /**
     * Dismiss/reject recommendation with reason
     *
     * @group Car Rentals - Vendor
     */
    public function dismiss(Car $car, DemandPricingRecommendation $recommendation, Request $request)
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

        // Verify recommendation belongs to car
        if ($recommendation->car_id !== $car->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Not Found')
                ->setStatusCode(404)
                ->toApiResponse();
        }

        // Check if already applied or dismissed
        if ($recommendation->status !== 'pending') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('This recommendation has already been ' . $recommendation->status)
                ->setStatusCode(400)
                ->toApiResponse();
        }

        // Validate reasons
        $request->validate([
            'reason' => ['required', 'string', 'in:too_high,too_low,inventory_issue,not_applicable,other'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Update recommendation as dismissed
        $recommendation->update([
            'status' => 'dismissed',
            'rejected_reason' => $request->input('reason'),
            'vendor_notes' => $request->input('notes'),
        ]);

        return $this
            ->httpResponse()
            ->setData(new DemandPricingRecommendationResource($recommendation))
            ->setMessage('Recommendation dismissed')
            ->toApiResponse();
    }

    /**
     * Tweak price (±10%) and apply recommendation
     *
     * @group Car Rentals - Vendor
     */
    public function adjust(Car $car, DemandPricingRecommendation $recommendation, Request $request)
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

        // Verify recommendation belongs to car
        if ($recommendation->car_id !== $car->id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Not Found')
                ->setStatusCode(404)
                ->toApiResponse();
        }

        // Check if already applied or dismissed
        if ($recommendation->status !== 'pending') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('This recommendation has already been ' . $recommendation->status)
                ->setStatusCode(400)
                ->toApiResponse();
        }

        // Validate adjustment percentage
        $request->validate([
            'adjustment_percent' => ['required', 'numeric', 'min:-10', 'max:10'],
        ], [
            'adjustment_percent.required' => 'Adjustment percentage is required',
            'adjustment_percent.min' => 'Adjustment must be at least -10%',
            'adjustment_percent.max' => 'Adjustment cannot exceed +10%',
        ]);

        $adjustmentPercent = $request->input('adjustment_percent');
        $recommendedPrice = $recommendation->recommended_value;
        $adjustedPrice = $recommendedPrice * (1 + $adjustmentPercent / 100);
        $adjustedPrice = round($adjustedPrice, 2);

        // Update car rental rate
        $car->update(['rental_rate' => $adjustedPrice]);

        // Mark recommendation as applied with adjustment
        $recommendation->update([
            'status' => 'applied',
            'applied_at' => now(),
            'applied_by' => $customer->id,
            'adjustment_applied' => $adjustmentPercent,
        ]);

        return $this
            ->httpResponse()
            ->setData(new DemandPricingRecommendationResource($recommendation))
            ->setMessage("Recommendation applied with {$adjustmentPercent}% adjustment")
            ->toApiResponse();
    }

    /**
     * Get dashboard summary metrics
     *
     * @group Car Rentals - Vendor
     */
    public function summary(Request $request)
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

        // Get metrics for vendor's recommendations
        $vendorCars = Car::where('vendor_id', $customer->id)->pluck('id');

        $total = DemandPricingRecommendation::whereIn('car_id', $vendorCars)->count();
        $pending = DemandPricingRecommendation::whereIn('car_id', $vendorCars)->where('status', 'pending')->count();
        $applied = DemandPricingRecommendation::whereIn('car_id', $vendorCars)->where('status', 'applied')->count();
        $dismissed = DemandPricingRecommendation::whereIn('car_id', $vendorCars)->where('status', 'dismissed')->count();

        // Calculate average confidence score for pending recommendations
        $avgConfidenceScore = DemandPricingRecommendation::whereIn('car_id', $vendorCars)
            ->where('status', 'pending')
            ->avg('confidence_score') ?? 0;

        // Get total estimated revenue impact from applied recommendations
        $totalRevenueImpact = DemandPricingRecommendation::whereIn('car_id', $vendorCars)
            ->where('status', 'applied')
            ->selectRaw('SUM((recommended_value - (SELECT rental_rate FROM cr_cars WHERE cr_cars.id = car_id)) * 30) as impact')
            ->value('impact') ?? 0;

        $summary = [
            'total_recommendations' => $total,
            'pending_count' => $pending,
            'applied_count' => $applied,
            'dismissed_count' => $dismissed,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'applied_percentage' => $total > 0 ? round(($applied / $total) * 100, 2) : 0,
            'average_confidence_score' => round($avgConfidenceScore * 100, 2),
            'estimated_revenue_impact' => round($totalRevenueImpact, 2),
        ];

        return $this
            ->httpResponse()
            ->setData($summary)
            ->toApiResponse();
    }
}
