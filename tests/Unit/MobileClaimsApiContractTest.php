<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MobileClaimsApiContractTest extends TestCase
{
    protected string $repoRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoRoot = dirname(__DIR__, 2);
    }

    public function test_mobile_claims_api_routes_are_registered(): void
    {
        $routes = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/routes/api.php');
        $this->assertIsString($routes);

        $this->assertStringContainsString("Route::prefix('bookings/{booking}/claims')", $routes);
        $this->assertStringContainsString("'Claims\\CustomerClaimController@index'", $routes);
        $this->assertStringContainsString("'Claims\\CustomerClaimController@timeline'", $routes);
        $this->assertStringContainsString("Route::middleware(['vendor'])->prefix('vendor')->group", $routes);
        $this->assertStringContainsString("'Claims\\VendorClaimController@index'", $routes);
        $this->assertStringContainsString("'Claims\\VendorClaimController@timeline'", $routes);
        $this->assertStringContainsString("Route::prefix('admin/bookings')->group", $routes);
        $this->assertStringContainsString("'Claims\\AdminClaimController@queue'", $routes);
        $this->assertStringContainsString("'Claims\\AdminClaimController@metrics'", $routes);
        $this->assertStringContainsString("'Claims\\AdminClaimController@store'", $routes);
        $this->assertStringContainsString("'Claims\\AdminClaimController@update'", $routes);
    }

    public function test_mobile_claims_api_controllers_enforce_audience_and_permissions(): void
    {
        $customerController = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Controllers/API/Claims/CustomerClaimController.php');
        $vendorController = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Controllers/API/Claims/VendorClaimController.php');
        $adminController = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Controllers/API/Claims/AdminClaimController.php');

        $this->assertIsString($customerController);
        $this->assertIsString($vendorController);
        $this->assertIsString($adminController);

        $this->assertStringContainsString("->where('customer_id', \$customer?->id)", $customerController);
        $this->assertStringContainsString('buildPublicTimelineRows', $customerController);

        $this->assertStringContainsString("->where('vendor_id', \$vendor?->id)", $vendorController);
        $this->assertStringContainsString('buildPublicTimelineRows', $vendorController);

        $this->assertStringContainsString("authorizePermission('car-rentals.bookings.claims.index')", $adminController);
        $this->assertStringContainsString("authorizePermission('car-rentals.bookings.claims.assign')", $adminController);
        $this->assertStringContainsString("authorizePermission('car-rentals.bookings.claims.resolve')", $adminController);
        $this->assertStringContainsString("authorizePermission('car-rentals.bookings.claims.financial')", $adminController);
        $this->assertStringContainsString('ClaimResolutionSettlementService', $adminController);
        $this->assertStringContainsString('TripTimelineBuilder', $adminController);
        $this->assertStringContainsString('notifyPublicClaimChanges', $adminController);
    }

    public function test_mobile_claims_resources_and_requests_define_public_and_admin_contracts(): void
    {
        $claimResource = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Resources/ClaimResource.php');
        $claimDetailResource = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Resources/ClaimDetailResource.php');
        $timelineResource = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Resources/ClaimTimelineResource.php');
        $adminQueueResource = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Resources/AdminClaimQueueResource.php');
        $storeRequest = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Requests/API/AdminStoreBookingClaimRequest.php');
        $updateRequest = file_get_contents($this->repoRoot . '/platform/plugins/car-rentals/src/Http/Requests/API/AdminUpdateBookingClaimRequest.php');

        $this->assertIsString($claimResource);
        $this->assertIsString($claimDetailResource);
        $this->assertIsString($timelineResource);
        $this->assertIsString($adminQueueResource);
        $this->assertIsString($storeRequest);
        $this->assertIsString($updateRequest);

        $this->assertStringContainsString("'booking_number'", $claimResource);
        $this->assertStringContainsString("'outcome_action'", $claimResource);
        $this->assertStringNotContainsString("'settlement_metadata'", $claimResource);
        $this->assertStringNotContainsString("'checklist_notes'", $claimResource);

        $this->assertStringContainsString("'settlement_metadata'", $claimDetailResource);
        $this->assertStringContainsString("'checklist_notes'", $claimDetailResource);
        $this->assertStringContainsString("'actor'", $timelineResource);
        $this->assertStringContainsString("'sla_breached'", $adminQueueResource);

        $this->assertStringContainsString("'outcome_action'", $storeRequest);
        $this->assertStringContainsString("'evidence_provenance'", $storeRequest);
        $this->assertStringContainsString("'escalated'", $updateRequest);
        $this->assertStringContainsString("'resolution_note'", $updateRequest);
    }
}
