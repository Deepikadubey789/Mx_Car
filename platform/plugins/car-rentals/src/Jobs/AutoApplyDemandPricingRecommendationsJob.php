<?php

namespace Botble\CarRentals\Jobs;

use Botble\CarRentals\Services\AutoApplyQueueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoApplyDemandPricingRecommendationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(AutoApplyQueueService $service): void
    {
        $result = $service->autoApplyRecommendations();
        
        // Log result for monitoring
        \Illuminate\Support\Facades\Log::info('Auto-apply job completed', [
            'applied' => $result['applied'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors'],
        ]);
    }
}
