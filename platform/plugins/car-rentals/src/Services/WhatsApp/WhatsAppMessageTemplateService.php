<?php

namespace Botble\CarRentals\Services\WhatsApp;

use Botble\CarRentals\Models\WhatsAppMessageTemplate;
use Illuminate\Support\Facades\Log;

class WhatsAppMessageTemplateService
{
    /**
     * Get default templates (to be seeded)
     *
     * @return array
     */
    public static function getDefaultTemplates(): array
    {
        return [
            [
                'name' => 'booking_confirmed',
                'event_type' => 'booking_confirmed',
                'label' => 'Booking Confirmed',
                'template_content' => "🎉 Your booking is confirmed!\n\nBooking Reference: {{booking_reference}}\nCar: {{car_name}} ({{car_year}})\nPickup: {{pickup_date}} at {{pickup_time}}\nReturn: {{return_date}} at {{return_time}}\nLocation: {{pickup_location}}\nTotal: {{total_amount}}\n\nWe'll see you soon!",
                'description' => 'Sent when booking status changes to confirmed',
                'placeholders' => ['booking_reference', 'car_name', 'car_year', 'pickup_date', 'pickup_time', 'return_date', 'return_time', 'pickup_location', 'total_amount'],
            ],
            [
                'name' => 'booking_cancelled',
                'event_type' => 'booking_cancelled',
                'label' => 'Booking Cancelled',
                'template_content' => "Your booking has been cancelled.\n\nBooking Reference: {{booking_reference}}\nReason: {{cancellation_reason}}\nRefund Amount: {{refund_amount}}\n\nRefund will be processed within 3-5 business days.\nThank you for choosing us!",
                'description' => 'Sent when booking is cancelled',
                'placeholders' => ['booking_reference', 'cancellation_reason', 'refund_amount'],
            ],
            [
                'name' => 'payment_confirmed',
                'event_type' => 'payment_confirmed',
                'label' => 'Payment Confirmed',
                'template_content' => "✓ Payment Received\n\nBooking Reference: {{booking_reference}}\nAmount: {{payment_amount}}\nDate: {{payment_date}}\nMethod: {{payment_method}}\n\nYour booking is secure. See you soon!",
                'description' => 'Sent when payment is completed',
                'placeholders' => ['booking_reference', 'payment_amount', 'payment_date', 'payment_method'],
            ],
            [
                'name' => 'pickup_reminder',
                'event_type' => 'pickup_reminder',
                'label' => 'Pickup Reminder (24hrs)',
                'template_content' => "⏰ Reminder: Your pickup is tomorrow!\n\nBooking Reference: {{booking_reference}}\nCar: {{car_name}}\nTime: {{pickup_time}}\nLocation: {{pickup_location}}\nAddress: {{pickup_address}}\n\nPlease arrive 15 minutes early. Have your ID and booking confirmation ready.",
                'description' => 'Sent 24 hours before pickup',
                'placeholders' => ['booking_reference', 'car_name', 'pickup_time', 'pickup_location', 'pickup_address'],
            ],
            [
                'name' => 'return_reminder',
                'event_type' => 'return_reminder',
                'label' => 'Return Reminder (24hrs)',
                'template_content' => "⏰ Reminder: Return the car tomorrow!\n\nBooking Reference: {{booking_reference}}\nReturn Time: {{return_time}}\nLocation: {{return_location}}\nAddress: {{return_address}}\n\nPlease return the car clean and with a full tank. Thank you!",
                'description' => 'Sent 24 hours before return',
                'placeholders' => ['booking_reference', 'return_time', 'return_location', 'return_address'],
            ],
            [
                'name' => 'dispute_created',
                'event_type' => 'dispute_created',
                'label' => 'Dispute/Claim Created',
                'template_content' => "We've received your claim.\n\nBooking Reference: {{booking_reference}}\nClaim ID: {{claim_id}}\nCategory: {{claim_category}}\nAmount Claimed: {{claimed_amount}}\nStatus: Under Review\n\nWe will investigate and respond within 5 business days.",
                'description' => 'Sent when a new dispute/claim is created',
                'placeholders' => ['booking_reference', 'claim_id', 'claim_category', 'claimed_amount'],
            ],
            [
                'name' => 'dispute_resolved',
                'event_type' => 'dispute_resolved',
                'label' => 'Dispute Resolved',
                'template_content' => "Your claim has been resolved.\n\nBooking Reference: {{booking_reference}}\nClaim ID: {{claim_id}}\nOutcome: {{claim_outcome}}\nApproved Amount: {{approved_amount}}\n\nThank you for your patience. {{resolution_note}}",
                'description' => 'Sent when a dispute/claim is resolved',
                'placeholders' => ['booking_reference', 'claim_id', 'claim_outcome', 'approved_amount', 'resolution_note'],
            ],
        ];
    }

    /**
     * Get template by name
     *
     * @param string $templateName
     * @return WhatsAppMessageTemplate|null
     */
    public function getTemplate(string $templateName): ?WhatsAppMessageTemplate
    {
        try {
            return WhatsAppMessageTemplate::where('name', $templateName)
                ->where('is_active', true)
                ->first();
        } catch (\Exception $e) {
            Log::warning('Failed to get template', [
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Build message from template with personalization
     *
     * @param string $templateName Template name
     * @param array $data Personalization data
     * @param WhatsAppService $whatsAppService Service for building message
     * @return string|null Built message or null if template not found
     */
    public function buildMessage(string $templateName, array $data, WhatsAppService $whatsAppService): ?string
    {
        $template = $this->getTemplate($templateName);

        if (!$template) {
            Log::warning('Template not found', ['template' => $templateName]);
            return null;
        }

        return $whatsAppService->buildMessage($template->template_content, $data);
    }

    /**
     * Get templates for event type
     *
     * @param string $eventType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTemplatesByEventType(string $eventType)
    {
        return WhatsAppMessageTemplate::where('event_type', $eventType)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Seed default templates
     *
     * @return void
     */
    public function seedDefaultTemplates(): void
    {
        foreach (self::getDefaultTemplates() as $template) {
            try {
                WhatsAppMessageTemplate::updateOrCreate(
                    ['name' => $template['name']],
                    $template
                );
            } catch (\Exception $e) {
                Log::error('Failed to seed template', [
                    'template' => $template['name'],
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
