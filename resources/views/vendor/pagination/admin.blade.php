@if ($paginator->hasPages())
<nav style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-top:1.25rem;">
    <div style="font-size:.8rem; color:var(--muted); font-weight:500;">
        Hiển thị <strong style="color:var(--ink)">{{ $paginator->firstItem() }}</strong>–<strong style="color:var(--ink)">{{ $paginator->lastItem() }}</strong>
        trong tổng số <strong style="color:var(--primary)">{{ $paginator->total() }}</strong> kết quả
    </div>
    <div style="display:flex; gap:.35rem; align-items:center; flex-wrap:wrap;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.42rem .8rem;border:1px solid var(--line);border-radius:9px;font-size:.8rem;font-weight:600;color:var(--muted);background:var(--surface-soft);cursor:not-allowed;opacity:.55;">
                <i class="fa-solid fa-chevron-left" style="font-size:.7rem;"></i> Trước
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.42rem .8rem;border:1px solid var(--line);border-radius:9px;font-size:.8rem;font-weight:600;color:var(--ink-soft);background:var(--surface);text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-soft)'">
                <i class="fa-solid fa-chevron-left" style="font-size:.7rem;"></i> Trước
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:.42rem .6rem;font-size:.8rem;color:var(--muted);">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 .5rem;border:1px solid var(--primary);border-radius:9px;font-size:.8rem;font-weight:700;color:#fff;background:var(--primary);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 .5rem;border:1px solid var(--line);border-radius:9px;font-size:.8rem;font-weight:600;color:var(--ink-soft);background:var(--surface);text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.background='rgba(79,70,229,.06)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-soft)';this.style.background='var(--surface)'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.42rem .8rem;border:1px solid var(--line);border-radius:9px;font-size:.8rem;font-weight:600;color:var(--ink-soft);background:var(--surface);text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--ink-soft)'">
                Tiếp <i class="fa-solid fa-chevron-right" style="font-size:.7rem;"></i>
            </a>
        @else
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.42rem .8rem;border:1px solid var(--line);border-radius:9px;font-size:.8rem;font-weight:600;color:var(--muted);background:var(--surface-soft);cursor:not-allowed;opacity:.55;">
                Tiếp <i class="fa-solid fa-chevron-right" style="font-size:.7rem;"></i>
            </span>
        @endif
    </div>
</nav>
@endif
