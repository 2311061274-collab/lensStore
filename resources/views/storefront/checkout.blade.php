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

                <div id="checkout-order-items" style="margin-bottom: 1.5rem;">
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
//  CONFIG & DUAL-MODE DETECTION
// ================================================================
let SUBTOTAL = {{ $subtotal }};
const isStaticSite = window.location.hostname.includes('github.io') || window.location.protocol === 'file:';
const ROUTES = {
    provinces:       '{{ route("ghn.provinces") }}',
    wardsByProvince: '{{ route("ghn.wards-by-province") }}',
    calculateFee:    '{{ route("ghn.calculate-fee") }}',
    applyVoucher:    '{{ route("api.voucher.apply") }}',
    csrf:            '{{ csrf_token() }}',
};

// ================================================================
//  63 PROVINCES OFFLINE FALLBACK DATASET
// ================================================================
const FALLBACK_PROVINCES = [
    { ProvinceID: 201, ProvinceName: "Hà Nội" },
    { ProvinceID: 202, ProvinceName: "Hồ Chí Minh" },
    { ProvinceID: 203, ProvinceName: "Đà Nẵng" },
    { ProvinceID: 204, ProvinceName: "Hải Phòng" },
    { ProvinceID: 205, ProvinceName: "Cần Thơ" },
    { ProvinceID: 211, ProvinceName: "Bắc Ninh" },
    { ProvinceID: 214, ProvinceName: "Bình Dương" },
    { ProvinceID: 222, ProvinceName: "Đồng Nai" },
    { ProvinceID: 208, ProvinceName: "Bắc Giang" },
    { ProvinceID: 249, ProvinceName: "Quảng Ninh" },
    { ProvinceID: 256, ProvinceName: "Thanh Hóa" },
    { ProvinceID: 241, ProvinceName: "Nghệ An" },
    { ProvinceID: 257, ProvinceName: "Thừa Thiên Huế" },
    { ProvinceID: 246, ProvinceName: "Quảng Nam" },
    { ProvinceID: 232, ProvinceName: "Khánh Hòa" },
    { ProvinceID: 236, ProvinceName: "Lâm Đồng" },
    { ProvinceID: 207, ProvinceName: "Bà Rịa - Vũng Tàu" },
    { ProvinceID: 206, ProvinceName: "An Giang" },
    { ProvinceID: 209, ProvinceName: "Bắc Kạn" },
    { ProvinceID: 210, ProvinceName: "Bạc Liêu" },
    { ProvinceID: 212, ProvinceName: "Bến Tre" },
    { ProvinceID: 213, ProvinceName: "Bình Định" },
    { ProvinceID: 215, ProvinceName: "Bình Phước" },
    { ProvinceID: 216, ProvinceName: "Bình Thuận" },
    { ProvinceID: 217, ProvinceName: "Cà Mau" },
    { ProvinceID: 218, ProvinceName: "Cao Bằng" },
    { ProvinceID: 219, ProvinceName: "Đắk Lắk" },
    { ProvinceID: 220, ProvinceName: "Đắk Nông" },
    { ProvinceID: 221, ProvinceName: "Điện Biên" },
    { ProvinceID: 223, ProvinceName: "Đồng Tháp" },
    { ProvinceID: 224, ProvinceName: "Gia Lai" },
    { ProvinceID: 225, ProvinceName: "Hà Giang" },
    { ProvinceID: 226, ProvinceName: "Hà Nam" },
    { ProvinceID: 227, ProvinceName: "Hà Tĩnh" },
    { ProvinceID: 228, ProvinceName: "Hải Dương" },
    { ProvinceID: 229, ProvinceName: "Hậu Giang" },
    { ProvinceID: 230, ProvinceName: "Hòa Bình" },
    { ProvinceID: 231, ProvinceName: "Hưng Yên" },
    { ProvinceID: 233, ProvinceName: "Kiên Giang" },
    { ProvinceID: 234, ProvinceName: "Kon Tum" },
    { ProvinceID: 235, ProvinceName: "Lai Châu" },
    { ProvinceID: 237, ProvinceName: "Lạng Sơn" },
    { ProvinceID: 238, ProvinceName: "Lào Cai" },
    { ProvinceID: 239, ProvinceName: "Long An" },
    { ProvinceID: 240, ProvinceName: "Nam Định" },
    { ProvinceID: 242, ProvinceName: "Ninh Bình" },
    { ProvinceID: 243, ProvinceName: "Ninh Thuận" },
    { ProvinceID: 244, ProvinceName: "Phú Thọ" },
    { ProvinceID: 245, ProvinceName: "Phú Yên" },
    { ProvinceID: 247, ProvinceName: "Quảng Bình" },
    { ProvinceID: 248, ProvinceName: "Quảng Ngãi" },
    { ProvinceID: 250, ProvinceName: "Quảng Trị" },
    { ProvinceID: 251, ProvinceName: "Sóc Trăng" },
    { ProvinceID: 252, ProvinceName: "Sơn La" },
    { ProvinceID: 253, ProvinceName: "Tây Ninh" },
    { ProvinceID: 254, ProvinceName: "Thái Bình" },
    { ProvinceID: 255, ProvinceName: "Thái Nguyên" },
    { ProvinceID: 258, ProvinceName: "Tiền Giang" },
    { ProvinceID: 259, ProvinceName: "Trà Vinh" },
    { ProvinceID: 260, ProvinceName: "Tuyên Quang" },
    { ProvinceID: 261, ProvinceName: "Vĩnh Long" },
    { ProvinceID: 262, ProvinceName: "Vĩnh Phúc" },
    { ProvinceID: 263, ProvinceName: "Yên Bái" }
];

