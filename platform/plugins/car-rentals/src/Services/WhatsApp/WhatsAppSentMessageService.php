<?php

namespace Botble\CarRentals\Services\WhatsApp;

use App\Models\ChatMessage;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\WhatsAppSentMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppSentMessageService
{
    protected WhatsAppService $whatsAppService;
    protected WhatsAppMessageTemplateService $templateService;

    public function __construct(
        WhatsAppService $whatsAppService,
        WhatsAppMessageTemplateService $templateService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->templateService = $templateService;
    }

    /**
     * Send message using template and log it
     *
     * @param Customer $customer
     * @param string $templateName Template identifier
     * @param array $personalizationData Data to personalize template
     * @param string $eventType Event type (for logging)
     * @param Booking|null $booking Associated booking
     * @param BookingClaim|null $claim Associated claim
     * @return array ['success' => bool, 'message_id' => ?string, 'error' => ?string]
     */
    public function sendFromTemplate(
        Customer $customer,
        string $templateName,
        array $personalizationData = [],
        string $eventType = 'manual',
        ?Booking $booking = null,
        ?BookingClaim $claim = null
    ): array {
        try {
            // Build message from template
            $message = $this->templateService->buildMessage(
                $templateName,
                $personalizationData,
                $this->whatsAppService
            );

            if (!$message) {
                return $this->logFailedAttempt(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    $templateName,
                    null,
                    'Template not found or empty'
                );
            }

            // Send message
            $sendResult = $this->whatsAppService->sendMessage($customer, $message, $eventType);

            if ($sendResult['success']) {
                // Log successful send
                return $this->logSuccessfulSend(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    $templateName,
                    $message,
                    $sendResult
                );
            } else {
                // Log failed send
                return $this->logFailedAttempt(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    $templateName,
                    $message,
                    $sendResult['error'] ?? 'Unknown error',
                    $sendResult['response'] ?? null
                );
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send from template error', [
                'customer_id' => $customer->id,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return $this->logFailedAttempt(
                $customer,
                $booking,
                $claim,
                $eventType,
                $templateName,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Send custom message directly (not from template)
     *
     * @param Customer $customer
     * @param string $message Message content
     * @param string $eventType Event type
     * @param Booking|null $booking
     * @param BookingClaim|null $claim
     * @return array
     */
    public function sendCustom(
        Customer $customer,
        string $message,
        string $eventType = 'manual',
        ?Booking $booking = null,
        ?BookingClaim $claim = null
    ): array {
        try {
            // Send message
            $sendResult = $this->whatsAppService->sendMessage($customer, $message, $eventType);

            if ($sendResult['success']) {
                return $this->logSuccessfulSend(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    null,
                    $message,
                    $sendResult
                );
            } else {
                return $this->logFailedAttempt(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    null,
                    $message,
                    $sendResult['error'] ?? 'Unknown error',
                    $sendResult['response'] ?? null
                );
            }
        } catch (\Exception $e) {
            return $this->logFailedAttempt(
                $customer,
                $booking,
                $claim,
                $eventType,
                null,
                $message,
                $e->getMessage()
            );
        }
    }

    /**
     * Send an approved Meta template directly (for outside 24-hour window).
     */
    public function sendTemplateDirect(
        Customer $customer,
        string $templateName,
        string $languageCode = 'en_US',
        string $eventType = 'manual_template',
        ?Booking $booking = null,
        ?BookingClaim $claim = null
    ): array {
        try {
            $sendResult = $this->whatsAppService->sendTemplateMessage(
                $customer,
                $templateName,
                $languageCode,
                $eventType
            );

            if ($sendResult['success']) {
                return $this->logSuccessfulSend(
                    $customer,
                    $booking,
                    $claim,
                    $eventType,
                    $templateName,
                    '[Template message sent via Meta template API]',
                    $sendResult
                );
            }

            return $this->logFailedAttempt(
                $customer,
                $booking,
                $claim,
                $eventType,
                $templateName,
                '[Template message failed]',
                $sendResult['error'] ?? 'Unknown error',
                $sendResult['response'] ?? null
            );
        } catch (\Throwable $e) {
            return $this->logFailedAttempt(
                $customer,
                $booking,
                $claim,
                $eventType,
                $templateName,
                '[Template message exception]',
                $e->getMessage()
            );
        }
    }

    /**
     * Log successful message send
     *
     * @param Customer $customer
     * @param Booking|null $booking
     * @param BookingClaim|null $claim
     * @param string $eventType
     * @param string|null $templateName
     * @param string $messageContent
     * @param array $sendResult
     * @return array
     */
    protected function logSuccessfulSend(
        Customer $customer,
        ?Booking $booking,
        ?BookingClaim $claim,
        string $eventType,
        ?string $templateName,
        string $messageContent,
        array $sendResult
    ): array {
        try {
            WhatsAppSentMessage::create([
                'customer_id' => $customer->id,
                'booking_id' => $booking?->id,
                'claim_id' => $claim?->id,
                'phone_number' => $this->whatsAppService->normalizePhoneNumber(
                    $customer->whatsapp ?? $customer->phone
                ),
                'event_type' => $eventType,
                'template_name' => $templateName,
                'message_content' => $messageContent,
                'status' => 'accepted',
                'provider_message_id' => $sendResult['message_id'] ?? null,
                'meta_response' => $sendResult['response'] ?? null,
                'sent_at' => now(),
                'status_updated_at' => now(),
            ]);

            Log::info('WhatsApp message logged as sent', [
                'customer_id' => $customer->id,
                'event_type' => $eventType,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log sent message', ['error' => $e->getMessage()]);
        }

        return [
            'success' => true,
            'message_id' => $sendResult['message_id'] ?? null,
        ];
    }

    /**
     * Update a sent message status from WhatsApp webhook callbacks.
     */
    public function updateStatusFromWebhook(
        string $providerMessageId,
        string $providerStatus,
        array $statusPayload = [],
        ?string $errorMessage = null
    ): bool {
        $message = WhatsAppSentMessage::query()
            ->where('provider_message_id', $providerMessageId)
            ->latest('id')
            ->first();

        if (! $message) {
            Log::warning('WhatsApp status callback did not match any sent message', [
                'provider_message_id' => $providerMessageId,
                'provider_status' => $providerStatus,
            ]);

            return false;
        }

        $normalizedStatus = $this->normalizeProviderStatus($providerStatus);
        $response = (array) ($message->meta_response ?? []);
        $response['latest_status_callback'] = $statusPayload;

        $message->status = $normalizedStatus;
        $message->status_updated_at = $this->resolveStatusTimestamp($statusPayload);
        $message->meta_response = $response;

        if ($normalizedStatus === 'failed' && $errorMessage) {
            $message->error_message = $errorMessage;
        }

        $message->save();

        return true;
    }

    protected function normalizeProviderStatus(string $providerStatus): string
    {
        return match (strtolower(trim($providerStatus))) {
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
            default => 'accepted',
        };
    }

    protected function resolveStatusTimestamp(array $statusPayload): Carbon
    {
        $timestamp = (string) ($statusPayload['timestamp'] ?? '');

        if ($timestamp !== '' && ctype_digit($timestamp)) {
            return Carbon::createFromTimestamp((int) $timestamp);
        }

        return now();
    }

    /**
     * Log failed message attempt
     *
     * @param Customer $customer
     * @param Booking|null $booking
     * @param BookingClaim|null $claim
     * @param string $eventType
     * @param string|null $templateName
     * @param string|null $messageContent
     * @param string $errorMessage
     * @param array|null $providerResponse
     * @return array
     */
    protected function logFailedAttempt(
        Customer $customer,
        ?Booking $booking,
        ?BookingClaim $claim,
        string $eventType,
        ?string $templateName,
        ?string $messageContent,
        string $errorMessage,
        ?array $providerResponse = null
    ): array {
        try {
            WhatsAppSentMessage::create([
                'customer_id' => $customer->id,
                'booking_id' => $booking?->id,
                'claim_id' => $claim?->id,
                'phone_number' => $this->whatsAppService->normalizePhoneNumber(
                    $customer->whatsapp ?? $customer->phone
                ),
                'event_type' => $eventType,
                'template_name' => $templateName,
                'message_content' => $messageContent,
                'status' => 'failed',
                'meta_response' => $providerResponse,
                'error_message' => $errorMessage,
            ]);

            Log::warning('WhatsApp message send failed and logged', [
                'customer_id' => $customer->id,
                'event_type' => $eventType,
                'error' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log failed message', ['error' => $e->getMessage()]);
        }

        return [
            'success' => false,
            'error' => $errorMessage,
            'error_code' => data_get($providerResponse, 'error.code'),
            'response' => $providerResponse,
        ];
    }

    /**
     * Get sent messages for a booking
     *
     * @param Booking $booking
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingSentMessages(Booking $booking)
    {
        return WhatsAppSentMessage::where('booking_id', $booking->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get sent messages for a claim
     *
     * @param BookingClaim $claim
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClaimSentMessages(BookingClaim $claim)
    {
        return WhatsAppSentMessage::where('claim_id', $claim->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
