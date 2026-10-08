{{--
  Partial chọn quyền theo nhóm.
  Expects: $permissions (Collection), $selected (array of permission names)
--}}
@php
    use App\Support\PermissionCatalog;
    $byName = $permissions->keyBy('name');
    $selected = $selected ?? [];
    $total = $permissions->count();
    $selectedCount = count(array_intersect($selected, $permissions->pluck('name')->all()));
@endphp

@push('styles')
<style>
    .role-shell { max-width: 920px; margin: 0 auto; }
    .role-identity {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.35rem 1.5rem;
        margin-bottom: 1.15rem;
    }
    .role-identity .form-group { margin-bottom: 0; }
    .perm-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .perm-toolbar h2 {
        font-family: var(--display);
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: -.02em;
    }
    .perm-toolbar .hint { font-size: .8rem; color: var(--muted); margin-top: .2rem; }
    .perm-count {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .75rem;
        border-radius: 999px;
        background: var(--accent-soft);
        color: var(--accent);
        font-size: .78rem;
        font-weight: 700;
    }
    .perm-toolbar-actions {
        display: flex;
        gap: .5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .perm-link-btn {
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: .78rem;
        font-weight: 600;
        padding: .4rem .75rem;
        border-radius: 9px;
        cursor: pointer;
        font-family: inherit;
        transition: .15s;
    }
    .perm-link-btn:hover { border-color: var(--line-strong); color: var(--ink); background: var(--surface-soft); }
    .perm-group {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: .9rem;
        overflow: hidden;
    }
    .perm-group.is-danger { border-color: #f0c4c0; }
    .perm-group-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .95rem 1.15rem;
        background: var(--surface-soft);
        border-bottom: 1px solid var(--line);
    }
    .perm-group.is-danger .perm-group-head { background: var(--danger-soft); border-bottom-color: #f0c4c0; }
    .perm-group-title {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }
    .perm-group-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        background: var(--accent-soft);
        color: var(--accent);
        flex-shrink: 0;
    }
    .perm-group.is-danger .perm-group-icon { background: #fff; color: var(--danger); }
    .perm-group-title strong {
        display: block;
        font-size: .95rem;
        font-weight: 700;
        color: var(--ink);
    }
    .perm-group-title span {
        display: block;
        font-size: .75rem;
        color: var(--muted);
        margin-top: .1rem;
    }
    .perm-group-toggle {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .78rem;
        font-weight: 600;
        color: var(--ink-soft);
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
        text-transform: none;
    }
    .perm-group-toggle input { accent-color: var(--accent); width: 15px; height: 15px; }
    .perm-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }
    @media (max-width: 720px) {
        .perm-list { grid-template-columns: 1fr; }
    }
    .perm-item {
        display: flex;
        align-items: flex-start;
        gap: .8rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--line);
        border-right: 1px solid var(--line);
        cursor: pointer;
        transition: background .15s;
        margin: 0;
        text-transform: none !important;
        font-weight: 400 !important;
        letter-spacing: normal !important;
        color: inherit;
    }
    .perm-item:nth-child(2n) { border-right: none; }
    .perm-item:nth-last-child(-n+2) { border-bottom: none; }
    @media (max-width: 720px) {
        .perm-item { border-right: none; }
        .perm-item:last-child { border-bottom: none; }
        .perm-item:nth-last-child(2) { border-bottom: 1px solid var(--line); }
    }
    .perm-item:hover { background: #fafbfd; }
    .perm-item.is-on { background: var(--accent-soft); }
    .perm-item input[type="checkbox"] {
        margin-top: .2rem;
        width: 16px;
        height: 16px;
        accent-color: var(--accent);
        flex-shrink: 0;
    }
    .perm-item-body { min-width: 0; }
    .perm-item-name {
        display: flex;
        align-items: center;
        gap: .45rem;
        font-weight: 600;
        font-size: .88rem;
        color: var(--ink);
    }
    .perm-item-name i { font-size: .78rem; color: var(--accent); }
    .perm-item-desc {
        font-size: .76rem;
        color: var(--muted);
        margin-top: .2rem;
        line-height: 1.4;
    }
    .role-actions {
        position: sticky;
        bottom: 1rem;
        margin-top: 1.25rem;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(10px);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow-hover);
        padding: .9rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        z-index: 20;
    }
    .role-actions .btn { min-width: 160px; justify-content: center; }
</style>
@endpush

<div class="perm-toolbar">
    <div>
        <h2>Quyền hạn</h2>
        <div class="hint">Chọn theo nhóm nghiệp vụ — gắn đúng quyền cần thiết cho chức vụ.</div>
    </div>
    <div class="perm-toolbar-actions">
        <span class="perm-count" id="permCountBadge">
            <i class="fa-solid fa-check-double"></i>
            <span id="permCountText">{{ $selectedCount }}/{{ $total }} đã chọn</span>
        </span>
        <button type="button" class="perm-link-btn" id="permSelectAll">Chọn tất cả</button>
        <button type="button" class="perm-link-btn" id="permClearAll">Bỏ chọn</button>
    </div>
