@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:2rem;flex-wrap:wrap;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:#f8fafc;color:#cbd5e1;cursor:not-allowed;font-size:0.9rem;">
            <i class="fa-solid fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:white;color:#64748b;transition:all 0.2s;font-size:0.9rem;text-decoration:none;" onmouseover="this.style.background='#4f46e5';this.style.color='white';this.style.borderColor='#4f46e5';" onmouseout="this.style.background='white';this.style.color='#64748b';this.style.borderColor='#e2e8f0';">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;color:#94a3b8;">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:#4f46e5;color:white;font-weight:700;font-size:0.88rem;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:white;color:#64748b;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;" onmouseover="this.style.background='#4f46e5';this.style.color='white';this.style.borderColor='#4f46e5';" onmouseout="this.style.background='white';this.style.color='#64748b';this.style.borderColor='#e2e8f0';">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:white;color:#64748b;transition:all 0.2s;font-size:0.9rem;text-decoration:none;" onmouseover="this.style.background='#4f46e5';this.style.color='white';this.style.borderColor='#4f46e5';" onmouseout="this.style.background='white';this.style.color='#64748b';this.style.borderColor='#e2e8f0';">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    @else
        <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:#f8fafc;color:#cbd5e1;cursor:not-allowed;font-size:0.9rem;">
            <i class="fa-solid fa-chevron-right"></i>
        </span>
    @endif
</nav>
@endif
