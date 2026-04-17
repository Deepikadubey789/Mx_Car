<?php

namespace Botble\CarRentals\Http\Controllers\Webhook;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Models\WhatsAppConfig;
use Botble\CarRentals\Models\WhatsAppWebhookLog;
use Botble\CarRentals\Services\WhatsApp\WhatsAppSentMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppWebhookController extends BaseController
{
    public function __construct(protected WhatsAppSentMessageService $sentMessageService)
    {
    }

    public function handle(Request $request): JsonResponse|Response
    {
        Log::info('WhatsApp webhook received', ['request' => $request->all()]);
        if ($request->isMethod('get')) {
            return $this->verify($request);
        }

        return $this->ingestStatuses($request);
    }

    protected function verify(Request $request): JsonResponse|Response
    {
        $mode = (string) $request->query('hub_mode', $request->query('hub.mode', ''));
        $token = (string) $request->query('hub_verify_token', $request->query('hub.verify_token', ''));
        $challenge = (string) $request->query('hub_challenge', $request->query('hub.challenge', ''));

        if ($mode !== 'subscribe' || $challenge === '') {
            return response()->json(['success' => false, 'error' => 'Invalid verification payload'], 400);
        }

        $config = WhatsAppConfig::enabled()->first();

        if (! $config || $token !== (string) $config->webhook_verify_token) {
            return response()->json(['success' => false, 'error' => 'Invalid verify token'], 403);
        }

        return response($challenge, 200, ['Content-Type' => 'text/plain']);
    }

    protected function ingestStatuses(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        $processed = 0;

        foreach ((array) data_get($payload, 'entry', []) as $entry) {
            foreach ((array) data_get($entry, 'changes', []) as $change) {
                $value = (array) data_get($change, 'value', []);
                $metadataPhone = (string) data_get($value, 'metadata.display_phone_number', '');

                foreach ((array) data_get($value, 'statuses', []) as $statusData) {
                    $messageId = (string) data_get($statusData, 'id', '');
                    if ($messageId === '') {
                        continue;
                    }

                    $status = (string) data_get($statusData, 'status', 'unknown');
                    $recipientPhone = (string) data_get($statusData, 'recipient_id', '');
                    $errorMessage = $this->buildStatusError($statusData);

                    WhatsAppWebhookLog::updateOrCreate(
                        ['message_id' => $messageId],
                        [
                            'phone_number' => $metadataPhone,
                            'sender_phone' => $recipientPhone,
                            'status' => $status,
                            'raw_payload' => $statusData,
                            'error_message' => $errorMessage,
                        ]
                    );

                    $this->sentMessageService->updateStatusFromWebhook(
                        $messageId,
                        $status,
                        $statusData,
                        $errorMessage
                    );

                    $processed++;
                }
            }
        }

        if ($processed === 0) {
            Log::info('WhatsApp webhook received without status updates', [
                'payload' => $payload,
            ]);
        }

        return response()->json(['success' => true, 'processed' => $processed]);
    }

    protected function buildStatusError(array $statusData): ?string
    {
        $errors = (array) data_get($statusData, 'errors', []);
        if ($errors === []) {
            return null;
        }

        $parts = [];
        foreach ($errors as $error) {
            $message = (string) data_get($error, 'message', '');
            $code = data_get($error, 'code');
            if ($message === '' && $code === null) {
                continue;
            }

            $parts[] = trim(($code !== null ? '[' . $code . '] ' : '') . $message);
        }

        return $parts !== [] ? implode('; ', $parts) : null;
    }
}
