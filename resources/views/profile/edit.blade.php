@extends('layouts.app')

@section('title', 'Tài khoản cá nhân - LensStore')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-2">
        <i class="fa-solid fa-user-circle" style="font-size: 2rem; color: var(--primary);"></i>
        <div>
            <h1 style="margin-bottom: 0;">Tài khoản cá nhân</h1>
            <p class="text-muted" style="margin-top: 4px;">Quản lý thông tin bảo mật và liên hệ của bạn.</p>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
    
    <!-- Cột 1: Thông tin liên hệ -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-address-card text-muted"></i> Thông tin liên hệ
        </h3>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                <div style="margin-bottom: 15px; position: relative; display: inline-block;">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    @else
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #6366f1); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <label for="avatar" style="position: absolute; bottom: 0; right: 0; background: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 1px solid var(--line);">
                        <i class="fa-solid fa-camera" style="color: var(--primary);"></i>
                    </label>
                </div>
                <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
            </div>

            <div class="form-group">
                <label>Họ và tên <span style="color:var(--danger)">*</span></label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required style="padding-left: 2.5rem;">
                </div>
            </div>

            <div class="form-group">
                <label>Email (Đăng nhập)</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="padding-left: 2.5rem; background: #f1f5f9; cursor: not-allowed;">
                </div>
            </div>

            <div class="form-group">
                <label>Số điện thoại liên hệ</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-phone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}" style="padding-left: 2.5rem;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Địa chỉ thường trú</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-map-location-dot" style="position: absolute; left: 14px; top: 14px; color: var(--text-muted);"></i>
                    <textarea name="address" class="form-control" rows="3" style="padding-left: 2.5rem; resize: vertical;">{{ old('address', $user->address ?? '') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-floppy-disk"></i> Lưu thông tin
            </button>
        </form>
    </div>

    <!-- Cột 2: Đổi mật khẩu -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-lock text-muted"></i> Thay đổi mật khẩu
        </h3>
        <form action="{{ route('profile.update-password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Mật khẩu hiện tại <span style="color:var(--danger)">*</span></label>
                <div style="position: relative;">
                    <i class="fa-solid fa-key" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="current_password" class="form-control" required style="padding-left: 2.5rem;">
                </div>
            </div>

            <div class="form-group">
                <label>Mật khẩu mới <span style="color:var(--danger)">*</span></label>
                <div style="position: relative;">
                    <i class="fa-solid fa-unlock-keyhole" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="password" class="form-control" required style="padding-left: 2.5rem;" placeholder="Tối thiểu 6 ký tự">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Xác nhận mật khẩu mới <span style="color:var(--danger)">*</span></label>
                <div style="position: relative;">
                    <i class="fa-solid fa-unlock-keyhole" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="password_confirmation" class="form-control" required style="padding-left: 2.5rem;">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center; background: #fff;">
                <i class="fa-solid fa-shield-halved"></i> Đổi mật khẩu
            </button>
        </form>
    </div>
</div>

<!-- Sổ địa chỉ -->
<div class="card" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-address-book text-muted"></i> Sổ địa chỉ giao hàng
        </h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="showAddressModal()">
            <i class="fa-solid fa-plus"></i> Thêm địa chỉ mới
        </button>
    </div>
    
    @if(auth()->user()->addresses->count() > 0)
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach(auth()->user()->addresses as $addr)
                <div style="border: 1px solid var(--line); border-radius: 12px; padding: 1.25rem; position: relative; {{ $addr->is_default ? 'border-color: var(--primary); box-shadow: 0 4px 12px rgba(79,70,229,.1);' : '' }}">
                    @if($addr->is_default)
                        <span style="position: absolute; top: 12px; right: 12px; background: var(--primary); color: white; font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: bold;">Mặc định</span>
                    @endif
                    <div style="margin-bottom: 8px;">
                        <strong>{{ $addr->name }}</strong>
                        @if($addr->phone)
                            <span class="text-muted" style="margin-left: 8px;">| {{ $addr->phone }}</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 0.9rem; margin-bottom: 15px; line-height: 1.5;">
                        {{ $addr->address }}<br>
                        {{ $addr->district }}, {{ $addr->province }}
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn" style="padding: 4px 10px; font-size: 0.8rem; background: #f1f5f9; color: var(--ink) !important; box-shadow: none;" onclick="editAddress({{ $addr->toJson() }})">Sửa</button>
                        <form action="{{ route('profile.addresses.destroy', $addr) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="padding: 4px 10px; font-size: 0.8rem; background: #fee2e2; color: var(--danger) !important; box-shadow: none;">Xóa</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px; color: var(--muted);">
            <i class="fa-solid fa-map-location-dot" style="font-size: 2rem; opacity: 0.5; margin-bottom: 10px; display: block;"></i>
            Bạn chưa có địa chỉ giao hàng nào.
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa Địa chỉ -->
<div id="address-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 id="address-modal-title" style="margin-bottom: 1.5rem;">Thêm địa chỉ mới</h3>
        <form id="address-form" method="POST" action="{{ route('profile.addresses.store') }}">
            @csrf
            <input type="hidden" name="_method" id="address-method" value="POST">
            
            <div class="form-group">
                <label>Họ tên người nhận <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" id="addr-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại <span style="color:var(--danger)">*</span></label>
                <input type="text" name="phone" id="addr-phone" class="form-control" required>
            </div>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Tỉnh / Thành phố <span style="color:var(--danger)">*</span></label>
                    <select name="province" id="addr-province" class="form-control" required onchange="loadWards(this.options[this.selectedIndex])">
                        <option value="">Chọn Tỉnh/Thành</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phường / Xã <span style="color:var(--danger)">*</span></label>
                    <select name="district" id="addr-district" class="form-control" required>
                        <option value="">Chọn Phường/Xã</option>
                    </select>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">* Không còn cấp Quận/Huyện</div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Địa chỉ cụ thể (Số nhà, đường, phường/xã) <span style="color:var(--danger)">*</span></label>
                <textarea name="address" id="addr-address" class="form-control" rows="2" required></textarea>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_default" id="addr-default" value="1" style="width: 16px; height: 16px;">
                <label for="addr-default" style="margin-bottom: 0;">Đặt làm địa chỉ mặc định</label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1; justify-content: center; background: #f1f5f9; color: var(--ink) !important;" onclick="closeAddressModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Lưu địa chỉ</button>
            </div>
        </form>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr !important;
    }
}
/* Select2 Custom Styles to match form-control */
.select2-container .select2-selection--single {
    height: 44px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background-color: var(--surface);
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-main);
    line-height: normal;
    padding-left: 1rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px;
    right: 10px;
}
.select2-dropdown {
    border-color: var(--border);
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>

<!-- Load jQuery & Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        // You can add a preview logic here if you want
        // But the simplest is just showing the selected filename
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.parentElement.querySelector('img');
            const placeholder = input.parentElement.querySelector('div > div');
            if(img) {
                img.src = e.target.result;
            } else if(placeholder) {
                // If it was a placeholder, we might want to replace it with an img
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.style.cssText = "width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);";
                placeholder.replaceWith(newImg);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<script>
    const modal = document.getElementById('address-modal');
    const form = document.getElementById('address-form');
    const methodInput = document.getElementById('address-method');
    const title = document.getElementById('address-modal-title');
    
    let ghndata = { provinces: [] };

    async function fetchGHN() {
        try {
            const res = await fetch("{{ route('ghn.provinces') }}");
            const data = await res.json();
            if(data.data) {
                ghndata.provinces = data.data;
                const select = document.getElementById('addr-province');
                data.data.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.ProvinceName;
                    option.dataset.id = p.ProvinceID;
                    option.textContent = p.ProvinceName;
                    select.appendChild(option);
                });
            }
        } catch(e) {}
    }

    async function loadWards(option, selectedWard = null) {
        let provinceId;
        // Check if option is a Select2 element or native option
        if(option && option.dataset && option.dataset.id) {
            provinceId = option.dataset.id;
        } else if(option) {
            // If called from jQuery select2 event, we can get data from the option element
            const jOpt = $(option).find(':selected');
            provinceId = jOpt.attr('data-id');
        }
        
        const distSelect = document.getElementById('addr-district');
        distSelect.innerHTML = '<option value="">Đang tải Phường/Xã...</option>';
        if ($.fn.select2) $('#addr-district').trigger('change');
        
        if(!provinceId) return;

        try {
            const res = await fetch("{{ route('ghn.wards-by-province') }}?province_id=" + provinceId);
            const data = await res.json();
            distSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            if(data.data) {
                data.data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.WardName;
                    opt.textContent = d.WardName;
                    distSelect.appendChild(opt);
                });
                if(selectedWard) {
                    distSelect.value = selectedWard;
                }
                if ($.fn.select2) $('#addr-district').trigger('change');
            }
        } catch(e) {}
    }

    fetchGHN();

    function initSelect2() {
        if (!$.fn.select2) return;
        $('#addr-province').select2({
            dropdownParent: $('#address-modal'),
            width: '100%'
        }).on('change', function() {
            loadWards(this);
        });
        $('#addr-district').select2({
            dropdownParent: $('#address-modal'),
            width: '100%'
        });
    }

    function showAddressModal() {
        title.textContent = 'Thêm địa chỉ mới';
        form.action = "{{ route('profile.addresses.store') }}";
        methodInput.value = 'POST';
        form.reset();
        document.getElementById('addr-district').innerHTML = '<option value="">Chọn Phường/Xã</option>';
        modal.style.display = 'flex';
        
        if ($.fn.select2) {
            $('#addr-province').val('').trigger('change');
            $('#addr-district').val('').trigger('change');
        }
        initSelect2();
    }

    function closeAddressModal() {
        modal.style.display = 'none';
        if ($.fn.select2) {
            $('#addr-province').select2('destroy');
            $('#addr-district').select2('destroy');
        }
    }

    window.editAddress = async function(addr) {
        title.textContent = 'Cập nhật địa chỉ';
        form.action = `/profile/addresses/${addr.id}`;
        methodInput.value = 'PUT';
        
        document.getElementById('addr-name').value = addr.name;
        document.getElementById('addr-phone').value = addr.phone;
        document.getElementById('addr-address').value = addr.address;
        document.getElementById('addr-default').checked = !!addr.is_default;
        
        const provSelect = document.getElementById('addr-province');
        provSelect.value = addr.province;
        
        if ($.fn.select2) {
            $('#addr-province').val(addr.province).trigger('change');
        }
        
        const opt = $(provSelect).find(':selected')[0];
        if (opt) await loadWards(opt, addr.district);
        
        modal.style.display = 'flex';
        initSelect2();
    };
</script>
@endsection
