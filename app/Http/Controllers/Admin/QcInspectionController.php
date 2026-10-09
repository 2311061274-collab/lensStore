<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QcInspection;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcInspectionController extends Controller
{
    public function index()
    {
        $inspections = QcInspection::with('returnRequest', 'product', 'user')->latest()->paginate(15);
        $pendingReturns = ReturnRequest::with('order.items.product')->where('status', 'approved')->get();
        return view('admin.qc_inspections.index', compact('inspections', 'pendingReturns'));
    }

    public function create(Request $request)
    {
        $returnId = $request->query('return_request_id');
        $returnRequest = ReturnRequest::with('order.items.product')->findOrFail($returnId);
        return view('admin.qc_inspections.create', compact('returnRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'return_request_id' => 'required|exists:return_requests,id',
            'product_id' => 'required|exists:products,id',
            'condition' => 'required|in:perfect,scratched,broken,used',
            'final_action' => 'required|in:restock,send_to_vendor,liquidate',
            'quantity' => 'required|integer|min:1',
            'inspection_note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $qc = QcInspection::create([
                'return_request_id' => $request->return_request_id,
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'condition' => $request->condition,
                'final_action' => $request->final_action,
                'quantity' => $request->quantity,
                'inspection_note' => $request->inspection_note
            ]);

            $product = Product::findOrFail($request->product_id);
            
            // Xử lý luồng tồn kho dựa trên Action
            if ($request->final_action === 'restock') {
                $product->increment('stock', $request->quantity); // Cộng lại kho bán
                $note = 'QC: Hoàn hảo, nhập lại kho (Mã phiếu trả: ' . $request->return_request_id . ')';
            } elseif ($request->final_action === 'send_to_vendor') {
                $product->increment('defective_stock', $request->quantity); // Vào kho lỗi
                $note = 'QC: Hàng lỗi, nhập kho chờ bảo hành (Mã phiếu trả: ' . $request->return_request_id . ')';
            } else {
                $product->increment('defective_stock', $request->quantity); // Hoặc kho thanh lý (dùng chung kho lỗi tạm)
                $note = 'QC: Hàng cũ/hỏng, nhập kho chờ thanh lý (Mã phiếu trả: ' . $request->return_request_id . ')';
            }

            InventoryTransaction::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $request->quantity,
                'reference_type' => QcInspection::class,
                'reference_id' => $qc->id,
                'note' => $note
            ]);

            // Tự động chuyển trạng thái đơn trả hàng thành hoàn tất (giả định)
            ReturnRequest::where('id', $request->return_request_id)->update(['status' => 'completed']);

            DB::commit();
            return redirect()->route('admin.qc_inspections.index')->with('success', 'Đã lưu kết quả kiểm định & cập nhật kho!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
