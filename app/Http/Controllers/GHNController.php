<?php

namespace App\Http\Controllers;

use App\Services\GHNService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    public function __construct(protected GHNService $ghn) {}

    /** GET /ghn/provinces */
    public function provinces(): JsonResponse
    {
        $result = $this->ghn->getProvinces();
        return response()->json($result);
    }

    /** GET /ghn/districts?province_id=xxx (dùng nội bộ) */
    public function districts(Request $request): JsonResponse
    {
        $request->validate(['province_id' => 'required|integer']);
        $result = $this->ghn->getDistricts((int) $request->province_id);
        return response()->json($result);
    }

    /** GET /ghn/wards?district_id=xxx (dùng nội bộ) */
    public function wards(Request $request): JsonResponse
    {
        $request->validate(['district_id' => 'required|integer']);
        $result = $this->ghn->getWards((int) $request->district_id);
        return response()->json($result);
    }

    /**
     * GET /ghn/wards-by-province?province_id=xxx
     *
     * Lấy tất cả Phường/Xã của một tỉnh mà không cần qua cấp Quận/Huyện.
     * Áp dụng theo đơn vị hành chính Việt Nam mới (bỏ cấp Quận/Huyện từ 1/7/2025).
     * District vẫn được gắn kèm vào mỗi Phường/Xã để gửi GHN nhưng ẩn khỏi UI người dùng.
     */
    public function wardsByProvince(Request $request): JsonResponse
    {
        $request->validate(['province_id' => 'required|integer']);
        $provinceId = (int) $request->province_id;

        // Bước 1: lấy danh sách quận/huyện (ẩn trên UI)
        $districtResult = $this->ghn->getDistricts($provinceId);
        if (!$districtResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải dữ liệu địa chính.',
                'data'    => [],
            ]);
        }

        $districts = $districtResult['data'];
        $allWards  = [];

        // Bước 2: lấy phường/xã của từng quận/huyện
        foreach ($districts as $district) {
            $wardResult = $this->ghn->getWards((int) $district['DistrictID']);
            if (!$wardResult['success'] || empty($wardResult['data'])) {
                continue;
            }

            foreach ($wardResult['data'] as $ward) {
                $allWards[] = [
                    'WardCode'    => $ward['WardCode'],
                    'WardName'    => $ward['WardName'],
                    // Gắn kèm quận/huyện để dùng nội bộ khi gửi GHN
                    'DistrictID'  => $district['DistrictID'],
                    'DistrictName'=> $district['DistrictName'],
                ];
            }
        }

        // Sắp xếp theo tên
        usort($allWards, fn($a, $b) => strcmp($a['WardName'], $b['WardName']));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $allWards]);
    }

    /** POST /ghn/calculate-fee */
    public function calculateFee(Request $request): JsonResponse
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
            'weight'         => 'nullable|integer|min:1',
        ]);

        $result = $this->ghn->calculateShippingFee(
            (int) $request->to_district_id,
            $request->to_ward_code,
            (int) ($request->weight ?? 500)
        );

        return response()->json($result);
    }
}
