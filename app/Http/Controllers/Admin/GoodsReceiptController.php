<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    // Hiển thị danh sách phiếu nhập kho
    public function index()
    {
        $receipts = GoodsReceipt::with('supplier', 'user')->latest()->paginate(15);

        return view('admin.goods_receipts.index', compact('receipts'));
    }

    // Hiển thị form tạo phiếu nhập
    public function create()
    {
        $productsByBrand = Product::all()->groupBy('brand');
        $suppliers = Supplier::all();

        return view('admin.goods_receipts.create', compact('productsByBrand', 'suppliers'));
    }

    // Lưu phiếu nhập nháp
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'new_supplier_name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $supplierId = $request->supplier_id;
            if (empty($supplierId) && ! empty($request->new_supplier_name)) {
                $supplier = Supplier::create(['name' => $request->new_supplier_name]);
                $supplierId = $supplier->id;
            }

            // Tạo phiếu nhập mới
            $receipt = GoodsReceipt::create([
                'receipt_code' => 'PNK-'.date('YmdHis').'-'.rand(100, 999),
                'supplier_id' => $supplierId,
                'user_id' => auth()->id(),
                'note' => $request->note,
                'status' => 'draft', // Lưu nháp trước, chưa cộng kho
            ]);

            $totalAmount = 0;

            // Thêm chi tiết phiếu nhập
            foreach ($request->products as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $totalAmount += $totalPrice;

                GoodsReceiptDetail::create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }

            // Cập nhật tổng tiền
            $receipt->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('admin.goods_receipts.show', $receipt)->with('success', 'Đã tạo phiếu nhập nháp.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Lỗi tạo phiếu nhập: '.$e->getMessage());
        }
    }

    // Xem chi tiết và duyệt phiếu nhập
    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load('details.product', 'supplier', 'user');

        return view('admin.goods_receipts.show', compact('goodsReceipt'));
    }

    // DUYỆT PHIẾU NHẬP - LOGIC QUAN TRỌNG NHẤT
    public function complete(GoodsReceipt $goodsReceipt)
    {
        DB::beginTransaction();
        try {
            // Khóa bản ghi phiếu nhập để chống click lặp đồng thời
            $receipt = GoodsReceipt::lockForUpdate()->findOrFail($goodsReceipt->id);

            if ($receipt->status === 'completed') {
                DB::rollBack();

                return back()->with('error', 'Phiếu nhập này đã được hoàn thành rồi.');
            }

            // 1. Chuyển trạng thái phiếu nhập thành hoàn thành
            $receipt->update(['status' => 'completed']);

            // 2. Chạy vòng lặp cộng số lượng tồn kho và ghi lịch sử (Thẻ kho)
            foreach ($receipt->details as $detail) {
                // Khóa bản ghi sản phẩm trước khi tăng tồn kho
                $product = Product::lockForUpdate()->findOrFail($detail->product_id);

                // Cộng dồn kho
                $product->increment('stock', $detail->quantity);

                // Ghi nhận lịch sử biến động kho (Thẻ kho)
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'in', // Nhập kho
                    'quantity' => $detail->quantity,
                    'reference_type' => GoodsReceipt::class,
                    'reference_id' => $receipt->id,
                    'note' => 'Nhập kho từ phiếu '.$receipt->receipt_code,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Đã duyệt phiếu nhập thành công. Tồn kho đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra khi duyệt: '.$e->getMessage());
        }
    }
}
