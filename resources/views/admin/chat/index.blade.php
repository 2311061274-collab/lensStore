@extends('layouts.admin')

@section('title', 'Live chat')
@section('subtitle', 'Trả lời khách hàng trực tiếp — hội thoại toàn màn hình')
@section('body-class', 'is-chat-page')

@section('content')
<div class="ls-ws" id="ls-ws">
    <aside class="ls-ws-side">
        <div class="ls-ws-side-head">
            <div>
                <strong>Hội thoại</strong>
                <small id="ls-ws-count">Đang tải...</small>
            </div>
        </div>
        <div class="ls-ws-search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" id="ls-ws-search" placeholder="Tìm tên hoặc email khách hàng..." autocomplete="off">
        </div>
        <div class="ls-ws-users" id="ls-ws-users">
            <div class="ls-ws-empty">Đang tải danh sách...</div>
        </div>
    </aside>

    <section class="ls-ws-main">
        <header class="ls-ws-head" id="ls-ws-title">
            <div class="ls-ws-head-av" id="ls-ws-head-av"><i class="fa-solid fa-comments"></i></div>
            <div class="ls-ws-head-text">
                <strong>Chọn khách hàng</strong>
                <span>Danh sách bên trái — tin nhắn dài sẽ tự xuống dòng, không làm vỡ layout</span>
            </div>
        </header>

        <div class="ls-ws-thread" id="ls-ws-thread">
            <div class="ls-ws-empty">
                <i class="fa-regular fa-comments"></i>
                Chọn một khách hàng để xem và trả lời tin nhắn
            </div>
        </div>

        <div id="ls-ws-img-preview" style="display: none; padding: 10px 18px; background: #f8fafc; border-top: 1px solid var(--line); position: relative;">
            <img id="ls-ws-img-preview-img" src="" alt="Preview" style="max-height: 80px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <button type="button" id="ls-ws-img-remove" style="position: absolute; top: 5px; left: 85px; background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: grid; place-items: center;"><i class="fa-solid fa-xmark" style="font-size: 12px;"></i></button>
        </div>
        <form class="ls-ws-foot" id="ls-ws-form" autocomplete="off" style="position: relative;">
            <label style="cursor: pointer; padding: 10px; color: #8b95a5; font-size: 20px; transition: color 0.2s;" title="Đính kèm ảnh" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='#8b95a5'">
                <i class="fa-regular fa-image"></i>
                <input type="file" id="ls-ws-img-input" accept="image/*" style="display: none;">
            </label>
            <button type="button" id="ls-ws-emoji-btn" style="background: none; border: none; cursor: pointer; padding: 10px; color: #8b95a5; font-size: 20px; transition: color 0.2s;" title="Biểu tượng cảm xúc" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='#8b95a5'">
                <i class="fa-regular fa-face-smile"></i>
            </button>
            <textarea id="ls-ws-input" rows="1" maxlength="2000" placeholder="Nhập câu trả lời..." disabled></textarea>
            <button type="submit" id="ls-ws-send" disabled>
                <i class="fa-solid fa-paper-plane"></i> Gửi
            </button>
            <div id="ls-ws-emoji-picker-container" style="display: none; position: absolute; bottom: 70px; left: 50px; z-index: 1000; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; background: white;">
                <emoji-picker></emoji-picker>
            </div>
        </form>
    </section>
</div>
@endsection

