<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ClaimsWorkflowContractTest extends TestCase
{
    protected string $repoRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoRoot = dirname(__DIR__, 2);
    }

    public function test_claim_model_and_controller_include_workflow_and_settlement_contracts(): void
    {
        $claimModel = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Models/BookingClaim.php');
        $claimController = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Controllers/BookingClaimController.php');
        $timelineBuilder = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Services/TripTimelineBuilder.php');
        $settlementService = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Services/ClaimResolutionSettlementService.php');
        $migration = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/database/migrations/2026_04_16_180000_extend_cr_booking_claims_for_workflow_and_settlement.php');
        $permissions = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/config/permissions.php');

        $this->assertIsString($claimModel);
        $this->assertIsString($claimController);
        $this->assertIsString($timelineBuilder);
        $this->assertIsString($settlementService);
        $this->assertIsString($migration);
        $this->assertIsString($permissions);

        $this->assertStringContainsString("'awaiting_docs'", $claimModel);
        $this->assertStringContainsString("'ready_for_decision'", $claimModel);
        $this->assertStringContainsString("'outcome_action'", $claimModel);
        $this->assertStringContainsString("'resolution_due_at'", $claimModel);

        $this->assertStringContainsString('assertValidTransition', $claimController);
        $this->assertStringContainsString("in_array(\$to, ['ready_for_decision', 'resolved', 'rejected', 'closed_no_action']", $claimController);
        $this->assertStringContainsString('notifyAssignmentIfChanged', $claimController);
        $this->assertStringContainsString('notifySlaBreachIfNeeded', $claimController);
        $this->assertStringContainsString("car-rentals.bookings.claims.financial", $claimController);

        $this->assertStringContainsString('timeline_claim_settlement', $timelineBuilder);
        $this->assertStringContainsString('evidence_provenance', $timelineBuilder);

        $this->assertStringContainsString('public function settle', $settlementService);
        $this->assertStringContainsString("'capture_deposit'", $settlementService);
        $this->assertStringContainsString("'release_deposit'", $settlementService);
        $this->assertStringContainsString("'partial_refund'", $settlementService);

        $this->assertStringContainsString('liability_decision', $migration);
        $this->assertStringContainsString('settlement_status', $migration);
        $this->assertStringContainsString('first_response_due_at', $migration);
        $this->assertStringContainsString('evidence_provenance', $migration);

        $this->assertStringContainsString('car-rentals.bookings.claims.index', $permissions);
        $this->assertStringContainsString('car-rentals.bookings.claims.assign', $permissions);
        $this->assertStringContainsString('car-rentals.bookings.claims.resolve', $permissions);
        $this->assertStringContainsString('car-rentals.bookings.claims.financial', $permissions);
    }
}
