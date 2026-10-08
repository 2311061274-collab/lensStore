<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MomoService
{
    private string $endpoint;
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private bool   $verifySSL;
    private string $redirectUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->endpoint     = config('services.momo.endpoint');
        $this->partnerCode  = config('services.momo.partner_code');
        $this->accessKey    = config('services.momo.access_key');
        $this->secretKey    = config('services.momo.secret_key');
        $this->verifySSL    = (bool) config('services.momo.verify_ssl', false);
        $this->redirectUrl  = config('services.momo.redirect_url');
        $this->ipnUrl       = config('services.momo.ipn_url');
    }

    /**
     * Tạo yêu cầu thanh toán và trả về payUrl để redirect khách hàng sang MoMo.
     */
    public function createPayment(Order $order): array
    {
        $requestId    = $this->partnerCode . '_' . $order->id . '_' . time();
        $orderId      = $this->partnerCode . '_ORDER_' . $order->id . '_' . time();
        $orderInfo    = 'Thanh toán đơn hàng LensStore #' . $order->id;
        $amount       = (int) round($order->total);
        $requestType  = 'captureWallet'; // Dùng QR / App MoMo (thay vì thẻ ATM)
        $extraData    = base64_encode(json_encode(['order_id' => $order->id]));

        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$this->ipnUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$this->redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'accessKey'   => $this->accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl'      => $this->ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        // Lưu giao dịch chờ thanh toán
        PaymentTransaction::create([
            'order_id'         => $order->id,
            'gateway'          => 'momo',
            'gateway_order_id' => $orderId,
            'amount'           => $amount,
            'status'           => 'pending',
            'request_payload'  => $payload,
        ]);

        $response = $this->post($this->endpoint, $payload);

        if (isset($response['payUrl'])) {
            // Lưu response
            PaymentTransaction::where('gateway_order_id', $orderId)
                ->update(['response_payload' => $response]);

            return ['success' => true, 'pay_url' => $response['payUrl']];
        }

        Log::error('MoMo createPayment failed', ['response' => $response]);

        return ['success' => false, 'message' => $response['message'] ?? 'Không thể kết nối đến MoMo.'];
    }

    /**
     * Xử lý dữ liệu callback/IPN từ MoMo gửi về.
     */
    public function handleCallback(array $data): bool
    {
        if (!$this->isValidSignature($data)) {
            Log::warning('MoMo invalid signature', $data);
            return false;
        }

        $transaction = PaymentTransaction::where('gateway_order_id', $data['orderId'])->first();
        if (!$transaction) {
            Log::warning('MoMo transaction not found', ['orderId' => $data['orderId']]);
            return false;
        }

        $transaction->update([
            'transaction_id'   => $data['transId'] ?? null,
            'result_code'      => $data['resultCode'],
            'message'          => $data['message'] ?? null,
            'response_payload' => $data,
        ]);

        if ((int) $data['resultCode'] === 0) {
            // Thành công
            $transaction->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);
            $transaction->order->update(['payment_status' => 'paid']);

            return true;
        }

        // Thất bại
        $transaction->update(['status' => 'failed']);
        return false;
    }

    /**
     * Xác thực chữ ký trả về từ MoMo.
     */
    public function isValidSignature(array $data): bool
    {
        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $expected = hash_hmac('sha256', $rawSignature, $this->secretKey);

        return hash_equals($expected, $data['signature'] ?? '');
    }

    /**
     * Gửi POST request JSON đến MoMo endpoint.
     */
    private function post(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => $this->verifySSL,
            CURLOPT_SSL_VERIFYHOST => $this->verifySSL ? 2 : 0,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $result = curl_exec($ch);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error('MoMo cURL error: ' . $err);
            return ['error' => $err];
        }

        return json_decode($result, true) ?? [];
    }
}
