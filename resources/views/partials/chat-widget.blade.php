<div id="chat-root" class="chat-root">
    <button id="chat-toggle" class="chat-toggle" aria-label="Open chat widget">
        💬 Chat with us
    </button>

    <div id="chat-panel" class="chat-panel" role="region" aria-label="Chat interface">
        <div class="chat-header">
            <div class="chat-header-top">
                <div class="chat-brand">
                    <span class="chat-title">MXCar AI Assistant</span>
                    <span class="chat-status" id="chat-status" aria-live="polite" title="Connection status">●</span>
                </div>
                <div class="chat-header-actions">
                    <button id="chat-search-toggle" class="chat-icon-btn" aria-label="Search messages" title="Search">🔍</button>
                    <button id="chat-export-btn" class="chat-icon-btn" aria-label="Export conversation" title="Export">⬇️</button>
                    <button id="chat-clear-btn" class="chat-icon-btn" aria-label="Clear chat" title="Clear">🗑️</button>
                    <button id="chat-close" class="chat-close" aria-label="Close chat">&times;</button>
                </div>
            </div>
            <div class="chat-conversation-info" id="chat-conversation-info"></div>
        </div>

        <div id="chat-search-box" class="chat-search-box" style="display: none;">
            <input
                type="text"
                id="chat-search-input"
                placeholder="Search messages..."
                class="chat-search-input"
                aria-label="Search messages"
            />
        </div>

        <div id="chat-messages" class="chat-messages" role="log" aria-live="polite" aria-label="Chat messages"></div>

        <div id="chat-suggested" class="chat-suggested"></div>

        <form id="chat-form" class="chat-form">
            <input
                id="chat-input"
                type="text"
                placeholder="Ask about our vehicles..."
                autocomplete="off"
                class="chat-input"
            />
            <button type="submit" class="chat-submit">Send</button>
        </form>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700&display=swap');

    @keyframes carBob {
        from { transform: translateY(0); }
        to { transform: translateY(-2px); }
    }

    @keyframes dotBounce {
        0%, 80%, 100% { transform: scale(0.7); opacity: 0.4; }
        40% { transform: scale(1); opacity: 1; }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Chat Root Container */
    .chat-root {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9999;
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    /* Toggle Button */
    .chat-toggle {
        padding: 12px 20px;
        background: #0A0F1E;
        color: white;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .chat-toggle:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        transform: translateY(-2px);
    }

    /* Chat Panel */
    .chat-panel {
        display: none;
        position: absolute;
        right: 0;
        bottom: 80px;
        width: 380px;
        height: 550px;
        background: #f5f6f8;
        border: none;
        border-radius: 20px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18), 0 4px 16px rgba(0, 0, 0, 0.08);
        flex-direction: column;
        overflow: hidden;
    }

    .chat-panel.open {
        display: flex;
    }

    /* Chat Header */
    .chat-header {
        background: #0A0F1E;
        color: white;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
    }

    .chat-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .chat-brand {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-title {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .chat-status {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        display: inline-block;
        animation: pulse 2s ease-in-out infinite;
        flex-shrink: 0;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .chat-header-actions {
        display: flex;
        gap: 4px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .chat-icon-btn {
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: white;
        cursor: pointer;
        font-size: 14px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .chat-icon-btn:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    .chat-conversation-info {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.45);
        font-weight: 400;
        letter-spacing: 0.03em;
        line-height: 1.4;
    }

    /* Close Button */
    .chat-close {
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: white;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .chat-close:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    /* Messages Container */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px 14px;
        background: #f5f6f8;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-messages::-webkit-scrollbar {
        width: 3px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    /* Chat Form */
    .chat-form {
        display: flex;
        gap: 8px;
        padding: 10px 12px;
        border-top: 0.5px solid #e5e7eb;
        background: white;
        flex-shrink: 0;
    }

    /* Chat Input */
    .chat-input {
        flex: 1;
        padding: 10px 14px;
        border: 0.5px solid #e5e7eb;
        border-radius: 24px;
        font-size: 13px;
        outline: none;
        background: #f5f6f8;
        color: #1a1a2e;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.2s;
    }

    .chat-input:focus {
        border-color: #0A0F1E;
        box-shadow: none;
    }

    /* Submit Button */
    .chat-submit {
        width: 40px;
        height: 40px;
        background: #EF9F27;
        color: #0A0F1E;
        border: 2px solid #EF9F27;
        border-radius: 50%;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(239, 159, 39, 0.3);
        font-family: 'Syne', sans-serif;
        letter-spacing: 0.5px;
    }

    .chat-submit:hover:not(:disabled) {
        background: #0A0F1E;
        color: #EF9F27;
        border-color: #EF9F27;
        box-shadow: 0 4px 12px rgba(239, 159, 39, 0.5);
        transform: translateY(-2px);
    }

    .chat-submit:active:not(:disabled) {
        transform: translateY(0);
    }

    .chat-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #EF9F27;
        color: #0A0F1E;
    }

    /* Typing Bubble */
    .chat-typing-bubble {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        border-radius: 16px 16px 16px 4px;
        padding: 10px 14px;
        margin-bottom: 0;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        width: fit-content;
        max-width: 240px;
    }

    .chat-car-icon {
        animation: carBob 0.55s ease-in-out infinite alternate;
        flex-shrink: 0;
    }

    .chat-dots {
        display: flex;
        gap: 3px;
        align-items: center;
    }

    .chat-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #EF9F27;
        animation: dotBounce 1s ease-in-out infinite;
    }

    .chat-dot:nth-child(2) {
        animation-delay: 0.15s;
    }

    .chat-dot:nth-child(3) {
        animation-delay: 0.3s;
    }

    /* User Message */
    .chat-message-user {
        margin-bottom: 0;
        padding: 10px 14px;
        border-radius: 16px 16px 4px 16px;
        word-wrap: break-word;
        max-width: 240px;
        margin-left: auto;
        margin-right: 0;
        background: #0A0F1E;
        color: white;
        box-shadow: none;
        font-size: 13px;
        display: block;
        width: fit-content;
    }

    /* Assistant Message */
    .chat-message-assistant {
        margin-bottom: 0;
        padding: 10px 14px;
        border-radius: 16px 16px 16px 4px;
        word-wrap: break-word;
        max-width: 240px;
        background: white;
        color: #1a1a2e;
        margin-right: 0;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        font-size: 13px;
    }

    .chat-section-title {
        font-weight: 700;
        color: #EF9F27;
        margin-top: 8px;
        margin-bottom: 6px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .chat-list-item {
        margin-left: 12px;
        margin-bottom: 5px;
        font-size: 13px;
        line-height: 1.55;
        position: relative;
        padding-left: 6px;
    }

    .chat-list-item:before {
        content: '•';
        position: absolute;
        left: 0;
        color: #EF9F27;
    }

    .chat-text {
        margin-bottom: 6px;
        font-size: 13px;
        line-height: 1.55;
    }

    .chat-content-wrapper {
        display: flex;
        flex-direction: column;
    }

    /* Search Box */
    .chat-search-box {
        padding: 8px 12px;
        border-bottom: 0.5px solid #e5e7eb;
        background: white;
    }

    .chat-search-input {
        width: 100%;
        padding: 8px 10px;
        border: 0.5px solid #0A0F1E;
        border-radius: 4px;
        font-size: 12px;
        outline: none;
        background: #f5f6f8;
        font-family: 'DM Sans', sans-serif;
    }

    .chat-search-input:focus {
        box-shadow: 0 0 0 2px rgba(10, 15, 30, 0.1);
    }

    /* Suggested Questions */
    .chat-suggested {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-top: 0.5px solid #e5e7eb;
        background: white;
    }

    .chat-suggested-title {
        font-size: 10px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .chat-suggestion-btn {
        padding: 8px 10px;
        background: #f5f6f8;
        border: 0.5px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: #1a1a2e;
        transition: all 0.2s;
        text-align: left;
        font-family: 'DM Sans', sans-serif;
    }

    .chat-suggestion-btn:hover {
        background: #0A0F1E;
        color: white;
        border-color: #0A0F1E;
    }

    /* Message Timestamp */
    .chat-message-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 0;
        position: relative;
        gap: 2px;
    }

    .chat-message-time {
        font-size: 10px;
        color: #9ca3af;
        margin-bottom: 0;
        padding: 0 2px;
    }

    .chat-message-user .chat-message-time {
        text-align: right;
    }

    .chat-message-assistant .chat-message-time {
        text-align: left;
    }

    .chat-message-content {
        position: relative;
        display: flex;
        gap: 4px;
        align-items: flex-end;
    }

    .chat-message-user .chat-message-content {
        flex-direction: row-reverse;
    }

    .chat-message-actions {
        display: none;
        gap: 2px;
        padding: 2px;
    }

    .chat-message-wrapper:hover .chat-message-actions {
        display: flex;
    }

    .chat-message-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 12px;
        padding: 2px 4px;
        color: #666;
        transition: all 0.2s;
        opacity: 0.6;
    }

    .chat-message-action-btn:hover {
        opacity: 1;
        color: #EF9F27;
    }

    .chat-message-text {
        flex: 1;
    }

    /* Edit Mode */
    .chat-message-edit-box {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .chat-message-edit-input {
        flex: 1;
        padding: 6px 8px;
        border: 0.5px solid #0A0F1E;
        border-radius: 4px;
        font-size: 13px;
        outline: none;
        font-family: 'DM Sans', sans-serif;
    }

    .chat-message-edit-btn {
        padding: 4px 8px;
        background: #0A0F1E;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 11px;
        transition: background 0.2s;
    }

    .chat-message-edit-btn:hover {
        background: #EF9F27;
    }

    /* Toast Notification */
    .chat-toast {
        position: fixed;
        bottom: 100px;
        right: 30px;
        background: #333;
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 12px;
        z-index: 10000;
        animation: slideUp 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .chat-panel {
            width: 100vw !important;
            height: 100vh !important;
            max-height: 100vh;
            position: fixed !important;
            right: 0 !important;
            bottom: 0 !important;
            border-radius: 0 !important;
        }

        .chat-root {
            right: 0 !important;
            bottom: 0 !important;
        }

        .chat-toggle {
            width: 100%;
            border-radius: 0;
        }

        .chat-icon-btn {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .chat-suggestion-btn {
            padding: 10px 12px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .chat-header {
            padding: 10px 12px;
        }

        .chat-title {
            font-size: 13px;
        }

        .chat-icon-btn {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .chat-form {
            padding: 10px;
            gap: 6px;
        }

        .chat-input,
        .chat-submit {
            font-size: 12px;
            padding: 8px 10px;
        }
    }
</style>

<script>
    // Pass authenticated user info to JavaScript (if user is logged in)
    // Note: The site uses the 'customer' guard for authentication
    window.ChatAuthUser = @json(auth('customer')->user()) || null;
    window.ChatUserId = @json(auth('customer')->id()) || null;
</script>

<script>
(function() {
    const API_URL = '/api/chat/send';
    const STORAGE_KEY = 'chat_conversation_id';
    const STORAGE_TITLE_KEY = 'chat_conversation_title';
    const STORAGE_START_KEY = 'chat_conversation_start';
    const MAX_RETRIES = 3;
    const REQUEST_TIMEOUT = 30000; // 30 seconds
    let SUGGESTED_QUESTIONS = []; // Loaded from admin settings via API

    const toggle = document.getElementById('chat-toggle');
    const closeBtn = document.getElementById('chat-close');
    const panel = document.getElementById('chat-panel');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messagesDiv = document.getElementById('chat-messages');
    const statusEl = document.getElementById('chat-status');
    const infoEl = document.getElementById('chat-conversation-info');
    const suggestedEl = document.getElementById('chat-suggested');
    const searchToggleBtn = document.getElementById('chat-search-toggle');
    const searchBox = document.getElementById('chat-search-box');
    const searchInput = document.getElementById('chat-search-input');
    const clearBtn = document.getElementById('chat-clear-btn');
    const exportBtn = document.getElementById('chat-export-btn');
    
    let historyLoaded = false;
    let allMessages = [];
    let retryCount = 0;
    let lastMessageId = 0;
    let isOnline = true;

    // Initialize
    init();

    function init() {
        setOnlineStatus(true);
        setupEventListeners();
        loadSuggestedQuestions();
        // Questions will be shown after history is loaded
    }

    function setupEventListeners() {
        toggle.addEventListener('click', handleToggle);
        closeBtn.addEventListener('click', () => panel.classList.remove('open'));
        form.addEventListener('submit', handleFormSubmit);
        clearBtn.addEventListener('click', handleClearChat);
        exportBtn.addEventListener('click', handleExportChat);
        searchToggleBtn.addEventListener('click', toggleSearch);
        searchInput.addEventListener('input', handleSearch);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') input.blur();
        });
        window.addEventListener('online', () => setOnlineStatus(true));
        window.addEventListener('offline', () => setOnlineStatus(false));
    }

    async function loadSuggestedQuestions() {
        try {
            const res = await fetch('/api/chat/suggested-questions', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                signal: AbortSignal.timeout(REQUEST_TIMEOUT),
            });

            if (res.ok) {
                const data = await res.json();
                if (data.questions && Array.isArray(data.questions)) {
                    SUGGESTED_QUESTIONS = data.questions;
                    console.log('Loaded suggested questions:', SUGGESTED_QUESTIONS);
                }
            }
        } catch (err) {
            console.warn('Could not load suggested questions, using defaults:', err);
            // Use default questions if fetch fails
        }
    }

    function handleToggle() {
        const isHidden = !panel.classList.contains('open');
        if (isHidden) {
            panel.classList.add('open');
            if (!historyLoaded) {
                // Show loader while fetching history
                showLoader();
                loadConversationHistory();
            }
            input.focus();
        } else {
            panel.classList.remove('open');
            searchBox.style.display = 'none';
        }
    }

    function setOnlineStatus(online) {
        isOnline = online;
        statusEl.textContent = online ? '●' : '○';
        statusEl.style.color = online ? '#4ade80' : '#f87171';
        statusEl.title = online ? 'Connected' : 'Offline';
    }

    function updateConversationInfo() {
        const conversationId = localStorage.getItem(STORAGE_KEY);
        const startTime = localStorage.getItem(STORAGE_START_KEY);
        const title = localStorage.getItem(STORAGE_TITLE_KEY);
        
        if (conversationId && startTime) {
            const start = new Date(parseInt(startTime));
            const now = new Date();
            const minutes = Math.floor((now - start) / 60000);
            const durationText = minutes > 0 ? `${minutes}m` : 'Just started';
            infoEl.textContent = `ID: ${conversationId} • ${durationText}${title ? ' • ' + title : ''}`;
        }
    }

    function showSuggestedQuestions() {
        suggestedEl.innerHTML = `
            <div class="chat-suggested-title">Suggested Questions</div>
            ${SUGGESTED_QUESTIONS.map(q => `
                <button type="button" class="chat-suggestion-btn" data-question="${q}">
                    ${q}
                </button>
            `).join('')}
        `;

        suggestedEl.querySelectorAll('.chat-suggestion-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                input.value = btn.dataset.question;
                input.focus();
                form.dispatchEvent(new Event('submit'));
                suggestedEl.innerHTML = '';
            });
        });
    }

    function hideSuggested() {
        if (suggestedEl.children.length > 0) {
            suggestedEl.innerHTML = '';
        }
    }

    function handleClearChat() {
        if (!confirm('Are you sure? This will clear all messages in this conversation.')) return;
        
        messagesDiv.innerHTML = '';
        allMessages = [];
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STORAGE_TITLE_KEY);
        localStorage.removeItem(STORAGE_START_KEY);
        infoEl.textContent = '';
        showSuggestedQuestions();
        showToast('Chat cleared', 'success');
    }

    function handleExportChat() {
        if (allMessages.length === 0) {
            showToast('No messages to export', 'error');
            return;
        }

        const conversationId = localStorage.getItem(STORAGE_KEY);
        const title = localStorage.getItem(STORAGE_TITLE_KEY) || 'MXCar Chat';
        const startTime = localStorage.getItem(STORAGE_START_KEY);
        
        const data = {
            title,
            conversationId,
            startTime: new Date(parseInt(startTime)).toLocaleString(),
            messages: allMessages
        };

        const json = JSON.stringify(data, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `chat-${conversationId}-${Date.now()}.json`;
        a.click();
        URL.revokeObjectURL(url);
        
        showToast('Conversation exported as JSON', 'success');
    }

    function toggleSearch() {
        if (searchBox.style.display === 'none') {
            searchBox.style.display = 'block';
            searchInput.focus();
        } else {
            searchBox.style.display = 'none';
            searchInput.value = '';
            document.querySelectorAll('.chat-message-wrapper').forEach(m => m.style.opacity = '1');
        }
    }

    function handleSearch(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.chat-message-wrapper').forEach(msg => {
            const text = msg.textContent.toLowerCase();
            msg.style.opacity = text.includes(query) ? '1' : '0.3';
        });
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'chat-toast';
        toast.textContent = message;
        toast.style.background = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6'
        }[type] || '#333';
        
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideUp 0.3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    async function loadConversationHistory() {
        const conversationId = localStorage.getItem(STORAGE_KEY);
        if (!conversationId) {
            hideLoader();
            historyLoaded = true;
            showSuggestedQuestions();
            return;
        }

        try {
            // Build query with conversation ID and optional user ID
            const params = new URLSearchParams({
                conversation_id: conversationId,
            });
            if (window.ChatUserId) {
                params.append('user_id', window.ChatUserId);
            }

            const res = await fetch(`/api/chat/history?${params.toString()}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                signal: AbortSignal.timeout(REQUEST_TIMEOUT),
            });

            hideLoader();

            if (!res.ok) throw new Error('Failed to load history');

            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                // History found - show messages
                data.messages.forEach(msg => {
                    appendMessage(msg.role, msg.content, msg.timestamp || new Date().toISOString());
                });
                hideSuggested();
                updateConversationInfo();
                setTimeout(() => {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }, 50);
            } else {
                // No history - show suggested questions
                showSuggestedQuestions();
            }
            historyLoaded = true;
        } catch (err) {
            hideLoader();
            console.error('Error loading history:', err);
            historyLoaded = true;
            showSuggestedQuestions();
        }
    }

    function showLoader() {
        const wrapperEl = document.createElement('div');
        wrapperEl.className = 'chat-message-wrapper';
        wrapperEl.id = 'chat-loader';
        
        const loaderEl = document.createElement('div');
        loaderEl.className = 'chat-typing-bubble';
        loaderEl.innerHTML = `
            <svg class="chat-car-icon" width="26" height="14" viewBox="0 0 26 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="6" width="24" height="6" rx="2" fill="#334155"/>
                <path d="M5 6 L8 2 L18 2 L21 6 Z" fill="#475569"/>
                <rect x="21" y="7" width="3" height="2" rx="1" fill="#FBBF24" opacity="0.9"/>
                <circle cx="18" cy="12" r="2.5" fill="#0f172a" stroke="#EF9F27" stroke-width="1"/>
                <circle cx="8" cy="12" r="2.5" fill="#0f172a" stroke="#EF9F27" stroke-width="1"/>
                <rect x="1" y="9.5" width="24" height="1" rx="0.5" fill="#EF9F27" opacity="0.8"/>
            </svg>
            <div class="chat-dots">
                <div class="chat-dot"></div>
                <div class="chat-dot"></div>
                <div class="chat-dot"></div>
            </div>
        `;
        
        wrapperEl.appendChild(loaderEl);
        messagesDiv.appendChild(wrapperEl);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function hideLoader() {
        const loader = document.getElementById('chat-loader');
        if (loader) loader.remove();
    }

    function formatMessage(text) {
        const lines = text.split('\n').filter(line => line.trim());
        const elements = [];
        let currentSection = null;
        let sectionContent = [];

        for (const line of lines) {
            const trimmed = line.trim();
            
            if (trimmed.includes('===') || (trimmed.toUpperCase() === trimmed && trimmed.includes(':'))) {
                if (currentSection && sectionContent.length > 0) {
                    elements.push({ type: 'section', title: currentSection, content: sectionContent });
                    sectionContent = [];
                }
                currentSection = trimmed.replace(/=/g, '').trim();
            } else if (trimmed.startsWith('•') || trimmed.startsWith('-') || /^\d+\./.test(trimmed)) {
                sectionContent.push({ type: 'item', text: trimmed });
            } else {
                sectionContent.push({ type: 'text', text: trimmed });
            }
        }

        if (currentSection && sectionContent.length > 0) {
            elements.push({ type: 'section', title: currentSection, content: sectionContent });
        } else if (sectionContent.length > 0) {
            elements.push({ type: 'content', content: sectionContent });
        }

        return elements;
    }

    function appendMessage(role, text, timestamp = null, messageId = null) {
        if (!messageId) messageId = ++lastMessageId;
        
        const time = timestamp ? new Date(timestamp) : new Date();
        const timeStr = time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

        const wrapperEl = document.createElement('div');
        wrapperEl.className = 'chat-message-wrapper';
        wrapperEl.id = `msg-${messageId}`;

        const timeEl = document.createElement('div');
        timeEl.className = 'chat-message-time';
        timeEl.textContent = timeStr;

        const contentEl = document.createElement('div');
        contentEl.className = 'chat-message-content';

        const actionsEl = document.createElement('div');
        actionsEl.className = 'chat-message-actions';

        if (role === 'user') {
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-message-user';
            msgEl.id = `text-${messageId}`;
            msgEl.textContent = text;
            
            const copyBtn = document.createElement('button');
            copyBtn.className = 'chat-message-action-btn';
            copyBtn.innerHTML = '📋';
            copyBtn.title = 'Copy';
            copyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                navigator.clipboard.writeText(text);
                showToast('Copied to clipboard');
            });

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'chat-message-action-btn';
            deleteBtn.innerHTML = '🗑️';
            deleteBtn.title = 'Delete';
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                wrapperEl.remove();
                allMessages = allMessages.filter(m => m.id !== messageId);
            });

            const editBtn = document.createElement('button');
            editBtn.className = 'chat-message-action-btn';
            editBtn.innerHTML = '✏️';
            editBtn.title = 'Edit';
            editBtn.addEventListener('click', (e) => {
                e.preventDefault();
                enableEditMode(messageId, text);
            });

            actionsEl.appendChild(copyBtn);
            actionsEl.appendChild(editBtn);
            actionsEl.appendChild(deleteBtn);
            contentEl.appendChild(msgEl);
            contentEl.appendChild(actionsEl);
        } else {
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-message-assistant';
            msgEl.id = `text-${messageId}`;
            
            const formatted = formatMessage(text);
            msgEl.innerHTML = '';

            formatted.forEach(element => {
                if (element.type === 'section') {
                    const titleEl = document.createElement('div');
                    titleEl.className = 'chat-section-title';
                    titleEl.textContent = element.title;
                    msgEl.appendChild(titleEl);

                    const contentWrapper = document.createElement('div');
                    contentWrapper.className = 'chat-content-wrapper';
                    element.content.forEach(item => {
                        const itemEl = document.createElement('div');
                        itemEl.className = item.type === 'item' ? 'chat-list-item' : 'chat-text';
                        itemEl.textContent = item.text;
                        contentWrapper.appendChild(itemEl);
                    });
                    msgEl.appendChild(contentWrapper);
                } else {
                    element.content.forEach(item => {
                        const itemEl = document.createElement('div');
                        itemEl.className = item.type === 'item' ? 'chat-list-item' : 'chat-text';
                        itemEl.textContent = item.text;
                        msgEl.appendChild(itemEl);
                    });
                }
            });

            const copyBtn = document.createElement('button');
            copyBtn.className = 'chat-message-action-btn';
            copyBtn.innerHTML = '📋';
            copyBtn.title = 'Copy';
            copyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                navigator.clipboard.writeText(text);
                showToast('Copied to clipboard');
            });

            actionsEl.appendChild(copyBtn);
            contentEl.appendChild(msgEl);
            contentEl.appendChild(actionsEl);
        }

        wrapperEl.appendChild(timeEl);
        wrapperEl.appendChild(contentEl);
        messagesDiv.appendChild(wrapperEl);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        allMessages.push({ id: messageId, role, content: text, timestamp: time.toISOString() });
    }

    function enableEditMode(messageId, originalText) {
        const textEl = document.getElementById(`text-${messageId}`);
        if (!textEl) return;

        const editBox = document.createElement('div');
        editBox.className = 'chat-message-edit-box';
        editBox.innerHTML = `
            <input type="text" class="chat-message-edit-input" value="${originalText}" />
            <button type="button" class="chat-message-edit-btn">Save</button>
        `;

        const input = editBox.querySelector('input');
        const saveBtn = editBox.querySelector('button');

        const handleSave = () => {
            const newText = input.value.trim();
            if (newText) {
                // Restore the message element with updated text
                textEl.textContent = newText;
                textEl.replaceWith(textEl);
                editBox.remove();
                
                // Update the message in allMessages array
                allMessages = allMessages.map(m => m.id === messageId ? { ...m, content: newText } : m);
                showToast('Message updated');
            } else {
                showToast('Message cannot be empty', 'error');
            }
        };

        saveBtn.addEventListener('click', (e) => {
            e.preventDefault();
            handleSave();
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleSave();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                editBox.replaceWith(textEl);
            }
        });

        textEl.replaceWith(editBox);
        input.focus();
        input.select();
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        if (!isOnline) {
            showToast('You are offline. Check your connection.', 'error');
            return;
        }

        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';
        input.disabled = true;
        form.querySelector('button[type="submit"]').disabled = true;
        hideSuggested();

        let conversationId = localStorage.getItem(STORAGE_KEY);
        showLoader();

        const makeRequest = async (attempt = 1) => {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), REQUEST_TIMEOUT);
                
                // Log client-side info for debugging
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const authUser = window.ChatAuthUser;
                console.log('Chat Request Debug:', {
                    authUser: authUser ? authUser.id : 'NOT_LOGGED_IN',
                    csrfToken: csrfToken ? 'PRESENT' : 'MISSING',
                    conversationId: conversationId,
                    credentials: 'include'
                });

                const res = await fetch(API_URL, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken,
                    },
                    body: JSON.stringify({
                        message: text,
                        conversation_id: conversationId ? parseInt(conversationId) : null,
                        user_id: window.ChatUserId || null,  // Send user ID from frontend
                    }),
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);
                const data = await res.json();
                hideLoader();
                
                // Log response for debugging
                console.log('Chat Response Debug:', {
                    status: res.status,
                    user_id_in_response: data.user_id || 'NOT_PRESENT',
                    conversation_id: data.conversation_id,
                });

                if (res.status === 429) {
                    showToast('Rate limit reached. You\'ve used your daily quota.', 'error');
                    input.disabled = false;
                    form.querySelector('button[type="submit"]').disabled = false;
                    return;
                }

                if (!res.ok) {
                    if (attempt < MAX_RETRIES) {
                        showToast(`Retrying... (${attempt}/${MAX_RETRIES})`, 'info');
                        await new Promise(r => setTimeout(r, 1000 * attempt));
                        return makeRequest(attempt + 1);
                    }
                    throw new Error(data.error || 'Failed to send message');
                }

                if (data.conversation_id) {
                    if (!conversationId) {
                        localStorage.setItem(STORAGE_KEY, data.conversation_id);
                        localStorage.setItem(STORAGE_START_KEY, Date.now());
                        updateConversationInfo();
                    }
                    conversationId = data.conversation_id;
                }

                appendMessage('assistant', data.assistant);
            } catch (err) {
                hideLoader();
                if (err.name === 'AbortError') {
                    if (attempt < MAX_RETRIES) {
                        showToast(`Request timed out. Retrying... (${attempt}/${MAX_RETRIES})`, 'info');
                        await new Promise(r => setTimeout(r, 1000 * attempt));
                        return makeRequest(attempt + 1);
                    }
                    showToast('Request timed out. Please try again.', 'error');
                } else {
                    showToast(err.message || 'Network error. Check your connection.', 'error');
                }
            } finally {
                input.disabled = false;
                form.querySelector('button[type="submit"]').disabled = false;
                input.focus();
            }
        };

        await makeRequest();
    }

    input.focus();
})();
</script>
