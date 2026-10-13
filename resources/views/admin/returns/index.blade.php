@extends('layouts.admin')

@section('title', 'Yêu cầu trả hàng & Quản lý vận đơn hoàn')

@section('content')
<div class="topbar">
    <div>
        <div class="breadcrumb">Quản lý / Đổi Trả & Thu Hồi</div>
        <h1>Yêu cầu Trả hàng & Vận đơn hoàn</h1>
    </div>
</div>

<div class="content">
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); margin-bottom: 1.25rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#4f46e5;"><i class="fa-solid fa-boxes-packing"></i></div>
            <div class="label">Tổng yêu cầu</div>
            <div class="value">{{ $counts['all'] }}</div>
            <div class="hint">Tất cả yêu cầu đổi trả</div>
        </div>
        <div class="stat-card tone-warn">
            <div class="stat-icon" style="background:#f59e0b;"><i class="fa-solid fa-clock"></i></div>
            <div class="label">Chờ duyệt yêu cầu</div>
            <div class="value text-warn">{{ $counts['pending'] }}</div>
            <div class="hint">Cần xem xét video & chứng từ</div>
        </div>
        <div class="stat-card tone-info">
            <div class="stat-icon" style="background:#0284c7;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            <div class="label">Chờ hoàn tiền</div>
            <div class="value" style="color:#0284c7;">{{ $counts['pending_refund'] }}</div>
            <div class="hint">{{ number_format($totalPendingRefund, 0, ',', '.') }} ₫ chờ chuyển</div>
        </div>
        <div class="stat-card tone-ok">
            <div class="stat-icon" style="background:#10b981;"><i class="fa-solid fa-circle-check"></i></div>
            <div class="label">Đã hoàn tiền</div>
            <div class="value text-ok">{{ $counts['refunded'] }}</div>
            <div class="hint">{{ number_format($totalRefunded, 0, ',', '.') }} ₫ đã giải ngân</div>
        </div>
        <div class="stat-card tone-danger">
            <div class="stat-icon" style="background:#ef4444;"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="label">Đã từ chối</div>
            <div class="value" style="color:var(--danger);">{{ $counts['rejected'] }}</div>
            <div class="hint">Không đáp ứng chính sách</div>
        </div>
    </div>

    @if(!empty($reasonCounts) && count($reasonCounts) > 0)
    <div class="grid-2" style="margin-bottom: 1.25rem;">
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--primary);margin-right:6px;"></i> Phân bố lý do đổi trả</div>
                <span class="badge" style="background:var(--surface-soft);color:var(--ink-soft)">{{ array_sum($reasonCounts) }} phản hồi</span>
            </div>
            <div style="height:220px;position:relative;">
                <canvas id="returnReasonChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fa-solid fa-list-check" style="color:var(--teal);margin-right:6px;"></i> Chi tiết theo lý do</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;padding-top:0.25rem;">
                @php $totalR = max(1, array_sum($reasonCounts)); @endphp
                @foreach($reasonCounts as $rReason => $rTotal)
                @php $pct = round(($rTotal / $totalR) * 100); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                        <span style="font-weight:600;color:var(--ink);">{{ $rReason }}</span>
                        <span style="color:var(--muted);font-weight:600;">{{ $rTotal }} ({{ $pct }}%)</span>
                    </div>
                    <div style="width:100%;height:7px;background:var(--surface-soft);border-radius:99px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg, var(--primary), #818cf8);border-radius:99px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Thanh tìm kiếm & Lọc trạng thái --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1rem;">
        <div class="status-tabs" style="margin-bottom:0;">
            <a href="{{ route('admin.returns.index', array_filter(['search' => $search])) }}" class="{{ empty($status) && empty($refundStatus) ? 'is-active' : '' }}">
                Tất cả · {{ $counts['all'] }}
            </a>
            <a href="{{ route('admin.returns.index', array_filter(['status' => 'pending', 'search' => $search])) }}" class="{{ ($status ?? '') === 'pending' ? 'is-active' : '' }}">
                Chờ duyệt · {{ $counts['pending'] }}
            </a>
            <a href="{{ route('admin.returns.index', array_filter(['status' => 'approved', 'refund_status' => 'pending', 'search' => $search])) }}" class="{{ ($refundStatus ?? '') === 'pending' && ($status ?? '') === 'approved' ? 'is-active' : '' }}">
                Chờ hoàn tiền · {{ $counts['pending_refund'] }}
            </a>
            <a href="{{ route('admin.returns.index', array_filter(['refund_status' => 'refunded', 'search' => $search])) }}" class="{{ ($refundStatus ?? '') === 'refunded' ? 'is-active' : '' }}">
                Đã hoàn tiền · {{ $counts['refunded'] }}
            </a>
            <a href="{{ route('admin.returns.index', array_filter(['status' => 'rejected', 'search' => $search])) }}" class="{{ ($status ?? '') === 'rejected' ? 'is-active' : '' }}">
                Từ chối · {{ $counts['rejected'] }}
            </a>
        </div>

        {{-- Thanh tra cứu đa năng theo mã vận đơn, mã yêu cầu trả hoặc mã đơn gốc --}}
        <form method="GET" action="{{ route('admin.returns.index') }}" style="display:flex; align-items:center; gap:8px;">
            @if(!empty($status)) <input type="hidden" name="status" value="{{ $status }}"> @endif
            @if(!empty($refundStatus)) <input type="hidden" name="refund_status" value="{{ $refundStatus }}"> @endif
            <div style="position:relative;">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Tra cứu: Mã RT..., Đơn LS..., Mã vận đơn..." 
                       style="padding:0.45rem 2rem 0.45rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-size:0.88rem; width:280px;">
                @if(!empty($search))
                    <a href="{{ route('admin.returns.index', array_filter(['status' => $status, 'refund_status' => $refundStatus])) }}" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#94a3b8; text-decoration:none;" title="Xóa tìm kiếm">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
            <button type="submit" class="btn btn-sm btn-primary" style="padding:0.45rem 0.9rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm
            </button>
        </form>
    </div>

    <div class="card flush" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Mã yêu cầu trả</th>
                        <th>Đơn hàng & Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Video mở hàng (Drive)</th>
                        <th>Vận đơn trả hàng</th>
                        <th style="text-align:right;">Số tiền hoàn</th>
                        <th>Trạng thái duyệt</th>
                        <th>Trạng thái hoàn tiền</th>
                        <th style="text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        {{-- Cột 1: Mã yêu cầu trả hàng & ngày tạo --}}
                        <td>
                            <strong style="color:#4f46e5; font-family:monospace; font-size:0.95rem;">
                                {{ $req->request_code ?: ('RT-'.str_pad($req->id, 6, '0', STR_PAD_LEFT)) }}
                            </strong>
                            <div style="font-size:0.75rem; color:var(--muted); margin-top:2px;">
                                {{ $req->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>

                        {{-- Cột 2: Đơn hàng gốc & Sản phẩm trả --}}
                        <td>
                            <a href="{{ route('admin.orders.show', $req->order_id) }}" style="color:var(--primary);font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fa-solid fa-receipt"></i> #{{ $req->order->order_code ?? str_pad($req->order_id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                            <div style="font-size:0.78rem; color:#334155; margin-top:3px; max-width:200px; line-height:1.35;">
                                @if($req->order && $req->order->items)
                                    @foreach($req->order->items as $item)
                                        <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $item->product_name }}">
                                            • {{ $item->product_name }} (x{{ $item->quantity }})
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div style="font-size:0.75rem; color:var(--muted); margin-top:2px;">
                                <strong>Lý do:</strong> {{ $req->reason }}
                            </div>
                        </td>

                        {{-- Cột 3: Thông tin khách hàng --}}
                        <td>
                            <strong>{{ $req->user->name ?? 'Khách vãng lai' }}</strong>
                            @if(!empty($req->user->phone))
                                <div style="font-size:0.75rem;color:var(--muted);margin-top:2px;"><i class="fa-solid fa-phone" style="font-size:0.7rem;"></i> {{ $req->user->phone }}</div>
                            @endif
                            @if(!empty($req->user->email))
                                <div style="font-size:0.72rem;color:var(--muted);"><i class="fa-solid fa-envelope" style="font-size:0.7rem;"></i> {{ $req->user->email }}</div>
                            @endif
                        </td>

                        {{-- Cột 4: Video mở hàng trên Google Drive --}}
                        <td>
                            @if($req->video_url)
                                <div>
                                    <a href="{{ $req->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm" style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:6px; font-size:0.78rem; font-weight:700; padding:0.3rem 0.65rem;" title="Mở video Google Drive trong tab mới">
                                        <i class="fa-brands fa-google-drive" style="color:#0f9d58;"></i> Mở video
                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;"></i>
                                    </a>
                                </div>
                                @if($req->google_drive_preview_url)
                                <button type="button" onclick="openDrivePreview('{{ $req->google_drive_preview_url }}', '{{ $req->request_code }}')" class="btn btn-sm btn-outline" style="margin-top:4px; font-size:0.72rem; padding:0.15rem 0.45rem;">
                                    <i class="fa-solid fa-play"></i> Xem trực tiếp
                                </button>
                                @endif
                            @else
                                <span style="color:#94a3b8; font-size:0.78rem; font-style:italic;">
                                    <i class="fa-solid fa-video-slash"></i> Không kèm link
                                </span>
                            @endif
                            @if($req->image)
                                <div style="margin-top:4px;">
                                    <button type="button" onclick="openEvidenceModal('{{ $req->image_url }}', '{{ $req->order->order_code ?? str_pad($req->order_id, 6, '0', STR_PAD_LEFT) }}', '{{ addslashes($req->reason) }}', '{{ addslashes($req->note ?? '') }}')" class="btn btn-sm btn-outline" style="font-size:0.72rem; padding:0.15rem 0.45rem;">
                                        <i class="fa-regular fa-image"></i> Ảnh kèm
                                    </button>
                                </div>
                            @endif
                        </td>

                        {{-- Cột 5: Mã vận đơn trả hàng & Trạng thái vận chuyển --}}
                        <td>
                            @if(!empty($req->tracking_code))
                                <div style="display:inline-flex; align-items:center; gap:6px;">
                                    <span class="badge" style="background:#eff6ff; color:#1d4ed8; font-family:monospace; border:1px solid #bfdbfe; font-size:0.85rem; font-weight:700;">
                                        {{ $req->tracking_code }}
                                    </span>
                                </div>
                                <div style="font-size:0.75rem; color:{{ $req->return_shipping_status_color }}; font-weight:700; margin-top:2px;">
                                    {{ $req->return_shipping_status_label }}
                                </div>
                            @else
                                <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:0.78rem;">
                                    Chưa có mã vận đơn
                                </span>
                                <div style="font-size:0.72rem; color:var(--muted); margin-top:2px;">
                                    {{ $req->return_shipping_status_label }}
                                </div>
                            @endif

                            {{-- Nút cập nhật mã vận đơn & Trạng thái vận chuyển --}}
                            @if($req->status !== 'rejected')
                            <div style="margin-top:4px;">
                                <button type="button" class="btn btn-sm btn-outline" style="padding:0.2rem 0.5rem; font-size:0.72rem;" onclick="openTrackingModal({{ $req->id }}, '{{ $req->request_code }}', '{{ addslashes($req->tracking_code ?? '') }}', '{{ $req->return_shipping_status }}')">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa vận đơn
                                </button>
                            </div>
                            @endif
                        </td>

                        {{-- Cột 6: Số tiền hoàn & tài khoản --}}
                        <td style="text-align:right; white-space:nowrap;">
                            <div style="font-weight:700; color:var(--danger); font-size:0.95rem;">
                                {{ number_format($req->refund_amount ?? $req->order->total ?? 0, 0, ',', '.') }} ₫
                            </div>
                            @if($req->bank_account_number)
                                <div style="font-size:0.72rem; color:var(--muted); margin-top:2px;" title="{{ $req->bank_name }} - {{ $req->bank_account_number }} - {{ $req->bank_account_holder }}">
                                    {{ $req->bank_name }} · {{ substr($req->bank_account_number, 0, 4) }}...
                                </div>
                            @endif
                        </td>

                        {{-- Cột 7: Trạng thái duyệt --}}
                        <td>
                            @if($req->status == 'pending')
                                <span class="badge" style="background:#fef9c3;color:#854d0e;border:1px solid #fef08a;">
                                    <i class="fa-solid fa-clock" style="margin-right:4px;"></i> Chờ duyệt
                                </span>
                            @elseif($req->status == 'approved')
                                <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
                                    <i class="fa-solid fa-circle-check" style="margin-right:4px;"></i> Đã chấp nhận
                                </span>
                            @elseif($req->status == 'completed')
                                <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">
                                    <i class="fa-solid fa-check-double" style="margin-right:4px;"></i> Hoàn tất
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
                                    <i class="fa-solid fa-circle-xmark" style="margin-right:4px;"></i> Từ chối
                                </span>
                            @endif
                        </td>

                        {{-- Cột 8: Trạng thái hoàn tiền --}}
                        <td>
                            @if($req->refund_status == 'refunded')
                                <span class="badge" style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;">
                                    <i class="fa-solid fa-money-bill-transfer" style="margin-right:4px;"></i> Đã hoàn tiền
                                </span>
                                @if($req->refunded_at)
                                    <div style="font-size:0.72rem;color:var(--muted);margin-top:3px;">
                                        {{ $req->refunded_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            @elseif($req->status == 'approved')
                                <span class="badge" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;">
                                    <i class="fa-solid fa-hourglass-half" style="margin-right:4px;"></i> Chờ chuyển tiền
                                </span>
                            @else
                                <span style="color:var(--muted);font-size:0.78rem;">Chưa xử lý</span>
                            @endif
                        </td>

                        {{-- Cột 9: Thao tác --}}
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.orders.show', $req->order_id) }}" class="btn btn-sm btn-outline" title="Xem đơn hàng gốc">
                                    <i class="fa-solid fa-eye"></i> Xem đơn
                                </a>

                                {{-- Duyệt / Từ chối (Khi Pending) --}}
                                @if($req->status == 'pending')
                                <form action="{{ route('admin.returns.update-status', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Chấp thuận yêu cầu đổi trả {{ $req->request_code }} cho đơn #{{ $req->order->order_code ?? $req->order_id }}?');">
                                        <i class="fa-solid fa-check"></i> Duyệt
                                    </button>
                                </form>
                                <form action="{{ route('admin.returns.update-status', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Từ chối yêu cầu đổi trả {{ $req->request_code }}?');">
                                        <i class="fa-solid fa-xmark"></i> Từ chối
                                    </button>
                                </form>
                                @endif

                                {{-- Nút xác nhận hoàn tiền --}}
                                @if($req->status == 'approved' && $req->refund_status != 'refunded')
                                <button type="button" class="btn btn-sm btn-primary" style="background:#059669; border-color:#059669; color:#fff;" onclick="openRefundModal({{ $req->id }}, '{{ $req->order->order_code ?? $req->order_id }}', {{ $req->refund_amount ?? $req->order->total ?? 0 }}, '{{ addslashes($req->bank_name ?? 'Chưa rõ') }}', '{{ addslashes($req->bank_account_holder ?? '') }}', '{{ addslashes($req->bank_account_number ?? '') }}')">
                                    <i class="fa-solid fa-money-bill-wave"></i> Hoàn tiền
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state" style="text-align:center; padding:3rem 1rem;">
                                <i class="fa-solid fa-inbox" style="font-size:2.5rem; display:block; margin-bottom:0.75rem; opacity:0.35;"></i>
                                <span style="font-size:0.95rem; color:#64748b;">Không tìm thấy yêu cầu đổi trả nào phù hợp bộ lọc hoặc từ khóa tìm kiếm.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top:1.25rem;">
        {{ $requests->links('vendor.pagination.admin') }}
    </div>
</div>

{{-- MODAL CẬP NHẬT MÃ VẬN ĐƠN & TRẠNG THÁI VẬN CHUYỂN --}}
<div id="trackingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; max-width:480px; width:90%; padding:2rem; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); position:relative;">
        <h3 style="margin-bottom:0.5rem; font-size:1.25rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-truck-ramp-box" style="color:#2563eb;"></i> Cập nhật Mã Vận Đơn Trả Hàng
        </h3>
        <p style="font-size:0.85rem; color:#64748b; margin-bottom:1.25rem;">
            Yêu cầu: <strong id="trackingReqCode" style="color:#4f46e5;"></strong>. Nhập mã vận đơn do đơn vị vận chuyển (GHN, ViettelPost...) cung cấp.
        </p>

        <form id="trackingForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:1.2rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Mã vận đơn trả hàng (Waybill Tracking Code)
                </label>
                <input type="text" name="tracking_code" id="modalTrackingCode" class="form-control" placeholder="VD: GHN28491729, VTP829102..." style="width:100%; padding:0.65rem 0.85rem; border:1.5px solid #cbd5e1; border-radius:8px; font-family:monospace; font-weight:700; font-size:1rem;">
                <div style="font-size:0.75rem; color:#64748b; margin-top:4px;">
                    Để trống nếu đơn vị vận chuyển chưa cấp mã (hệ thống sẽ hiển thị "Chưa có mã vận đơn").
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Trạng thái vận chuyển đơn hoàn <span style="color:red">*</span>
                </label>
                <select name="return_shipping_status" id="modalShippingStatus" class="form-control" style="width:100%; padding:0.65rem 0.85rem; border:1.5px solid #cbd5e1; border-radius:8px; font-weight:600;" required>
                    <option value="pending_tracking">Chờ cấp mã vận đơn</option>
                    <option value="tracking_assigned">Đã có mã vận đơn</option>
                    <option value="picking_up">Đang lấy hàng</option>
                    <option value="delivering_back">Đang vận chuyển về</option>
                    <option value="received">Đã nhận hàng tại kho (Chờ kiểm định QC)</option>
                </select>
                <div style="font-size:0.75rem; color:#64748b; margin-top:4px;">
                    Chỉ chọn "Đã nhận hàng tại kho" khi thủ kho đã nhận được kiện hàng vật lý trên thực tế.
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeTrackingModal()" class="btn btn-secondary" style="padding:0.65rem 1.25rem;">Hủy</button>
                <button type="submit" class="btn btn-primary" style="padding:0.65rem 1.25rem; font-weight:700;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu vận đơn
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL XEM TRỰC TIẾP VIDEO GOOGLE DRIVE --}}
<div id="drivePreviewModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.85); z-index:9999; justify-content:center; align-items:center; padding:1.5rem; backdrop-filter:blur(4px);">
    <div style="background:#ffffff; border-radius:16px; width:100%; max-width:800px; height:80vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);">
        <div style="padding:0.9rem 1.25rem; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
            <div style="font-weight:800; font-size:1rem; color:#1e293b; display:flex; align-items:center; gap:8px;">
                <i class="fa-brands fa-google-drive" style="color:#0f9d58;"></i> Video mở hàng <span id="drivePreviewTitle" style="color:#4f46e5;"></span>
            </div>
            <button type="button" onclick="closeDrivePreview()" style="background:none; border:none; font-size:1.25rem; color:#64748b; cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div style="flex:1; background:#000; position:relative;">
            <iframe id="drivePreviewFrame" src="" style="width:100%; height:100%; border:none;" allow="autoplay"></iframe>
        </div>
        <div style="padding:0.75rem 1.25rem; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <a id="drivePreviewDirectLink" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline" style="display:inline-flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Mở trên Google Drive trực tiếp
            </a>
            <button type="button" onclick="closeDrivePreview()" class="btn btn-sm btn-secondary">Đóng</button>
        </div>
    </div>
