<?php

namespace Botble\CarRentals\Jobs;

use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Services\DemandPricingRecommendationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDemandPricingRecommendationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected int $horizonDays = 30)
    {
    }

    public function handle(DemandPricingRecommendationService $service): void
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(max(1, $this->horizonDays));

        Car::query()
            ->whereHas('pricingPolicy', function ($query): void {
                $query->where('demand_recommendations_enabled', true);
            })
            ->chunkById(50, function ($cars) use ($service, $startDate, $endDate): void {
                foreach ($cars as $car) {
                    $service->generateForCar($car, $startDate, $endDate);
                }
            });
    }
}
