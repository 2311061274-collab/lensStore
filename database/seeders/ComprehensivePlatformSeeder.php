<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\GoodsIssue;
use App\Models\GoodsIssueDetail;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\QcInspection;
use App\Models\Voucher;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Support\PermissionCatalog;

class ComprehensivePlatformSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Khởi tạo toàn bộ Quyền hạn (Permissions) & Vai trò (Roles)
        $allPermNames = [];
        foreach (PermissionCatalog::groups() as $group) {
            foreach ($group['perms'] as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
                $allPermNames[] = $perm;
            }
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($allPermNames);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions([
            'view_dashboard',
            'manage_orders',
            'manage_products',
            'manage_categories',
            'manage_goods_receipts',
            'manage_goods_issues',
            'manage_qc_inspections',
        ]);

        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // 2. Tài khoản Admin & Khách hàng
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0987654321',
                'cccd' => '001200000001',
                'birthday' => '1990-01-01',
                'gender' => 'male',
                'address' => 'Hà Nội, Việt Nam',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        $customer = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Khách Hàng Mẫu',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0912345678',
                'cccd' => '001200000002',
                'birthday' => '1995-05-15',
                'gender' => 'female',
                'address' => 'Quận 1, TP. Hồ Chí Minh',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $customer->assignRole('customer');

        // 3. Vouchers
        Voucher::firstOrCreate(
            ['code' => 'LENS10'],
            [
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 5000000,
                'max_discount_value' => 2000000,
                'usage_limit' => 100,
                'used_count' => 0,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'FREESHIP'],
            [
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_value' => 1000000,
                'usage_limit' => 500,
                'used_count' => 0,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ]
        );

        // 4. Nhà cung cấp (Supplier)
        $supplier = Supplier::firstOrCreate(
            ['email' => 'contact@sony.com.vn'],
            [
                'name' => 'Sony Electronics Việt Nam',
                'phone' => '02838222222',
                'address' => 'Tầng 6, President Place, 93 Nguyễn Du, P. Bến Nghé, Quận 1, TP. HCM',
            ]
        );

        // 5. Đơn hàng mẫu
        $products = Product::take(3)->get();
        if ($products->isNotEmpty()) {
            $p1 = $products[0];
            $p2 = $products[1] ?? $products[0];

            $order1 = Order::firstOrCreate(
                ['order_code' => 'LS-' . date('Ymd') . '-001'],
                [
                    'user_id' => $customer->id,
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone,
                    'province_id' => 202,
                    'province_name' => 'TP. Hồ Chí Minh',
                    'district_id' => 1442,
                    'district_name' => 'Quận 1',
                    'ward_code' => '20101',
                    'ward_name' => 'Phường Bến Nghé',
                    'address_detail' => '123 Đường Lê Lợi',
                    'shipping_fee' => 30000,
                    'subtotal' => $p1->price,
                    'total' => $p1->price + 30000,
                    'payment_method' => 'cod',
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'ghn_order_code' => 'GHN123456',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order1->id, 'product_id' => $p1->id],
                [
                    'product_name' => $p1->name,
                    'unit_price' => $p1->price,
                    'quantity' => 1,
                    'subtotal' => $p1->price,
                ]
            );

            $order2 = Order::firstOrCreate(
                ['order_code' => 'LS-' . date('Ymd') . '-002'],
                [
                    'user_id' => $customer->id,
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone,
                    'province_id' => 201,
                    'province_name' => 'Hà Nội',
                    'district_id' => 1482,
                    'district_name' => 'Quận Ba Đình',
                    'ward_code' => '11001',
                    'ward_name' => 'Phường Điện Biên',
                    'address_detail' => '45 Hoàng Diệu',
                    'shipping_fee' => 35000,
                    'subtotal' => $p2->price,
                    'total' => $p2->price + 35000,
                    'payment_method' => 'momo',
                    'status' => 'delivering',
                    'payment_status' => 'paid',
                    'ghn_order_code' => 'GHN789012',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order2->id, 'product_id' => $p2->id],
                [
                    'product_name' => $p2->name,
                    'unit_price' => $p2->price,
                    'quantity' => 1,
                    'subtotal' => $p2->price,
                ]
            );

            $order3 = Order::firstOrCreate(
                ['order_code' => 'LS-' . date('Ymd') . '-003'],
                [
                    'user_id' => $customer->id,
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone,
                    'province_id' => 202,
                    'province_name' => 'TP. Hồ Chí Minh',
                    'district_id' => 1442,
                    'district_name' => 'Quận 1',
                    'ward_code' => '20101',
                    'ward_name' => 'Phường Bến Nghé',
                    'address_detail' => '88 Nguyễn Huệ',
                    'shipping_fee' => 30000,
                    'subtotal' => $p1->price,
                    'total' => $p1->price + 30000,
                    'payment_method' => 'cod',
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'ghn_order_code' => 'GHN345678',
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order3->id, 'product_id' => $p1->id],
                [
                    'product_name' => $p1->name,
                    'unit_price' => $p1->price,
                    'quantity' => 1,
                    'subtotal' => $p1->price,
                ]
            );

            // 6. Yêu cầu đổi trả
            $returnReq = ReturnRequest::firstOrCreate(
                ['order_id' => $order1->id],
                [
                    'user_id' => $customer->id,
                    'reason' => 'Đổi tiêu cự phù hợp hơn với nhu cầu công việc',
                    'note' => 'Ống kính nguyên seal chưa bóc seal, đầy đủ hộp và bảo hành.',
                    'status' => 'approved',
                    'tracking_code' => 'RET-10023',
                ]
            );

            // 7. Đánh giá sản phẩm
            Review::firstOrCreate(
                ['user_id' => $customer->id, 'product_id' => $p1->id],
                [
                    'order_id' => $order1->id,
                    'rating' => 5,
                    'comment' => 'Ống kính chụp cực kỳ sắc nét, lấy nét êm ái nhanh chóng, đóng gói cẩn thận!',
                    'is_visible' => true,
                ]
            );

            // 8. Phiếu nhập kho
            $gr = GoodsReceipt::firstOrCreate(
                ['receipt_code' => 'PNK-' . date('Ymd') . '-001'],
                [
                    'supplier_id' => $supplier->id,
                    'user_id' => $admin->id,
                    'total_amount' => $p1->price * 5,
                    'note' => 'Nhập kho lô ống kính chính hãng Sony Việt Nam quý 4',
                    'status' => 'completed',
                ]
            );

            GoodsReceiptDetail::firstOrCreate(
                ['goods_receipt_id' => $gr->id, 'product_id' => $p1->id],
                [
                    'quantity' => 5,
                    'unit_price' => $p1->price * 0.8,
                    'subtotal' => $p1->price * 0.8 * 5,
                ]
            );

            // 9. Phiếu xuất kho
            $gi = GoodsIssue::firstOrCreate(
                ['issue_code' => 'PXK-' . date('Ymd') . '-001'],
                [
                    'order_id' => $order1->id,
                    'user_id' => $admin->id,
                    'type' => 'sale',
                    'status' => 'completed',
                    'note' => 'Xuất kho giao đơn hàng ' . $order1->order_code,
                ]
            );

            GoodsIssueDetail::firstOrCreate(
                ['goods_issue_id' => $gi->id, 'product_id' => $p1->id],
                [
                    'quantity' => 1,
                ]
            );

            // 10. Biên bản kiểm định chất lượng (QC)
            QcInspection::firstOrCreate(
                ['return_request_id' => $returnReq->id],
                [
                    'product_id' => $p1->id,
                    'user_id' => $admin->id,
                    'condition' => 'perfect',
                    'final_action' => 'restock',
                    'quantity' => 1,
                    'inspection_note' => 'Hàng mới 100%, nguyên tem niêm phong, kiểm định đạt chuẩn nhập lại kho.',
                ]
            );
        }
    }
}
