<?php

namespace App\Support;

class PermissionCatalog
{
    /**
     * Nhóm quyền theo nghiệp vụ (thứ tự hiển thị).
     */
    public static function groups(): array
    {
        return [
            'overview' => [
                'title' => 'Tổng quan',
                'desc' => 'Trang điều khiển & báo cáo',
                'icon' => 'fa-chart-pie',
                'perms' => ['view_dashboard', 'view_reports'],
            ],
            'commerce' => [
                'title' => 'Kinh doanh',
                'desc' => 'Đơn hàng, sản phẩm, danh mục',
                'icon' => 'fa-store',
                'perms' => ['manage_orders', 'manage_products', 'manage_categories'],
            ],
            'inventory' => [
                'title' => 'Quản lý Kho hàng',
                'desc' => 'Nhập xuất kho & kiểm định',
                'icon' => 'fa-warehouse',
                'perms' => ['manage_goods_receipts', 'manage_goods_issues', 'manage_qc_inspections'],
            ],
            'crm' => [
                'title' => 'Khách hàng & Marketing',
                'desc' => 'CRM và mã giảm giá',
                'icon' => 'fa-users',
                'perms' => ['manage_customers', 'manage_vouchers'],
            ],
            'content' => [
                'title' => 'Nội dung',
                'desc' => 'Quản lý bài viết và tin tức',
                'icon' => 'fa-newspaper',
                'perms' => ['manage_news', 'view_news', 'create_news', 'edit_news', 'delete_news'],
            ],
            'system' => [
                'title' => 'Hệ thống',
                'desc' => 'Tài khoản & phân quyền — quyền nhạy cảm',
                'icon' => 'fa-shield-halved',
                'tone' => 'danger',
                'perms' => ['manage_users', 'manage_roles'],
            ],
        ];
    }

    public static function meta(): array
    {
        return [
            'view_dashboard' => [
                'label' => 'Xem Dashboard',
                'icon' => 'fa-chart-pie',
                'desc' => 'Truy cập trang tổng quan',
                'color' => '#1a7a6d',
            ],
            'view_reports' => [
                'label' => 'Xem Báo cáo',
                'icon' => 'fa-chart-column',
                'desc' => 'Thống kê doanh thu, xuất CSV',
                'color' => '#c45c26',
            ],
            'manage_orders' => [
                'label' => 'Quản lý Đơn hàng',
                'icon' => 'fa-boxes-packing',
                'desc' => 'Xem & cập nhật trạng thái / GHN',
                'color' => '#b45309',
            ],
            'manage_products' => [
                'label' => 'Quản lý Sản phẩm',
                'icon' => 'fa-camera',
                'desc' => 'Thêm, sửa, xóa ống kính',
                'color' => '#1b7a4a',
            ],
            'manage_categories' => [
                'label' => 'Quản lý Danh mục',
                'icon' => 'fa-layer-group',
                'desc' => 'Thêm, sửa, xóa danh mục',
                'color' => '#2b5ea8',
            ],
            'manage_goods_receipts' => [
                'label' => 'Quản lý Nhập kho',
                'icon' => 'fa-truck-ramp-box',
                'desc' => 'Thêm và quản lý phiếu nhập kho',
                'color' => '#8e44ad',
            ],
            'manage_goods_issues' => [
                'label' => 'Quản lý Xuất kho',
                'icon' => 'fa-arrow-right-from-bracket',
                'desc' => 'Thêm và quản lý phiếu xuất kho',
                'color' => '#9b59b6',
            ],
            'manage_qc_inspections' => [
                'label' => 'Kiểm định Hàng trả (QC)',
                'icon' => 'fa-microscope',
                'desc' => 'Kiểm tra và đánh giá hàng hoàn về',
                'color' => '#2980b9',
            ],
            'manage_customers' => [
                'label' => 'Quản lý Khách hàng',
                'icon' => 'fa-address-card',
                'desc' => 'Hồ sơ CRM & ghi chú tư vấn',
                'color' => '#5b4b8a',
            ],
            'manage_vouchers' => [
                'label' => 'Quản lý Voucher',
                'icon' => 'fa-ticket',
                'desc' => 'Tạo và quản lý mã giảm giá',
                'color' => '#a84c1d',
            ],
            'manage_users' => [
                'label' => 'Quản lý Tài khoản',
                'icon' => 'fa-user-group',
                'desc' => 'Xem, thêm, sửa tài khoản nhân sự',
                'color' => '#0f766e',
            ],
            'manage_roles' => [
                'label' => 'Phân quyền Hệ thống',
                'icon' => 'fa-shield-halved',
                'desc' => 'Tạo chức vụ & gán quyền',
                'color' => '#c0352b',
            ],
            'manage_news' => [
                'label' => 'Quản lý Tin tức',
                'icon' => 'fa-newspaper',
                'desc' => 'Toàn quyền quản lý tin tức',
                'color' => '#d35400',
            ],
            'view_news' => [
                'label' => 'Xem Tin tức',
                'icon' => 'fa-eye',
                'desc' => 'Xem danh sách bài viết',
                'color' => '#f39c12',
            ],
            'create_news' => [
                'label' => 'Thêm Tin tức',
                'icon' => 'fa-plus',
                'desc' => 'Đăng bài viết mới',
                'color' => '#27ae60',
            ],
            'edit_news' => [
                'label' => 'Sửa Tin tức',
                'icon' => 'fa-pen',
                'desc' => 'Cập nhật nội dung bài viết',
                'color' => '#2980b9',
            ],
            'delete_news' => [
                'label' => 'Xóa Tin tức',
                'icon' => 'fa-trash',
                'desc' => 'Xóa bài viết',
                'color' => '#c0392b',
            ],
        ];
    }

    public static function label(string $name): array
    {
        return self::meta()[$name] ?? [
            'label' => $name,
            'icon' => 'fa-key',
            'desc' => '',
            'color' => '#7a8494',
        ];
    }
}
