(function() {
    const initMessaging = function() {
        const wrapper = document.querySelector('.trip-chat-wrapper:not([data-initialized])');
        if (!wrapper) return;
        
        wrapper.setAttribute('data-initialized', 'true');
        
        const chatList = wrapper.querySelector('#trip-chat-messages');
        const form = wrapper.querySelector('.trip-chat-form');
        if (!form) return;
        
        const input = form.querySelector('input[name="message"]');
        const fetchUrl = wrapper.dataset.fetchUrl;
        const storeUrl = wrapper.dataset.storeUrl;
        const escalateBtn = wrapper.querySelector('.btn-escalate');
        const deescalateBtn = wrapper.querySelector('.btn-deescalate');
        
        // Ensure we always have a CSRF token across all panels (Customer, Admin, Vendor)
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) csrfToken = tokenInput.value;
        }

        function scrollToBottom() {
            const body = wrapper.querySelector('.chat-container-body');
            if (body) body.scrollTop = body.scrollHeight;
        }

        function loadMessages() {
            if (!fetchUrl) return;
            
            fetch(fetchUrl, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok (' + res.status + ')');
                return res.json();
            })
            .then(data => {
                if(data.error) {
                    chatList.innerHTML = `<div class="text-center text-danger my-3"><i class="ti ti-alert-circle"></i> ${data.message || 'Error loading messages'}</div>`;
                    return;
                }
                const body = wrapper.querySelector('.chat-container-body');
                const scrollPos = body.scrollTop;
                const scrollHeight = body.scrollHeight;
                const clientHeight = body.clientHeight;
                const isBottom = scrollHeight - scrollPos <= clientHeight + 100;

                chatList.innerHTML = data.data.html;

                if (isBottom || scrollHeight === 0) {
                    scrollToBottom();
                }
            })
            .catch(err => {
                console.error('Trip Messaging Error:', err);
                chatList.innerHTML = `<div class="text-center text-danger my-3"><i class="ti ti-alert-circle"></i> Connection error. Please try refreshing.</div>`;
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = input.value.trim();
            if(!msg) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            const formData = new FormData(form);
            
            fetch(storeUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                input.value = '';
                submitBtn.disabled = false;
                if(!data.error) {
                    chatList.innerHTML = data.data.html;
                    scrollToBottom();
                }
            }).catch(() => {
                submitBtn.disabled = false;
            });
        });

        if (escalateBtn) {
            escalateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if(confirm('Are you sure you want to escalate this trip to the support team?')) {
                    const escUrl = wrapper.dataset.escalateUrl;
                    
                    fetch(escUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    }).then(() => window.location.reload());
                }
            });
        }

        if (deescalateBtn) {
            deescalateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if(confirm('Resolve this escalation?')) {
                    const deescUrl = this.dataset.url;
                    
                    fetch(deescUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    }).then(() => window.location.reload());
                }
            });
        }

        // Initial load
        loadMessages();
        
        // Setup Echo
        if (window.EchoConfig && typeof window.Echo !== 'undefined') {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: window.EchoConfig.pusherKey,
                cluster: window.EchoConfig.pusherCluster,
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                }
            });

            const bookingId = wrapper.dataset.bookingId;

            window.Echo.private('trip-messaging.' + bookingId)
                .listen('.NewTripMessage', (e) => {
                    loadMessages();
                });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMessaging);
    } else {
        initMessaging();
    }
    
    // Also listen for Botble's AJAX loaded events
    $(document).on('ajaxComplete', function() {
        initMessaging();
    });
})();
