<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Services\Kyc\KycVerificationService;
use Illuminate\Console\Command;

class PruneKycPayloadsCommand extends Command
{
    protected $signature = 'car-rentals:prune-kyc-payloads {--days= : Override retention days}';

    protected $description = 'Prune stale KYC provider payloads to minimal metadata.';

    public function handle(KycVerificationService $kycVerificationService): int
    {
        $daysOption = $this->option('days');
        $days = is_numeric($daysOption) ? (int) $daysOption : null;

        $updated = $kycVerificationService->pruneExpiredProviderPayloads($days);

        $this->info(sprintf('Pruned %d KYC verification payload(s).', $updated));

        return self::SUCCESS;
    }
}