// ================================================================
//  State
// ================================================================
let allProvinces  = [];
let allWards      = [];
let selectedWard  = null;
let shippingFee   = 0;
let discountAmt   = 0;

// ================================================================
//  Helpers
// ================================================================
function formatVND(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
}

function normalizeStr(s) {
    return String(s || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/^(tinh|thanh pho|tp\.?|huyen|quan|thi xa|thi tran|xa|phuong)\s+/g, '')
        .trim();
}

function calculateFallbackFee() {
    return (SUBTOTAL >= 5000000) ? 0 : 30000;
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

function syncClientCheckoutItems() {
    try {
        const raw = localStorage.getItem('ls_cart_items');
        if (!raw) return;
        const items = JSON.parse(raw).filter(it => it.is_selected !== false);
        if (!items || items.length === 0) return;

        const container = document.getElementById('checkout-order-items');
        if (!container) return;

        container.innerHTML = '';
        let newSubtotal = 0;
        items.forEach(it => {
            const price = parseFloat(it.price) || 0;
            const qty = parseInt(it.quantity) || 1;
            const itemTotal = price * qty;
            newSubtotal += itemTotal;

            const div = document.createElement('div');
            div.className = 'order-item';
            div.innerHTML = `
                <img src="${it.image || ''}" class="order-img" alt="${it.name}">
                <div class="order-info">
                    <div class="order-name">${it.name}</div>
                    <div class="order-qty">Số lượng: ${qty}</div>
                </div>
                <div class="order-price">${formatVND(itemTotal)}</div>
            `;
            container.appendChild(div);
        });

        SUBTOTAL = newSubtotal;
        const subtotalEl = document.querySelector('.summary-row span:last-child');
        if (subtotalEl) subtotalEl.textContent = formatVND(newSubtotal);
        updateTotal();
    } catch(e) {}
}

function enableOrder(enable) {
    const btn = document.getElementById('btn-place-order');
    if (btn) btn.disabled = !enable;
}

function setHidden(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val ?? '';
}

// ================================================================
//  Province searchable dropdown
// ================================================================
const provinceInput    = document.getElementById('province_search');
const provinceDropdown = document.getElementById('province_dropdown');
const wardInput        = document.getElementById('ward_search');
const wardDropdown     = document.getElementById('ward_dropdown');

async function loadProvinces() {
    let loaded = false;
    if (!isStaticSite) {
        try {
            const resp = await fetch(ROUTES.provinces);
            if (resp.ok) {
                const json = await resp.json();
                if (json.success && json.data && json.data.length > 0) {
                    allProvinces = json.data.sort((a, b) =>
                        a.ProvinceName.localeCompare(b.ProvinceName, 'vi')
                    );
                    loaded = true;
                }
            }
        } catch (e) {
            console.warn('loadProvinces API failed, using fallback:', e);
        }
    }

    if (!loaded) {
        allProvinces = [...FALLBACK_PROVINCES].sort((a, b) =>
            a.ProvinceName.localeCompare(b.ProvinceName, 'vi')
        );
    }

    renderProvinceDropdown('');

    // Tự động điền sổ địa chỉ mặc định
    const selectedAddress = document.querySelector('input[name="saved_address_id"]:checked');
    if (selectedAddress && selectedAddress.value !== 'new') {
        fillAddress(selectedAddress);
    }
}

provinceInput?.addEventListener('input', function () {
    renderProvinceDropdown(this.value.trim().toLowerCase());
    openProvinceDropdown();
});

provinceInput?.addEventListener('focus', function () {
    renderProvinceDropdown(this.value.trim().toLowerCase());
    openProvinceDropdown();
});

function renderProvinceDropdown(q) {
    if (!provinceDropdown) return;
    const filtered = q
        ? allProvinces.filter(p => p.ProvinceName.toLowerCase().includes(q) || normalizeStr(p.ProvinceName).includes(normalizeStr(q)))
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

function openProvinceDropdown()  { provinceDropdown?.classList.add('open'); }
function closeProvinceDropdown() { provinceDropdown?.classList.remove('open'); }

async function selectProvince(province) {
    const provinceId   = province.ProvinceID;
    const provinceName = province.ProvinceName;

    if (provinceInput) provinceInput.value = provinceName;
    closeProvinceDropdown();

    setHidden('province_id_input',   provinceId);
    setHidden('province_name_input', provinceName);
    setHidden('district_id_input',   '');
    setHidden('district_name_input', '');
    setHidden('ward_code_input',     '');
    setHidden('ward_name_input',     '');

    selectedWard = null;
    allWards = [];
    closeWardDropdown();

    if (wardInput) {
        wardInput.value = '';
        wardInput.disabled = false;
        wardInput.placeholder = 'Gõ tên Phường / Xã để chọn...';
    }

    // Tạm tính phí vận chuyển theo chính sách LensStore để không khóa nút bấm
    const baseFee = calculateFallbackFee();
    setShippingDisplay(
        `<span class="fee-badge success"><i class="fa-solid fa-check"></i> ${baseFee === 0 ? 'Miễn phí' : formatVND(baseFee)} (GHN Tiêu chuẩn)</span>`,
        baseFee
    );
    enableOrder(true);

    if (!isStaticSite && ROUTES.wardsByProvince) {
        try {
            const resp = await fetch(ROUTES.wardsByProvince + '?province_id=' + provinceId);
            if (resp.ok) {
                const json = await resp.json();
                if (json.success && json.data && json.data.length > 0) {
                    allWards = json.data;
                }
            }
        } catch (e) {
            console.warn('wardsByProvince error:', e);
        }
    }
}

// ================================================================
//  Ward searchable dropdown
// ================================================================
wardInput?.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    renderWardDropdown(q);
    openWardDropdown();

    // Cập nhật giá trị vào hidden fields
    setHidden('ward_name_input', this.value.trim());
    if (!document.getElementById('ward_code_input').value) setHidden('ward_code_input', '1A1301');
    if (!document.getElementById('district_id_input').value) setHidden('district_id_input', '1582');
    if (!document.getElementById('district_name_input').value) setHidden('district_name_input', this.value.trim());
    enableOrder(true);
});

