<?php

namespace Tests\Feature;

use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Services\DepositHoldSettlementService;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Tests\TestCase;

class DepositHoldSettlementServiceTest extends TestCase
{
    protected DepositHoldSettlementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DepositHoldSettlementService::class);
        Carbon::setTestNow(Carbon::parse('2026-04-08 12:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Helper: Create a test booking with all required fields
     */
    protected function createTestBooking(array $overrides = []): Booking
    {
        $defaults = [
            'customer_id' => 1,
            'car_id' => 1,
            'pick_up_location_id' => 1,
            'drop_off_location_id' => 1,
            'pick_up_date' => now()->addDay(),
            'drop_off_date' => now()->addDays(3),
            'status' => 'completed',
            'amount' => 500.00,
            'sub_total' => 500.00,
            'deposit_hold_status' => 'authorized',
            'deposit_hold_amount' => 100.00,
            'deposit_captured_amount' => 0,
            'deposit_released_amount' => 0,
        ];

        return Booking::create(array_merge($defaults, $overrides));
    }

    /**
     * Helper: Create a test payment with all required fields
     */
    protected function createTestPayment(Booking $booking, array $overrides = []): Payment
    {
        $defaults = [
            'reference_id' => $booking->id,
            'reference_type' => Booking::class,
            'amount' => 100.00,
            'is_authorized_hold' => true,
            'authorized_amount' => 100.00,
            'captured_amount' => 0,
            'released_amount' => 0,
            'currency' => 'USD',
        ];

        return Payment::create(array_merge($defaults, $overrides));
    }

    /**
     * Test 1: Release full authorization hold - funds returned to customer
     */
    public function test_release_full_authorization_hold(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_amount' => 100.00,
        ]);

        $payment = $this->createTestPayment($booking, [
            'amount' => 100.00,
            'authorized_amount' => 100.00,
        ]);

        $result = $this->service->settle($booking, 'release');

        $this->assertTrue($result['ok']);
        $booking->refresh();
        
        $this->assertContains($booking->deposit_hold_status, ['release_pending_provider_expiry', 'released']);
        $this->assertEquals(0, $booking->deposit_captured_amount);
        $this->assertEquals(100.00, $booking->deposit_released_amount);
        $this->assertNotNull($booking->deposit_settled_at);
    }

    /**
     * Test 2: Partial capture - keep $50 for damages, refund $100
     */
    public function test_partial_capture_of_authorization_hold(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_amount' => 150.00,
        ]);

        $payment = $this->createTestPayment($booking, [
            'amount' => 150.00,
            'authorized_amount' => 150.00,
        ]);

        $result = $this->service->settle($booking, 'capture_partial', 50.00);

        $this->assertTrue($result['ok']);
        $booking->refresh();
        
        $this->assertEquals('captured', $booking->deposit_hold_status);
        $this->assertEquals(50.00, $booking->deposit_captured_amount);  // Keep $50 for damages
        $this->assertEquals(100.00, $booking->deposit_released_amount); // Refund $100
        $this->assertNotNull($booking->deposit_settled_at);
    }

    /**
     * Test 3: Full capture - keep entire deposit
     */
    public function test_full_capture_of_authorization_hold(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_amount' => 200.00,
        ]);

        $payment = $this->createTestPayment($booking, [
            'amount' => 200.00,
            'authorized_amount' => 200.00,
        ]);

        $result = $this->service->settle($booking, 'capture_full');

        $this->assertTrue($result['ok']);
        $booking->refresh();
        
        $this->assertEquals('captured', $booking->deposit_hold_status);
        $this->assertEquals(200.00, $booking->deposit_captured_amount);
        $this->assertEquals(0, $booking->deposit_released_amount);
        $this->assertNotNull($booking->deposit_settled_at);
    }

    /**
     * Test 4: Partial capture with max amount = authorized amount
     */
    public function test_partial_capture_with_exactly_authorized_amount(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_amount' => 100.00,
        ]);

        $payment = $this->createTestPayment($booking, [
            'amount' => 100.00,
            'authorized_amount' => 100.00,
        ]);

        // Capture exactly the authorized amount (no refund, no remainder)
        $result = $this->service->settle($booking, 'capture_partial', 100.00);

        $this->assertTrue($result['ok']);
        $booking->refresh();
        
        $this->assertEquals('captured', $booking->deposit_hold_status);
        $this->assertEquals(100.00, $booking->deposit_captured_amount);
        $this->assertEquals(0, $booking->deposit_released_amount);
    }

    /**
     * Test 5: Validation - Reject partial capture with amount <= 0
     */
    public function test_partial_capture_rejects_zero_amount(): void
    {
        $booking = $this->createTestBooking();
        $payment = $this->createTestPayment($booking);

        $result = $this->service->settle($booking, 'capture_partial', 0);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('greater than 0', $result['message']);
        
        $booking->refresh();
        $this->assertEquals('authorized', $booking->deposit_hold_status);
        $this->assertNull($booking->deposit_settled_at);
    }

    /**
     * Test 6: Validation - Reject partial capture with negative amount
     */
    public function test_partial_capture_rejects_negative_amount(): void
    {
        $booking = Booking::create([
            'customer_id' => 1,
            'car_id' => 1,
            'pick_up_location_id' => 1,
            'drop_off_location_id' => 1,
            'pick_up_date' => now()->addDay(),
            'drop_off_date' => now()->addDays(3),
            'status' => 'completed',
            'amount' => 500.00,
            'deposit_hold_status' => 'authorized',
            'deposit_hold_amount' => 100.00,
        ]);

        $payment = Payment::create([
            'reference_id' => $booking->id,
            'reference_type' => Booking::class,
            'amount' => 100.00,
            'is_authorized_hold' => true,
            'authorized_amount' => 100.00,
            'currency' => 'USD',
        ]);

        $result = $this->service->settle($booking, 'capture_partial', -50.00);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('greater than 0', $result['message']);
    }

    /**
     * Test 7: Validation - Reject partial capture with amount > authorized
     */
    public function test_partial_capture_rejects_overflow(): void
    {
        $booking = Booking::create([
            'customer_id' => 1,
            'car_id' => 1,
            'pick_up_location_id' => 1,
            'drop_off_location_id' => 1,
            'pick_up_date' => now()->addDay(),
            'drop_off_date' => now()->addDays(3),
            'status' => 'completed',
            'amount' => 500.00,
            'deposit_hold_status' => 'authorized',
            'deposit_hold_amount' => 100.00,
        ]);

        $payment = Payment::create([
            'reference_id' => $booking->id,
            'reference_type' => Booking::class,
            'amount' => 100.00,
            'is_authorized_hold' => true,
            'authorized_amount' => 100.00,
            'currency' => 'USD',
        ]);

        $result = $this->service->settle($booking, 'capture_partial', 150.00);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('cannot exceed', $result['message']);
    }

    /**
     * Test 8: Validation - Reject if already settled
     */
    public function test_reject_settlement_if_already_settled(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_status' => 'captured',
            'deposit_hold_amount' => 100.00,
            'deposit_captured_amount' => 100.00,
            'deposit_settled_at' => now(),
        ]);

        $payment = $this->createTestPayment($booking, [
            'is_authorized_hold' => false,
            'captured_amount' => 100.00,
        ]);

        $result = $this->service->settle($booking, 'release');

        $this->assertTrue($result['ok']);
        $this->assertStringContainsString('already settled', $result['message']);
    }

    /**
     * Test 9: Validation - Reject if no authorized hold exists
     */
    public function test_reject_settlement_if_no_authorized_hold(): void
    {
        $booking = $this->createTestBooking([
            'deposit_hold_status' => null,
            'deposit_hold_amount' => 0,
        ]);

        $result = $this->service->settle($booking, 'release');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('No authorized deposit hold', $result['message']);
    }

    /**
     * Test 10: Verify payment model updates on settlement
     */
    public function test_payment_model_updates_on_settlement(): void
    {
        $booking = Booking::create([
            'customer_id' => 1,
            'car_id' => 1,
            'pick_up_location_id' => 1,
            'drop_off_location_id' => 1,
            'pick_up_date' => now()->addDay(),
            'drop_off_date' => now()->addDays(3),
            'status' => 'completed',
            'amount' => 500.00,
            'deposit_hold_status' => 'authorized',
            'deposit_hold_amount' => 100.00,
        ]);

        $payment = Payment::create([
            'reference_id' => $booking->id,
            'reference_type' => Booking::class,
            'amount' => 100.00,
            'is_authorized_hold' => true,
            'authorized_amount' => 100.00,
            'captured_amount' => 0,
            'released_amount' => 0,
            'currency' => 'USD',
        ]);

        $result = $this->service->settle($booking, 'release');

        $this->assertTrue($result['ok']);
        $payment->refresh();
        
        $this->assertFalse($payment->is_authorized_hold);
        $this->assertEquals(0, $payment->captured_amount);
        $this->assertEquals(100.00, $payment->released_amount);
        $this->assertNotNull($payment->released_at);
        $this->assertNull($payment->captured_at);
    }

    /**
     * Test 11: Verify settlement timestamps are set correctly
     */
    public function test_settlement_timestamps_are_set(): void
    {
        $booking = Booking::create([
            'customer_id' => 1,
            'car_id' => 1,
            'pick_up_location_id' => 1,
            'drop_off_location_id' => 1,
            'pick_up_date' => now()->addDay(),
            'drop_off_date' => now()->addDays(3),
            'status' => 'completed',
            'amount' => 500.00,
            'deposit_hold_status' => 'authorized',
            'deposit_hold_amount' => 50.00,
        ]);

        $payment = Payment::create([
            'reference_id' => $booking->id,
            'reference_type' => Booking::class,
            'amount' => 50.00,
            'is_authorized_hold' => true,
            'authorized_amount' => 50.00,
            'currency' => 'USD',
        ]);

        $beforeTime = now();
        $result = $this->service->settle($booking, 'capture_partial', 25.00);
        $afterTime = now();

        $this->assertTrue($result['ok']);
        $booking->refresh();
        $payment->refresh();
        
        $this->assertNotNull($booking->deposit_settled_at);
        $this->assertTrue($booking->deposit_settled_at->isBetween($beforeTime, $afterTime));
        $this->assertNotNull($payment->captured_at);
        $this->assertNotNull($payment->released_at);
    }
}