</div>

{{-- MODAL XÁC NHẬN ĐÃ HOÀN TIỀN AN TOÀN --}}
<div id="refundModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; max-width:500px; width:90%; padding:2rem; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); position:relative;">
        <h3 style="margin-bottom:0.5rem; font-size:1.3rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-money-bill-transfer" style="color:#059669;"></i> Xác nhận đã hoàn tiền
        </h3>
        <p style="font-size:0.88rem; color:#64748b; margin-bottom:1.25rem;">
            Vui lòng đối soát và chỉ xác nhận sau khi việc chuyển tiền thực tế qua ngân hàng đã thành công.
        </p>

        <form id="refundForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:1rem; margin-bottom:1.25rem; font-size:0.85rem; line-height:1.6;">
                <div>Đơn hàng: <strong id="modalOrderCode" style="color:#4f46e5;"></strong></div>
                <div>Ngân hàng: <strong id="modalBankName"></strong></div>
                <div>Số tài khoản: <strong id="modalAccountNumber" style="font-family:monospace; font-size:0.95rem;"></strong></div>
                <div>Chủ tài khoản: <strong id="modalAccountHolder"></strong></div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Số tiền thực hoàn (₫) <span style="color:red">*</span>
                </label>
                <input type="number" name="refund_amount" id="modalRefundAmount" class="form-control" style="width:100%; padding:0.6rem 0.8rem; border:1.5px solid #cbd5e1; border-radius:8px; font-weight:700; color:#059669; font-size:1.05rem;" required min="1000">
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Phương thức hoàn tiền <span style="color:red">*</span>
                </label>
                <select name="refund_method" class="form-control" style="width:100%; padding:0.6rem 0.8rem; border:1.5px solid #cbd5e1; border-radius:8px;" required>
                    <option value="bank_transfer" selected>Chuyển khoản ngân hàng 24/7</option>
                    <option value="momo">Ví MoMo</option>
                    <option value="cash">Tiền mặt tại quầy</option>
                </select>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Mã tham chiếu / Mã giao dịch ngân hàng
                </label>
                <input type="text" name="refund_reference" class="form-control" placeholder="VD: FT24285749281729 hoặc mã UNC..." style="width:100%; padding:0.6rem 0.8rem; border:1.5px solid #cbd5e1; border-radius:8px;">
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-weight:700; font-size:0.88rem; margin-bottom:5px; color:#334155;">
                    Ghi chú nội bộ
                </label>
                <textarea name="refund_note" class="form-control" rows="2" placeholder="Ghi chú thêm về giao dịch hoàn tiền..." style="width:100%; padding:0.6rem 0.8rem; border:1.5px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeRefundModal()" class="btn btn-secondary" style="padding:0.65rem 1.25rem;">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary" style="background:#059669; border-color:#059669; padding:0.65rem 1.25rem; font-weight:700;" onclick="this.disabled=true; this.form.submit();">
                    <i class="fa-solid fa-check"></i> Xác nhận đã chuyển tiền
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL XEM ẢNH BẰNG CHỨNG (LIGHTBOX) --}}
<div id="evidenceModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.8); z-index:9999; justify-content:center; align-items:center; padding:1.5rem; backdrop-filter:blur(4px);">
    <div style="background:#ffffff; border-radius:16px; width:100%; max-width:680px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
            <div>
                <h3 style="margin:0; font-size:1.05rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                    <i class="fa-regular fa-image" style="color:var(--primary);"></i> Bằng chứng hoàn hàng <span id="evidenceOrderCode" style="color:var(--primary);"></span>
                </h3>
            </div>
            <button type="button" onclick="closeEvidenceModal()" style="background:none; border:none; font-size:1.25rem; color:#64748b; cursor:pointer; padding:4px 8px; border-radius:6px; line-height:1;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div style="padding:1rem 1.25rem; overflow-y:auto; flex:1; text-align:center; background:#0f172a; display:flex; align-items:center; justify-content:center;">
            <img id="evidenceImage" src="" alt="Bằng chứng trả hàng" style="max-width:100%; max-height:55vh; object-fit:contain; border-radius:8px;">
        </div>

        <div style="padding:0.9rem 1.25rem; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:0.85rem;">
            <div style="margin-bottom:4px;"><strong>Lý do:</strong> <span id="evidenceReason" style="color:#1e293b;"></span></div>
            <div id="evidenceNoteWrap" style="color:#64748b; display:none;"><strong>Ghi chú:</strong> <span id="evidenceNote"></span></div>
        </div>

        <div style="padding:0.75rem 1.25rem; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#ffffff;">
            <a id="evidenceDownloadBtn" href="#" target="_blank" class="btn btn-sm btn-outline" style="display:inline-flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Mở ảnh kích thước gốc
            </a>
            <button type="button" onclick="closeEvidenceModal()" class="btn btn-sm btn-secondary">Đóng</button>
        </div>
    </div>
