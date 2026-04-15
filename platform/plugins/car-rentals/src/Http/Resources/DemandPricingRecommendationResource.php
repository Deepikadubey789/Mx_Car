<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandPricingRecommendationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Determine confidence label based on score
        $confidenceScore = $this->confidence_score ?? 0;
        $confidenceLabel = match (true) {
            $confidenceScore >= 0.9 => 'Very High',
            $confidenceScore >= 0.75 => 'High',
            $confidenceScore >= 0.6 => 'Moderate',
            default => 'Low',
        };

        // Get car name with fallback
        $carName = $this->whenLoaded('car', fn () => $this->car?->name, 'Unknown Car');

        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'car_name' => $carName,
            'recommendation_date' => $this->recommendation_date?->format('Y-m-d'),
            'current_price' => round($this->car?->rental_rate ?? 0, 2),
            'recommended_price' => round($this->recommended_value ?? 0, 2),
            'confidence_score' => round(($this->confidence_score ?? 0) * 100, 2),
            'confidence_score_percent' => round(($this->confidence_score ?? 0) * 100, 2) . '%',
            'confidence_label' => $confidenceLabel,
            'demand_score' => round($this->demand_score ?? 0, 2),
            'revenue_impact' => $this->calculateRevenueImpact(),
            'signals_breakdown' => $this->parseSignalsBreakdown(),
            'reason_codes' => $this->reason_codes ?? [],
            'status' => $this->status,
            'adjustment_applied' => $this->adjustment_applied ? $this->adjustment_applied . '%' : null,
            'rejected_reason' => $this->rejected_reason,
            'vendor_notes' => $this->vendor_notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'applied_at' => $this->applied_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Calculate estimated revenue impact
     */
    private function calculateRevenueImpact(): string
    {
        $currentPrice = $this->car?->rental_rate ?? 0;
        $recommendedPrice = $this->recommended_value ?? 0;

        if ($currentPrice === 0) {
            return '$0.00';
        }

        // Calculate per-booking impact
        $priceChange = $recommendedPrice - $currentPrice;

        // Estimate for 30-day month
        $monthlyImpact = $priceChange * 25; // Assuming ~25 bookings per month

        return '$' . number_format($monthlyImpact, 2);
    }

    /**
     * Parse and format signals breakdown
     */
    private function parseSignalsBreakdown(): array
    {
        $reasonCodes = $this->reason_codes ?? [];

        $signals = [];
        $signalDescriptions = [
            'high_occupancy' => 'High occupancy detected',
            'low_occupancy' => 'Low occupancy detected',
            'seasonal_demand' => 'Seasonal demand trends',
            'day_of_week' => 'Day of week patterns',
            'lead_time_pressure' => 'Near-term booking lead time',
            'competitor_pricing' => 'Competitor pricing signals',
            'market_trends' => 'Market trend analysis',
            'inventory_level' => 'Inventory level factors',
            'conversion_rate' => 'Booking conversion rates',
        ];

        foreach ($reasonCodes as $code) {
            if (isset($signalDescriptions[$code])) {
                $signals[] = [
                    'code' => $code,
                    'description' => $signalDescriptions[$code],
                ];
            }
        }

        return $signals;
    }
}
