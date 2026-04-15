<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Jobs\GenerateDemandPricingRecommendationsJob;
use Illuminate\Console\Command;

class GenerateDemandPricingRecommendationsCommand extends Command
{
    protected $signature = 'car-rentals:generate-demand-pricing-recommendations {--days=30} {--sync}';

    protected $description = 'Generate demand-aware pricing recommendations for cars with recommendations enabled';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = max(1, $days);

        if ($this->option('sync')) {
            $this->info('Starting demand pricing recommendations generation...');
            $startTime = microtime(true);
            
            $job = new GenerateDemandPricingRecommendationsJob($days);
            $job->handle(app(\Botble\CarRentals\Services\DemandPricingRecommendationService::class));
            
            $elapsed = round(microtime(true) - $startTime, 2);
            $this->info(sprintf('Generated demand pricing recommendations synchronously for %d day(s) in %ss.', $days, $elapsed));

            return self::SUCCESS;
        }

        GenerateDemandPricingRecommendationsJob::dispatch($days);
        $this->info(sprintf('Demand pricing recommendation job dispatched for %d day(s).', $days));

        return self::SUCCESS;
    }
}