@push('styles')
<style>
    .ls-ws {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        width: 100%;
        flex: 1;
        min-width: 0;
        min-height: 0;
        height: 100%;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .ls-ws-side,
    .ls-ws-main {
        min-width: 0;
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .ls-ws-side {
        background: #14181f;
        color: #c5ccd6;
        border-right: 1px solid rgba(255,255,255,.06);
    }
    .ls-ws-side-head {
        flex: 0 0 auto;
        padding: 16px 16px 10px;
    }
    .ls-ws-side-head strong {
        display: block;
        color: #fff;
        font-size: 15px;
        letter-spacing: -.02em;
    }
    .ls-ws-side-head small {
        color: #7a8494;
        font-size: 12px;
    }
    .ls-ws-search-wrap {
        flex: 0 0 auto;
        margin: 0 12px 10px;
        position: relative;
    }
    .ls-ws-search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #7a8494;
        font-size: 12px;
        pointer-events: none;
    }
    .ls-ws-search-wrap input {
        width: 100%;
        max-width: 100%;
        padding: 9px 10px 9px 32px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.1);
        background: rgba(255,255,255,.06);
        color: #fff;
        font-size: 13px;
        font-family: inherit;
    }
    .ls-ws-search-wrap input:focus {
        outline: none;
        border-color: rgba(196,92,38,.55);
    }
    .ls-ws-users {
        flex: 1 1 auto;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 0 8px 10px;
    }
    .ls-ws-user {
        width: 100%;
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        padding: 10px;
        border: none;
        border-radius: 12px;
        background: transparent;
        color: inherit;
        text-align: left;
        cursor: pointer;
        font-family: inherit;
    }
    .ls-ws-user:hover { background: rgba(255,255,255,.06); }
    .ls-ws-user.active { background: rgba(196,92,38,.22); }
    .ls-ws-av {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: #2a3340;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 12px;
        color: #fff;
    }
    .ls-ws-user-meta { min-width: 0; }
    .ls-ws-user-meta strong,
    .ls-ws-user-meta p {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ls-ws-user-meta strong { color: #fff; font-size: 13px; }
    .ls-ws-user-meta p { margin: 3px 0 0; font-size: 11px; color: #8b95a5; }
    .ls-ws-dot {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #c45c26;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .ls-ws-dot.show { display: inline-flex; }
    .ls-ws-main { background: #f4f6f9; }
    .ls-ws-head {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #fff;
        border-bottom: 1px solid var(--line);
        min-width: 0;
    }
    .ls-ws-head-av {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--accent-soft);
        color: var(--accent);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-weight: 800;
    }
    .ls-ws-head-text { min-width: 0; }
    .ls-ws-head-text strong,
    .ls-ws-head-text span {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ls-ws-head-text strong { font-size: 15px; }
    .ls-ws-head-text span { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .ls-ws-thread {
        flex: 1 1 auto;
        min-height: 0;
        min-width: 0;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 18px;
    }
    .ls-ws-empty {
        text-align: center;
        color: var(--muted);
        padding: 64px 20px;
        overflow-wrap: anywhere;
    }
    .ls-ws-empty i { display: block; font-size: 28px; margin-bottom: 10px; opacity: .5; }
    .ls-ws-msg {
        display: flex;
        margin: 0 0 12px;
        max-width: min(72%, 680px);
        min-width: 0;
    }
    .ls-ws-msg.mine { margin-left: auto; justify-content: flex-end; }
    .ls-ws-bubble {
        max-width: 100%;
        min-width: 0;
        padding: 10px 12px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.5;
        background: #fff;
        border: 1px solid var(--line);
        overflow-wrap: anywhere;
        word-break: break-word;
        white-space: pre-wrap;
    }
    .ls-ws-msg.mine .ls-ws-bubble {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
        border-bottom-right-radius: 6px;
    }
    .ls-ws-msg.theirs .ls-ws-bubble { border-bottom-left-radius: 6px; }
    .ls-ws-meta {
        margin-top: 6px;
        font-size: 11px;
        opacity: .72;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ls-ws-prod {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        color: inherit;
        overflow-wrap: anywhere;
    }
    .ls-ws-foot {
        flex: 0 0 auto;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        padding: 12px 16px 14px;
        background: #fff;
        border-top: 1px solid var(--line);
        min-width: 0;
    }
    .ls-ws-foot textarea {
        flex: 1 1 auto;
        min-width: 0;
        max-height: 120px;
        resize: none;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 11px 12px;
        border: 1px solid var(--line);
        border-radius: 12px;
        font: inherit;
        font-size: 14px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }
    .ls-ws-foot textarea:focus {
        outline: none;
        border-color: #e0a07a;
        box-shadow: 0 0 0 3px rgba(196,92,38,.12);
    }
    .ls-ws-foot button {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        border-radius: 12px;
        background: var(--accent);
        color: #fff;
        padding: 11px 16px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
    }
    .ls-ws-foot button:disabled { opacity: .45; cursor: not-allowed; }

    @media (max-width: 980px) {
        .ls-ws { grid-template-columns: 250px minmax(0, 1fr); }
        .ls-ws-msg { max-width: 86%; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const usersBox = document.getElementById('ls-ws-users');
    const thread = document.getElementById('ls-ws-thread');
    const title = document.getElementById('ls-ws-title');
    const input = document.getElementById('ls-ws-input');
    const form = document.getElementById('ls-ws-form');
    const sendBtn = document.getElementById('ls-ws-send');
    const search = document.getElementById('ls-ws-search');
    const countEl = document.getElementById('ls-ws-count');
    const navBadge = document.getElementById('admin-chat-nav-badge');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const urls = {
        users: @json(route('admin.chat.users')),
        send: @json(route('admin.chat.send')),
        messages: @json(url('/admin/chat/messages'))
    };

    let currentUserId = null;
    let lastId = 0;
    let loaded = false;
    let sending = false;
    let usersCache = [];
    let usersFingerprint = '';
    let stickToBottom = true;
    let loadingUsers = false;
    let loadingMessages = false;

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, (s) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }
    function initials(name) {
        return String(name || '?').split(/\s+/).map((w) => w.charAt(0)).slice(0, 2).join('').toUpperCase();
    }
    function setNavBadge(n) {
        if (!navBadge) return;
        const count = Number(n) || 0;
        navBadge.textContent = count > 9 ? '9+' : String(count);
        navBadge.classList.toggle('show', count > 0);
    }
    function resizeInput() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }

    function renderUsers(users, force) {
        usersCache = users || [];
        const fp = usersCache.map((u) => [u.id, u.last_id, u.unread, u.last_message].join(':')).join('|') + '|' + (search.value || '');
        if (!force && fp === usersFingerprint) return;
        usersFingerprint = fp;

        const q = (search.value || '').trim().toLowerCase();
        const filtered = usersCache.filter((u) => !q || (u.name || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q));
        countEl.textContent = filtered.length ? (filtered.length + ' cuộc trò chuyện') : 'Chưa có hội thoại';

        if (!filtered.length) {
            usersBox.innerHTML = '<div class="ls-ws-empty" style="color:#8b95a5;padding:28px 12px">Chưa có hội thoại</div>';
            return;
        }

        usersBox.innerHTML = filtered.map((u) => `
            <button type="button" class="ls-ws-user ${currentUserId == u.id ? 'active' : ''}" data-id="${u.id}">
                <div class="ls-ws-av">${escapeHtml(initials(u.name))}</div>
                <div class="ls-ws-user-meta">
                    <strong>${escapeHtml(u.name)}</strong>
                    <p>${escapeHtml(u.last_message || 'Chưa có nội dung')}</p>
                </div>
                <span class="ls-ws-dot ${u.unread ? 'show' : ''}">${u.unread > 9 ? '9+' : (u.unread || '')}</span>
            </button>
        `).join('');
        usersBox.querySelectorAll('.ls-ws-user').forEach((el) => {
            el.addEventListener('click', () => selectUser(Number(el.dataset.id)));
        });
    }

    function bubbleHtml(msg) {
        const product = msg.product
            ? `<a class="ls-ws-prod" href="${escapeHtml(msg.product.url)}" target="_blank" rel="noopener"><i class="fa-solid fa-camera"></i> ${escapeHtml(msg.product.name)}</a>`
            : '';
        const image = msg.image_url ? `<a href="${escapeHtml(msg.image_url)}" target="_blank" style="display: block; margin-top: 6px;"><img src="${escapeHtml(msg.image_url)}" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); object-fit: cover;"></a>` : '';
        return `<div class="ls-ws-msg ${msg.is_mine ? 'mine' : 'theirs'}" data-id="${msg.id}">
            <div class="ls-ws-bubble">${product}${escapeHtml(msg.content)}${image}<div class="ls-ws-meta">${escapeHtml(msg.sender_name)} · ${escapeHtml(msg.created_at || '')}</div></div>
        </div>`;
    }

    function renderThread(messages) {
        if (!messages.length) {
            thread.innerHTML = '<div class="ls-ws-empty"><i class="fa-regular fa-comment"></i>Chưa có tin nhắn</div>';
            lastId = 0;
            return;
        }
        thread.innerHTML = messages.map(bubbleHtml).join('');
        lastId = messages[messages.length - 1].id;
        if (stickToBottom) thread.scrollTop = thread.scrollHeight;
    }

    function appendMessages(messages) {
        if (!messages.length) return;
        const empty = thread.querySelector('.ls-ws-empty');
        if (empty) empty.remove();
        messages.forEach((msg) => {
            if (thread.querySelector(`[data-id="${msg.id}"]`)) return;
            thread.insertAdjacentHTML('beforeend', bubbleHtml(msg));
            lastId = Math.max(lastId, msg.id);
        });
        if (stickToBottom) thread.scrollTop = thread.scrollHeight;
    }

    thread.addEventListener('scroll', () => {
        stickToBottom = thread.scrollHeight - thread.scrollTop - thread.clientHeight < 64;
    });

    async function loadUsers(force) {
        if (loadingUsers) return;
        loadingUsers = true;
        try {
            const res = await fetch(urls.users, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            renderUsers(data.users || [], force);
            setNavBadge(data.unread || 0);
        } catch (e) {
        } finally {
            loadingUsers = false;
        }
    }

    async function loadMessages(full) {
        if (!currentUserId || loadingMessages) return;
        loadingMessages = true;
        try {
            const qs = (!full && lastId) ? `?since_id=${lastId}` : '';
            const res = await fetch(`${urls.messages}/${currentUserId}${qs}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (full || !loaded) {
                renderThread(data.messages || []);
                loaded = true;
            } else {
                appendMessages(data.messages || []);
            }
        } catch (e) {
        } finally {
            loadingMessages = false;
        }
    }

    async function selectUser(id) {
        currentUserId = id;
        lastId = 0;
        loaded = false;
        stickToBottom = true;
        input.disabled = false;
        sendBtn.disabled = false;
        const user = usersCache.find((u) => u.id == id);
        document.getElementById('ls-ws-head-av').textContent = initials(user?.name || '?');
        title.querySelector('.ls-ws-head-text').innerHTML =
            `<strong>${escapeHtml(user?.name || 'Khách hàng')}</strong><span>${escapeHtml(user?.email || '')}</span>`;
        usersBox.querySelectorAll('.ls-ws-user').forEach((el) => el.classList.toggle('active', Number(el.dataset.id) === id));
        thread.innerHTML = '<div class="ls-ws-empty">Đang tải tin nhắn...</div>';
        input.focus();
        await loadMessages(true);
        usersFingerprint = '';
        await loadUsers(true);
    }

    async function sendMessage() {
        const text = (input.value || '').trim();
        const imgInputElem = document.getElementById('ls-ws-img-input');
        const file = imgInputElem ? imgInputElem.files[0] : null;
        if ((!text && !file) || !currentUserId || sending) return;

        sending = true;
        sendBtn.disabled = true;
        try {
            const formData = new FormData();
            if (text) formData.append('message', text);
            formData.append('user_id', currentUserId);
            if (file) formData.append('image', file);

            const res = await fetch(urls.send, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: formData
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                alert(data.error || data.message || 'Không gửi được tin nhắn.');
                return;
            }
            input.value = '';
            if (imgInputElem) imgInputElem.value = '';
            const preview = document.getElementById('ls-ws-img-preview');
            if (preview) preview.style.display = 'none';

            resizeInput();
            stickToBottom = true;
            appendMessages([data]);
        } catch (e) {
            alert('Không gửi được tin nhắn.');
        } finally {
            sending = false;
            sendBtn.disabled = false;
            input.focus();
            const emojiPickerContainer = document.getElementById('ls-ws-emoji-picker-container');
            if (emojiPickerContainer) emojiPickerContainer.style.display = 'none';
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage();
    });
    input.addEventListener('input', resizeInput);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    search.addEventListener('input', () => renderUsers(usersCache, true));

    loadUsers(true);
    setInterval(async () => {
        await loadMessages(false);
        await loadUsers(false);
    }, 2500);

    // Setup image preview & emojis
    const imgInput = document.getElementById('ls-ws-img-input');
    const imgPreview = document.getElementById('ls-ws-img-preview');
    const imgPreviewImg = document.getElementById('ls-ws-img-preview-img');
    const imgRemove = document.getElementById('ls-ws-img-remove');
    const emojiBtn = document.getElementById('ls-ws-emoji-btn');
    const emojiPickerContainer = document.getElementById('ls-ws-emoji-picker-container');
    const picker = emojiPickerContainer ? emojiPickerContainer.querySelector('emoji-picker') : null;

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
            if (input.disabled) return;
            emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'none' ? 'block' : 'none';
        });

        picker.addEventListener('emoji-click', event => {
            if (input.disabled) return;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            input.value = input.value.substring(0, start) + event.detail.unicode + input.value.substring(end);
            input.selectionStart = input.selectionEnd = start + event.detail.unicode.length;
            resizeInput();
            input.focus();
        });

        document.addEventListener('click', (e) => {
            if (emojiPickerContainer && emojiPickerContainer.style.display === 'block' && !emojiPickerContainer.contains(e.target) && e.target !== emojiBtn && !emojiBtn.contains(e.target)) {
                emojiPickerContainer.style.display = 'none';
            }
        });
    }
})();
</script>
@endpush
