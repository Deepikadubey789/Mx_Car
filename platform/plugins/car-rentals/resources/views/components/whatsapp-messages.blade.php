@if ($whatsappMessages->count() > 0)
    <div class="card whatsapp-messages-card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="icon-phone"></i> {{ __('WhatsApp Messages') }}
                <span class="badge badge-primary">{{ $whatsappMessages->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="whatsapp-messages-list">
                @forelse ($whatsappMessages as $message)
                    <div class="whatsapp-message-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="message-sender mb-1">
                                    <strong>{{ $message->whatsapp_metadata['sender_name'] ?? 'Unknown Sender' }}</strong>
                                    <span class="text-muted ms-2" style="font-size: 0.85rem;">
                                        {{ $message->phone_number }}
                                    </span>
                                </div>
                                <div class="message-content mb-2" style="background-color: #f0f0f0; padding: 10px; border-radius: 5px;">
                                    {{ $message->content }}
                                </div>
                                <div class="message-meta text-muted" style="font-size: 0.8rem;">
                                    <i class="icon-calendar"></i>
                                    {{ $message->created_at->format('M d, Y H:i') }}
                                    @if ($message->whatsapp_metadata['type'] !== 'text')
                                        <span class="ms-2 badge badge-info">
                                            {{ ucfirst($message->whatsapp_metadata['type'] ?? 'unknown') }}
                                        </span>
                                    @endif
                                </div>
                                @if ($message->whatsapp_metadata['media_url'])
                                    <div class="message-media mt-2">
                                        <a href="{{ $message->whatsapp_metadata['media_url'] }}" target="_blank" class="badge badge-secondary">
                                            <i class="icon-link"></i> View Media
                                        </a>
                                    </div>
                                @endif
                                @if (isset($message->whatsapp_metadata['classification']))
                                    <div class="message-classification mt-2 text-muted" style="font-size: 0.8rem;">
                                        <strong>Category:</strong> {{ ucfirst($message->whatsapp_metadata['classification']['category'] ?? 'general') }}
                                        <span class="ms-2">
                                            (Confidence: {{ round($message->whatsapp_metadata['classification']['confidence'] * 100) }}%)
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-2">
                                <span class="badge badge-success">
                                    <i class="icon-check"></i> Mirrored
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">{{ __('No WhatsApp messages yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endif

<style>
.whatsapp-messages-list {
    max-height: 500px;
    overflow-y: auto;
}

.whatsapp-message-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.message-content {
    word-break: break-word;
    white-space: pre-wrap;
}
</style>
