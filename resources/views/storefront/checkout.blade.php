@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --surface: #ffffff;
        --surface2: #f8fafc;
        --border: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --radius-lg: 16px;
        --radius-md: 10px;
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.05);
    }
    .checkout-container { max-width: 1200px; margin: 3rem auto; padding: 0 2.5rem; }
    .checkout-title { font-size: 2.2rem; font-weight: 800; margin-bottom: 2.5rem; letter-spacing: -0.5px; color: var(--text-main); }
    .checkout-grid { display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 2.5rem; }

    .checkout-card { background: var(--surface); padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid rgba(226, 232, 240, 0.6); }
    .checkout-card-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--surface2); display: flex; align-items: center; gap: 10px; color: var(--text-main); }
    .checkout-card-title i { color: var(--primary); }

    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.95rem; }
    .form-control { width: 100%; padding: 0.85rem 1rem; border: 1.5px solid var(--border); border-radius: var(--radius-md); font-family: inherit; font-size: 1rem; transition: all 0.2s; background: var(--surface); color: var(--text-main); box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    select.form-control { cursor: pointer; }
    select.form-control:disabled { background: #f1f5f9; cursor: not-allowed; color: var(--text-muted); }

    /* Province search wrapper (same as ward) */
    .province-search-wrap { position: relative; }
    .province-search-input { width: 100%; padding: 0.85rem 1rem; border: 1.5px solid var(--border); border-radius: var(--radius-md); font-family: inherit; font-size: 1rem; transition: all 0.2s; background: var(--surface); color: var(--text-main); box-sizing: border-box; }
    .province-search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,0.1); }
    .province-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--surface); border: 1.5px solid var(--primary); border-radius: var(--radius-md); max-height: 240px; overflow-y: auto; z-index: 1000; box-shadow: 0 8px 24px rgba(0,0,0,0.12); display: none; }
    .province-dropdown.open { display: block; }
    .province-option { padding: 0.75rem 1rem; cursor: pointer; font-size: 0.95rem; color: var(--text-main); transition: background .15s; border-bottom: 1px solid var(--surface2); }
    .province-option:last-child { border-bottom: none; }
    .province-option:hover, .province-option.active { background: #eef2ff; color: var(--primary); font-weight: 600; }
    .province-option.no-result { color: var(--text-muted); cursor: default; font-style: italic; }

    /* Ward search wrapper */
    .ward-search-wrap { position: relative; }
    .ward-search-input { width: 100%; padding: 0.85rem 1rem; border: 1.5px solid var(--border); border-radius: var(--radius-md); font-family: inherit; font-size: 1rem; transition: all 0.2s; background: var(--surface); color: var(--text-main); box-sizing: border-box; }
    .ward-search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,0.1); }
    .ward-search-input:disabled { background: #f1f5f9; cursor: not-allowed; color: var(--text-muted); }
    .ward-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--surface); border: 1.5px solid var(--primary); border-radius: var(--radius-md); max-height: 240px; overflow-y: auto; z-index: 999; box-shadow: 0 8px 24px rgba(0,0,0,0.12); display: none; }
    .ward-dropdown.open { display: block; }
    .ward-option { padding: 0.75rem 1rem; cursor: pointer; font-size: 0.95rem; color: var(--text-main); transition: background .15s; border-bottom: 1px solid var(--surface2); }
    .ward-option:last-child { border-bottom: none; }
    .ward-option:hover, .ward-option.active { background: #eef2ff; color: var(--primary); font-weight: 600; }
    .ward-option.no-result { color: var(--text-muted); cursor: default; font-style: italic; }

    .payment-option { display: flex; align-items: center; gap: 15px; padding: 1.2rem; border: 1.5px solid var(--border); border-radius: var(--radius-md); margin-bottom: 1rem; cursor: pointer; transition: all 0.2s; background: var(--surface); }
    .payment-option:hover { border-color: #cbd5e1; background: var(--surface2); }
    .payment-option.selected { border-color: var(--primary); background: rgba(79, 70, 229, 0.03); }
    .payment-option input[type="radio"] { width: 20px; height: 20px; accent-color: var(--primary); cursor: pointer; }
    .payment-icon { width: 40px; height: 40px; background: var(--surface2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.2rem; }
    .payment-text { font-weight: 600; color: var(--text-main); }

    .order-item { display: flex; gap: 15px; align-items: center; padding: 1rem 0; border-bottom: 1px dashed var(--border); }
    .order-item:last-child { border-bottom: none; }
    .order-img { width: 65px; height: 65px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--surface2); }
    .order-info { flex-grow: 1; }
    .order-name { font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 4px; }
    .order-qty { color: var(--text-muted); font-size: 0.85rem; }
    .order-price { font-weight: 800; color: var(--text-main); }

    .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: var(--text-muted); }
    .summary-total { display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--surface2); font-size: 1.3rem; color: #ef4444; font-weight: 900; }

    .btn-checkout { width: 100%; margin-top: 2rem; background: linear-gradient(135deg, var(--primary), #3b82f6); color: white; padding: 1.2rem; border-radius: var(--radius-md); font-weight: 700; font-size: 1.1rem; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4); display: flex; align-items: center; justify-content: center; gap: 10px; }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5); }
    .btn-checkout:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .fee-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .fee-badge.loading { background: #fef9c3; color: #92400e; }
    .fee-badge.success { background: #dcfce7; color: #166534; }
    .fee-badge.hint { background: #f0f9ff; color: #0369a1; }
    .fee-badge.error { background: #fee2e2; color: #991b1b; }

    .admin-note { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.4rem; display: flex; align-items: center; gap: 4px; }

    @media (max-width: 900px) {
        .checkout-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="checkout-container">
    <h2 class="checkout-title">Hoàn tất đơn hàng</h2>

    @if ($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid #fecaca;">
            <ul style="margin: 0; padding-left: 20px; font-weight: 500;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-grid">
        <!-- CỘT TRÁI: FORM -->
        <div>
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" class="checkout-card">
                @csrf

                {{-- Hidden fields lưu dữ liệu địa chính --}}
                <input type="hidden" name="province_id"    id="province_id_input">
                <input type="hidden" name="province_name"  id="province_name_input">
                <input type="hidden" name="district_id"    id="district_id_input">
                <input type="hidden" name="district_name"  id="district_name_input">
                <input type="hidden" name="ward_code"      id="ward_code_input">
                <input type="hidden" name="ward_name"      id="ward_name_input">
                <input type="hidden" name="shipping_fee"   id="shipping_fee_input" value="0">
                <input type="hidden" name="voucher_code"   id="voucher_code_hidden">
                <input type="hidden" name="discount_amount" id="discount_amount_hidden" value="0">

                <h3 class="checkout-card-title"><i class="fa-solid fa-location-dot"></i> Thông tin nhận hàng</h3>

                @if(Auth::check())
                    <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border);">
                        <label class="form-label" style="margin-bottom: 1rem;"><i class="fa-solid fa-address-book" style="color:var(--primary); margin-right:5px;"></i> Chọn từ Sổ địa chỉ</label>
                        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                            @php
                                $defaultAddrId = optional(Auth::user()->addresses->firstWhere('is_default', true))->id;
                                $selectedAddrId = old('saved_address_id', $defaultAddrId);
                            @endphp
                            @foreach(Auth::user()->addresses as $addr)
                                <label class="saved-address-option" style="display: block; cursor: pointer; padding: 1rem; border: 1.5px solid var(--border); border-radius: 8px; background: white; position: relative;">
                                    <input type="radio" name="saved_address_id" value="{{ $addr->id }}" onchange="fillAddress(this)" style="position: absolute; top: 1rem; right: 1rem; accent-color: var(--primary);" {{ $selectedAddrId == $addr->id ? 'checked' : '' }}>
                                    <div style="font-weight: 600; margin-bottom: 4px; padding-right: 20px;">{{ $addr->name }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">{{ $addr->phone }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4;">{{ $addr->address }}<br>{{ $addr->district }}, {{ $addr->province }}</div>
                                    @if($addr->is_default)
                                        <span style="display: inline-block; margin-top: 8px; background: var(--primary); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold;">Mặc định</span>
                                    @endif
                                </label>
                            @endforeach
                            <div class="saved-address-option" onclick="showAddressModal()" style="display: block; cursor: pointer; padding: 1rem; border: 1.5px dashed var(--primary); border-radius: 8px; background: #eff6ff; position: relative; transition: all 0.2s;">
                                <div style="font-weight: 600; display: flex; height: 100%; align-items: center; justify-content: center; gap: 8px; color: var(--primary);">
                                    <i class="fa-solid fa-plus"></i> Nhập địa chỉ mới
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Họ và tên người nhận</label>
                    <input type="text" id="checkout-name" name="recipient_name" value="{{ old('recipient_name', Auth::user()->name) }}" required class="form-control" placeholder="Ví dụ: Nguyễn Văn A">
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại liên hệ</label>
                    <input type="text" id="checkout-phone" name="recipient_phone" value="{{ old('recipient_phone', Auth::user()->phone ?? '') }}" required class="form-control" placeholder="Ví dụ: 0987654321">
                </div>

                <h3 class="checkout-card-title" style="margin-top:1.5rem;"><i class="fa-solid fa-map-location-dot"></i> Địa chỉ giao hàng</h3>

                {{-- TỈNH / THÀNH PHỐ (searchable) --}}
                <div class="form-group">
                    <label class="form-label">Tỉnh / Thành phố</label>
                    <div class="province-search-wrap">
                        <input type="text" id="province_search" class="province-search-input" placeholder="Gõ tên Tỉnh / Thành phố..." autocomplete="off">
                        <div id="province_dropdown" class="province-dropdown"><div class="province-option no-result">Đang tải dữ liệu...</div></div>
                    </div>
                </div>

                {{-- PHƯỜNG / XÃ (searchable, không có Quận/Huyện theo hành chính mới) --}}
                <div class="form-group">
                    <label class="form-label">Phường / Xã</label>
                    <div class="ward-search-wrap">
                        <input type="text" id="ward_search" class="ward-search-input" placeholder="-- Chọn Tỉnh trước --" disabled autocomplete="off">
                        <div id="ward_dropdown" class="ward-dropdown"></div>
                    </div>
                    <div class="admin-note">
                        <i class="fa-solid fa-circle-info"></i>
                        Theo đơn vị hành chính Việt Nam mới (hiệu lực 1/7/2025), không còn cấp Quận / Huyện.
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 2.5rem;">
                    <label class="form-label">Số nhà, tên đường</label>
                    <input type="text" id="checkout-address-detail" name="address_detail" value="{{ old('address_detail') }}" required class="form-control" placeholder="Ví dụ: 123 Nguyễn Văn Cừ">
                </div>

                <h3 class="checkout-card-title"><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h3>

                <label class="payment-option {{ old('payment_method', 'cod') == 'cod' ? 'selected' : '' }}" onclick="selectPayment(this)">
                    <input type="radio" name="payment_method" value="cod" required {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                    <div class="payment-icon"><i class="fa-solid fa-truck"></i></div>
                    <span class="payment-text">Thanh toán khi nhận hàng (COD)</span>
                </label>

                <label class="payment-option {{ old('payment_method') == 'momo' ? 'selected' : '' }}" onclick="selectPayment(this)">
                    <input type="radio" name="payment_method" value="momo" required {{ old('payment_method') == 'momo' ? 'checked' : '' }}>
                    <div class="payment-icon" style="background: #fce7f3; display: flex; align-items: center; justify-content: center;">
                        <img src="https://developers.momo.vn/v3/assets/images/icon-52bd5808cecdb1970e1aeec3c31a3ee1.png" alt="MoMo" style="width: 24px; height: 24px; object-fit: contain; border-radius: 4px;">
                    </div>
                    <div>
                        <span class="payment-text">Ví MoMo</span>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Thanh toán nhanh qua ví điện tử MoMo</div>
                    </div>
                </label>
            </form>
        </div>

        <!-- CỘT PHẢI: TÓM TẮT -->
        <div>
            <div class="checkout-card" style="position: sticky; top: 100px;">
                <h3 class="checkout-card-title"><i class="fa-solid fa-bag-shopping"></i> Đơn hàng của bạn</h3>

                <div style="margin-bottom: 1.5rem;">
                    @php $subtotal = 0; @endphp
                    @foreach($carts as $cart)
                        @php $subtotal += $cart->product->price * $cart->quantity; @endphp
                        <div class="order-item">
                            <img src="{{ $cart->product->image_url }}" class="order-img" alt="Product">
                            <div class="order-info">
                                <div class="order-name">{{ \Illuminate\Support\Str::limit($cart->product->name, 35) }}</div>
                                <div class="order-qty">Số lượng: {{ $cart->quantity }}</div>
                            </div>
                            <div class="order-price">{{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }} ₫</div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px dashed var(--border);">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="voucher_code_input" class="form-control" placeholder="Mã giảm giá" style="text-transform: uppercase;">
                        <button type="button" class="btn btn-primary" onclick="applyVoucher()" style="white-space: nowrap;">Áp dụng</button>
                    </div>
                    <div id="voucher-msg" style="margin-top: 8px; font-size: 0.85rem; font-weight: 500;"></div>
                </div>

                <div>
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span style="font-weight:600;color:var(--text-main);">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span id="shipping-fee-display" style="font-weight:600;">
                            <span class="fee-badge hint"><i class="fa-solid fa-truck"></i> Chọn địa chỉ để tính phí</span>
                        </span>
                    </div>
                    <div class="summary-row" id="discount-row" style="display: none; color: #16a34a;">
                        <span>Giảm giá</span>
                        <span id="discount-display" style="font-weight:600;">0 ₫</span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng thanh toán:</span>
                        <span id="total-display">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                    </div>
                </div>

                <button type="button" id="btn-place-order" onclick="submitOrder()" class="btn-checkout" disabled>
                    <i class="fa-solid fa-lock"></i> Đặt Hàng An Toàn
                </button>
                <div style="text-align:center;margin-top:1rem;font-size:0.8rem;color:var(--text-muted);">
                    <i class="fa-solid fa-shield-halved"></i> Thông tin của bạn được bảo mật tuyệt đối
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Địa chỉ -->
<div id="address-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 id="address-modal-title" style="margin-bottom: 1.5rem;">Thêm địa chỉ mới</h3>
        <form id="address-form" method="POST" action="{{ route('profile.addresses.store') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
            
            <div class="form-group">
                <label>Họ tên người nhận <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" id="addr-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại <span style="color:#ef4444">*</span></label>
                <input type="text" name="phone" id="addr-phone" class="form-control" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Tỉnh / Thành phố <span style="color:#ef4444">*</span></label>
                    <select name="province" id="addr-province" class="form-control" required onchange="loadModalWards(this.options[this.selectedIndex])">
                        <option value="">Chọn Tỉnh/Thành</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phường / Xã <span style="color:#ef4444">*</span></label>
                    <select name="district" id="addr-district" class="form-control" required>
                        <option value="">Chọn Phường/Xã</option>
                    </select>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">* Không còn cấp Quận/Huyện</div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Địa chỉ cụ thể (Số nhà, đường, phường/xã) <span style="color:#ef4444">*</span></label>
                <textarea name="address" id="addr-address" class="form-control" rows="2" required></textarea>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_default" id="addr-default" value="1" style="width: 16px; height: 16px;">
                <label for="addr-default" style="margin-bottom: 0; font-weight: 500;">Đặt làm địa chỉ mặc định</label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1; padding: 1rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: var(--text-main);" onclick="closeAddressModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; background: var(--primary); color: white;">Lưu địa chỉ</button>
            </div>
        </form>
    </div>
</div>

<script>
// ================================================================
//  CONFIG
// ================================================================
const SUBTOTAL = {{ $subtotal }};
const ROUTES = {
    provinces:       '{{ route("ghn.provinces") }}',
    wardsByProvince: '{{ route("ghn.wards-by-province") }}',
    calculateFee:    '{{ route("ghn.calculate-fee") }}',
    applyVoucher:    '{{ route("api.voucher.apply") }}',
    csrf:            '{{ csrf_token() }}',
};

// ================================================================
//  State
// ================================================================
let allProvinces  = [];   // toàn bộ tỉnh thành phố
let allWards      = [];   // toàn bộ ward của tỉnh đã chọn
let selectedWard  = null; // ward đang được chọn
let shippingFee   = 0;
let discountAmt   = 0;

// ================================================================
//  Helpers
// ================================================================
function formatVND(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
}

function updateTotal() {
    let total = SUBTOTAL + shippingFee - discountAmt;
    if (total < 0) total = 0;
    document.getElementById('total-display').textContent = formatVND(total);
}

function setShippingDisplay(html, fee = 0) {
    document.getElementById('shipping-fee-display').innerHTML = html;
    document.getElementById('shipping_fee_input').value = fee;
    shippingFee = fee;
    updateTotal();
}

function enableOrder(enable) {
    document.getElementById('btn-place-order').disabled = !enable;
}

function setHidden(id, val) {
    document.getElementById(id).value = val ?? '';
}

// ================================================================
//  Province dropdown: load on page load
// ================================================================
async function loadProvinces() {
    try {
        const resp = await fetch(ROUTES.provinces);
        const json = await resp.json();

        if (json.success && json.data.length > 0) {
            allProvinces = json.data.sort((a, b) =>
                a.ProvinceName.localeCompare(b.ProvinceName, 'vi')
            );
            renderProvinceDropdown('');
            
            // Auto-fill default address if selected (after provinces are loaded)
            const selectedAddress = document.querySelector('input[name="saved_address_id"]:checked');
            if (selectedAddress && selectedAddress.value !== 'new') {
                fillAddress(selectedAddress);
            }
        }
    } catch (e) {
        console.error('loadProvinces error', e);
    }
}

// ================================================================
//  Province searchable dropdown
// ================================================================
const provinceInput    = document.getElementById('province_search');
const provinceDropdown = document.getElementById('province_dropdown');

provinceInput.addEventListener('input', function () {
    renderProvinceDropdown(this.value.trim().toLowerCase());
    openProvinceDropdown();
});

provinceInput.addEventListener('focus', function () {
    renderProvinceDropdown(this.value.trim().toLowerCase());
    openProvinceDropdown();
});

function renderProvinceDropdown(q) {
    const filtered = q
        ? allProvinces.filter(p => p.ProvinceName.toLowerCase().includes(q))
        : allProvinces;

    provinceDropdown.innerHTML = '';

    if (filtered.length === 0) {
        provinceDropdown.innerHTML = '<div class="province-option no-result">Không tìm thấy kết quả</div>';
        return;
    }

    filtered.slice(0, 100).forEach(p => {
        const div = document.createElement('div');
        div.className   = 'province-option';
        div.textContent = p.ProvinceName;
        div.addEventListener('click', () => selectProvince(p));
        provinceDropdown.appendChild(div);
    });
}

function openProvinceDropdown()  { provinceDropdown.classList.add('open'); }
function closeProvinceDropdown() { provinceDropdown.classList.remove('open'); }

async function selectProvince(province) {
    const provinceId   = province.ProvinceID;
    const provinceName = province.ProvinceName;

    provinceInput.value = provinceName;
    closeProvinceDropdown();

    setHidden('province_id_input',   provinceId);
    setHidden('province_name_input', provinceName);
    setHidden('district_id_input',   '');
    setHidden('district_name_input', '');
    setHidden('ward_code_input',     '');
    setHidden('ward_name_input',     '');

    // Reset ward search
    selectedWard = null;
    allWards = [];
    closeWardDropdown();
    enableOrder(false);
    setShippingDisplay('<span class="fee-badge hint"><i class="fa-solid fa-truck"></i> Chọn địa chỉ để tính phí</span>');

    const wardInput = document.getElementById('ward_search');
    wardInput.value       = '';
    wardInput.placeholder = 'Đang tải danh sách Phường / Xã...';
    wardInput.disabled    = true;

    try {
        const resp = await fetch(ROUTES.wardsByProvince + '?province_id=' + provinceId);
        const json = await resp.json();

        if (json.success && json.data.length > 0) {
            allWards = json.data;
            wardInput.disabled    = false;
            wardInput.placeholder = 'Gõ tên Phường / Xã để tìm kiếm...';
        } else {
            wardInput.placeholder = 'Không có dữ liệu Phường / Xã';
        }
    } catch (e) {
        console.error('wardsByProvince error', e);
        wardInput.placeholder = 'Lỗi tải dữ liệu';
    }
}

// ================================================================
//  Ward searchable dropdown
// ================================================================
const wardInput    = document.getElementById('ward_search');
const wardDropdown = document.getElementById('ward_dropdown');

wardInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    renderWardDropdown(q);
    openWardDropdown();
});

wardInput.addEventListener('focus', function () {
    if (allWards.length > 0) {
        renderWardDropdown(this.value.trim().toLowerCase());
        openWardDropdown();
    }
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.ward-search-wrap'))     closeWardDropdown();
    if (!e.target.closest('.province-search-wrap')) closeProvinceDropdown();
});

function renderWardDropdown(q) {
    const filtered = q
        ? allWards.filter(w => w.WardName.toLowerCase().includes(q))
        : allWards;

    wardDropdown.innerHTML = '';

    if (filtered.length === 0) {
        wardDropdown.innerHTML = '<div class="ward-option no-result">Không tìm thấy kết quả</div>';
        return;
    }

    // Giới hạn hiển thị 100 kết quả để tránh lag
    filtered.slice(0, 100).forEach(w => {
        const div = document.createElement('div');
        div.className  = 'ward-option';
        div.textContent = w.WardName;
        div.addEventListener('click', () => selectWard(w));
        wardDropdown.appendChild(div);
    });

    if (filtered.length > 100) {
        const more = document.createElement('div');
        more.className   = 'ward-option no-result';
        more.textContent = `... và ${filtered.length - 100} kết quả khác. Gõ thêm để lọc.`;
        wardDropdown.appendChild(more);
    }
}

function openWardDropdown()  { wardDropdown.classList.add('open'); }
function closeWardDropdown() { wardDropdown.classList.remove('open'); }

async function selectWard(ward) {
    selectedWard = ward;
    wardInput.value = ward.WardName;
    closeWardDropdown();

    // Gán hidden fields
    setHidden('ward_code_input',     ward.WardCode);
    setHidden('ward_name_input',     ward.WardName);
    setHidden('district_id_input',   ward.DistrictID);
    setHidden('district_name_input', ward.DistrictName);

    enableOrder(false);
    setShippingDisplay('<span class="fee-badge loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tính phí vận chuyển...</span>');

    // Tính phí vận chuyển
    try {
        const resp = await fetch(ROUTES.calculateFee, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': ROUTES.csrf,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                to_district_id: parseInt(ward.DistrictID),
                to_ward_code:   ward.WardCode,
                weight:         500,
            }),
        });

        const json = await resp.json();

        if (json.success && json.data?.total !== undefined) {
            const fee = json.data.total;
            setShippingDisplay(
                `<span class="fee-badge success"><i class="fa-solid fa-check"></i> ${formatVND(fee)}</span>`,
                fee
            );
            enableOrder(true);
        } else {
            setShippingDisplay('<span class="fee-badge error"><i class="fa-solid fa-circle-exclamation"></i> Không tính được phí ship</span>');
        }
    } catch (e) {
        console.error('calculateFee error', e);
        setShippingDisplay('<span class="fee-badge error"><i class="fa-solid fa-circle-exclamation"></i> Lỗi kết nối</span>');
    }
}

// ================================================================
//  Submit
// ================================================================
function submitOrder() {
    if (!document.getElementById('ward_code_input').value) {
        alert('Vui lòng chọn Phường / Xã!');
        return;
    }
    if (!document.querySelector('input[name="payment_method"]:checked')) {
        alert('Vui lòng chọn phương thức thanh toán!');
        return;
    }
    document.getElementById('checkout-form').submit();
}

function selectPayment(elem) {
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    elem.classList.add('selected');
    elem.querySelector('input').checked = true;
}

// ================================================================
//  Voucher
// ================================================================
async function applyVoucher() {
    const code = document.getElementById('voucher_code_input').value.trim();
    const msgBox = document.getElementById('voucher-msg');
    
    if (!code) {
        msgBox.innerHTML = '<span style="color:var(--danger)">Vui lòng nhập mã giảm giá.</span>';
        return;
    }

    msgBox.innerHTML = '<span style="color:var(--text-muted)"><i class="fa-solid fa-spinner fa-spin"></i> Đang kiểm tra...</span>';

    try {
        const resp = await fetch(ROUTES.applyVoucher, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': ROUTES.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ code: code, subtotal: SUBTOTAL }),
        });

        const json = await resp.json();

        if (json.success) {
            discountAmt = json.data.discount_amount;
            document.getElementById('voucher_code_hidden').value = json.data.code;
            document.getElementById('discount_amount_hidden').value = discountAmt;

            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('discount-display').textContent = '-' + formatVND(discountAmt);
            
            msgBox.innerHTML = '<span style="color:var(--success)"><i class="fa-solid fa-check"></i> ' + json.message + '</span>';
            updateTotal();
        } else {
            discountAmt = 0;
            document.getElementById('voucher_code_hidden').value = '';
            document.getElementById('discount_amount_hidden').value = 0;
            
            document.getElementById('discount-row').style.display = 'none';
            msgBox.innerHTML = '<span style="color:var(--danger)"><i class="fa-solid fa-xmark"></i> ' + json.message + '</span>';
            updateTotal();
        }
    } catch (e) {
        msgBox.innerHTML = '<span style="color:var(--danger)">Lỗi kết nối. Vui lòng thử lại.</span>';
    }
}

// ================================================================
//  Init
// ================================================================
loadProvinces();

// ================================================================
//  Address Book
// ================================================================
const savedAddresses = @json(Auth::check() ? Auth::user()->addresses->keyBy('id') : []);

function fillAddress(input) {
    if (input.value === 'new') {
        clearAddress();
        return;
    }
    
    const addr = savedAddresses[input.value];
    if (!addr) return;
    
    document.getElementById('checkout-name').value = addr.name;
    document.getElementById('checkout-phone').value = addr.phone;
    document.getElementById('checkout-address-detail').value = addr.address;
    
    // Set hidden inputs and display
    document.getElementById('province_search').value = addr.province;
    document.getElementById('ward_search').value = addr.district;
    
    // Find Province ID from our preloaded allProvinces
    const provinceObj = allProvinces.find(p => p.ProvinceName === addr.province);
    if (!provinceObj) return;

    setHidden('province_id_input',   provinceObj.ProvinceID);
    setHidden('province_name_input', provinceObj.ProvinceName);

    setShippingDisplay('<span class="fee-badge loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tính phí vận chuyển...</span>');
    enableOrder(false);

    // Fetch wards to find the specific ward object
    fetch(ROUTES.wardsByProvince + '?province_id=' + provinceObj.ProvinceID)
        .then(res => res.json())
        .then(json => {
            if (json.success && json.data) {
                allWards = json.data; // update global
                const wardObj = json.data.find(w => w.WardName === addr.district);
                if (wardObj) {
                    setHidden('ward_code_input',     wardObj.WardCode);
                    setHidden('ward_name_input',     wardObj.WardName);
                    setHidden('district_id_input',   wardObj.DistrictID);
                    setHidden('district_name_input', wardObj.DistrictName);
                    
                    // Actually calculate fee
                    fetch(ROUTES.calculateFee, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': ROUTES.csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            to_district_id: wardObj.DistrictID,
                            to_ward_code: wardObj.WardCode,
                            weight: 500
                        })
                    })
                    .then(r => r.json())
                    .then(feeJson => {
                        if (feeJson.success) {
                            shippingFee = feeJson.data.total;
                            document.getElementById('shipping_fee_input').value = shippingFee;
                            setShippingDisplay(formatVND(shippingFee), shippingFee);
                            enableOrder(true);
                        } else {
                            shippingFee = 0;
                            document.getElementById('shipping_fee_input').value = 0;
                            setShippingDisplay('<span class="fee-badge error"><i class="fa-solid fa-circle-exclamation"></i> Lỗi: ' + feeJson.message + '</span>', 0);
                        }
                    });
                } else {
                    setShippingDisplay('<span class="fee-badge error"><i class="fa-solid fa-circle-exclamation"></i> Vui lòng chọn Phường/Xã hợp lệ.</span>', 0);
                }
            }
        });
}

function clearAddress() {
    document.getElementById('checkout-name').value = '{{ Auth::user()->name ?? "" }}';
    document.getElementById('checkout-phone').value = '{{ Auth::user()->phone ?? "" }}';
    document.getElementById('checkout-address-detail').value = '';
    
    document.getElementById('province_search').value = '';
    document.getElementById('ward_search').value = '';
    
    document.getElementById('province_id_input').value = '';
    document.getElementById('province_name_input').value = '';
    document.getElementById('district_id_input').value = '';
    document.getElementById('district_name_input').value = '';
    
    document.getElementById('shipping_fee_input').value = '0';
    setShippingDisplay('<span class="fee-badge hint"><i class="fa-solid fa-truck"></i> Chọn địa chỉ để tính phí</span>', 0);
    enableOrder(false);
    
    updateTotal();
}

function uncheckAddresses() {
    document.querySelectorAll('input[name="saved_address_id"]').forEach(el => el.checked = false);
}

// Modal functions
function showAddressModal() {
    document.getElementById('address-form').reset();
    document.getElementById('addr-district').innerHTML = '<option value="">Chọn Phường/Xã</option>';
    document.getElementById('address-modal').style.display = 'flex';
    
    // Populate modal province select
    const select = document.getElementById('addr-province');
    select.innerHTML = '<option value="">Chọn Tỉnh/Thành</option>';
    allProvinces.forEach(p => {
        const option = document.createElement('option');
        option.value = p.ProvinceName;
        option.dataset.id = p.ProvinceID;
        option.textContent = p.ProvinceName;
        select.appendChild(option);
    });
}

function closeAddressModal() {
    document.getElementById('address-modal').style.display = 'none';
}

async function loadModalWards(option) {
    let provinceId = option?.dataset?.id;
    const distSelect = document.getElementById('addr-district');
    distSelect.innerHTML = '<option value="">Đang tải Phường/Xã...</option>';
    
    if(!provinceId) return;

    try {
        const res = await fetch(ROUTES.wardsByProvince + "?province_id=" + provinceId);
        const data = await res.json();
        distSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if(data.data) {
            data.data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.WardName;
                opt.textContent = d.WardName;
                distSelect.appendChild(opt);
            });
        }
    } catch(e) {}
}

</script>
@endsection
