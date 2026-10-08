{{-- ===== SHARED STOREFRONT NAVBAR ===== --}}
<style>
:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --accent: #f59e0b;
    --dark: #0f172a;
    --surface: #ffffff;
    --surface2: #f8fafc;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --radius: 16px;
}
.sf-cart-menu::before, .sf-acc-menu::before {
    content: '';
    position: absolute;
    top: -15px;
    left: 0;
    width: 100%;
    height: 15px;
}
</style>

<nav class="navbar" style="background:rgba(255,255,255,0.97);backdrop-filter:blur(16px);padding:0 2.5rem;height:72px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 0 rgba(0,0,0,0.08);position:sticky;top:0;z-index:1000;">
    <div style="display:flex;align-items:center;gap:24px;">
        <a href="{{ route('storefront.index') }}" style="font-size:1.5rem;font-weight:800;color:var(--primary);text-decoration:none;display:flex;align-items:center;gap:10px;letter-spacing:-0.5px;">
            <div style="width:36px;height:36px;background:var(--primary);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;">
                <i class="fa-solid fa-camera-retro"></i>
            </div>
            LensStore
        </a>
        {{-- Navigation menu --}}
        <div style="display:flex;align-items:center;gap:4px;">
            <a href="{{ route('storefront.index') }}" style="text-decoration:none;color:{{ request()->routeIs('storefront.index') ? 'var(--primary)' : 'var(--text-muted)' }};font-weight:{{ request()->routeIs('storefront.index') ? '600' : '500' }};padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;background:{{ request()->routeIs('storefront.index') ? 'rgba(79,70,229,0.08)' : 'transparent' }};">Trang chủ</a>
            <a href="{{ route('storefront.products') }}" style="text-decoration:none;color:{{ request()->routeIs('storefront.products') ? 'var(--primary)' : 'var(--text-muted)' }};font-weight:{{ request()->routeIs('storefront.products') ? '600' : '500' }};padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;background:{{ request()->routeIs('storefront.products') ? 'rgba(79,70,229,0.08)' : 'transparent' }};">Sản phẩm</a>
            <a href="{{ route('storefront.news') }}" style="text-decoration:none;color:{{ request()->routeIs('storefront.news') ? 'var(--primary)' : 'var(--text-muted)' }};font-weight:{{ request()->routeIs('storefront.news') ? '600' : '500' }};padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;background:{{ request()->routeIs('storefront.news') ? 'rgba(79,70,229,0.08)' : 'transparent' }};">Tin tức</a>
            <a href="{{ route('storefront.about') }}" style="text-decoration:none;color:{{ request()->routeIs('storefront.about') ? 'var(--primary)' : 'var(--text-muted)' }};font-weight:{{ request()->routeIs('storefront.about') ? '600' : '500' }};padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;background:{{ request()->routeIs('storefront.about') ? 'rgba(79,70,229,0.08)' : 'transparent' }};">Giới thiệu</a>
            <a href="{{ route('storefront.support') }}" style="text-decoration:none;color:{{ request()->routeIs('storefront.support') ? 'var(--primary)' : 'var(--text-muted)' }};font-weight:{{ request()->routeIs('storefront.support') ? '600' : '500' }};padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;background:{{ request()->routeIs('storefront.support') ? 'rgba(79,70,229,0.08)' : 'transparent' }};">Hỗ trợ</a>
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:0.75rem;">
        @auth
            @if(in_array(auth()->user()->role, ['admin','staff']))
                <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;color:var(--text-muted);font-weight:500;padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;"><i class="fa-solid fa-gauge"></i> Quản lý</a>
            @endif
            <div class="sf-cart-dropdown" style="position:relative;display:inline-block;">
                <a href="{{ route('cart.index') }}" style="position:relative;cursor:pointer;text-decoration:none;color:var(--text-muted);font-weight:500;padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
                    @php $sfCartItems = \App\Models\Cart::where('user_id', auth()->id())->get(); @endphp
                    @if($sfCartItems->sum('quantity') > 0)
                        <span style="position:absolute;top:-4px;right:-2px;background:#ef4444;color:white;border-radius:50%;padding:1px 5px;font-size:0.65rem;font-weight:700;line-height:1.4;">{{ $sfCartItems->sum('quantity') }}</span>
                    @endif
                </a>
                <div class="sf-cart-menu" style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:white;width:340px;box-shadow:0 20px 40px rgba(0,0,0,0.12);border-radius:16px;z-index:100;border:1px solid var(--border);overflow:hidden;">
                    <div style="padding:14px 18px;font-weight:700;border-bottom:1px solid var(--border);font-size:0.9rem;">🛒 Giỏ hàng của bạn</div>
                    @if($sfCartItems->isEmpty())
                        <div style="padding:2rem;text-align:center;color:var(--text-muted);">
                            <i class="fa-solid fa-cart-shopping" style="font-size:2rem;color:#cbd5e1;margin-bottom:0.75rem;display:block;"></i>
                            Giỏ hàng đang trống
                        </div>
                    @else
                        <div style="max-height:260px;overflow-y:auto;">
                            @foreach($sfCartItems->take(4) as $item)
                            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);">
                                <img src="{{ $item->product->image_url ?? '' }}" style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->product->name ?? '' }}</div>
                                    <div style="color:#ef4444;font-size:0.82rem;font-weight:700;">₫{{ number_format($item->product->price ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div style="color:var(--text-muted);font-size:0.82rem;white-space:nowrap;">x{{ $item->quantity }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div style="padding:12px 16px;display:flex;gap:8px;">
                            <a href="{{ route('cart.index') }}" style="flex:1;text-align:center;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:0.85rem;font-weight:600;color:var(--text-main);text-decoration:none;">Xem giỏ</a>
                            <a href="{{ route('checkout.index') }}" style="flex:1;text-align:center;padding:8px;background:var(--primary);border-radius:8px;font-size:0.85rem;font-weight:600;color:white;text-decoration:none;">Thanh toán</a>
                        </div>
                    @endif
                </div>
            </div>
            <style>.sf-cart-dropdown:hover .sf-cart-menu{display:block!important;}</style>

            <a href="{{ route('orders.index') }}" style="text-decoration:none;color:var(--text-muted);font-weight:500;padding:6px 12px;border-radius:8px;font-size:0.9rem;"><i class="fa-solid fa-clipboard-list"></i> Đơn hàng</a>

            <div class="sf-acc-dropdown" style="position:relative;display:inline-block;">
                <a style="cursor:pointer;text-decoration:none;color:var(--text-muted);font-weight:500;padding:6px 12px;border-radius:8px;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                    @else
                        <span style="width:30px;height:30px;background:var(--primary);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                    {{ auth()->user()->name }}
                    <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;"></i>
                </a>
                <div class="sf-acc-menu" style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:white;min-width:200px;box-shadow:0 20px 40px rgba(0,0,0,0.12);border-radius:16px;z-index:100;border:1px solid var(--border);overflow:hidden;padding:8px 0;">
                    <a href="{{ route('profile.edit') }}" style="color:var(--text-main);display:flex;align-items:center;gap:10px;padding:10px 16px;text-decoration:none;font-size:0.9rem;transition:background 0.15s;"><i class="fa-solid fa-user" style="width:16px;color:var(--primary);"></i> Hồ sơ cá nhân</a>
                    <a href="{{ route('orders.index') }}" style="color:var(--text-main);display:flex;align-items:center;gap:10px;padding:10px 16px;text-decoration:none;font-size:0.9rem;transition:background 0.15s;"><i class="fa-solid fa-box" style="width:16px;color:var(--primary);"></i> Đơn hàng của tôi</a>
                    <div style="height:1px;background:var(--border);margin:4px 0;"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="width:100%;text-align:left;padding:10px 16px;background:transparent;border:none;cursor:pointer;color:#ef4444;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:10px;"><i class="fa-solid fa-right-from-bracket" style="width:16px;"></i> Đăng xuất</button>
                    </form>
                </div>
            </div>
            <style>.sf-acc-dropdown:hover .sf-acc-menu{display:block!important;}.sf-acc-menu a:hover{background:var(--surface2)!important;}</style>
        @else
            <a href="{{ route('login') }}" style="text-decoration:none;color:var(--text-muted);font-weight:500;padding:6px 14px;border-radius:8px;font-size:0.9rem;"><i class="fa-solid fa-user"></i> Đăng nhập</a>
            <a href="{{ route('register') }}" style="text-decoration:none;background:var(--primary);color:white;font-weight:600;padding:8px 18px;border-radius:10px;font-size:0.9rem;transition:background 0.2s;"><i class="fa-solid fa-user-plus"></i> Đăng ký</a>
        @endauth
    </div>
</nav>
