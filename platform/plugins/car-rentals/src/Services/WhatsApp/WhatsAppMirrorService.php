<?php

namespace Botble\CarRentals\Services\WhatsApp;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Botble\CarRentals\Models\Customer;
use Illuminate\Support\Facades\Log;

class WhatsAppMirrorService
{
    protected MessageClassificationService $classificationService;

    public function __construct(MessageClassificationService $classificationService)
    {
        $this->classificationService = $classificationService;
    }

    /**
     * Mirror a WhatsApp message to the platform
     *
     * @param Customer $customer Customer who sent the message
     * @param array $message Normalized message data from WhatsAppService::parseIncomingMessage
     * @param array $classification Classification result from MessageClassificationService::classifyMessage
     * @return ChatMessage|null The created/stored message or null on failure
     */
    public function mirrorMessage(
        Customer $customer,
        array $message,
        array $classification = []
    ): ?ChatMessage {
        try {
            // Classify if not provided
            if (empty($classification)) {
                $classification = $this->classificationService->classifyMessage(
                    $customer,
                    $message['content'] ?? '',
                    $message
                );
            }

            // Get or create conversation
            $conversation = $this->getOrCreateConversation(
                $customer,
                $classification['context_id'] ?? null,
                $classification['context_type'] ?? 'none'
            );

            // Create the chat message
            $chatMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user', // WhatsApp message is from user
                'content' => $message['content'] ?? '',
                'source' => 'whatsapp',
                'provider_message_id' => $message['message_id'] ?? null,
                'phone_number' => $message['sender_phone'] ?? null,
                'timestamp_ms' => $message['timestamp_ms'] ?? null,
                'whatsapp_metadata' => [
                    'sender_name' => $message['sender_name'] ?? null,
                    'type' => $message['type'] ?? 'text',
                    'media_url' => $message['media_url'] ?? null,
                    'wa_id' => $message['wa_id'] ?? null,
                    'classification' => $classification,
                    'mirrored_at' => now()->toIso8601String(),
                ],
                'meta' => [
                    'booking_context' => $classification['context_type'] === 'booking' ? $classification['context_id'] : null,
                    'claim_context' => $classification['context_type'] === 'claim' ? $classification['context_id'] : null,
                ],
            ]);

            Log::info('WhatsApp message mirrored', [
                'message_id' => $message['message_id'],
                'customer_id' => $customer->id,
                'chat_message_id' => $chatMessage->id,
                'context_type' => $classification['context_type'],
                'context_id' => $classification['context_id'],
            ]);

            return $chatMessage;
        } catch (\Exception $e) {
            Log::error('WhatsApp message mirroring failed', [
                'message_id' => $message['message_id'] ?? 'unknown',
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Get or create a chat conversation for WhatsApp messages
     *
     * Conversions are scoped by customer + context (booking/claim/general)
     *
     * @param Customer $customer
     * @param int|null $contextId Booking ID or Claim ID
     * @param string $contextType 'booking' | 'claim' | 'none'
     * @return ChatConversation
     */
    protected function getOrCreateConversation(
        Customer $customer,
        ?int $contextId = null,
        string $contextType = 'none'
    ): ChatConversation {
        // Try to find existing conversation
        $query = ChatConversation::where('user_id', $customer->id)
            ->where('source', 'whatsapp')
            ->where('context_type', $contextType);

        if ($contextId) {
            $query->where('context_id', $contextId);
        }

        $conversation = $query->first();

        if ($conversation) {
            return $conversation;
        }

        // Create new conversation
        return ChatConversation::create([
            'user_id' => $customer->id,
            'session_id' => 'whatsapp-' . uniqid(),
            'source' => 'whatsapp',
            'context_type' => $contextType,
            'context_id' => $contextId,
            'metadata' => [
                'phone_number' => $customer->whatsapp,
                'created_from_webhook' => true,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get all WhatsApp messages for a specific conversation/context
     *
     * @param int|null $bookingId
     * @param int|null $claimId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhatsAppMessages(?int $bookingId = null, ?int $claimId = null)
    {
        if ($bookingId) {
            return ChatMessage::whereHas('conversation', function ($q) use ($bookingId) {
                $q->where('context_type', 'booking')
                    ->where('context_id', $bookingId)
                    ->where('source', 'whatsapp');
            })
            ->where('source', 'whatsapp')
            ->orderBy('created_at', 'asc')
            ->get();
        }

        if ($claimId) {
            return ChatMessage::whereHas('conversation', function ($q) use ($claimId) {
                $q->where('context_type', 'claim')
                    ->where('context_id', $claimId)
                    ->where('source', 'whatsapp');
            })
            ->where('source', 'whatsapp')
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return collect();
    }
}
