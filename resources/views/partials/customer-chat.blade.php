@php
    $chatUser = auth()->user();
    $isStaffChat = \App\Support\ChatSupport::isStaff($chatUser);
    $productContext = isset($product) ? $product : null;
@endphp

@if(!$isStaffChat)
<style>
    .ls-chat-fab, .ls-chat-panel, .ls-chat-panel * { box-sizing: border-box; }
    .ls-chat-fab {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 9990;
        width: 62px;
        height: 62px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        box-shadow: 0 12px 30px rgba(79,70,229,.38);
        display: grid;
        place-items: center;
        font-size: 1.35rem;
        transition: transform .2s, box-shadow .2s;
    }
    .ls-chat-fab:hover { transform: translateY(-2px) scale(1.04); }
    .ls-chat-fab.is-hidden { display: none; }
    .ls-chat-fab .ls-chat-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        display: none;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
    }
    .ls-chat-fab .ls-chat-badge.show { display: flex; }
    .ls-chat-panel {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 9991;
        width: min(400px, calc(100vw - 24px));
        height: min(580px, calc(100vh - 40px));
        background: #fff;
        border-radius: 22px;
        overflow: hidden;
        display: none;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(15,23,42,.22);
        border: 1px solid #e2e8f0;
        font-family: Inter, system-ui, sans-serif;
    }
    .ls-chat-panel.is-open { display: flex; animation: lsChatIn .22s ease; min-height: 0; }
    .ls-chat-head {
        flex: 0 0 auto;
        background: linear-gradient(135deg, #312e81, #4f46e5 60%, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ls-chat-avatar {
        width: 42px; height: 42px; border-radius: 14px;
        background: rgba(255,255,255,.16);
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .ls-chat-head h3 { margin: 0; font-size: 15px; font-weight: 800; letter-spacing: -.02em; }
    .ls-chat-head p { margin: 2px 0 0; font-size: 12px; opacity: .85; }
    .ls-chat-close {
        margin-left: auto;
        width: 34px; height: 34px; border: none; border-radius: 10px;
        background: rgba(255,255,255,.14); color: #fff; cursor: pointer;
    }
    .ls-chat-product-bar {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #eef2ff;
        border-bottom: 1px solid #e0e7ff;
        font-size: 12px;
        color: #3730a3;
        font-weight: 600;
    }
    .ls-chat-product-bar.is-on { display: flex; }
    .ls-chat-product-bar button {
        margin-left: auto; border: none; background: none; cursor: pointer; color: #6366f1;
    }
    .ls-chat-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 16px 14px;
        background: linear-gradient(180deg, #f8fafc, #fff);
    }
    .ls-chat-empty { text-align: center; color: #64748b; padding: 48px 16px; }
    .ls-chat-empty i { font-size: 28px; color: #818cf8; display: block; margin-bottom: 10px; }
    .ls-msg {
        display: flex;
        margin-bottom: 12px;
        max-width: 86%;
        min-width: 0;
        height: auto;
    }
    .ls-msg.mine { margin-left: auto; justify-content: flex-end; }
    .ls-bubble {
        display: block;
        height: auto;
        padding: 10px 12px;
        border-radius: 16px;
        font-size: 13.5px;
        line-height: 1.45;
        max-width: 100%;
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
        white-space: normal;
        box-shadow: 0 1px 2px rgba(15,23,42,.05);
    }
    .ls-msg.mine .ls-bubble {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        border-bottom-right-radius: 6px;
    }
    .ls-msg.theirs .ls-bubble {
        background: #fff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 6px;
    }
    .ls-msg-meta { font-size: 10px; opacity: .7; margin-top: 4px; }
    .ls-prod-chip {
        display: block;
        margin-bottom: 6px;
        padding: 7px 9px;
        border-radius: 10px;
        background: rgba(255,255,255,.18);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        color: inherit;
        border: 1px solid rgba(255,255,255,.2);
    }
    .ls-msg.theirs .ls-prod-chip {
        background: #eef2ff;
        color: #3730a3;
        border-color: #c7d2fe;
    }
    .ls-chat-foot {
        flex: 0 0 auto;
        padding: 10px 12px 12px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
        display: flex;
        gap: 8px;
        min-width: 0;
    }
    .ls-chat-foot input {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 11px 12px;
        font-size: 14px;
        outline: none;
        font-family: inherit;
    }
    .ls-chat-foot input:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
    .ls-chat-foot button {
        width: 44px; border: none; border-radius: 12px;
        background: #4f46e5; color: #fff; cursor: pointer; font-size: 16px;
    }
    .ls-chat-foot button:disabled { opacity: .55; cursor: not-allowed; }
    .ls-chat-guest {
        padding: 28px 20px;
        text-align: center;
        color: #475569;
    }
    .ls-chat-guest a {
        display: inline-flex;
        margin-top: 14px;
        padding: 10px 16px;
        border-radius: 12px;
        background: #4f46e5;
        color: #fff;
        font-weight: 700;
        text-decoration: none;
    }
</style>

<button type="button" class="ls-chat-fab" id="ls-chat-toggle" aria-label="Chat hỗ trợ">
    <i class="fa-solid fa-comments"></i>
    <span class="ls-chat-badge" id="ls-chat-badge">0</span>
</button>

<div class="ls-chat-panel" id="ls-chat-panel" role="dialog" aria-label="Hỗ trợ khách hàng">
    <div class="ls-chat-head">
        <div class="ls-chat-avatar"><i class="fa-solid fa-headset"></i></div>
        <div style="flex: 1;">
            <h3>Hỗ trợ LensStore</h3>
            <p>Nhân viên trả lời trực tiếp</p>
        </div>
        <button type="button" class="ls-chat-close" id="ls-chat-toggle-ai-btn" aria-label="Gặp nhân viên" style="width: auto; padding: 0 10px; font-size: 12px; background: rgba(0,0,0,0.2);"><i class="fa-solid fa-user-tie"></i> <span id="ls-chat-ai-status">Gặp nhân viên</span></button>
        <button type="button" class="ls-chat-close" id="ls-chat-close" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ls-chat-product-bar" id="ls-chat-product-bar">
        <i class="fa-solid fa-camera"></i>
        <span id="ls-chat-product-label"></span>
        <button type="button" id="ls-chat-product-clear" aria-label="Bỏ sản phẩm"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="ls-chat-body" id="ls-chat-messages">
        @guest
            <div class="ls-chat-guest">
                <i class="fa-solid fa-lock" style="font-size:28px;color:#818cf8;display:block;margin-bottom:10px"></i>
                Đăng nhập để chat với nhân viên về sản phẩm, bảo hành và đơn hàng.
                <br>
                <a href="{{ route('login') }}">Đăng nhập ngay</a>
            </div>
        @else
            <div class="ls-chat-empty" id="ls-chat-loading"><i class="fa-solid fa-spinner fa-spin"></i>Đang tải hội thoại...</div>
        @endguest
    </div>
    @auth
    <div id="ls-chat-img-preview" style="display: none; padding: 10px 14px; background: #f8fafc; border-top: 1px solid #e2e8f0; position: relative;">
        <img id="ls-chat-img-preview-img" src="" alt="Preview" style="max-height: 80px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <button type="button" id="ls-chat-img-remove" style="position: absolute; top: 5px; left: 80px; background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: grid; place-items: center;"><i class="fa-solid fa-xmark" style="font-size: 12px;"></i></button>
    </div>
    <div class="ls-chat-foot">
        <label style="cursor: pointer; padding: 8px 10px; color: #64748b; font-size: 20px; transition: color 0.2s;" title="Đính kèm ảnh" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#64748b'">
            <i class="fa-regular fa-image"></i>
            <input type="file" id="ls-chat-img-input" accept="image/*" style="display: none;">
        </label>
        <button type="button" id="ls-chat-emoji-btn" style="background: none; border: none; cursor: pointer; padding: 8px 4px 8px 0; color: #64748b; font-size: 20px; transition: color 0.2s;" title="Biểu tượng cảm xúc" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#64748b'">
            <i class="fa-regular fa-face-smile"></i>
        </button>
        <input type="text" id="ls-chat-input" maxlength="2000" placeholder="Nhập tin nhắn..." autocomplete="off">
        <button type="button" id="ls-chat-send" aria-label="Gửi"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
    <div id="ls-chat-emoji-picker-container" style="display: none; position: absolute; bottom: 65px; left: 10px; z-index: 1000; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; background: white;">
        <emoji-picker></emoji-picker>
    </div>
    @endauth
</div>

<script>
(function () {
    const panel = document.getElementById('ls-chat-panel');
    const toggle = document.getElementById('ls-chat-toggle');
    const closeBtn = document.getElementById('ls-chat-close');
    const box = document.getElementById('ls-chat-messages');
    const input = document.getElementById('ls-chat-input');
    const sendBtn = document.getElementById('ls-chat-send');
    const badge = document.getElementById('ls-chat-badge');
    const productBar = document.getElementById('ls-chat-product-bar');
    const productLabel = document.getElementById('ls-chat-product-label');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const isAuth = {{ auth()->check() ? 'true' : 'false' }};
    const urls = {
        messages: @json(auth()->check() ? route('user.chat.messages') : null),
        send: @json(auth()->check() ? route('user.chat.send') : null),
        unread: @json(auth()->check() ? route('user.chat.unread') : null),
    };

    let lastId = 0;
    let loaded = false;
    let sending = false;
    let stickToBottom = true;
    let productId = {{ $productContext?->id ? (int) $productContext->id : 'null' }};
    let productName = @json($productContext?->name);

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, (s) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    function setBadge(n) {
        const count = Number(n) || 0;
        badge.textContent = count > 9 ? '9+' : String(count);
        badge.classList.toggle('show', count > 0);
    }

    function setProduct(id, name) {
        productId = id || null;
        productName = name || null;
        if (productId && productName) {
            productLabel.textContent = 'Đang hỏi về: ' + productName;
            productBar.classList.add('is-on');
        } else {
            productBar.classList.remove('is-on');
        }
    }

    if (productId && productName) setProduct(productId, productName);

    document.getElementById('ls-chat-product-clear')?.addEventListener('click', () => setProduct(null, null));

    function bubbleHtml(msg) {
        const mine = msg.is_mine;
        const product = msg.product
            ? `<a class="ls-prod-chip" href="${escapeHtml(msg.product.url)}"><i class="fa-solid fa-camera"></i> ${escapeHtml(msg.product.name)}</a>`
            : '';
        const text = escapeHtml(msg.content || '').replace(/\n/g, '<br>');
        const image = msg.image_url ? `<a href="${escapeHtml(msg.image_url)}" target="_blank" style="display: block; margin-top: 6px;"><img src="${escapeHtml(msg.image_url)}" style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); object-fit: cover;"></a>` : '';
        return `<div class="ls-msg ${mine ? 'mine' : 'theirs'}" data-id="${msg.id}"><div class="ls-bubble">${product}${text}${image}<div class="ls-msg-meta">${mine ? 'Bạn' : escapeHtml(msg.sender_name)} · ${escapeHtml(msg.created_at || '')}</div></div></div>`;
    }

    function renderAll(messages) {
        if (!messages.length) {
            box.innerHTML = `<div class="ls-chat-empty"><i class="fa-regular fa-comments"></i>Bắt đầu chat với nhân viên LensStore.<br><small>Hỏi thông số, bảo hành hoặc tình trạng hàng.</small></div>`;
            lastId = 0;
            return;
        }
        box.innerHTML = messages.map(bubbleHtml).join('');
        lastId = messages[messages.length - 1].id;
        if (stickToBottom) box.scrollTop = box.scrollHeight;
    }

    function appendMessages(messages) {
        if (!messages.length) return;
        const empty = box.querySelector('.ls-chat-empty');
        if (empty) empty.remove();
        messages.forEach((msg) => {
            if (box.querySelector(`[data-id="${msg.id}"]`)) return;
            box.insertAdjacentHTML('beforeend', bubbleHtml(msg));
            lastId = Math.max(lastId, msg.id);
        });
        if (stickToBottom) box.scrollTop = box.scrollHeight;
    }

    box.addEventListener('scroll', () => {
        stickToBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 48;
    });

    async function loadMessages(full) {
        if (!isAuth || !urls.messages) return;
        const params = new URLSearchParams();
        if (!full && lastId) params.set('since_id', String(lastId));
        if (panel.classList.contains('is-open')) params.set('mark_read', '1');
        const res = await fetch(urls.messages + (params.toString() ? '?' + params.toString() : ''), {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();
        if (full || !loaded) {
            renderAll(data.messages || []);
            loaded = true;
        } else {
            appendMessages(data.messages || []);
        }
        if (!panel.classList.contains('is-open')) setBadge(data.unread || 0);
        else setBadge(0);
    }

    async function loadUnread() {
        if (!isAuth || !urls.unread || panel.classList.contains('is-open')) return;
        const res = await fetch(urls.unread, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        setBadge(data.unread || 0);
    }

    async function sendMessage() {
        if (!isAuth || sending) return;
        const text = (input.value || '').trim();
        const imgInputElem = document.getElementById('ls-chat-img-input');
        const file = imgInputElem ? imgInputElem.files[0] : null;
        if (!text && !file) return;

        sending = true;
        sendBtn.disabled = true;
        input.disabled = true;

        try {
            const formData = new FormData();
            if (text) formData.append('message', text);
            if (productId) formData.append('product_id', productId);
            if (file) formData.append('image', file);

            const res = await fetch(urls.send, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: formData
            });
            const data = await res.json();
            if (!res.ok) {
                alert(data.error || data.message || 'Không gửi được tin nhắn.');
                return;
            }
            
            input.value = '';
            if (imgInputElem) imgInputElem.value = '';
            const preview = document.getElementById('ls-chat-img-preview');
            if (preview) preview.style.display = 'none';

            appendMessages([data]);
        } catch (e) {
            alert('Không gửi được tin nhắn. Vui lòng thử lại.');
        } finally {
            sending = false;
            sendBtn.disabled = false;
            input.disabled = false;
            input.focus();
            const emojiPickerContainer = document.getElementById('ls-chat-emoji-picker-container');
            if (emojiPickerContainer) emojiPickerContainer.style.display = 'none';
        }
    }

    function openChat(product) {
        if (product?.id) setProduct(product.id, product.name);
        panel.classList.add('is-open');
        toggle.classList.add('is-hidden');
        setBadge(0);
        loaded = false;
        lastId = 0;
        if (isAuth) loadMessages(true);
        input?.focus();
    }

    function closeChat() {
        panel.classList.remove('is-open');
        toggle.classList.remove('is-hidden');
    }

    // Toggle AI Feature
    let aiDisabled = false;
    document.getElementById('ls-chat-toggle-ai-btn')?.addEventListener('click', async () => {
        if (!isAuth) return;
        aiDisabled = !aiDisabled;
        
        try {
            await fetch('/user/chat/toggle-ai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ disable_ai: aiDisabled })
            });
            
            const statusText = document.getElementById('ls-chat-ai-status');
            const toggleBtn = document.getElementById('ls-chat-toggle-ai-btn');
            
            if (aiDisabled) {
                statusText.innerText = 'Gọi AI';
                toggleBtn.style.background = '#ef4444'; // Red to indicate AI is off
                
                // Gửi một tin nhắn hệ thống
                appendMessages([{
                    id: 999999 + Date.now(),
                    is_mine: false,
                    content: "🧑‍💻 Đã kết nối với Nhân viên hỗ trợ. Bạn vui lòng đợi trong giây lát, nhân viên sẽ phản hồi ngay nhé!",
                    sender_name: "Hệ thống",
                    created_at: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})
                }]);
            } else {
                statusText.innerText = 'Gặp nhân viên';
                toggleBtn.style.background = 'rgba(0,0,0,0.2)'; // Normal
                
                appendMessages([{
                    id: 999999 + Date.now(),
                    is_mine: false,
                    content: "🤖 Trợ lý AI đã được bật trở lại.",
                    sender_name: "Hệ thống",
                    created_at: new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})
                }]);
            }
        } catch (e) {
            console.error(e);
        }
    });

    toggle.addEventListener('click', () => openChat());
    closeBtn.addEventListener('click', closeChat);
    sendBtn?.addEventListener('click', sendMessage);
    input?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    window.LensStoreChat = { open: openChat, attachProduct: setProduct };

    if (isAuth) {
        loadUnread();
        setInterval(() => {
            if (panel.classList.contains('is-open')) loadMessages(false);
            else loadUnread();
        }, 2500);

        // Setup image preview & emojis
        const imgInput = document.getElementById('ls-chat-img-input');
        const imgPreview = document.getElementById('ls-chat-img-preview');
        const imgPreviewImg = document.getElementById('ls-chat-img-preview-img');
        const imgRemove = document.getElementById('ls-chat-img-remove');
        const emojiBtn = document.getElementById('ls-chat-emoji-btn');
        const emojiPickerContainer = document.getElementById('ls-chat-emoji-picker-container');
        const picker = document.querySelector('emoji-picker');

        if (imgInput) {
            imgInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imgPreviewImg.src = e.target.result;
                        imgPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            imgRemove.addEventListener('click', () => {
                imgInput.value = '';
                imgPreview.style.display = 'none';
                imgPreviewImg.src = '';
            });
        }

        if (emojiBtn && picker) {
            if (!window.emojiPickerLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = 'https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js';
                document.head.appendChild(script);
                window.emojiPickerLoaded = true;
            }
            
            emojiBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'none' ? 'block' : 'none';
            });

            picker.addEventListener('emoji-click', event => {
                input.value += event.detail.unicode;
                input.focus();
            });

            document.addEventListener('click', (e) => {
                if (emojiPickerContainer.style.display === 'block' && !emojiPickerContainer.contains(e.target) && e.target !== emojiBtn && !emojiBtn.contains(e.target)) {
                    emojiPickerContainer.style.display = 'none';
                }
            });
        }
    }
})();
</script>
@endif
