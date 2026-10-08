<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    public function __construct(protected MomoService $momo) {}

    /**
     * Thanh toán lại đơn hàng (khi lần trước thất bại / pending quá lâu).
     */
    public function payAgain(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($order->payment_status, ['paid'])) {
            return back()->with('error', 'Đơn hàng này đã được thanh toán rồi.');
        }

        $result = $this->momo->createPayment($order);

        if ($result['success']) {
            return redirect($result['pay_url']);
        }

        return back()->with('error', $result['message'] ?? 'Không thể kết nối đến MoMo. Vui lòng thử lại.');
    }

    /**
     * Người dùng được MoMo redirect về đây sau khi thanh toán.
     */
    public function callback(Request $request)
    {
        $data = $request->all();

        Log::info('MoMo Callback received', $data);

        $success = $this->momo->handleCallback($data);

        if ($success) {
            // Tìm order_id từ extra_data
            $orderId = $this->extractOrderId($data['extraData'] ?? '');

            return redirect()->route('orders.show', $orderId)
                ->with('success', 'Thanh toán MoMo thành công! Đơn hàng của bạn đang được xử lý.');
        }

        $orderId = $this->extractOrderId($data['extraData'] ?? '');
        $message = $data['message'] ?? 'Thanh toán không thành công.';

        return redirect()->route('orders.show', $orderId)
            ->with('error', 'Thanh toán thất bại: ' . $message);
    }

    /**
     * Webhook IPN từ server MoMo gửi ngầm để cập nhật trạng thái.
     */
    public function ipn(Request $request)
    {
        $data = $request->all();

        Log::info('MoMo IPN received', $data);

        $success = $this->momo->handleCallback($data);

        // MoMo yêu cầu trả về HTTP 200 dù thành công hay thất bại
        return response()->json([
            'partnerCode' => config('services.momo.partner_code'),
            'requestId'   => $data['requestId'] ?? '',
            'orderId'     => $data['orderId'] ?? '',
            'resultCode'  => $success ? 0 : 1,
            'message'     => $success ? 'Confirmed' : 'Failed',
            'responseTime' => now()->timestamp * 1000,
        ]);
    }

    // ─────────────────────────────────────────────────
    //  Private helpers
    // ─────────────────────────────────────────────────

    private function extractOrderId(string $extraData): ?int
    {
        if (!$extraData) return null;
        $decoded = json_decode(base64_decode($extraData), true);
        return $decoded['order_id'] ?? null;
    }
}
