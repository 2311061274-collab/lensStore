@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 3rem auto; padding: 0 2.5rem;">
    <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 2rem;">Giỏ hàng của bạn</h2>

    @if(session('error'))
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    {{-- KHỐI HIỂN THỊ KHI GIỎ HÀNG TRỐNG --}}
    <div id="cartEmptyState" style="{{ $carts->isEmpty() ? 'display: block;' : 'display: none;' }} text-align: center; padding: 5rem; background: var(--surface); border-radius: 12px; border: 1px solid var(--border);">
        <i class="fa-solid fa-cart-shopping" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; font-weight: 700;">Giỏ hàng đang trống</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Hãy tìm thêm các ống kính tuyệt vời khác để trang bị cho máy ảnh của bạn nhé!</p>
        <a href="{{ route('storefront.products') }}" class="btn-primary" style="display: inline-block; padding: 0.8rem 1.8rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
            <i class="fa-solid fa-compass" style="margin-right: 6px;"></i> Khám phá sản phẩm
        </a>
    </div>

    {{-- LƯỚI GIỎ HÀNG CHÍNH --}}
    <div id="cartMainGrid" style="{{ $carts->isEmpty() ? 'display: none;' : 'display: grid;' }} grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <div style="background: var(--surface); padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                <input type="checkbox" id="selectAll" checked style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                <label for="selectAll" style="font-weight: 600; cursor: pointer; font-size: 1.1rem; user-select: none;">Chọn tất cả</label>
            </div>

            <div id="cartItemsList">
                @php $total = 0; @endphp
                @foreach($carts as $cart)
                    @if($cart->is_selected)
                        @php $total += ($cart->product->price ?? 0) * $cart->quantity; @endphp
                    @endif
                    <div class="cart-item" data-id="{{ $cart->id }}" data-product-id="{{ $cart->product_id }}" data-price="{{ $cart->product->price ?? 0 }}" data-qty="{{ $cart->quantity }}" data-name="{{ $cart->product->name ?? '' }}" data-image="{{ $cart->product->image_url ?? '' }}" data-url="{{ route('storefront.show', $cart->product_id) }}" style="display: flex; align-items: center; gap: 1.5rem; background: var(--surface); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem;">
                        <input type="checkbox" class="cart-checkbox" value="{{ $cart->id }}" {{ $cart->is_selected ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                        <img src="{{ $cart->product->image_url ?? '' }}" class="cart-item-img" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                        <div style="flex-grow: 1; min-width: 0;">
                            <a href="{{ route('storefront.show', $cart->product_id) }}" class="cart-item-name" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); text-decoration: none; display: block; margin-bottom: 0.35rem;">{{ $cart->product->name ?? '' }}</a>
                            <div class="cart-item-price" style="color: #ef4444; font-weight: 700; font-size: 1.05rem;">{{ number_format($cart->product->price ?? 0, 0, ',', '.') }} ₫</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: var(--surface2);">
                                <button type="button" class="btn-qty-minus" style="width: 32px; height: 34px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i></button>
                                <input type="number" class="item-qty-input" value="{{ $cart->quantity }}" min="1" style="width: 50px; height: 34px; padding: 0; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); background: white; text-align: center; font-weight: 700; font-size: 0.95rem; color: var(--text-main); outline: none;">
                                <button type="button" class="btn-qty-plus" style="width: 32px; height: 34px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i></button>
                            </div>
                            <button type="button" class="btn-delete-cart-item" data-id="{{ $cart->id }}" style="background: #fee2e2; color: #ef4444; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Xóa khỏi giỏ hàng"><i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div style="background: var(--surface); padding: 2rem; border-radius: 12px; border: 1px solid var(--border); height: fit-content; position: sticky; top: 100px;">
            <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">Tổng đơn hàng</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem;">
                <span>Tạm tính:</span>
                <span id="subtotalDisplay" style="font-weight: 600;">{{ number_format($total, 0, ',', '.') }} ₫</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.2rem; color: #ef4444; font-weight: 800;">
                <span>Tổng tiền:</span>
                <span id="totalDisplay">{{ number_format($total, 0, ',', '.') }} ₫</span>
            </div>
            <a href="{{ route('checkout.index') }}" id="checkoutBtn" style="display: block; width: 100%; text-align: center; background: var(--primary); color: white; padding: 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all 0.2s; {{ $total == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">Tiến hành Thanh toán</a>
            
            <div id="saveStatus" style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 1rem; opacity: 0; transition: opacity 0.3s;">Đã lưu thay đổi</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emptyStateEl = document.getElementById('cartEmptyState');
    const mainGridEl = document.getElementById('cartMainGrid');
    const itemsListEl = document.getElementById('cartItemsList');
    const selectAllBtn = document.getElementById('selectAll');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const saveStatus = document.getElementById('saveStatus');
    const csrfToken = '{{ csrf_token() }}';

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    // 1. Đồng bộ huy hiệu giỏ hàng trên Navbar
    function updateNavbarBadge(count) {
        const badge = document.getElementById('cart-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        }
        document.querySelectorAll('.sf-cart-dropdown span, .sf-cart-badge').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? '' : 'none';
        });
    }

    // 2. Khởi tạo & Đọc danh sách sản phẩm (Client-Side Storage)
    let cartItems = [];
    try {
        const stored = localStorage.getItem('ls_cart_items');
        if (stored) {
            cartItems = JSON.parse(stored);
        }
    } catch(e) {
        cartItems = [];
    }

    // Nếu localStorage chưa có danh sách nhưng có sản phẩm trên server render
    const domItems = itemsListEl.querySelectorAll('.cart-item');
    if ((!cartItems || cartItems.length === 0) && domItems.length > 0) {
        cartItems = [];
        domItems.forEach(el => {
            cartItems.push({
                id: el.dataset.id,
                product_id: el.dataset.productId || el.dataset.id,
                name: el.dataset.name || el.querySelector('.cart-item-name')?.textContent?.trim() || '',
                price: parseFloat(el.dataset.price) || 0,
                image: el.dataset.image || el.querySelector('img')?.src || '',
                url: el.dataset.url || el.querySelector('a')?.href || '#',
                quantity: parseInt(el.dataset.qty) || 1,
                is_selected: el.querySelector('.cart-checkbox')?.checked ?? true
            });
        });
        localStorage.setItem('ls_cart_items', JSON.stringify(cartItems));
    }

    // Render lại danh sách từ cartItems nếu có
    if (cartItems && cartItems.length > 0) {
        renderCartDOM(cartItems);
    } else if (domItems.length === 0) {
        showEmptyState();
    }

    // Hàm render toàn bộ danh sách sản phẩm ra HTML
    function renderCartDOM(items) {
        if (!items || items.length === 0) {
            showEmptyState();
            return;
        }

        emptyStateEl.style.display = 'none';
        mainGridEl.style.display = 'grid';
        itemsListEl.innerHTML = '';

        items.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'cart-item';
            row.dataset.id = item.id;
            row.dataset.productId = item.product_id || item.id;
            row.dataset.price = item.price;
            row.dataset.qty = item.quantity;
            row.style.cssText = 'display: flex; align-items: center; gap: 1.5rem; background: var(--surface); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem;';

            const isChecked = item.is_selected !== false ? 'checked' : '';
            const priceFormatted = formatVND(item.price);

            row.innerHTML = `
                <input type="checkbox" class="cart-checkbox" value="${item.id}" ${isChecked} style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                <img src="${item.image || ''}" class="cart-item-img" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                <div style="flex-grow: 1; min-width: 0;">
                    <a href="${item.url || '#'}" class="cart-item-name" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); text-decoration: none; display: block; margin-bottom: 0.35rem;">${item.name || 'Ống kính máy ảnh'}</a>
                    <div class="cart-item-price" style="color: #ef4444; font-weight: 700; font-size: 1.05rem;">${priceFormatted}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: var(--surface2);">
                        <button type="button" class="btn-qty-minus" style="width: 32px; height: 34px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i></button>
                        <input type="number" class="item-qty-input" value="${item.quantity}" min="1" style="width: 50px; height: 34px; padding: 0; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); background: white; text-align: center; font-weight: 700; font-size: 0.95rem; color: var(--text-main); outline: none;">
                        <button type="button" class="btn-qty-plus" style="width: 32px; height: 34px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i></button>
                    </div>
                    <button type="button" class="btn-delete-cart-item" data-id="${item.id}" style="background: #fee2e2; color: #ef4444; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Xóa khỏi giỏ hàng"><i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i></button>
                </div>
            `;

            itemsListEl.appendChild(row);
        });

        attachItemEvents();
        recalc();
    }

    function showEmptyState() {
        emptyStateEl.style.display = 'block';
        mainGridEl.style.display = 'none';
        localStorage.setItem('ls_cart_items', '[]');
        localStorage.setItem('ls_cart_count', '0');
        updateNavbarBadge(0);
    }

    // 3. Tính toán lại tổng tiền & đồng bộ
    function recalc() {
        const rows = itemsListEl.querySelectorAll('.cart-item');
        let total = 0;
        let totalCount = 0;
        let allChecked = true;
        let anyChecked = false;
        let selections = [];

        cartItems = [];

        rows.forEach(row => {
            const id = row.dataset.id;
            const price = parseFloat(row.dataset.price) || 0;
            const qtyInput = row.querySelector('.item-qty-input');
            const qty = Math.max(1, parseInt(qtyInput ? qtyInput.value : row.dataset.qty) || 1);
            row.dataset.qty = qty;
            
            const checkbox = row.querySelector('.cart-checkbox');
            const checked = checkbox ? checkbox.checked : true;

            if (checked) {
                total += (price * qty);
                anyChecked = true;
            } else {
                allChecked = false;
            }

            totalCount += qty;

            const name = row.querySelector('.cart-item-name')?.textContent?.trim() || '';
            const image = row.querySelector('img')?.src || '';
            const url = row.querySelector('a')?.href || '#';

            cartItems.push({
                id: id,
                product_id: row.dataset.productId || id,
                name: name,
                price: price,
                image: image,
                url: url,
                quantity: qty,
                is_selected: checked
            });

            selections.push({ id: id, is_selected: checked });
        });

        if (rows.length === 0) {
            showEmptyState();
            return;
        }

        if (selectAllBtn) {
            selectAllBtn.checked = allChecked && rows.length > 0;
        }

        subtotalDisplay.textContent = formatVND(total);
        totalDisplay.textContent = formatVND(total);

        if (!anyChecked) {
            checkoutBtn.style.opacity = '0.5';
            checkoutBtn.style.pointerEvents = 'none';
        } else {
            checkoutBtn.style.opacity = '1';
            checkoutBtn.style.pointerEvents = 'auto';
        }

        // Lưu vào localStorage
        localStorage.setItem('ls_cart_items', JSON.stringify(cartItems));
        localStorage.setItem('ls_cart_count', totalCount.toString());
        updateNavbarBadge(totalCount);

        // Lưu lên server nếu backend khả dụng
        saveSelectionToServer(selections);
    }

    // Gán các sự kiện tương tác trên mỗi sản phẩm
    function attachItemEvents() {
        const rows = itemsListEl.querySelectorAll('.cart-item');
        rows.forEach(row => {
            const id = row.dataset.id;
            const checkbox = row.querySelector('.cart-checkbox');
            const qtyInput = row.querySelector('.item-qty-input');
            const btnMinus = row.querySelector('.btn-qty-minus');
            const btnPlus = row.querySelector('.btn-qty-plus');
            const btnDelete = row.querySelector('.btn-delete-cart-item');

            if (checkbox) {
                checkbox.onchange = () => recalc();
            }

            if (btnMinus) {
                btnMinus.onclick = () => {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val > 1) {
                        qtyInput.value = val - 1;
                        recalc();
                        syncQuantityToServer(id, val - 1);
                    }
                };
            }

            if (btnPlus) {
                btnPlus.onclick = () => {
                    let val = parseInt(qtyInput.value) || 1;
                    qtyInput.value = val + 1;
                    recalc();
                    syncQuantityToServer(id, val + 1);
                };
            }

            if (qtyInput) {
                qtyInput.onchange = () => {
                    let val = Math.max(1, parseInt(qtyInput.value) || 1);
                    qtyInput.value = val;
                    recalc();
                    syncQuantityToServer(id, val);
                };
            }

            if (btnDelete) {
                btnDelete.onclick = () => {
                    row.style.opacity = '0.3';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        row.remove();
                        recalc();
                        syncRemoveToServer(id);
                    }, 200);
                };
            }
        });
    }

    // Chọn tất cả
    if (selectAllBtn) {
        selectAllBtn.addEventListener('change', function() {
            const checkboxes = itemsListEl.querySelectorAll('.cart-checkbox');
            checkboxes.forEach(cb => { cb.checked = this.checked; });
            recalc();
        });
    }

    // Backend sync helpers (fail silently on static site)
    let saveTimeout;
    function saveSelectionToServer(selections) {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            fetch('{{ route("cart.update-selection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ selections: selections })
            }).then(res => res.json()).then(data => {
                if(data.success && saveStatus) {
                    saveStatus.style.opacity = '1';
                    setTimeout(() => saveStatus.style.opacity = '0', 1500);
                }
            }).catch(() => {});
        }, 500);
    }

    function syncQuantityToServer(id, quantity) {
        fetch('/cart/update/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ _method: 'PUT', quantity: quantity })
        }).catch(() => {});
    }

    function syncRemoveToServer(id) {
        fetch('/cart/remove/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        }).catch(() => {});
    }

    attachItemEvents();
    recalc();
});
</script>
@endsection