</div>

@foreach(PermissionCatalog::groups() as $groupKey => $group)
    @php
        $groupPerms = collect($group['perms'])->map(fn ($k) => $byName->get($k))->filter();
    @endphp
    @continue($groupPerms->isEmpty())
    <div class="perm-group {{ ($group['tone'] ?? '') === 'danger' ? 'is-danger' : '' }}" data-group="{{ $groupKey }}">
        <div class="perm-group-head">
            <div class="perm-group-title">
                <div class="perm-group-icon"><i class="fa-solid {{ $group['icon'] }}"></i></div>
                <div>
                    <strong>{{ $group['title'] }}</strong>
                    <span>{{ $group['desc'] }} · {{ $groupPerms->count() }} quyền</span>
                </div>
            </div>
            <label class="perm-group-toggle">
                <input type="checkbox" class="group-toggle" data-group="{{ $groupKey }}">
                Chọn nhóm này
            </label>
        </div>
        <div class="perm-list">
            @foreach($groupPerms as $permission)
                @php
                    $info = PermissionCatalog::label($permission->name);
                    $on = in_array($permission->name, $selected, true);
                @endphp
                <label class="perm-item {{ $on ? 'is-on' : '' }}" data-group="{{ $groupKey }}">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           class="perm-cb"
                           data-group="{{ $groupKey }}"
                           @checked($on)>
                    <div class="perm-item-body">
                        <div class="perm-item-name">
                            <i class="fa-solid {{ $info['icon'] }}" style="color: {{ $info['color'] }}"></i>
                            {{ $info['label'] }}
                        </div>
                        @if($info['desc'])
                            <div class="perm-item-desc">{{ $info['desc'] }}</div>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>
    </div>
@endforeach

{{-- Quyền lạ (nếu có trong DB nhưng chưa map nhóm) --}}
@php
    $mapped = collect(PermissionCatalog::groups())->pluck('perms')->flatten()->all();
    $orphan = $permissions->reject(fn ($p) => in_array($p->name, $mapped, true));
@endphp
@if($orphan->isNotEmpty())
    <div class="perm-group" data-group="other">
        <div class="perm-group-head">
            <div class="perm-group-title">
                <div class="perm-group-icon"><i class="fa-solid fa-key"></i></div>
                <div>
                    <strong>Khác</strong>
                    <span>Quyền chưa phân nhóm</span>
                </div>
            </div>
            <label class="perm-group-toggle">
                <input type="checkbox" class="group-toggle" data-group="other">
                Chọn nhóm này
            </label>
        </div>
        <div class="perm-list">
            @foreach($orphan as $permission)
                @php $on = in_array($permission->name, $selected, true); @endphp
                <label class="perm-item {{ $on ? 'is-on' : '' }}" data-group="other">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="perm-cb" data-group="other" @checked($on)>
                    <div class="perm-item-body">
                        <div class="perm-item-name"><i class="fa-solid fa-key"></i> {{ $permission->name }}</div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
(function () {
    const total = {{ $total }};
    const cbs = () => [...document.querySelectorAll('.perm-cb')];
    const countText = document.getElementById('permCountText');

    function refreshItem(cb) {
        cb.closest('.perm-item')?.classList.toggle('is-on', cb.checked);
    }

    function refreshGroup(group) {
        const groupCbs = cbs().filter(c => c.dataset.group === group);
        const toggle = document.querySelector(`.group-toggle[data-group="${group}"]`);
        if (!toggle || !groupCbs.length) return;
        const checked = groupCbs.filter(c => c.checked).length;
        toggle.checked = checked === groupCbs.length;
        toggle.indeterminate = checked > 0 && checked < groupCbs.length;
    }

    function refreshAll() {
        const checked = cbs().filter(c => c.checked).length;
        if (countText) countText.textContent = `${checked}/${total} đã chọn`;
        [...new Set(cbs().map(c => c.dataset.group))].forEach(refreshGroup);
    }

    cbs().forEach(cb => {
        cb.addEventListener('change', () => {
            refreshItem(cb);
            refreshAll();
        });
    });

    document.querySelectorAll('.group-toggle').forEach(toggle => {
        toggle.addEventListener('change', () => {
            cbs().filter(c => c.dataset.group === toggle.dataset.group).forEach(cb => {
                cb.checked = toggle.checked;
                refreshItem(cb);
            });
            refreshAll();
        });
    });

    document.getElementById('permSelectAll')?.addEventListener('click', () => {
        cbs().forEach(cb => { cb.checked = true; refreshItem(cb); });
        refreshAll();
    });

    document.getElementById('permClearAll')?.addEventListener('click', () => {
        cbs().forEach(cb => { cb.checked = false; refreshItem(cb); });
        refreshAll();
    });

    refreshAll();
})();
</script>
@endpush
