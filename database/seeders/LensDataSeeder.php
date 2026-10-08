<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class LensDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ để tránh trùng lặp
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Tạo các danh mục ống kính máy ảnh
        $categoriesData = [
            [
                'name' => 'Ống kính Zoom Chuẩn (Standard Zoom)',
                'description' => 'Ống kính đa dụng dải tiêu cự 24-70mm, 28-75mm phục vụ chụp sự kiện, phóng sự cưới, chân dung và phong cảnh du lịch hàng ngày.',
            ],
            [
                'name' => 'Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)',
                'description' => 'Ống kính một tiêu cự khẩu độ cực lớn f/1.2 - f/1.8 mang lại độ sắc nét quang học tối đa và hiệu ứng xóa phông bokeh nghệ thuật.',
            ],
            [
                'name' => 'Ống kính Góc Siêu Rộng (Ultra-Wide Angle)',
                'description' => 'Dải tiêu cự từ 14mm đến 24mm mở rộng góc nhìn bao quát, chuyên dùng cho chụp phong cảnh thiên nhiên, kiến trúc và nội thất.',
            ],
            [
                'name' => 'Ống kính Siêu Tele (Telephoto Lens)',
                'description' => 'Ống kính tiêu cự xa từ 70-200mm đến 400mm bắt trọn chủ thể từ khoảng cách xa, chuyên cho thể thao, sân khấu và chim muông dã ngoại.',
            ],
            [
                'name' => 'Ống kính Chụp Cận Cảnh (Macro Lens)',
                'description' => 'Ống kính chuyên biệt với tỉ lệ phóng đại 1:1 hoặc lớn hơn, tái hiện siêu sắc nét các chi tiết tí hon, trang sức, hoa lá và côn trùng.',
            ],
            [
                'name' => 'Ống kính Quay Phim & Điện Ảnh (Cine Lens)',
                'description' => 'Ống kính điện ảnh chất lượng cao kiểm soát hiện tượng focus breathing, vòng lấy nét mượt mà dành cho các nhà làm phim chuyên nghiệp.',
            ],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $cat) {
            $createdCategories[$cat['name']] = Category::create($cat);
        }

        // 2. Tạo danh sách các sản phẩm ống kính thực tế
        $productsData = [
            [
                'category_id' => $createdCategories['Ống kính Zoom Chuẩn (Standard Zoom)']->id,
                'name' => 'Sony FE 24-70mm f/2.8 GM II',
                'sku' => 'SEL2470GM2',
                'focal_length' => '24-70mm',
                'aperture' => 'f/2.8',
                'mount' => 'Sony E-Mount',
                'price' => 49990000,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính zoom tiêu chuẩn hàng đầu thế giới dành cho máy ảnh Full-Frame Sony. Thiết kế nhỏ gọn hơn 22% so với đời 1, độ nét vượt trội từ tâm đến rìa và 4 motor lấy nét XD Linear cực nhanh.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)']->id,
                'name' => 'Sony FE 50mm f/1.2 GM',
                'sku' => 'SEL50F12GM',
                'focal_length' => '50mm',
                'aperture' => 'f/1.2',
                'mount' => 'Sony E-Mount',
                'price' => 47990000,
                'stock' => 8,
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=80',
                'description' => 'Tuyệt tác ống kính chân dung tiêu cự vàng 50mm với khẩu độ siêu lớn f/1.2 cho hiệu ứng xóa phông mịn màng như kem, độ tương phản tuyệt hảo trong mọi điều kiện ánh sáng.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Siêu Tele (Telephoto Lens)']->id,
                'name' => 'Sony FE 70-200mm f/2.8 GM OSS II',
                'sku' => 'SEL70200GM2',
                'focal_length' => '70-200mm',
                'aperture' => 'f/2.8',
                'mount' => 'Sony E-Mount',
                'price' => 62990000,
                'stock' => 6,
                'image' => 'https://images.unsplash.com/photo-1502982720700-bfff97f2da8d?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính telephoto zoom f/2.8 nhẹ nhất phân khúc với hệ thống chống rung OSS quang học cao cấp, bắt nét chuyển động nhanh hoàn hảo cho thể thao và chân dung ngoại cảnh.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Zoom Chuẩn (Standard Zoom)']->id,
                'name' => 'Canon RF 24-70mm f/2.8L IS USM',
                'sku' => 'CANON-RF-2470L',
                'focal_length' => '24-70mm',
                'aperture' => 'f/2.8',
                'mount' => 'Canon RF',
                'price' => 56500000,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính cao cấp dòng L trứ danh của Canon ngàm RF, trang bị chống rung quang học 5-stops IS cùng vòng điều khiển Control Ring tiện lợi.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)']->id,
                'name' => 'Canon RF 50mm f/1.2L USM',
                'sku' => 'CANON-RF-50F12L',
                'focal_length' => '50mm',
                'aperture' => 'f/1.2',
                'mount' => 'Canon RF',
                'price' => 54900000,
                'stock' => 7,
                'image' => 'https://images.unsplash.com/photo-1590291103653-997f5deee922?w=600&auto=format&fit=crop&q=80',
                'description' => 'Đỉnh cao chất lượng quang học dành cho hệ máy Canon EOS R. Thấu kính cao cấp UD loại bỏ quang sai, mang lại màu da trung thực và sắc nét đáng kinh ngạc.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chụp Cận Cảnh (Macro Lens)']->id,
                'name' => 'Canon RF 100mm f/2.8L Macro IS USM',
                'sku' => 'CANON-RF-100MACRO',
                'focal_length' => '100mm',
                'aperture' => 'f/2.8',
                'mount' => 'Canon RF',
                'price' => 33500000,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1512790182412-b19e6d62bc39?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính macro chuyên nghiệp đầu tiên thế giới có độ phóng đại lên đến 1.4x và vòng điều khiển Spherical Aberration (SA) tùy biến hiệu ứng bokeh.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)']->id,
                'name' => 'Nikon NIKKOR Z 85mm f/1.2 S',
                'sku' => 'NIKON-Z-85F12S',
                'focal_length' => '85mm',
                'aperture' => 'f/1.2',
                'mount' => 'Nikon Z',
                'price' => 68990000,
                'stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính chân dung đỉnh cao của hệ Nikon Z Series. Lớp phủ Nano Crystal và ARNEO triệt tiêu hoàn toàn bóng ma và hiện tượng lóa sáng.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Góc Siêu Rộng (Ultra-Wide Angle)']->id,
                'name' => 'Nikon NIKKOR Z 14-24mm f/2.8 S',
                'sku' => 'NIKON-Z-1424F28S',
                'focal_length' => '14-24mm',
                'aperture' => 'f/2.8',
                'mount' => 'Nikon Z',
                'price' => 58000000,
                'stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1507646227500-4d389b0012be?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính góc siêu rộng f/2.8 nhẹ nhất và gọn nhất cho máy ảnh không gương lật Nikon Z, có thể gắn trực tiếp filter 112mm ở đầu hood.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Zoom Chuẩn (Standard Zoom)']->id,
                'name' => 'Sigma 24-70mm f/2.8 DG DN II Art',
                'sku' => 'SIGMA-2470-ART2',
                'focal_length' => '24-70mm',
                'aperture' => 'f/2.8',
                'mount' => 'Sony E-Mount / L-Mount',
                'price' => 29500000,
                'stock' => 22,
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&auto=format&fit=crop&q=80',
                'description' => 'Phiên bản Mark II thế hệ mới cải tiến toàn diện: trọng lượng giảm, motor HLA siêu êm tốc độ cao gấp 3 lần, vòng khẩu độ có click/de-click.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)']->id,
                'name' => 'Sigma 85mm f/1.4 DG DN Art',
                'sku' => 'SIGMA-85F14-ART',
                'focal_length' => '85mm',
                'aperture' => 'f/1.4',
                'mount' => 'Sony E-Mount',
                'price' => 24900000,
                'stock' => 18,
                'image' => 'https://images.unsplash.com/photo-1510127031490-453be2b45466?w=600&auto=format&fit=crop&q=80',
                'description' => 'Được mệnh danh là "Vua chụp chân dung", kích thước nhỏ gọn chỉ bằng lòng bàn tay nhưng cho chất lượng ảnh sánh ngang các ống kính đắt tiền nhất.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Zoom Chuẩn (Standard Zoom)']->id,
                'name' => 'Tamron 28-75mm f/2.8 Di III VXD G2',
                'sku' => 'TAMRON-2875-G2',
                'focal_length' => '28-75mm',
                'aperture' => 'f/2.8',
                'mount' => 'Sony E-Mount / Nikon Z',
                'price' => 19990000,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính zoom f/2.8 quốc dân được yêu thích nhất mọi thời đại. Hiệu năng vượt tầm giá, kích thước cực kỳ gọn nhẹ thuận tiện mang đi du lịch.',
                'status' => 'in_stock',
            ],
            [
                'category_id' => $createdCategories['Ống kính Chân Dung & Tiêu Cự Cố Định (Prime Lens)']->id,
                'name' => 'Fujifilm XF 56mm f/1.2 R WR',
                'sku' => 'FUJI-XF56F12-WR',
                'focal_length' => '56mm (tương đương 85mm)',
                'aperture' => 'f/1.2',
                'mount' => 'Fujifilm X-Mount',
                'price' => 23500000,
                'stock' => 11,
                'image' => 'https://images.unsplash.com/photo-1500485035595-cbe6f645feb1?w=600&auto=format&fit=crop&q=80',
                'description' => 'Ống kính chân dung huyền thoại của hệ máy cảm biến APS-C Fujifilm, có khả năng kháng thời tiết Weather-Resistant và độ phân giải xuất sắc.',
                'status' => 'in_stock',
            ],
        ];

        foreach ($productsData as $prod) {
            Product::create($prod);
        }
    }
}
