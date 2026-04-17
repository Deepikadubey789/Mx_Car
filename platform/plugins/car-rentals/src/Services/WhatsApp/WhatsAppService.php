<?php

namespace Botble\CarRentals\Services\WhatsApp;

use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\WhatsAppConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected function getActiveConfig(): ?WhatsAppConfig
    {
        return WhatsAppConfig::where('enabled', true)->first();
    }

    protected function getAccessToken(WhatsAppConfig $config): string
    {
        return $config->getDecryptedAccessToken();
    }

    protected function sendPayload(WhatsAppConfig $config, array $payload): \Illuminate\Http\Client\Response
    {
        return Http::withToken($this->getAccessToken($config))
            ->post("https://graph.facebook.com/{$config->api_version}/{$config->phone_number_id}/messages", $payload);
    }

    /**
     * Send a message via WhatsApp Business API
     *
     * @param Customer $customer
     * @param string $message Message content
     * @param string $eventType Event type (for logging)
     * @return array ['success' => bool, 'message_id' => ?string, 'error' => ?string]
     */
    public function sendMessage(Customer $customer, string $message, string $eventType = 'manual'): array
    {
        try {
            // Get WhatsApp configuration
            $config = $this->getActiveConfig();
            if (!$config) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp not configured',
                ];
            }

            // Validate customer has phone number
            $phone = $this->normalizePhoneNumber($customer->whatsapp ?? $customer->phone);
            if (!$phone) {
                return [
                    'success' => false,
                    'error' => 'Customer phone number must be in E.164 format (for example +918302786768)',
                ];
            }

            // Read token from config (supports encrypted and plain values).
            $accessToken = $this->getAccessToken($config);

            if (! $accessToken) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp access token is missing',
                ];
            }

            // Call Meta API
            $response = $this->sendPayload($config, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => ltrim($phone, '+'),
                'type' => 'text',
                'text' => [
                    'preview_url' => true,
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('WhatsApp message sent successfully', [
                    'customer_id' => $customer->id,
                    'event_type' => $eventType,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data,
                ];
            } else {
                $error = $response->json('error.message') ?? $response->body();
                Log::warning('WhatsApp message send failed', [
                    'customer_id' => $customer->id,
                    'error' => $error,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'error' => $error,
                    'response' => $response->json(),
                ];
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp send service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send an approved WhatsApp template message.
     */
    public function sendTemplateMessage(
        Customer $customer,
        string $templateName,
        string $languageCode = 'en_US',
        string $eventType = 'manual_template'
    ): array {
        try {
            $config = $this->getActiveConfig();
            if (! $config) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp not configured',
                ];
            }

            $phone = $this->normalizePhoneNumber($customer->whatsapp ?? $customer->phone);
            if (! $phone) {
                return [
                    'success' => false,
                    'error' => 'Customer phone number must be in E.164 format (for example +918302786768)',
                ];
            }

            if (! $this->getAccessToken($config)) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp access token is missing',
                ];
            }

            $response = $this->sendPayload($config, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => ltrim($phone, '+'),
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp template message sent successfully', [
                    'customer_id' => $customer->id,
                    'event_type' => $eventType,
                    'template' => $templateName,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data,
                ];
            }

            $error = $response->json('error.message') ?? $response->body();

            return [
                'success' => false,
                'error' => $error,
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp send template service error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Normalize phone number to strict E.164-like format (+ and 8-15 digits).
     *
     * @param string $phone
     * @return string|null Normalized phone or null if invalid
     */
    public function normalizePhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $trimmed = trim($phone);
        if (! str_starts_with($trimmed, '+')) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $trimmed);
        if (! $digits) {
            return null;
        }

        $length = strlen($digits);
        if ($length < 8 || $length > 15) {
            return null;
        }

        return '+' . $digits;
    }

    /**
     * Build message from template with personalization
     *
     * @param string $template Template content with {{placeholders}}
     * @param array $data Data to fill placeholders
     * @return string Personalized message
     */
    public function buildMessage(string $template, array $data = []): string
    {
        $message = $template;

        foreach ($data as $key => $value) {
            if ($value === null) {
                $value = '(not available)';
            }
            $message = str_replace('{{' . $key . '}}', (string)$value, $message);
        }

        // Remove any remaining empty placeholders
        $message = preg_replace('/\{\{[^}]+\}\}/', '', $message);

        return trim($message);
    }
}
