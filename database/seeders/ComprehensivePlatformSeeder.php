<?php

namespace Database\Seeders;

use App\Models\GoodsIssue;
use App\Models\GoodsIssueDetail;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\News;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\QcInspection;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Voucher;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
                ['order_code' => 'LS-'.date('Ymd').'-001'],
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
                ['order_code' => 'LS-'.date('Ymd').'-002'],
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
                ['order_code' => 'LS-'.date('Ymd').'-003'],
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
                ['receipt_code' => 'PNK-'.date('Ymd').'-001'],
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
                ['issue_code' => 'PXK-'.date('Ymd').'-001'],
                [
                    'order_id' => $order1->id,
                    'user_id' => $admin->id,
                    'type' => 'sale',
                    'status' => 'completed',
                    'note' => 'Xuất kho giao đơn hàng '.$order1->order_code,
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

        // 11. Tin tức và bài viết nhiếp ảnh với hình ảnh có sẵn trong dự án
        $sampleNews = [
            [
                'title' => 'Canon RF 50mm f/1.2L USM — Tiêu chuẩn mới cho ống kính 50mm',
                'description' => 'Canon đã định nghĩa lại tiêu chuẩn cho ống kính tiêu cự chuẩn với chiếc RF 50mm f/1.2L. Khẩu độ cực lớn, độ nét tuyệt vời và khả năng bokeh mê hoặc.',
                'content' => '<p>Canon RF 50mm f/1.2L USM là một trong những ống kính tiêu cự cố định cao cấp nhất dành cho hệ máy không gương lật Canon EOS R.</p><p>Ống kính trang bị hệ thống thấu kính phi cầu và kính tán xạ cực thấp UD, triệt tiêu quang sai và biến dạng màu sắc tối đa.</p>',
                'image_url' => '/uploads/products/canon_rf_50_f12.jpg',
                'tag' => 'review',
                'tag_text' => 'Review',
                'author' => 'Lê Hương',
                'read_time' => 10,
                'is_featured' => true,
            ],
            [
                'title' => 'Sony FE 85mm f/1.4 GM II — Ống kính chân dung xuất sắc nhất 2026',
                'description' => 'Sony nâng cấp dòng ống kính chân dung huyền thoại với kích thước nhỏ gọn hơn, động cơ lấy nét XD Linear Motor siêu nhanh và bokeh mượt mà.',
                'content' => '<p>Sony FE 85mm f/1.4 GM II đem lại hiệu năng vượt trội cho các nhiếp ảnh gia chụp ảnh cưới và chân dung thương mại.</p>',
                'image_url' => '/uploads/products/sony_50_f12_gm.jpg',
                'tag' => 'review',
                'tag_text' => 'Review',
                'author' => 'Admin',
                'read_time' => 8,
                'is_featured' => false,
            ],
            [
                'title' => '7 kỹ thuật bố cục ảnh đẹp mà mọi nhiếp ảnh gia cần biết',
                'description' => 'Nắm vững quy tắc một phần ba, đường dẫn hướng, khung lồng khung và sự cân bằng thị giác để tạo nên những tác phẩm nhiếp ảnh có chiều sâu.',
                'content' => '<p>Bố cục là linh hồn của bức ảnh. Hãy cùng LensStore khám phá 7 kỹ thuật cốt lõi giúp ảnh của bạn thu hút người nhìn hơn.</p>',
                'image_url' => '/uploads/products/canon_rf_24_70.jpg',
                'tag' => 'tips',
                'tag_text' => 'Mẹo',
                'author' => 'Admin',
                'read_time' => 6,
                'is_featured' => false,
            ],
            [
                'title' => 'Sigma 35mm f/1.4 Art vs Sony FE 35mm f/1.4 GM — Ai thắng?',
                'description' => 'So sánh chi tiết giữa hai đại diện xuất sắc nhất ở tiêu cự 35mm đa dụng cho các dòng máy ảnh Full-Frame Sony E-mount.',
                'content' => '<p>Cả hai ống kính đều mang lại chất lượng quang học đỉnh cao, nhưng mỗi ống kính lại có ưu thế riêng về trọng lượng và tốc độ lấy nét.</p>',
                'image_url' => '/uploads/products/sigma_24_70_art2.jpg',
                'tag' => 'compare',
                'tag_text' => 'So sánh',
                'author' => 'Admin',
                'read_time' => 7,
                'is_featured' => false,
            ],
            [
                'title' => 'Nikon Z 135mm f/1.8 S Plena — Ống kính chân dung đáng mong đợi nhất năm',
                'description' => 'Chiếc ống kính đặc biệt với danh hiệu Plena mang lại chất lượng vòng bokeh tròn tuyệt đối từ tâm đến tận rìa bức ảnh.',
                'content' => '<p>Nikon khẳng định vị thế quang học đỉnh cao với dòng ống kính Z Plena chuyên biệt cho bokeh và độ phân giải cao.</p>',
                'image_url' => '/uploads/products/nikon_z_85_f12.jpg',
                'tag' => 'review',
                'tag_text' => 'Review',
                'author' => 'Admin',
                'read_time' => 9,
                'is_featured' => false,
            ],
            [
                'title' => 'Cách chọn ống kính phù hợp với thể loại nhiếp ảnh của bạn',
                'description' => 'Phong cảnh, chân dung, macro, sự kiện hay đường phố? Hướng dẫn toàn diện giúp bạn đầu tư tiêu cự thông minh nhất.',
                'content' => '<p>Mỗi thể loại nhiếp ảnh yêu cầu đặc thù về góc nhìn, khẩu độ và tốc độ lấy nét khác nhau.</p>',
                'image_url' => '/uploads/products/nikon_z_14_24.jpg',
                'tag' => 'tips',
                'tag_text' => 'Mẹo',
                'author' => 'Admin',
                'read_time' => 5,
                'is_featured' => false,
            ],
            [
                'title' => 'Tamron 150-500mm f/5-6.7 Di III VC VXD — Tele Zoom đỉnh cao cho Sony E',
                'description' => 'Sự lựa chọn hoàn hảo cho nhiếp ảnh động vật hoang dã và thể thao với kích thước nhỏ gọn bất ngờ và chống rung VC xuất sắc.',
                'content' => '<p>Tamron chứng minh khả năng thiết kế ống kính siêu tele gọn nhẹ với tỷ lệ giá trên hiệu năng vượt trội.</p>',
                'image_url' => '/uploads/products/tamron_28_75_g2.jpg',
                'tag' => 'review',
                'tag_text' => 'Review',
                'author' => 'Admin',
                'read_time' => 8,
                'is_featured' => false,
            ],
            [
                'title' => 'Hiểu về khẩu độ, tốc độ màn trập và ISO — Bộ ba vàng của nhiếp ảnh',
                'description' => 'Tam giác phơi sáng căn bản giúp bạn hoàn toàn làm chủ máy ảnh ở chế độ thủ công Manual mà không phụ thuộc vào Auto.',
                'content' => '<p>Khẩu độ quyết định độ sâu trường ảnh, tốc độ bắt nét hành động, và ISO kiểm soát độ nhạy sáng của cảm biến.</p>',
                'image_url' => '/uploads/products/canon_rf_100_macro.jpg',
                'tag' => 'tips',
                'tag_text' => 'Mẹo',
                'author' => 'Admin',
                'read_time' => 6,
                'is_featured' => false,
            ],
            [
                'title' => 'Fujifilm X100VI — Máy ảnh compact hoàn hảo cho người yêu phong cách retro',
                'description' => 'Ống kính tích hợp 23mm f/2 thế hệ mới cùng cảm biến 40MP và giả lập màu phim danh tiếng tạo nên hiện tượng toàn cầu.',
                'content' => '<p>Fujifilm X100VI tiếp tục khẳng định vị thế máy ảnh compact đường phố cao cấp được săn đón nhất.</p>',
                'image_url' => '/uploads/products/fuji_xf_56_f12.jpg',
                'tag' => 'review',
                'tag_text' => 'Review',
                'author' => 'Admin',
                'read_time' => 7,
                'is_featured' => false,
            ],
            [
                'title' => '10 lỗi phổ biến người mới chụp ảnh thường mắc và cách khắc phục',
                'description' => 'Từ rung máy, sai điểm lấy nét, đến phơi sáng thừa thiếu — những lỗi cơ bản và giải pháp khắc phục đơn giản nhất.',
                'content' => '<p>Tránh được 10 sai sót phổ biến này sẽ giúp những bức ảnh của bạn sắc nét và chuyên nghiệp hơn trông thấy.</p>',
                'image_url' => '/uploads/products/sony_70_200_gm2.jpg',
                'tag' => 'tips',
                'tag_text' => 'Mẹo',
                'author' => 'Admin',
                'read_time' => 5,
                'is_featured' => false,
            ],
        ];

        foreach ($sampleNews as $n) {
            News::firstOrCreate(
                ['title' => $n['title']],
                $n
            );
        }
    }
}
