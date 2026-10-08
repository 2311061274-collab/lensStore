@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 3rem auto; padding: 0 2.5rem;">
    <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 2rem;">Giỏ hàng của bạn</h2>

    @if(session('error'))
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    @if($carts->isEmpty())
        <div style="text-align: center; padding: 5rem; background: var(--surface); border-radius: 12px; border: 1px solid var(--border);">
            <i class="fa-solid fa-cart-shopping" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Giỏ hàng đang trống</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Hãy tìm thêm các sản phẩm tuyệt vời khác nhé!</p>
            <a href="{{ route('storefront.index') }}" class="btn-primary" style="display: inline-block; padding: 0.8rem 1.5rem; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Tiếp tục mua sắm</a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div>
                <div style="background: var(--surface); padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                    <input type="checkbox" id="selectAll" style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                    <label for="selectAll" style="font-weight: 600; cursor: pointer; font-size: 1.1rem; user-select: none;">Chọn tất cả</label>
                </div>

                @php $total = 0; @endphp
                @foreach($carts as $cart)
                    @if($cart->is_selected)
                        @php $total += $cart->product->price * $cart->quantity; @endphp
                    @endif
                    <div class="cart-item" data-id="{{ $cart->id }}" data-price="{{ $cart->product->price }}" data-qty="{{ $cart->quantity }}" style="display: flex; align-items: center; gap: 1.5rem; background: var(--surface); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem;">
                        <input type="checkbox" class="cart-checkbox" value="{{ $cart->id }}" {{ $cart->is_selected ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                        <img src="{{ $cart->product->image_url }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                        <div style="flex-grow: 1;">
                            <a href="{{ route('storefront.show', $cart->product_id) }}" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); text-decoration: none;">{{ $cart->product->name }}</a>
                            <div style="color: #ef4444; font-weight: 700; margin-top: 0.5rem;">{{ number_format($cart->product->price, 0, ',', '.') }} ₫</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <form action="{{ route('cart.update', $cart->id) }}" method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" style="width: 60px; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; text-align: center;">
                                <button type="submit" style="background: var(--surface2); border: 1px solid var(--border); padding: 0.5rem; border-radius: 6px; cursor: pointer;"><i class="fa-solid fa-rotate"></i></button>
                            </form>
                            <form action="{{ route('cart.remove', $cart->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 0.6rem; border-radius: 6px; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
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
                <a href="{{ route('checkout.index') }}" id="checkoutBtn" style="display: block; width: 100%; text-align: center; background: var(--primary); color: white; padding: 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; {{ $total == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">Tiến hành Thanh toán</a>
                
                <div id="saveStatus" style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 1rem; opacity: 0; transition: opacity 0.3s;">Đã lưu thay đổi</div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllBtn = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.cart-checkbox');
                const subtotalDisplay = document.getElementById('subtotalDisplay');
                const totalDisplay = document.getElementById('totalDisplay');
                const checkoutBtn = document.getElementById('checkoutBtn');
                const saveStatus = document.getElementById('saveStatus');
                
                function formatVND(amount) {
                    return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
                }

                function calculateTotal() {
                    let total = 0;
                    let allChecked = true;
                    let anyChecked = false;
                    let selections = [];

                    checkboxes.forEach(cb => {
                        const item = cb.closest('.cart-item');
                        const price = parseFloat(item.dataset.price);
                        const qty = parseInt(item.dataset.qty);
                        
                        if (cb.checked) {
                            total += (price * qty);
                            anyChecked = true;
                        } else {
                            allChecked = false;
                        }

                        selections.push({
                            id: cb.value,
                            is_selected: cb.checked
                        });
                    });

                    selectAllBtn.checked = allChecked && checkboxes.length > 0;
                    
                    subtotalDisplay.textContent = formatVND(total);
                    totalDisplay.textContent = formatVND(total);

                    if (!anyChecked) {
                        checkoutBtn.style.opacity = '0.5';
                        checkoutBtn.style.pointerEvents = 'none';
                    } else {
                        checkoutBtn.style.opacity = '1';
                        checkoutBtn.style.pointerEvents = 'auto';
                    }

                    // Save state to server
                    saveSelection(selections);
                }

                let saveTimeout;
                function saveSelection(selections) {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        fetch('{{ route("cart.update-selection") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ selections: selections })
                        }).then(res => res.json())
                          .then(data => {
                              if(data.success) {
                                  saveStatus.style.opacity = '1';
                                  setTimeout(() => saveStatus.style.opacity = '0', 2000);
                              }
                          }).catch(err => console.error(err));
                    }, 500); // Debounce
                }

                selectAllBtn.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    calculateTotal();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', calculateTotal);
                });
                
                // Initial check
                let allInitChecked = true;
                checkboxes.forEach(cb => {
                    if(!cb.checked) allInitChecked = false;
                });
                if(checkboxes.length > 0) selectAllBtn.checked = allInitChecked;
            });
        </script>
    @endif
</div>
@endsection
