@php
    Theme::asset()->usePath(false)->add('animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
    $compactAdmin = $compactAdmin ?? false;
@endphp

<div
    class="trip-chat-wrapper card shadow-sm border-0 {{ $compactAdmin ? 'rounded trip-chat-wrapper--admin mt-0' : 'rounded-4 mt-4' }}"
    data-fetch-url="{{ $fetchUrl }}"
    data-store-url="{{ $storeUrl }}"
    data-escalate-url="{{ $escalateUrl ?? '' }}"
>
    <div class="card-header bg-white border-bottom {{ $compactAdmin ? 'py-2 px-3' : 'py-3' }} d-flex justify-content-between align-items-center {{ $compactAdmin ? 'rounded-top' : 'rounded-top-4' }}">
        <h5 class="mb-0 d-flex align-items-center {{ $compactAdmin ? 'fw-semibold fs-6' : 'fw-bold' }}">
            <i class="ti ti-messages text-primary me-2 {{ $compactAdmin ? 'fs-5' : 'fs-4' }}"></i>
            {{ __('plugins/car-rentals::disputes.trip_messaging_title') }}
        </h5>
        <div class="actions">
            @if(!empty($deescalateUrl) && $booking->is_escalated)
                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm btn-deescalate" data-url="{{ $deescalateUrl }}">
                    <i class="ti ti-check me-1"></i> Resolve Escalation
                </button>
            @endif
            @if(!empty($escalateUrl) && !$booking->is_escalated)
                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-escalate py-1">
                    <i class="ti ti-alert-triangle me-1"></i> Escalate to Support
                </button>
            @endif
            @if($booking->is_escalated)
                <span class="badge bg-danger rounded-pill px-3 py-2 ms-2 animate__animated animate__pulse animate__infinite">Escalated</span>
            @endif
        </div>
    </div>
    
    <div
        class="card-body p-0 chat-container-body"
        style="background: #f8fafc; overflow-y: auto; height: {{ $compactAdmin ? '260px' : '400px' }}; display: flex; flex-direction: column;"
    >
        <div class="trip-chat-list p-4 flex-grow-1" id="trip-chat-messages">
            <!-- Messages load here via JS -->
            <div class="text-center text-muted my-3"><span class="spinner-border spinner-border-sm" role="status"></span> Loading messages...</div>
        </div>
    </div>

    <div class="card-footer bg-white border-top {{ $compactAdmin ? 'p-2' : 'p-3' }} {{ $compactAdmin ? 'rounded-bottom' : 'rounded-bottom-4' }}">
        <div class="trip-chat-form position-relative">
            @csrf
            <div class="input-group {{ $compactAdmin ? '' : 'input-group-lg' }} rounded-pill shadow-sm overflow-hidden border">
                <input type="text" name="message" class="form-control border-0 {{ $compactAdmin ? 'px-3 py-2' : 'px-4' }}" placeholder="{{ __('Type your message here...') }}" required autocomplete="off" style="box-shadow: none;">
                <button type="button" class="btn btn-primary {{ $compactAdmin ? 'px-3' : 'px-4' }} d-flex align-items-center transition-all trip-chat-send-btn">
                    <span class="me-2 d-none d-sm-inline">Send</span> <i class="ti ti-send-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .trip-chat-wrapper {
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05) !important;
        transition: all 0.3s ease;
    }
    .trip-chat-wrapper:hover {
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important;
    }
    
    .chat-message-system {
        text-align: center;
        margin: 1.5rem 0;
    }
    .chat-message-system .system-bubble {
        display: inline-block;
        padding: 0.25rem 1rem;
        background: #f1f5f9;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }
    .chat-message-system .time {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    .trip-chat-wrapper {
        direction: ltr !important;
        text-align: left !important;
    }
    .chat-message-wrapper {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-end !important;
        justify-content: flex-start !important;
        width: 100% !important;
        margin-bottom: 1.5rem !important;
    }
    .chat-message-wrapper.mine {
        flex-direction: row-reverse !important;
        justify-content: flex-start !important;
    }
    .chat-message-wrapper .avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 12px;
        flex-shrink: 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .chat-message-wrapper.mine .avatar {
        margin-right: 0;
        margin-left: 12px;
    }
    .chat-message-wrapper .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .chat-message-wrapper .message-content {
        max-width: 75% !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
    }
    .chat-message-wrapper.theirs .message-content {
        align-items: flex-start !important;
        text-align: left !important;
    }
    .chat-message-wrapper.mine .message-content {
        align-items: flex-end !important;
        text-align: right !important;
    }
    
    .sender-name {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .bubble {
        padding: 14px 20px !important;
        border-radius: 18px;
        font-size: 0.95rem;
        line-height: 1.5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        word-break: break-word;
    }
    
    .chat-message-wrapper.theirs .bubble {
        background-color: white;
        border: 1px solid #f1f5f9;
        color: #334155;
        border-bottom-left-radius: 4px;
    }
    .chat-message-wrapper.mine .bubble {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }

    .message-content .time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 5px;
        padding: 0 4px;
    }

    .btn-deescalate, .btn-escalate {
        transition: transform 0.2s!important;
    }
    .btn-deescalate:hover, .btn-escalate:hover {
        color: #ffffff !important;
    }
    .btn-deescalate:active, .btn-escalate:active {
        transform: scale(0.95);
    }

    /* Scrollbar styling */
    .chat-container-body::-webkit-scrollbar {
        width: 6px;
    }
    .chat-container-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-container-body::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }

    .trip-chat-wrapper--admin {
        border: 1px solid var(--bs-border-color, rgba(0, 0, 0, 0.08)) !important;
        box-shadow: none !important;
    }
    .trip-chat-wrapper--admin .trip-chat-list {
        padding: 0.75rem 1rem !important;
    }
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js"></script>

<script>
    (function () {
        const initTripMessaging = function () {
            const wrapper = document.querySelector('.trip-chat-wrapper:not([data-initialized])');

            if (!wrapper) {
                return;
            }

            wrapper.setAttribute('data-initialized', 'true');

            const chatList = wrapper.querySelector('#trip-chat-messages');
            const form = wrapper.querySelector('.trip-chat-form');
            const input = wrapper.querySelector('input[name="message"]');
            const sendBtn = wrapper.querySelector('.trip-chat-send-btn');
            const fetchUrl = wrapper.dataset.fetchUrl || '';
            const storeUrl = wrapper.dataset.storeUrl || '';
            const escalateBtn = wrapper.querySelector('.btn-escalate');
            const deescalateBtn = wrapper.querySelector('.btn-deescalate');
            const chatBody = wrapper.querySelector('.chat-container-body');

            if (!chatList || !input || !sendBtn || !chatBody || !fetchUrl || !storeUrl) {
                if (chatList) {
                    chatList.innerHTML = '<div class="text-center text-danger my-3"><i class="ti ti-alert-circle"></i> Trip messaging is unavailable on this view.</div>';
                }

                return;
            }

            // Ensure we always have a CSRF token across all panels (Customer, Admin, Vendor)
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                const tokenInput = wrapper.querySelector('input[name="_token"]') || document.querySelector('input[name="_token"]');
                if (tokenInput) {
                    csrfToken = tokenInput.value;
                }
            }

            function renderError(message) {
                chatList.innerHTML = `<div class="text-center text-danger my-3"><i class="ti ti-alert-circle"></i> ${message}</div>`;
            }

            function scrollToBottom() {
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            function loadMessages() {
                fetch(fetchUrl, {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                    .then((res) => {
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }

                        return res.json();
                    })
                    .then((data) => {
                        if (data.error) {
                            renderError(data.message || 'Unable to load messages.');
                            return;
                        }

                        const scrollPos = chatBody.scrollTop;
                        const scrollHeight = chatBody.scrollHeight;
                        const clientHeight = chatBody.clientHeight;
                        const isBottom = scrollHeight - scrollPos <= clientHeight + 50;

                        const html = data?.data?.html || '<div class="text-center text-muted my-3">No messages yet.</div>';
                        chatList.innerHTML = html;

                        if (isBottom || scrollHeight === 0) {
                            scrollToBottom();
                        }
                    })
                    .catch((error) => {
                        console.error('Trip messaging load failed:', error);
                        renderError('Unable to load messages. Please refresh this page.');
                    });
            }

            const sendMessage = function () {
                const msg = input.value.trim();

                if (!msg) {
                    return;
                }

                sendBtn.disabled = true;

                const formData = new FormData();
                formData.append('message', msg);

                // Preserve CSRF token in request body if present.
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                };

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                fetch(storeUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers,
                    body: formData,
                })
                    .then((res) => {
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }

                        return res.json();
                    })
                    .then((data) => {
                        input.value = '';

                        if (!data.error) {
                            chatList.innerHTML = data?.data?.html || '<div class="text-center text-muted my-3">No messages yet.</div>';
                            scrollToBottom();
                        }
                    })
                    .catch((error) => {
                        console.error('Trip messaging send failed:', error);
                        renderError('Unable to send message. Please try again.');
                    })
                    .finally(() => {
                        sendBtn.disabled = false;
                    });
            };

            sendBtn.addEventListener('click', function (e) {
                e.preventDefault();
                sendMessage();
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });

            if (escalateBtn) {
                escalateBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (confirm('Are you sure you want to escalate this trip to the support team?')) {
                        const escUrl = wrapper.dataset.escalateUrl;

                        if (!escUrl) {
                            return;
                        }

                        fetch(escUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                            },
                            body: JSON.stringify({}),
                        })
                            .then(() => window.location.reload())
                            .catch((error) => {
                                console.error('Trip escalation failed:', error);
                            });
                    }
                });
            }

            if (deescalateBtn) {
                deescalateBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (confirm('Resolve this escalation?')) {
                        const deescUrl = this.dataset.url;

                        if (!deescUrl) {
                            return;
                        }

                        fetch(deescUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                            },
                            body: JSON.stringify({}),
                        })
                            .then(() => window.location.reload())
                            .catch((error) => {
                                console.error('Trip de-escalation failed:', error);
                            });
                    }
                });
            }

            // Initial load
            loadMessages();

            // Setup Echo
            const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
            const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}';
            const bookingId = '{{ $booking->id }}';

            if (pusherKey && typeof window.Echo !== 'undefined' && bookingId) {
                const EchoConstructor = typeof Echo !== 'undefined' ? Echo : window.Echo;

                if (typeof EchoConstructor === 'function') {
                    window.Echo = new EchoConstructor({
                        broadcaster: 'pusher',
                        key: pusherKey,
                        cluster: pusherCluster || 'mt1',
                        forceTLS: true,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth',
                        auth: {
                            headers: {
                                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                            },
                        },
                    });

                    window.Echo.private(`trip-messaging.${bookingId}`)
                        .listen('.NewTripMessage', () => {
                            loadMessages();
                        });
                }
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTripMessaging);
        } else {
            initTripMessaging();
        }

        if (typeof window.$ !== 'undefined') {
            $(document).on('ajaxComplete', function () {
                initTripMessaging();
            });
        }
    })();
</script>