wardInput?.addEventListener('focus', function () {
    renderWardDropdown(this.value.trim().toLowerCase());
    openWardDropdown();
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.ward-search-wrap'))     closeWardDropdown();
    if (!e.target.closest('.province-search-wrap')) closeProvinceDropdown();
});

function renderWardDropdown(q) {
    if (!wardDropdown) return;
    const filtered = q
        ? allWards.filter(w => w.WardName.toLowerCase().includes(q) || normalizeStr(w.WardName).includes(normalizeStr(q)))
        : allWards;

    wardDropdown.innerHTML = '';

    if (filtered.length === 0) {
        if (!q) {
            wardDropdown.innerHTML = '<div class="ward-option no-result">Hãy gõ tên Phường / Xã của bạn</div>';
        } else {
            const customDiv = document.createElement('div');
            customDiv.className = 'ward-option';
            customDiv.innerHTML = `<i class="fa-solid fa-location-dot" style="color:var(--primary);margin-right:6px;"></i> Sử dụng: <strong>${escapeHtml(q)}</strong>`;
            customDiv.addEventListener('click', () => {
                selectWard({
                    WardCode: '1A1301',
                    WardName: q,
                    DistrictID: '1582',
                    DistrictName: q
                });
            });
            wardDropdown.appendChild(customDiv);
        }
        return;
    }

    filtered.slice(0, 80).forEach(w => {
        const div = document.createElement('div');
        div.className   = 'ward-option';
        div.textContent = w.WardName;
        div.addEventListener('click', () => selectWard(w));
        wardDropdown.appendChild(div);
    });
}

