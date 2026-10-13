<?php

use App\Models\GoodsIssue;
use App\Models\GoodsReceipt;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Đồng bộ và đối soát dữ liệu Sổ cái biến động kho (inventory_transactions)
     * Bảo đảm Idempotent, không xóa lịch sử, sử dụng DB transaction.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Nạp bổ sung Thẻ kho cho Phiếu Nhập Kho chốt sổ (PNK-20261001093440-SYNC - Receipt #4)
            $syncReceipt = GoodsReceipt::with('details')->find(4);
            if ($syncReceipt) {
                foreach ($syncReceipt->details as $detail) {
                    $exists = InventoryTransaction::where('reference_type', GoodsReceipt::class)
                        ->where('reference_id', $syncReceipt->id)
                        ->where('product_id', $detail->product_id)
                        ->exists();

                    if (! $exists) {
                        InventoryTransaction::create([
                            'product_id' => $detail->product_id,
                            'type' => 'in',
                            'quantity' => $detail->quantity,
                            'reference_type' => GoodsReceipt::class,
                            'reference_id' => $syncReceipt->id,
                            'note' => 'Nhập kho chốt sổ ban đầu (Phiếu '.$syncReceipt->receipt_code.')',
                            'created_at' => $syncReceipt->created_at ?? now(),
                            'updated_at' => $syncReceipt->updated_at ?? now(),
                        ]);
                    }
                }
            }

            // 2. Nạp bổ sung Thẻ kho cho các Phiếu Xuất Kho bán hàng (GoodsIssue #1..#9)
            $completedIssues = GoodsIssue::with('details', 'order')
                ->where('type', 'sale')
                ->where('status', 'completed')
                ->get();

            foreach ($completedIssues as $issue) {
                foreach ($issue->details as $detail) {
                    $exists = InventoryTransaction::where('reference_type', GoodsIssue::class)
                        ->where('reference_id', $issue->id)
                        ->where('product_id', $detail->product_id)
                        ->exists();

                    if (! $exists) {
                        $orderCode = $issue->order ? ($issue->order->order_code ?? '#'.$issue->order_id) : '#'.$issue->order_id;
                        InventoryTransaction::create([
                            'product_id' => $detail->product_id,
                            'type' => 'out',
                            'quantity' => $detail->quantity,
                            'reference_type' => GoodsIssue::class,
                            'reference_id' => $issue->id,
                            'note' => 'Xuất bán Đơn hàng '.$orderCode.' (Phiếu '.$issue->issue_code.')',
                            'created_at' => $issue->created_at ?? now(),
                            'updated_at' => $issue->updated_at ?? now(),
                        ]);
                    }
                }
            }

            // 3. Ghi nhận giao dịch kiểm kê điều chỉnh số dư đầu kỳ (type = 'adjustment')
            // cho 10 sản phẩm có độ lệch do phiếu chốt sổ PNK-SYNC
            $adjustments = [
                // 7 sản phẩm có phiếu xuất bán hoàn tất trước khi chốt sổ tồn trên kệ
                1 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609170004',
                ],
                2 => [
                    'qty' => 3,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609160001 (x2) & #LS2609170002 (x1)',
                ],
                5 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609170005',
                ],
                8 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609290010',
                ],
                11 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609170006',
                ],
                12 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609170007',
                ],
                13 => [
                    'qty' => 1,
                    'note' => 'Kiểm kê: Điều chỉnh cân đối số dư đầu kỳ do phiếu chốt sổ PNK-SYNC ghi nhận sau khi đã xuất bán Đơn #LS2609300012',
                ],
                // 3 sản phẩm bị trùng lặp số lượng giữa phiếu nhập lẻ và phiếu chốt sổ toàn bộ
                4 => [
                    'qty' => -3,
                    'note' => 'Kiểm kê: Điều chỉnh giảm số lượng nhập trùng lặp giữa phiếu nhập đơn lẻ PNK-20261001091029 và phiếu chốt sổ PNK-SYNC',
                ],
                9 => [
                    'qty' => -4,
                    'note' => 'Kiểm kê: Điều chỉnh giảm số lượng nhập trùng lặp giữa phiếu nhập đơn lẻ PNK-20261001090950 và phiếu chốt sổ PNK-SYNC',
                ],
                24 => [
                    'qty' => -1,
                    'note' => 'Kiểm kê: Điều chỉnh giảm số lượng nhập trùng lặp giữa phiếu nhập đơn lẻ PNK-20261001091313 và phiếu chốt sổ PNK-SYNC',
                ],
            ];

            foreach ($adjustments as $productId => $adj) {
                // Chỉ áp dụng khi sản phẩm tồn tại trong CSDL (bảo đảm an toàn ngoại khóa cả môi trường test)
                if (! Product::where('id', $productId)->exists()) {
                    continue;
                }

                $alreadyAdjusted = InventoryTransaction::where('product_id', $productId)
                    ->where('type', 'adjustment')
                    ->where('note', 'like', 'Kiểm kê: Điều chỉnh%')
                    ->exists();

                if (! $alreadyAdjusted) {
                    InventoryTransaction::create([
                        'product_id' => $productId,
                        'type' => 'adjustment',
                        'quantity' => $adj['qty'],
                        'reference_type' => Product::class,
                        'reference_id' => $productId,
                        'note' => $adj['note'],
                        'created_at' => $syncReceipt ? $syncReceipt->created_at : now(),
                        'updated_at' => $syncReceipt ? $syncReceipt->updated_at : now(),
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Có thể rollback các bản ghi đồng bộ đã tạo nếu cần
        InventoryTransaction::where('note', 'like', 'Kiểm kê: Điều chỉnh%')
            ->orWhere('note', 'like', 'Nhập kho chốt sổ ban đầu%')
            ->orWhere('note', 'like', 'Xuất bán Đơn hàng%')
            ->delete();
    }
};