</div>

<script>
function openTrackingModal(reqId, reqCode, trackingCode, status) {
    const form = document.getElementById('trackingForm');
    form.action = '/admin/returns/' + reqId + '/tracking';
    document.getElementById('trackingReqCode').textContent = reqCode;
    document.getElementById('modalTrackingCode').value = trackingCode || '';
    document.getElementById('modalShippingStatus').value = status || 'pending_tracking';
    document.getElementById('trackingModal').style.display = 'flex';
}

function closeTrackingModal() {
    document.getElementById('trackingModal').style.display = 'none';
}

function openDrivePreview(previewUrl, reqCode) {
    document.getElementById('drivePreviewTitle').textContent = reqCode;
    document.getElementById('drivePreviewFrame').src = previewUrl;
    document.getElementById('drivePreviewDirectLink').href = previewUrl.replace('/preview', '/view');
    document.getElementById('drivePreviewModal').style.display = 'flex';
}

function closeDrivePreview() {
    document.getElementById('drivePreviewModal').style.display = 'none';
    document.getElementById('drivePreviewFrame').src = '';
}

function openEvidenceModal(url, orderCode, reason, note) {
    document.getElementById('evidenceImage').src = url;
    document.getElementById('evidenceOrderCode').textContent = '#' + orderCode;
    document.getElementById('evidenceReason').textContent = reason;
    const noteWrap = document.getElementById('evidenceNoteWrap');
    if (note && note.trim().length > 0) {
        document.getElementById('evidenceNote').textContent = note;
        noteWrap.style.display = 'block';
    } else {
        noteWrap.style.display = 'none';
    }
    document.getElementById('evidenceDownloadBtn').href = url;
    document.getElementById('evidenceModal').style.display = 'flex';
}