function openWardDropdown()  { wardDropdown?.classList.add('open'); }
function closeWardDropdown() { wardDropdown?.classList.remove('open'); }

async function selectWard(ward) {
    selectedWard = ward;
    if (wardInput) wardInput.value = ward.WardName;
    closeWardDropdown();

    setHidden('ward_code_input',     ward.WardCode);
    setHidden('ward_name_input',     ward.WardName);
    setHidden('district_id_input',   ward.DistrictID);
    setHidden('district_name_input', ward.DistrictName);

    const baseFee = calculateFallbackFee();
    setShippingDisplay(
        `<span class="fee-badge success"><i class="fa-solid fa-check"></i> ${baseFee === 0 ? 'Miễn phí' : formatVND(baseFee)} (GHN Tiêu chuẩn)</span>`,
        baseFee
    );
    enableOrder(true);

    if (!isStaticSite && ROUTES.calculateFee) {
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
            }
        } catch (e) {
            console.warn('calculateFee network error, retaining fallback fee:', e);
        }
    }
}

// ================================================================
//  Address Book
// ================================================================
const savedAddresses = @json(Auth::check() ? Auth::user()->addresses->keyBy('id') : []);

function fillAddress(input) {
    if (!input) return;
    if (input.value === 'new') {
        clearAddress();
        return;
    }

    const addr = savedAddresses[input.value];
    if (!addr) return;

    // Điền ngay thông tin người nhận
    const nameEl = document.getElementById('checkout-name');
    const phoneEl = document.getElementById('checkout-phone');
    const detailEl = document.getElementById('checkout-address-detail');

    if (nameEl) nameEl.value = addr.name || '';
    if (phoneEl) phoneEl.value = addr.phone || '';
    if (detailEl) detailEl.value = addr.address || '';

    // Điền thông tin địa phương
    if (provinceInput) provinceInput.value = addr.province || '';
    if (wardInput) {
        wardInput.value = addr.district || '';
        wardInput.disabled = false;
        wardInput.placeholder = 'Gõ tên Phường / Xã...';
    }

    // So khớp tỉnh thành linh hoạt (Fuzzy Match)
    const normAddrProv = normalizeStr(addr.province);
    const provinceObj = allProvinces.find(p => 
        p.ProvinceName === addr.province || 
        normalizeStr(p.ProvinceName) === normAddrProv ||
        p.ProvinceName.includes(addr.province) ||
        addr.province.includes(p.ProvinceName)
    ) || allProvinces[0] || { ProvinceID: 201, ProvinceName: addr.province || 'Hà Nội' };

    setHidden('province_id_input',   provinceObj.ProvinceID);
    setHidden('province_name_input', provinceObj.ProvinceName || addr.province);
    setHidden('district_id_input',   '1582');
    setHidden('district_name_input', addr.district || 'Huyện Đông Anh');
    setHidden('ward_code_input',     '1A1301');
    setHidden('ward_name_input',     addr.district || 'Thị trấn Đông Anh');

    // Luôn mở khóa nút Đặt hàng và hiển thị phí ship chuẩn
    const baseFee = calculateFallbackFee();
    const feeText = baseFee === 0 ? 'Miễn phí (Ưu đãi đơn hàng)' : formatVND(baseFee) + ' (GHN Tiêu chuẩn)';
    setShippingDisplay(`<span class="fee-badge success"><i class="fa-solid fa-check"></i> ${feeText}</span>`, baseFee);
    enableOrder(true);

    // Tính toán chi tiết qua GHN API nếu server online
    if (!isStaticSite && ROUTES.wardsByProvince) {
        fetch(ROUTES.wardsByProvince + '?province_id=' + provinceObj.ProvinceID)
            .then(res => res.json())
            .then(json => {
                if (json.success && json.data) {
                    allWards = json.data;
                    const normWard = normalizeStr(addr.district);
                    const wardObj = json.data.find(w => 
                        w.WardName === addr.district || 
                        normalizeStr(w.WardName) === normWard
                    );
                    if (wardObj) {
                        selectWard(wardObj);
                    }
                }
            })
            .catch(() => {});
    }
}

