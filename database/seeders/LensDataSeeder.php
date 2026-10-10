<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
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
                'image' => 'uploads/products/1790238752_6ab4e020869c1.jpg',
                'description' => 'Ống kính zoom tiêu chuẩn đầu bảng của dòng G Master thế hệ II. Trang bị 20 thấu kính trong 15 nhóm (gồm 2 thấu kính cực kỳ phi cầu XA, 2 ED và 2 Super ED), lớp phủ Nano AR Coating II triệt tiêu lóa sáng. Hệ thống 4 động cơ XD Linear AF lấy nét cực êm, theo dõi nét chuyển động tức thì, độ phóng đại 0.32x và trọng lượng chỉ 695g, nhẹ hơn 22% so với đời trước.',
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
                'image' => 'uploads/products/1790238927_6ab4e0cfb31a4.jpg',
                'description' => 'Tuyệt tác ống kính chân dung tiêu cự 50mm khẩu độ siêu lớn f/1.2 thuộc dòng G Master cao cấp nhất. Cấu trúc 14 thấu kính trong 10 nhóm với 3 thấu kính XA gia công độ chính xác 0.01 micron mang lại độ phân giải ngoạn mục và hiệu ứng bokeh tròn đều mịn màng nhờ 11 lá khẩu tròn. 4 mô-tơ lấy nét XD Linear phản hồi tức thời ngay cả khi chụp mở rộng khẩu tối đa.',
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
                'image' => 'uploads/products/1790239717_6ab4e3e58da2f.png',
                'description' => 'Ống kính telephoto zoom f/2.8 chuyên nghiệp nhẹ nhất thế giới ở mức 1045g (giảm 29% trọng lượng). Tích hợp chống rung quang học Optical SteadyShot (OSS) 3 chế độ chuyên dụng, 4 mô tơ XD Linear lấy nét nhanh gấp 4 lần thế hệ cũ. Vòng khẩu cơ học có nút gạt click/de-click và khóa Iris Lock tối ưu cho cả chụp thể thao, động vật hoang dã lẫn quay phim điện ảnh.',
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
                'image' => 'uploads/products/1790239745_6ab4e4017e75b.jpg',
                'description' => 'Ống kính zoom tiêu chuẩn chuyên nghiệp dòng L (viền đỏ) dành riêng cho hệ máy không gương lật Canon EOS R. Hệ thống chống rung quang học 5-stops IS (kết hợp thân máy lên tới 8 stops), động cơ Nano USM lấy nét mượt mà không tiếng ồn. Cấu tạo 21 thấu kính trong 15 nhóm với 3 thấu kính UD và 3 thấu kính phi cầu GMo, tích hợp vòng xoay Control Ring tùy biến khẩu độ/ISO linh hoạt.',
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
                'image' => 'uploads/products/1790239840_6ab4e4606a454.jpg',
                'description' => 'Chuẩn mực mới của dòng ống kính chân dung tiêu cự cố định Canon RF. Khẩu độ tối đa f/1.2 với 10 lá khẩu tròn tạo ra độ sâu trường ảnh siêu mỏng và bokeh mềm mại huyền ảo. Cấu trúc 15 thấu kính gồm thấu kính phi cầu và thấu kính UD triệt tiêu sắc sai hoàn toàn, lớp phủ Air Sphere Coating (ASC) chống bóng ma, kết cấu chống bụi và giọt bắn chuẩn L-Series.',
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
                'image' => 'uploads/products/1790238799_6ab4e04f0d3bd.jpg',
                'description' => 'Ống kính macro chuyên nghiệp đầu tiên trên thế giới sở hữu độ phóng đại tối đa lên đến 1.4x (thay vì 1.0x truyền thống). Tích hợp vòng điều khiển quang sai hình cầu Spherical Aberration (SA) độc quyền cho phép tùy biến đặc tính xóa phông mềm mịn hoặc viền nét bokeh. Chống rung Hybrid IS lên đến 8 stops kết hợp động cơ Dual Nano USM siêu tốc.',
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
                'image' => 'uploads/products/1790239033_6ab4e13977f56.jpg',
                'description' => 'Ống kính chân dung đỉnh cao thuộc dòng S-Line cao cấp nhất của hệ ngàm Nikon Z đường kính lớn 55mm. Khẩu độ f/1.2 với 11 lá khẩu tròn mang đến khả năng tách bạch chủ thể 3D ngoạn mục. Hệ thống lấy nét Multi-Focusing STM kép điều khiển độc lập 2 nhóm thấu kính, kết hợp lớp phủ Nano Crystal và ARNEO loại bỏ bóng mờ ngược sáng triệt để.',
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
                'image' => 'uploads/products/1790239894_6ab4e496c050f.png',
                'description' => 'Ống kính zoom góc siêu rộng f/2.8 nhẹ nhất thế giới trong cùng phân khúc với trọng lượng chỉ 650g. Kiểm soát hiện tượng lóa coma sagittal vượt trội giữ các ngôi sao luôn tròn trịa ở rìa ảnh. Hood rời chuyên dụng HB-97 cho phép lắp trực tiếp kính lọc ren vặn 112mm, đi kèm màn hình thông số OLED hiển thị cự ly lấy nét và độ sâu trường ảnh chính xác.',
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
                'image' => 'uploads/products/1790239920_6ab4e4b09fb1b.webp',
                'description' => 'Thế hệ Mark II của ống kính zoom tiêu chuẩn danh tiếng dòng Art dành cho máy ảnh Full-Frame Mirrorless. Trang bị động cơ lấy nét HLA (High-response Linear Actuator) nhanh gấp hơn 3 lần thế hệ trước, trọng lượng giảm 10% còn 745g. Tích hợp vòng chỉnh khẩu cơ học kèm nút gạt khóa khẩu và de-click, cự ly chụp cận cảnh tối thiểu chỉ 17cm.',
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
                'image' => 'uploads/products/1790239541_6ab4e335ceddd.jpg',
                'description' => 'Được ca ngợi là kiệt tác chân dung chuyên dụng với thiết kế tối ưu hoàn toàn cho máy ảnh không gương lật. Chiều dài chỉ 94.1mm và nặng 630g, cấu tạo 15 thấu kính gồm 5 thấu kính tán xạ thấp SLD loại bỏ viền tím sắc sai tối đa. 11 lá khẩu tròn mang đến bokeh mịn màng, công tắc gạt de-click vòng khẩu và nút gán chức năng AFL tiện dụng.',
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
                'image' => 'uploads/products/1790239588_6ab4e364e6779.jpg',
                'description' => 'Thế hệ G2 (Model A063) cải tiến toàn diện của chiếc ống kính zoom f/2.8 được ưa chuộng bậc nhất thế giới. Cấu trúc quang học mới nâng cao độ sắc nét mép ảnh, động cơ lấy nét tuyến tính VXD siêu tốc và cực êm. Trang bị cổng kết nối USB-C trực tiếp trên thân ống kính tương thích phần mềm Tamron Lens Utility để cá nhân hóa vòng xoay nét và cập nhật firmware.',
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
                'image' => 'uploads/products/1790239680_6ab4e3c0e3fa1.webp',
                'description' => 'Ống kính chân dung tiêu chuẩn vàng cho hệ cảm biến APS-C Fujifilm X-Series với góc nhìn quy đổi 85mm Full-Frame. Độ mở khẩu siêu lớn f/1.2 với 11 lá khẩu tròn mang lại hiệu ứng bokeh mờ nhòe tròn tuyệt đối. Cấu tạo 13 thấu kính trong 8 nhóm với 2 thấu kính phi cầu và 1 thấu kính ED, thân kim loại nguyên khối tích hợp khả năng kháng bụi và thời tiết khắc nghiệt WR.',
                'status' => 'in_stock',
            ],
        ];

        foreach ($productsData as $prod) {
            Product::create($prod);
        }
    }
}
