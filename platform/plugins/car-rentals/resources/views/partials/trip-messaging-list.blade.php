@forelse($messages as $message)
    @if($message->type === 'system_event' || $message->type === 'escalation')
        <div class="chat-message-system animate__animated animate__fadeInUp animate__faster">
            <span class="system-bubble {{ $message->type === 'escalation' ? 'text-danger' : 'text-muted' }}">
                <i class="ti {{ $message->type === 'escalation' ? 'ti-alert-triangle' : 'ti-info-circle' }}"></i>
                {{ $message->message }}
            </span>
            <small class="time">{{ $message->created_at->diffForHumans() }}</small>
        </div>
    @else
        @php
            // Determine if the message is from the logged-in person viewing the page
            // auth()->id() works for User (Admin). auth('customer')->id() for Vendor/Customer.
            // Check polymorphic sender type.
            $isMine = false;
            if ($message->sender_type == \Botble\CarRentals\Models\Customer::class && auth('customer')->check() && auth('customer')->id() == $message->sender_id) {
                $isMine = true;
            } elseif ($message->sender_type == \Botble\ACL\Models\User::class && auth()->check() && auth()->id() == $message->sender_id) {
                $isMine = true;
            }
        @endphp

        <div class="chat-message-wrapper {{ $isMine ? 'mine' : 'theirs' }} animate__animated animate__fadeInUp animate__faster">
            @if(!$isMine)
                <div class="avatar">
                    <img src="{{ $message->sender ? $message->sender->avatar_url : Botble\Media\Facades\RvMedia::getDefaultImage() }}" alt="{{ $message->sender->name ?? 'User' }}">
                </div>
            @endif
            <div class="message-content">
                <div class="sender-name">
                    @if($message->sender_type == \Botble\ACL\Models\User::class)
                        <span class="badge bg-primary text-white p-1">Support Team</span>
                    @elseif($message->sender_id == $booking->vendor_id)
                        <span class="badge bg-info text-white p-1">Host</span> {{ $message->sender?->name ?? '' }}
                    @elseif($message->sender_id == $booking->customer_id)
                        <span class="badge bg-secondary text-white p-1">Renter</span> {{ $message->sender?->name ?? '' }}
                    @endif
                </div>
                <div class="bubble">
                    {{ $message->message }}
                </div>
                <small class="time">{{ $message->created_at->format('M d, h:i A') }}</small>
            </div>
        </div>
    @endif
@empty
    <div class="text-center text-muted my-3">
        <i class="ti ti-message-circle-2"></i> No messages yet.
    </div>
@endforelse