function clearAddress() {
    const nameEl = document.getElementById('checkout-name');
    const phoneEl = document.getElementById('checkout-phone');
    const detailEl = document.getElementById('checkout-address-detail');

    if (nameEl) nameEl.value = '{{ Auth::user()->name ?? "" }}';
    if (phoneEl) phoneEl.value = '{{ Auth::user()->phone ?? "" }}';
    if (detailEl) detailEl.value = '';

    if (provinceInput) provinceInput.value = '';
    if (wardInput) {
        wardInput.value = '';
        wardInput.disabled = true;
        wardInput.placeholder = '-- Chọn Tỉnh trước --';
    }

    setHidden('province_id_input', '');
    setHidden('province_name_input', '');
    setHidden('district_id_input', '');
    setHidden('district_name_input', '');
    setHidden('ward_code_input', '');
    setHidden('ward_name_input', '');

    const baseFee = calculateFallbackFee();
    setShippingDisplay('<span class="fee-badge hint"><i class="fa-solid fa-truck"></i> Chọn địa chỉ để tính phí</span>', 0);
    enableOrder(false);
}

function selectPayment(elem) {
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    elem.classList.add('selected');
    const radio = elem.querySelector('input');
    if (radio) radio.checked = true;
}

// ================================================================
//  Voucher
// ================================================================
async function applyVoucher() {
    const codeInput = document.getElementById('voucher_code_input');
    const code = (codeInput?.value || '').trim().toUpperCase();
    const msgBox = document.getElementById('voucher-msg');
    
    if (!code) {
        if (msgBox) msgBox.innerHTML = '<span style="color:var(--danger)">Vui lòng nhập mã giảm giá.</span>';
        return;
    }

    function applyDiscount(disc, msg) {
        discountAmt = disc;
        setHidden('voucher_code_hidden', code);
        setHidden('discount_amount_hidden', discountAmt);

        const row = document.getElementById('discount-row');
        const disp = document.getElementById('discount-display');
        if (row) row.style.display = 'flex';
        if (disp) disp.textContent = '-' + formatVND(discountAmt);
        if (msgBox) msgBox.innerHTML = '<span style="color:var(--success)"><i class="fa-solid fa-check"></i> ' + msg + '</span>';
        updateTotal();
    }

    if (isStaticSite) {
        if (code === 'LENS10' || code === 'GIAM10' || code === 'WELCOME') {
            const disc = Math.min(500000, Math.round(SUBTOTAL * 0.1));
            applyDiscount(disc, `Áp dụng thành công mã ${code}: Giảm 10% (Tối đa 500.000 ₫)`);
        } else if (code === 'FREESHIP') {
            applyDiscount(shippingFee, `Áp dụng thành công mã ${code}: Miễn phí vận chuyển`);
        } else {
            if (msgBox) msgBox.innerHTML = '<span style="color:var(--danger)"><i class="fa-solid fa-xmark"></i> Mã không hợp lệ hoặc đã hết hạn. Hãy thử mã: LENS10</span>';
        }
        return;
    }

    if (msgBox) msgBox.innerHTML = '<span style="color:var(--text-muted)"><i class="fa-solid fa-spinner fa-spin"></i> Đang kiểm tra...</span>';

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
            applyDiscount(json.data.discount_amount, json.message);
        } else {
            discountAmt = 0;
            setHidden('voucher_code_hidden', '');
            setHidden('discount_amount_hidden', 0);
            const row = document.getElementById('discount-row');
            if (row) row.style.display = 'none';
            if (msgBox) msgBox.innerHTML = '<span style="color:var(--danger)"><i class="fa-solid fa-xmark"></i> ' + json.message + '</span>';
            updateTotal();
        }
    } catch (e) {
        if (code === 'LENS10' || code === 'GIAM10' || code === 'WELCOME') {
            const disc = Math.min(500000, Math.round(SUBTOTAL * 0.1));
            applyDiscount(disc, `Áp dụng thành công mã ${code}: Giảm 10%`);
        } else {
            if (msgBox) msgBox.innerHTML = '<span style="color:var(--danger)">Lỗi kết nối. Thử lại sau.</span>';
        }
    }
}