function closeEvidenceModal() {
    document.getElementById('evidenceModal').style.display = 'none';
    document.getElementById('evidenceImage').src = '';
}

function openRefundModal(reqId, orderCode, amount, bankName, holder, number) {
    const modal = document.getElementById('refundModal');
    const form = document.getElementById('refundForm');
    form.action = '/admin/returns/' + reqId + '/confirm-refund';
    document.getElementById('modalOrderCode').textContent = '#' + orderCode;
    document.getElementById('modalBankName').textContent = bankName;
    document.getElementById('modalAccountNumber').textContent = number || 'Chưa cung cấp';
    document.getElementById('modalAccountHolder').textContent = holder || 'Chưa cung cấp';
    document.getElementById('modalRefundAmount').value = amount;
    modal.style.display = 'flex';
}

function closeRefundModal() {
    document.getElementById('refundModal').style.display = 'none';
}
</script>
@endsection

@if(!empty($reasonCounts) && count($reasonCounts) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('returnReasonChart');
    if (!canvas) return;
    const labels = @json(array_keys($reasonCounts));
    const dataValues = @json(array_values($reasonCounts));
    const palette = ['#4f46e5', '#f59e0b', '#ef4444', '#10b981', '#06b6d4', '#8b5cf6', '#ec4899'];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 12,
                        font: { size: 12, family: 'Inter' }
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
@endif
