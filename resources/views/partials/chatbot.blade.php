@if(config('chatbot.enabled'))
<style>
    .chatbot-bubble {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--brand-secondary);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 24px rgba(55, 79, 167, 0.4), 0 2px 6px rgba(0, 0, 0, 0.15);
        z-index: 9998;
        transition: all 0.2s;
    }
    .chatbot-bubble:hover {
        background: #2a3d85;
        transform: scale(1.08);
    }
    .chatbot-bubble.open {
        transform: rotate(90deg);
    }
    .chatbot-bubble .icon-close { display: none; }
    .chatbot-bubble.open .icon-chat { display: none; }
    .chatbot-bubble.open .icon-close { display: block; }

    .chatbot-panel {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 400px;
        max-width: calc(100vw - 3rem);
        height: 600px;
        max-height: calc(100vh - 3rem);
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2), 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: chatbotOpen 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .chatbot-panel.open { display: flex; }

    @keyframes chatbotOpen {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .chatbot-header {
        background: linear-gradient(135deg, #0a0a0a 0%, #16203d 100%);
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
    }
    .chatbot-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--brand-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .chatbot-header-info { flex: 1; min-width: 0; }
    .chatbot-header-name {
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.15rem;
    }
    .chatbot-header-status {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.7);
    }
    .chatbot-header-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
    }

    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .chatbot-messages::-webkit-scrollbar { width: 6px; }
    .chatbot-messages::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

    .chatbot-msg {
        max-width: 85%;
        padding: 0.7rem 1rem;
        border-radius: 14px;
        font-size: 0.85rem;
        line-height: 1.5;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    .chatbot-msg.bot {
        align-self: flex-start;
        background: white;
        border: 1px solid var(--border);
        border-bottom-left-radius: 4px;
        color: var(--brand-primary);
    }
    .chatbot-msg.user {
        align-self: flex-end;
        background: var(--brand-secondary);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .chatbot-msg strong { font-weight: 700; }

    .chatbot-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }
    .chatbot-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        background: var(--brand-secondary);
        color: white;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s;
    }
    .chatbot-action-btn:hover {
        background: #2a3d85;
        color: white;
        text-decoration: none;
    }

    .chatbot-quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        background: #fafafa;
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }
    .chatbot-quick-btn {
        padding: 0.45rem 0.85rem;
        background: white;
        border: 1px solid var(--border-strong);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--brand-primary);
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .chatbot-quick-btn:hover {
        border-color: var(--brand-secondary);
        color: var(--brand-secondary);
    }

    .chatbot-typing {
        align-self: flex-start;
        background: white;
        border: 1px solid var(--border);
        border-radius: 14px;
        border-bottom-left-radius: 4px;
        padding: 0.7rem 1rem;
        display: flex;
        gap: 0.3rem;
    }
    .chatbot-typing span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--brand-muted);
        animation: chatbotTyping 1.2s infinite;
    }
    .chatbot-typing span:nth-child(2) { animation-delay: 0.2s; }
    .chatbot-typing span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes chatbotTyping {
        0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
        30%           { opacity: 1;   transform: translateY(-3px); }
    }

    .chatbot-input-row {
        display: flex;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: white;
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }
    .chatbot-input {
        flex: 1;
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        font-size: 0.85rem;
        font-family: inherit;
        resize: none;
        outline: none;
        max-height: 90px;
        min-height: 40px;
        line-height: 1.4;
        transition: all 0.15s;
    }
    .chatbot-input:focus {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.08);
    }
    .chatbot-send {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--brand-secondary);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .chatbot-send:hover {
        background: #2a3d85;
    }
    .chatbot-send:disabled {
        background: var(--border-strong);
        cursor: not-allowed;
    }

    @media (max-width: 480px) {
        .chatbot-panel {
            bottom: 0;
            right: 0;
            width: 100vw;
            height: 100vh;
            max-height: 100vh;
            max-width: 100vw;
            border-radius: 0;
        }
        .chatbot-bubble {
            bottom: 1rem;
            right: 1rem;
            width: 56px;
            height: 56px;
        }
        .chatbot-bubble.open { display: none; }
    }
</style>

<button type="button"
        class="chatbot-bubble"
        id="chatbotBubble"
        aria-label="Open chat">
    <i class="bi bi-chat-dots-fill icon-chat"></i>
    <i class="bi bi-x-lg icon-close"></i>
</button>