// ================================================================
//  Submit & Static Order Creation
// ================================================================
function submitOrder() {
    const name = (document.getElementById('checkout-name')?.value || '').trim();
    const phone = (document.getElementById('checkout-phone')?.value || '').trim();
    const address = (document.getElementById('checkout-address-detail')?.value || '').trim();
    const province = (provinceInput?.value || '').trim();
    const ward = (wardInput?.value || '').trim();
    const payment = document.querySelector('input[name="payment_method"]:checked')?.value;

    if (!name) {
        alert('Vui lòng nhập Họ và tên người nhận!');
        document.getElementById('checkout-name')?.focus();
        return;
    }
    if (!phone) {
        alert('Vui lòng nhập Số điện thoại người nhận!');
        document.getElementById('checkout-phone')?.focus();
        return;
    }
    if (!province) {
        alert('Vui lòng chọn Tỉnh / Thành phố!');
        provinceInput?.focus();
        return;
    }
    if (!ward) {
        alert('Vui lòng nhập hoặc chọn Phường / Xã!');
        wardInput?.focus();
        return;
    }
    if (!address) {
        alert('Vui lòng nhập số nhà, tên đường giao hàng!');
        document.getElementById('checkout-address-detail')?.focus();
        return;
    }
    if (!payment) {
        alert('Vui lòng chọn phương thức thanh toán!');
        return;
    }

    // Đảm bảo hidden fields luôn có dữ liệu hợp lệ
    if (!document.getElementById('province_name_input').value) setHidden('province_name_input', province);
    if (!document.getElementById('province_id_input').value) setHidden('province_id_input', '201');
    if (!document.getElementById('district_name_input').value) setHidden('district_name_input', ward);
    if (!document.getElementById('district_id_input').value) setHidden('district_id_input', '1582');
    if (!document.getElementById('ward_name_input').value) setHidden('ward_name_input', ward);
    if (!document.getElementById('ward_code_input').value) setHidden('ward_code_input', '1A1301');

    if (isStaticSite) {
        handleStaticCheckout(name, phone, address, province, ward, payment);
        return;
    }

    document.getElementById('checkout-form').submit();
}

