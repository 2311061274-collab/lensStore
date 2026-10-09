<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected string $baseUrl        = '';
    protected string $token          = '';
    protected int    $shopId         = 0;
    protected bool   $verifySsl      = false;
    protected int    $fromDistrictId = 0;

    public function __construct()
    {
        $this->baseUrl        = (string) (config('services.ghn.base_url') ?? 'https://dev-online-gateway.ghn.vn/shiip/public-api');
        $this->token          = (string) (config('services.ghn.token') ?? '');
        $this->shopId         = (int) config('services.ghn.shop_id', 0);
        $this->verifySsl      = (bool) config('services.ghn.verify_ssl', false);
        $this->fromDistrictId = (int) config('services.ghn.from_district_id', 0);
    }

    /**
     * Kiểm tra xem dịch vụ GHN đã được cấu hình Token hay chưa.
     */
    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /* ------------------------------------------------------------------ */
    /*  Helper: tạo HTTP client với header chung                            */
    /* ------------------------------------------------------------------ */
    private function client(bool $withShopId = false)
    {
        $headers = [
            'Token'        => $this->token,
            'Content-Type' => 'application/json',
        ];

        if ($withShopId) {
            $headers['ShopId'] = $this->shopId;
        }

        // Tăng timeout lên 30s để tránh sập nhanh khi server GHN chậm
        return Http::timeout(30)
                   ->withOptions(['verify' => $this->verifySsl])
                   ->withHeaders($headers);
    }

    /* ------------------------------------------------------------------ */
    /*  Địa chỉ hành chính                                                  */
    /* ------------------------------------------------------------------ */

    public function getProvinces(): array
    {
        try {
            $resp = $this->client()->get("{$this->baseUrl}/master-data/province");
            return $this->parseResponse($resp, 'getProvinces');
        } catch (\Exception $e) {
            return $this->handleException($e, 'getProvinces');
        }
    }

    public function getDistricts(int $provinceId): array
    {
        try {
            $resp = $this->client()->post("{$this->baseUrl}/master-data/district", [
                'province_id' => $provinceId,
            ]);
            return $this->parseResponse($resp, 'getDistricts');
        } catch (\Exception $e) {
            return $this->handleException($e, 'getDistricts');
        }
    }

    public function getWards(int $districtId): array
    {
        try {
            $resp = $this->client()->post("{$this->baseUrl}/master-data/ward", [
                'district_id' => $districtId,
            ]);
            return $this->parseResponse($resp, 'getWards');
        } catch (\Exception $e) {
            return $this->handleException($e, 'getWards');
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Phí vận chuyển                                                      */
    /* ------------------------------------------------------------------ */

    public function calculateShippingFee(
        int    $toDistrictId,
        string $toWardCode,
        int    $weight       = 500,
        int    $serviceTypeId = 2
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => true,
                'message' => 'Dịch vụ GHN chưa cấu hình token, áp dụng phí ship cố định.',
                'data'    => ['total' => 30000],
            ];
        }

        try {
            $resp = $this->client(withShopId: true)
                         ->post("{$this->baseUrl}/v2/shipping-order/fee", [
                             'service_type_id'  => $serviceTypeId,
                             'from_district_id' => $this->fromDistrictId,
                             'to_district_id'   => $toDistrictId,
                             'to_ward_code'     => $toWardCode,
                             'weight'           => $weight,
                         ]);

            $result = $this->parseResponse($resp, 'calculateShippingFee');
            
            // Nếu lỗi từ API nhưng không throw exception (ví dụ API trả về success = false)
            // Ta set phí fallback là 30,000 để user vẫn đặt được hàng.
            if (!$result['success']) {
                $result['success'] = true;
                $result['data'] = ['total' => 30000]; 
            }
            return $result;

        } catch (\Exception $e) {
            // Lỗi mạng/timeout -> Trả về phí mặc định 30k
            return [
                'success' => true, 
                'message' => 'Lỗi kết nối GHN, sử dụng phí ship mặc định.', 
                'data' => ['total' => 30000]
            ];
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Tạo / Hủy đơn hàng GHN                                             */
    /* ------------------------------------------------------------------ */

    public function createOrder(array $payload): array
    {
        try {
            $resp = $this->client(withShopId: true)
                         ->post("{$this->baseUrl}/v2/shipping-order/create", $payload);
            return $this->parseResponse($resp, 'createOrder');
        } catch (\Exception $e) {
            return $this->handleException($e, 'createOrder');
        }
    }

    public function cancelOrder(string $orderCode): array
    {
        try {
            $resp = $this->client(withShopId: true)
                         ->post("{$this->baseUrl}/v2/shipping-order/cancel", [
                             'order_codes' => [$orderCode],
                         ]);
            return $this->parseResponse($resp, 'cancelOrder');
        } catch (\Exception $e) {
            return $this->handleException($e, 'cancelOrder');
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Parse response                                                       */
    /* ------------------------------------------------------------------ */
    private function parseResponse($resp, string $context): array
    {
        if ($resp->failed()) {
            Log::error("GHNService [{$context}] HTTP error", [
                'status' => $resp->status(),
                'body'   => $resp->body(),
            ]);
            return ['success' => false, 'message' => 'Lỗi kết nối GHN API.', 'data' => []];
        }

        $json = $resp->json();

        if (($json['code'] ?? null) !== 200) {
            Log::warning("GHNService [{$context}] API error", $json);
            return [
                'success' => false,
                'message' => $json['message'] ?? 'GHN API trả về lỗi.',
                'data'    => [],
            ];
        }

        return ['success' => true, 'message' => 'OK', 'data' => $json['data'] ?? []];
    }

    private function handleException(\Exception $e, string $context): array
    {
        Log::error("GHNService [{$context}] Exception", [
            'message' => $e->getMessage(),
        ]);
        return ['success' => false, 'message' => 'Lỗi kết nối máy chủ GHN (Timeout).', 'data' => []];
    }
}
