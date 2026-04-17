<?php

namespace Tests\Unit\Services\WhatsApp;

require_once __DIR__ . '/../../../../platform/plugins/car-rentals/src/Services/WhatsApp/WhatsAppService.php';

use Botble\CarRentals\Services\WhatsApp\WhatsAppService;
use PHPUnit\Framework\TestCase;

class WhatsAppServiceTest extends TestCase
{
    protected WhatsAppService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WhatsAppService();
    }

    public function test_normalize_phone_number_requires_plus_prefix(): void
    {
        $this->assertNull($this->service->normalizePhoneNumber('918302786768'));
        $this->assertNull($this->service->normalizePhoneNumber('1234567890'));
    }

    public function test_normalize_phone_number_accepts_valid_e164_lengths(): void
    {
        $this->assertSame('+918302786768', $this->service->normalizePhoneNumber('+91 83027 86768'));
        $this->assertSame('+14155552671', $this->service->normalizePhoneNumber('+1 (415) 555-2671'));
    }

    public function test_normalize_phone_number_rejects_invalid_lengths(): void
    {
        $this->assertNull($this->service->normalizePhoneNumber('+1234567'));
        $this->assertNull($this->service->normalizePhoneNumber('+1234567890123456'));
    }

    public function test_build_message_replaces_placeholders_and_cleans_missing_values(): void
    {
        $message = $this->service->buildMessage(
            'Hello {{name}}, booking {{booking_id}}, note {{missing}}.',
            ['name' => 'Manish', 'booking_id' => 'BK-1001']
        );

        $this->assertSame('Hello Manish, booking BK-1001, note .', $message);
    }
}
