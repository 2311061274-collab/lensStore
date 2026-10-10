<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherApiController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $subtotal = (float) $request->subtotal;

        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.',
            ]);
        }

        if (! $voucher->isValidForOrder($subtotal)) {
            $msg = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.';
            if ($voucher->min_order_value > 0 && $subtotal < $voucher->min_order_value) {
                $msg = 'Đơn hàng chưa đạt giá trị tối thiểu '.number_format($voucher->min_order_value, 0, ',', '.').'đ để áp dụng mã này.';
            }

            return response()->json([
                'success' => false,
                'message' => $msg,
            ]);
        }

        $discount = $voucher->calculateDiscount($subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'data' => [
                'code' => $voucher->code,
                'discount_amount' => $discount,
            ],
        ]);
    }
}