<div class="chatbot-panel" id="chatbotPanel" role="dialog" aria-label="Chat assistant" aria-hidden="true">
    <div class="chatbot-header">
        <div class="chatbot-header-avatar">
            <i class="bi bi-robot"></i>
        </div>
        <div class="chatbot-header-info">
            <div class="chatbot-header-name">{{ config('chatbot.bot_name') }}</div>
            <div class="chatbot-header-status">Online · Typically replies instantly</div>
        </div>
        <button type="button" class="chatbot-bubble-icon-close" id="chatbotClose" style="background:none;border:none;color:white;font-size:1.2rem;cursor:pointer;padding:0.25rem;opacity:0.8;" aria-label="Close chat">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="chatbot-messages" id="chatbotMessages"></div>

    <div class="chatbot-quick-replies" id="chatbotQuickReplies"></div>

    <div class="chatbot-input-row">
        <textarea id="chatbotInput"
                  class="chatbot-input"
                  placeholder="Type your message..."
                  rows="1"
                  maxlength="1000"></textarea>
        <button type="button" class="chatbot-send" id="chatbotSend" aria-label="Send">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<script>
(function () {
    const bubble     = document.getElementById('chatbotBubble');
    const panel      = document.getElementById('chatbotPanel');
    const closeBtn   = document.getElementById('chatbotClose');
    const messages   = document.getElementById('chatbotMessages');
    const quickReels = document.getElementById('chatbotQuickReplies');
    const input      = document.getElementById('chatbotInput');
    const sendBtn    = document.getElementById('chatbotSend');

    if (!bubble || !panel) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const welcomeMessage = @json(config('chatbot.welcome_message'));
    const welcomeQuick   = @json(config('chatbot.quick_replies_welcome'));
    const endpoint       = @json(route('chatbot.message'));

    let hasBooted = false;
    let isSending = false;

    function openPanel() {
        panel.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
        bubble.classList.add('open');
        if (!hasBooted) {
            boot();
            hasBooted = true;
        }
        setTimeout(() => input.focus(), 200);
    }

    function closePanel() {
        panel.classList.remove('open');
        panel.setAttribute('aria-hidden', 'true');
        bubble.classList.remove('open');
    }

    bubble.addEventListener('click', () => {
        if (panel.classList.contains('open')) closePanel();
        else openPanel();
    });
    closeBtn?.addEventListener('click', closePanel);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel.classList.contains('open')) closePanel();
    });

    function boot() {
        appendBot(welcomeMessage);
        renderQuickReplies(welcomeQuick);
    }

    function appendUser(text) {
        const el = document.createElement('div');
        el.className = 'chatbot-msg user';
        el.textContent = text;
        messages.appendChild(el);
        scrollBottom();
    }

    function appendBot(html, actions) {
        const el = document.createElement('div');
        el.className = 'chatbot-msg bot';

        const content = document.createElement('div');
        content.innerHTML = html;
        el.appendChild(content);

        if (actions && actions.length) {
            const actionsRow = document.createElement('div');
            actionsRow.className = 'chatbot-actions';
            actions.forEach((a) => {
                const link = document.createElement('a');
                link.href = a.url;
                link.className = 'chatbot-action-btn';
                link.textContent = a.label;
                actionsRow.appendChild(link);
            });
            el.appendChild(actionsRow);
        }

        messages.appendChild(el);
        scrollBottom();
    }

    function showTyping() {
        const el = document.createElement('div');
        el.className = 'chatbot-typing';
        el.id = 'chatbotTyping';
        el.innerHTML = '<span></span><span></span><span></span>';
        messages.appendChild(el);
        scrollBottom();
    }

    function hideTyping() {
        document.getElementById('chatbotTyping')?.remove();
    }

    function scrollBottom() {
        setTimeout(() => { messages.scrollTop = messages.scrollHeight; }, 30);
    }

    function renderQuickReplies(replies) {
        quickReels.innerHTML = '';
        if (!replies || !replies.length) {
            quickReels.style.display = 'none';
            return;
        }
        quickReels.style.display = 'flex';
        replies.forEach((r) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chatbot-quick-btn';
            btn.textContent = r;
            btn.addEventListener('click', () => sendMessage(r));
            quickReels.appendChild(btn);
        });
    }

    async function sendMessage(text) {
        text = (text || '').trim();
        if (!text || isSending) return;

        isSending = true;
        sendBtn.disabled = true;
        appendUser(text);
        input.value = '';
        resizeInput();
        renderQuickReplies([]);
        showTyping();

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ message: text }),
            });

            hideTyping();

            if (!res.ok) {
                appendBot('Sorry, something went wrong. Please try again in a moment.');
                return;
            }

            const data = await res.json();
            appendBot(data.message || 'Sorry, I didn\'t catch that.', data.actions);
            renderQuickReplies(data.quick_replies || []);
        } catch (err) {
            hideTyping();
            appendBot('I couldn\'t reach the server. Please check your connection and try again.');
        } finally {
            isSending = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    sendBtn.addEventListener('click', () => sendMessage(input.value));

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(input.value);
        }
    });

    function resizeInput() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 90) + 'px';
    }
    input.addEventListener('input', resizeInput);
})();
</script>
@endif