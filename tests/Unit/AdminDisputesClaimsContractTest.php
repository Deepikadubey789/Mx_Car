<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdminDisputesClaimsContractTest extends TestCase
{
    protected string $repoRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repoRoot = dirname(__DIR__, 2);
    }

    public function test_bookings_routes_require_edit_permission_for_timeline_and_claims(): void
    {
        $contents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/routes/web.php');
        $this->assertIsString($contents);

        $this->assertStringContainsString("'as' => 'claims.index'", $contents);
        $this->assertStringContainsString("'as' => 'claims.metrics'", $contents);
        $this->assertStringContainsString("'as' => 'timeline'", $contents);
        $this->assertStringContainsString("'as' => 'claims.store'", $contents);
        $this->assertStringContainsString("'as' => 'claims.update'", $contents);
        $this->assertMatchesRegularExpression(
            "/'as' => 'claims\\.index',[\\s\\S]*?'permission' => 'car-rentals\\.bookings\\.claims\\.index'/",
            $contents
        );
        $this->assertMatchesRegularExpression(
            "/'as' => 'claims\\.metrics',[\\s\\S]*?'permission' => 'car-rentals\\.bookings\\.claims\\.index'/",
            $contents
        );
        $this->assertMatchesRegularExpression(
            "/'as' => 'timeline',[\\s\\S]*?'permission' => 'car-rentals\\.bookings\\.edit'/",
            $contents
        );
        $this->assertMatchesRegularExpression(
            "/'as' => 'claims\\.store',[\\s\\S]*?'permission' => 'car-rentals\\.bookings\\.claims\\.assign'/",
            $contents
        );
        $this->assertMatchesRegularExpression(
            "/'as' => 'claims\\.update',[\\s\\S]*?'permission' => 'car-rentals\\.bookings\\.claims\\.resolve'/",
            $contents
        );
    }

    public function test_booking_form_casefile_gate_is_bookings_edit_only(): void
    {
        $contents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/src/Forms/BookingForm.php');
        $this->assertIsString($contents);

        $this->assertStringContainsString("hasPermission('car-rentals.bookings.edit')", $contents);
        $this->assertStringNotContainsString("hasPermission('car-rentals.bookings.disputes.view')", $contents);
    }

    public function test_claims_and_support_actions_are_wired_for_timeline(): void
    {
        $builderContents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/src/Services/TripTimelineBuilder.php');
        $controllerContents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/src/Http/Controllers/BookingController.php');
        $claimsControllerContents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/src/Http/Controllers/BookingClaimController.php');
        $claimsQueueContents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/resources/views/bookings/claims-queue.blade.php');
        $serviceProviderContents = file_get_contents($this->repoRoot.'/platform/plugins/car-rentals/src/Providers/CarRentalsServiceProvider.php');

        $this->assertIsString($builderContents);
        $this->assertIsString($controllerContents);
        $this->assertIsString($claimsControllerContents);
        $this->assertIsString($claimsQueueContents);
        $this->assertIsString($serviceProviderContents);

        $this->assertStringContainsString("'claims.assignee'", $builderContents);
        $this->assertStringContainsString('claimRows', $builderContents);
        $this->assertStringContainsString('disputes.timeline_claim_created', $builderContents);
        $this->assertStringContainsString('disputes.timeline_claim_updated', $builderContents);

        $this->assertStringContainsString("'key_instructions_sent'", $controllerContents);
        $this->assertStringContainsString("'completion_update'", $controllerContents);
        $this->assertStringContainsString("'claim_created'", $claimsControllerContents);
        $this->assertStringContainsString("'claim_updated'", $claimsControllerContents);
        $this->assertStringContainsString('public function index(Request $request)', $claimsControllerContents);
        $this->assertStringContainsString('public function metrics(): JsonResponse', $claimsControllerContents);
        $this->assertStringContainsString('assertValidTransition', $claimsControllerContents);
        $this->assertStringContainsString('ClaimResolutionSettlementService', $claimsControllerContents);
        $this->assertStringContainsString('ClaimAssignmentNotification', $claimsControllerContents);
        $this->assertStringContainsString('ClaimSlaBreachNotification', $claimsControllerContents);
        $this->assertStringContainsString('withQueryString()', $claimsControllerContents);
        $this->assertStringContainsString("whereIn('status', ['open', 'under_review', 'awaiting_docs', 'ready_for_decision'])", $claimsControllerContents);
        $this->assertStringContainsString("route('car-rentals.bookings.claims.update'", $claimsQueueContents);
        $this->assertStringContainsString("'X-Requested-With': 'XMLHttpRequest'", $claimsQueueContents);
        $this->assertStringContainsString("'claim_updated'", $claimsControllerContents);
        $this->assertStringContainsString("'route' => 'car-rentals.bookings.claims.index'", $serviceProviderContents);
        $this->assertStringContainsString("'permissions' => ['car-rentals.bookings.claims.index']", $serviceProviderContents);
    }
}
