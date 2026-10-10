<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsIssue;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsIssueController extends Controller
{
    public function index()
    {
        $issues = GoodsIssue::with('user', 'order')->latest()->paginate(15);

        return view('admin.goods_issues.index', compact('issues'));
    }

    public function create()
    {
        $products = Product::all();

        return view('admin.goods_issues.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:warranty,destroy,other',
            'note' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $issue = GoodsIssue::create([
                'user_id' => auth()->id(),
                'type' => $request->type,
                'status' => 'completed', // Xử lý trừ luôn
                'note' => $request->note,
            ]);

            foreach ($request->products as $item) {
                // Khóa bản ghi sản phẩm để chống race condition khi nhiều nhân sự xuất kho đồng thời
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                // Trừ tồn kho
                if ($product->stock < $item['quantity']) {
                    throw new \Exception('Sản phẩm '.$product->name.' không đủ số lượng tồn kho để xuất (hiện có: '.$product->stock.', yêu cầu: '.$item['quantity'].').');
                }

                $product->decrement('stock', $item['quantity']);

                // Lưu chi tiết
                $issue->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);

                // Ghi nhận thẻ kho
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => GoodsIssue::class,
                    'reference_id' => $issue->id,
                    'note' => 'Xuất kho thủ công ('.$request->type.'): '.$request->note,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.goods_issues.index')->with('success', 'Đã tạo phiếu xuất kho và trừ tồn kho!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
