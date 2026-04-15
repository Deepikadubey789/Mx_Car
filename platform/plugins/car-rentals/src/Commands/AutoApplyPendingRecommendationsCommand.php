<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Services\AutoApplyQueueService;
use Illuminate\Console\Command;

class AutoApplyPendingRecommendationsCommand extends Command
{
    protected $signature = 'car-rentals:auto-apply-recommendations {--dry-run}';
    protected $description = 'Manually trigger auto-apply of pending recommendations';

    public function handle(AutoApplyQueueService $service): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - Recommendations will NOT be persisted');
        }

        $result = $service->autoApplyRecommendations();

        $this->info('✓ Applied: ' . $result['applied']);
        $this->info('⊘ Skipped: ' . $result['skipped']);
        if ($result['errors'] > 0) {
            $this->error('✗ Errors: ' . $result['errors']);
        }

        return self::SUCCESS;
    }
}
