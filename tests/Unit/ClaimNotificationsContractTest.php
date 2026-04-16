<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ClaimNotificationsContractTest extends TestCase
{
    protected string $repoRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoRoot = dirname(__DIR__, 2);
    }

    public function test_claim_notification_wiring_exists(): void
    {
        $controller = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Controllers/BookingClaimController.php');
        $dispatcher = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Services/ClaimNotificationDispatcher.php');
        $lang = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/resources/lang/en/disputes.php');

        $this->assertIsString($controller);
        $this->assertIsString($dispatcher);
        $this->assertIsString($lang);

        $this->assertStringContainsString('ClaimNotificationDispatcher', $controller);
        $this->assertStringContainsString('notifyOpened', $controller);
        $this->assertStringContainsString('notifyPublicClaimChanges', $controller);
        $this->assertStringContainsString('notifyDocsRequested', $dispatcher);
        $this->assertStringContainsString('notifyStatusUpdated', $dispatcher);
        $this->assertStringContainsString('notifyClosed', $dispatcher);
        $this->assertStringContainsString('notifyFinancialOutcome', $dispatcher);
        $this->assertStringContainsString('claim_notification_opened', $dispatcher);
        $this->assertStringContainsString('claim_notification_financial_outcome', $dispatcher);

        $this->assertStringContainsString('notification_claim_opened_subject', $lang);
        $this->assertStringContainsString('notification_claim_docs_requested_subject', $lang);
        $this->assertStringContainsString('notification_claim_status_updated_subject', $lang);
        $this->assertStringContainsString('notification_claim_closed_subject', $lang);
        $this->assertStringContainsString('notification_claim_financial_outcome_subject', $lang);
    }
}