function handleStaticCheckout(name, phone, address, province, ward, payment) {
    const btn = document.getElementById('btn-place-order');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang hoàn tất đơn hàng...';
    }

    const now = new Date();
    const orderId = 'LS' + now.toISOString().slice(2,10).replace(/-/g,'') + Math.floor(1000 + Math.random() * 9000);
    const dateFormatted = now.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    const items = [];
    document.querySelectorAll('.order-item').forEach(el => {
        items.push({
            name: el.querySelector('.order-name')?.textContent?.trim() || 'Ống kính máy ảnh',
            qty: el.querySelector('.order-qty')?.textContent?.trim() || 'Số lượng: 1',
            price: el.querySelector('.order-price')?.textContent?.trim() || '',
            image: el.querySelector('img')?.src || ''
        });
    });

    const totalStr = document.getElementById('total-display')?.textContent || formatVND(SUBTOTAL + shippingFee - discountAmt);
    const newOrder = {
        order_code: orderId,
        created_at: dateFormatted,
        recipient_name: name,
        recipient_phone: phone,
        address: `${address}, ${ward}, ${province}`,
        payment_method: payment === 'momo' ? 'Ví MoMo (Đã thanh toán)' : 'Thanh toán khi nhận hàng (COD)',
        status_label: 'Chờ xử lý',
        status_color: '#f59e0b',
        total: totalStr,
        items: items
    };

    try {
        const stored = JSON.parse(localStorage.getItem('ls_orders') || '[]');
        stored.unshift(newOrder);
        localStorage.setItem('ls_orders', JSON.stringify(stored));
        localStorage.removeItem('ls_cart_items');
        localStorage.setItem('ls_cart_count', '0');
        localStorage.setItem('ls_last_order_code', orderId);

        const badge = document.getElementById('cart-count');
        if (badge) { badge.textContent = '0'; badge.style.display = 'none'; }
        document.querySelectorAll('.sf-cart-dropdown span, .sf-cart-badge').forEach(el => {
            el.textContent = '0';
            el.style.display = 'none';
        });
    } catch(e) {}

    setTimeout(() => {
        const basePath = window.location.pathname.startsWith('/lensStore') ? '/lensStore' : '';
        window.location.href = basePath + '/orders/';
    }, 600);
}

// Modal functions
function showAddressModal() {
    const form = document.getElementById('address-form');
    if (form) form.reset();
    const distSelect = document.getElementById('addr-district');
    if (distSelect) distSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
    const modal = document.getElementById('address-modal');
    if (modal) modal.style.display = 'flex';
    
    const select = document.getElementById('addr-province');
    if (select) {
        select.innerHTML = '<option value="">Chọn Tỉnh/Thành</option>';
        allProvinces.forEach(p => {
            const option = document.createElement('option');
            option.value = p.ProvinceName;
            option.dataset.id = p.ProvinceID;
            option.textContent = p.ProvinceName;
            select.appendChild(option);
        });
    }
}

function closeAddressModal() {
    const modal = document.getElementById('address-modal');
    if (modal) modal.style.display = 'none';
}

async function loadModalWards(option) {
    let provinceId = option?.dataset?.id;
    const distSelect = document.getElementById('addr-district');
    if (!distSelect) return;
    distSelect.innerHTML = '<option value="">Đang tải Phường/Xã...</option>';
    
    if (!provinceId) return;

    if (isStaticSite) {
        distSelect.innerHTML = `
            <option value="">Chọn Phường/Xã</option>
            <option value="Phường 1">Phường 1</option>
            <option value="Phường 2">Phường 2</option>
            <option value="Phường Trung Tâm">Phường Trung Tâm</option>
            <option value="Thị trấn Trung Tâm">Thị trấn Trung Tâm</option>
        `;
        return;
    }

    try {
        const res = await fetch(ROUTES.wardsByProvince + "?province_id=" + provinceId);
        const data = await res.json();
        distSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (data.data && data.data.length > 0) {
            data.data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.WardName;
                opt.textContent = d.WardName;
                distSelect.appendChild(opt);
            });
        } else {
            distSelect.innerHTML = '<option value="Phường Trung Tâm">Phường Trung Tâm</option>';
        }
    } catch(e) {
        distSelect.innerHTML = '<option value="Phường Trung Tâm">Phường Trung Tâm</option>';
    }
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, s => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[s]));
}

// Khởi tạo
loadProvinces();
syncClientCheckoutItems();
</script>
@endsection

