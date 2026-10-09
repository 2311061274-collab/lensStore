# 🤖 AGENTS.md — Hướng Dẫn & Bộ Ngữ Cảnh AI Agent Cho Dự Án LensStore

> **Dành cho mọi AI Coding Agent (Antigravity IDE, Claude Code, Cursor, Copilot, Codex, v.v.)**  
> Bản tài liệu này là **Single Source of Truth** quy định toàn bộ cấu trúc, công nghệ, kiến trúc nghiệp vụ và quy tắc lập trình của dự án **LensStore**. Mọi thay đổi mã nguồn phải tuân thủ nghiêm ngặt các chỉ dẫn dưới đây.

---

## 1. Giới Thiệu Tổng Quan Dự Án

- **Tên dự án**: **LensStore** (Camera Lenses E-Commerce & Mini-ERP Platform)
- **Mục đích**: Hệ thống thương mại điện tử chuyên biệt kinh doanh ống kính máy ảnh chính hãng (Sony, Canon, Nikon, Fujifilm, Sigma, Tamron...), tích hợp trọn gói chuỗi cung ứng:
  1. **Bán hàng trực tuyến (Storefront)**: Danh mục sản phẩm theo thông số quang học (tiêu cự, khẩu độ, ngàm), giỏ hàng AJAX, checkout, mã giảm giá Voucher, theo dõi trạng thái đơn hàng thời gian thực, đánh giá Review, yêu cầu đổi/trả hàng.
  2. **Quản lý kho vận (Logistics - GHN API v2)**: Tự động tính phí vận chuyển chuẩn theo cấp Phường/Xã mới (bỏ cấp Quận/Huyện trên UI theo quy chuẩn 2025+), sinh mã vận đơn Giao Hàng Nhanh tự động.
  3. **Thanh toán trực tuyến (MoMo Gateway)**: Thanh toán quét mã QR / App MoMo (`captureWallet`), xác thực chữ ký số HMAC-SHA256, cơ chế thanh toán lại (`payAgain`), tiếp nhận Webhook IPN không cần CSRF.
  4. **Quản trị kho chuyên sâu (Mini ERP & WMS)**: Quản lý nhà cung cấp, Phiếu Nhập Kho (Goods Receipt), Phiếu Xuất Kho (Goods Issue), Lịch sử biến động kho đa hình (Inventory Transactions), Kiểm định chất lượng hàng hoàn về (QC Inspection - Restock / Bảo hành / Thanh lý).
  5. **Chăm sóc khách hàng & CRM**: Live Chat 2 chiều giữa khách hàng và nhân viên hỗ trợ (gửi kèm ảnh, trích dẫn sản phẩm), ghi chú chăm sóc khách hàng (Customer Notes), tính toán giá trị vòng đời (LTV - Lifetime Value).
  6. **Phân quyền người dùng (RBAC)**: Tích hợp Spatie Laravel Permission và phân chia nhóm quyền theo nghiệp vụ chuyên biệt (`PermissionCatalog`).

---

## 2. Công Nghệ Dự Án (Tech Stack & Environment)

| Tầng / Thành phần | Công nghệ / Thư viện | Phiên bản | Ghi chú quan trọng |
|---|---|---|---|
| **Ngôn ngữ lõi** | PHP | `^8.3` / `8.4` | Tận dụng PHP 8 Attributes, Typed Properties, Match expressions |
| **Framework Backend** | Laravel Framework | `^13.17` | Cấu trúc cấu hình hiện đại tại `bootstrap/app.php` |
| **Cơ sở dữ liệu** | MySQL / SQLite | MySQL 8.x / MariaDB | Local XAMPP (`lar_demo`), có hỗ trợ Aiven Cloud MySQL & SQLite cho Test |
| **Phân quyền** | Spatie Laravel Permission | `^8.3` | Quản lý Role & Permission đa tầng |
| **Frontend Styling** | Tailwind CSS | `^4.0.0` | Cấu hình `@import 'tailwindcss';` tại `resources/css/app.css` (Tailwind v4) |
| **Frontend Bundler** | Vite + Laravel Vite Plugin | Vite `^8.0`, Plugin `^3.1` | Build tài nguyên: `npm run build`, Dev: `npm run dev` |
| **Giao diện & UI** | Blade Templates + Vanilla JS | Blade Engine | Thiết kế Glassmorphism / Dark-Modern, Typography `Inter` & `Instrument Sans` |
| **Biểu đồ thống kê** | Chart.js | `4.4.1` (CDN) | Biểu đồ doanh thu 14 ngày & cơ cấu thương hiệu trong Admin Dashboard |
| **Bộ Icon** | Font Awesome Free | `6.4` / `6.5` | Icon SVG & CSS class trên toàn bộ giao diện |
| **Cổng thanh toán** | MoMo Payment Gateway | API v2 | Chữ ký HMAC-SHA256, Redirect URL & IPN Webhook ngầm |
| **Đơn vị vận chuyển**| GHN (Giao Hàng Nhanh) | API v2 | Tích hợp Master data địa chỉ, tính phí, tạo vận đơn, hủy vận đơn |
| **Email & OTP** | Laravel Mail & Notifications | SMTP / Mailtrap / Log | Xác thực tài khoản đăng ký qua OTP 6 chữ số (hạn 15 phút) |
| **DevOps / Container**| Docker & Nginx | Alpine / Debian | `Dockerfile` + Nginx cấu hình sẵn cho môi trường production / Render |
| **Kiểm thử & Linter**| PHPUnit & Laravel Pint | PHPUnit 12.5, Pint 1.27 | `php artisan test` và `vendor/bin/pint` |

---

## 3. Cấu Trúc Thư Mục Dự Án (Directory Structure)

```
c:\xampp\htdocs\lar_demo\
├── app/
│   ├── Console/                      # Console commands tùy biến
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php            # Đăng ký kèm xác thực OTP email, đăng nhập, phân luồng theo role
│   │   │   ├── CartController.php            # Giỏ hàng (add, update, updateSelection, buyNow, remove)
│   │   │   ├── CategoryController.php        # Quản trị danh mục ống kính
│   │   │   ├── Controller.php                # Base Controller
│   │   │   ├── ForgotPasswordController.php  # Quên mật khẩu qua OTP email
│   │   │   ├── GHNController.php             # API proxy nạp tỉnh/thành, phường/xã, tính phí ship
│   │   │   ├── MomoController.php            # Thanh toán lại, Callback & Webhook IPN MoMo
│   │   │   ├── NewsController.php            # Quản trị tin tức bài viết
│   │   │   ├── OrderActionController.php     # Xác nhận nhận hàng, tạo đơn trả hàng, gửi review
│   │   │   ├── OrderController.php           # Storefront: Checkout, tính tổng, trừ reserved_stock, tạo GHN
│   │   │   ├── ProductController.php         # Admin: CRUD ống kính, lọc tiêu cự/ngàm/hãng, bulk update giá
│   │   │   ├── ProfileController.php         # Hồ sơ cá nhân người dùng, đổi mật khẩu, upload avatar
│   │   │   ├── StorefrontController.php      # Giao diện khách: trang chủ, danh sách sản phẩm, tin tức, chi tiết
│   │   │   ├── UserAddressController.php     # Sổ địa chỉ giao hàng khách hàng
│   │   │   ├── UserController.php            # Quản trị tài khoản người dùng & gán vai trò
│   │   │   ├── VoucherController.php         # Quản trị mã giảm giá
│   │   │   ├── WishlistController.php        # Quản lý danh sách sản phẩm yêu thích
│   │   │   ├── Admin/
│   │   │   │   ├── ChatController.php            # Quản trị viên chat với khách hàng
│   │   │   │   ├── CustomerController.php        # CRM: LTV, lịch sử mua hàng, ghi chú, khóa tài khoản
│   │   │   │   ├── DashboardController.php       # Báo cáo tổng hợp, Chart.js, cảnh báo tồn kho, tự động hoàn tất
│   │   │   │   ├── GoodsIssueController.php      # Phiếu xuất kho (sale, damage, internal)
│   │   │   │   ├── GoodsReceiptController.php    # Phiếu nhập kho từ nhà cung cấp (draft -> complete)
│   │   │   │   ├── OrderController.php           # Quản trị đơn hàng: duyệt đơn, tạo GHN, xuất kho tự động
│   │   │   │   ├── QcInspectionController.php    # Kiểm định chất lượng hàng hoàn về (restock/vendor/liquidate)
│   │   │   │   ├── ReportController.php          # Báo cáo doanh thu, AOV, xuất CSV
│   │   │   │   ├── ReturnRequestController.php   # Duyệt yêu cầu đổi/trả hàng của khách
│   │   │   │   ├── ReviewController.php          # Kiểm duyệt đánh giá sản phẩm (toggle ẩn/hiện)
│   │   │   │   └── RoleController.php            # Phân quyền chức vụ (Spatie Roles & Permissions)
│   │   │   ├── Api/
│   │   │   │   └── VoucherApiController.php      # Kiểm tra và áp dụng voucher qua AJAX
│   │   │   └── User/
│   │   │       └── ChatController.php            # Khách hàng chat với nhân viên hỗ trợ qua AJAX
│   │   └── Middleware/
│   │       └── RoleMiddleware.php            # Middleware kiểm tra vai trò người dùng
│   ├── Models/                               # Danh mục Eloquent Models
│   │   ├── Cart.php                          # Giỏ hàng người dùng (user_id, product_id, quantity, is_selected)
│   │   ├── Category.php                      # Danh mục ống kính
│   │   ├── GoodsIssue.php                    # Phiếu xuất kho (PXK)
│   │   ├── GoodsIssueDetail.php              # Chi tiết mặt hàng xuất kho
│   │   ├── GoodsReceipt.php                  # Phiếu nhập kho (PNK)
│   │   ├── GoodsReceiptDetail.php            # Chi tiết mặt hàng nhập kho
│   │   ├── InventoryTransaction.php          # Sổ nhật ký biến động kho đa hình (morphTo)
│   │   ├── Message.php                       # Tin nhắn chat (kèm ảnh và trích dẫn sản phẩm)
│   │   ├── News.php                          # Bài viết tin tức nhiếp ảnh
│   │   ├── Order.php                         # Đơn hàng chính (trạng thái, thanh toán, mã GHN, voucher)
│   │   ├── OrderItem.php                     # Chi tiết sản phẩm trong đơn hàng
│   │   ├── PaymentTransaction.php            # Giao dịch cổng thanh toán MoMo
│   │   ├── Product.php                       # Sản phẩm ống kính (stock, reserved_stock, defective_stock)
│   │   ├── QcInspection.php                  # Biên bản kiểm định hàng hoàn về
│   │   ├── ReturnRequest.php                 # Yêu cầu đổi trả hàng từ khách
│   │   ├── Review.php                        # Đánh giá và số sao của khách hàng
│   │   ├── Supplier.php                      # Nhà cung cấp ống kính
│   │   ├── User.php                          # Người dùng (Authenticatable, MustVerifyEmail, HasRoles)
│   │   ├── UserAddress.php                   # Sổ địa chỉ giao hàng
│   │   ├── Voucher.php                       # Mã giảm giá (fixed/percent, min/max value, hạn dùng)
│   │   └── Wishlist.php                      # Danh sách yêu thích
│   ├── Notifications/
│   │   └── SendOtpVerification.php           # Mail notification gửi mã OTP 6 số
│   ├── Providers/
│   │   └── AppServiceProvider.php            # Cấu hình Paginator View cho Admin & Storefront
│   ├── Services/
│   │   ├── GHNService.php                    # Tích hợp API Giao Hàng Nhanh v2 (địa chỉ, tính phí, vận đơn)
│   │   └── MomoService.php                   # Tích hợp MoMo API (chữ ký số, captureWallet, IPN handler)
│   └── Support/
│       ├── ChatSupport.php                   # Helper định dạng payload chat và truy vấn hội thoại
│       └── PermissionCatalog.php             # Danh mục chuẩn hóa phân nhóm quyền hạn hệ thống
├── bootstrap/
│   ├── app.php                               # Cấu hình Routing, Middleware alias, CSRF exception, JSON exceptions
│   └── providers.php                         # Đăng ký Service Providers
├── config/                                   # Cấu hình ứng dụng (app, auth, database, services, permission...)
├── database/
│   ├── factories/                            # Factory tạo dữ liệu test
│   ├── migrations/                           # 42 migrations quản lý toàn bộ cấu trúc CSDL
│   └── seeders/
│       ├── DatabaseSeeder.php                # Seeder gốc
│       ├── LensDataSeeder.php                # Seeder dữ liệu mẫu sản phẩm ống kính và danh mục thực tế
│       ├── InventoryPermissionSeeder.php     # Seeder quyền hạn kho hàng
│       └── NewsPermissionSeeder.php          # Seeder quyền hạn tin tức
├── docker/                                   # File cấu hình Nginx, PHP-FPM, entrypoint cho Docker
├── public/
│   ├── uploads/                              # Thư mục lưu trữ hình ảnh tải lên (products, returns, samples)
│   └── index.php                             # Điểm đón đầu request của ứng dụng
├── resources/
│   ├── css/app.css                           # Tailwind CSS v4 entrypoint
│   ├── js/app.js                             # Javascript entrypoint
│   └── views/
│       ├── admin/                            # Giao diện quản trị (dashboard, orders, products, kho, CRM, chat...)
│       ├── auth/                             # Giao diện xác thực (login, register, verify-email, passwords/...)
│       ├── emails/                           # Blade templates cho email gửi OTP
│       ├── layouts/
│       │   ├── admin.blade.php               # Master layout cho trang quản trị Admin
│       │   ├── app.blade.php                 # Master layout cho Storefront khách hàng
│       │   └── auth.blade.php                # Master layout cho màn hình Đăng ký / Đăng nhập
│       ├── partials/                         # Các phân đoạn dùng chung (navbar, footer, chat widget)
│       └── storefront/                       # Giao diện bán hàng (index, products, detail, cart, checkout, orders...)
├── routes/
│   ├── console.php                           # Artisan commands
│   └── web.php                               # Toàn bộ định tuyến Web, Admin, Auth, API, Webhook
├── tests/                                    # Feature & Unit tests
├── Dockerfile                                # Dockerfile đa tầng triển khai ứng dụng
├── composer.json                             # Khai báo thư viện PHP & scripts
├── package.json                              # Khai báo thư viện Frontend & Vite
└── vite.config.js                            # Cấu hình Vite & Tailwind CSS v4
```

---

## 4. Bảng Tra Cứu Tuyến Đường (Routes & Endpoints Quick Reference)

| Nhóm | Phương thức & URL | Controller & Action | Tên Route | Mục đích |
|---|---|---|---|---|
| **Auth** | `GET /login`, `POST /login` | `AuthController` | `login`, `login.post` | Đăng nhập hệ thống |
| | `GET /register`, `POST /register` | `AuthController` | `register`, `register.post` | Đăng ký & sinh OTP email |
| | `GET /email/verify`, `POST /email/verify/otp` | `AuthController` | `verification.notice`, `...verify-otp` | Xác thực tài khoản bằng OTP |
| **Storefront** | `GET /` | `StorefrontController@index` | `storefront.index` | Trang chủ cửa hàng |
| | `GET /san-pham` | `StorefrontController@productsPage`| `storefront.products` | Danh sách ống kính (lọc & tìm kiếm) |
| | `GET /product/{id}` | `StorefrontController@show` | `storefront.show` | Chi tiết ống kính máy ảnh |
| | `GET /tin-tuc`, `GET /tin-tuc/{id}` | `StorefrontController` | `storefront.news`, `...show` | Trang tin tức & bài viết |
| **Giỏ & Đơn** | `GET /cart`, `POST /cart/add` | `CartController` | `cart.index`, `cart.add` | Quản lý giỏ hàng |
| | `POST /cart/update-selection` | `CartController@updateSelection` | `cart.update-selection` | Checkbox chọn mua hàng |
| | `GET /checkout`, `POST /checkout` | `OrderController@processCheckout` | `checkout.index`, `...process` | Xử lý đặt hàng (GHN + MoMo/COD) |
| | `GET /orders`, `GET /orders/{order}` | `OrderController` | `orders.index`, `orders.show` | Đơn hàng của tôi |
| | `POST /orders/{order}/confirm-received` | `OrderActionController` | `orders.confirm-received` | Khách xác nhận đã nhận hàng |
| | `POST /orders/{order}/return` | `OrderActionController` | `orders.return.store` | Khách gửi yêu cầu trả hàng |
| | `POST /orders/{order}/review` | `OrderActionController` | `orders.review.store` | Khách đánh giá đơn hàng |
| **Vận chuyển & TT**| `GET /ghn/provinces`, `/ghn/wards-by-province` | `GHNController` | `ghn.provinces`, `...wards-by-province` | Master data hành chính GHN |
| | `POST /ghn/calculate-fee` | `GHNController@calculateFee` | `ghn.calculate-fee` | Tính phí giao hàng |
| | `GET /orders/{order}/pay/momo` | `MomoController@payAgain` | `momo.pay-again` | Khách thanh toán lại qua MoMo |
| | `POST /payment/momo/ipn` | `MomoController@ipn` | `momo.ipn` | Webhook IPN từ MoMo (No CSRF) |
| **Live Chat** | `GET /user/chat/messages`, `POST /user/chat/send` | `UserChatController` | `user.chat.messages`, `...send` | Khách chat với tư vấn viên |
| | `GET /admin/chat/messages/{userId}` | `AdminChatController` | `admin.chat.messages` | Admin chat với khách hàng |
| **Admin Quản trị**| `GET /admin` | `DashboardController@index` | `admin.dashboard` | Bảng điều khiển quản trị |
| | `GET /admin/orders`, `PATCH /admin/orders/{order}/status`| `Admin\OrderController` | `admin.orders.index`, `...update-status`| Quản lý đơn hàng & tự động xuất kho |
| | `RESOURCE /admin/products` | `ProductController` | `admin.products.*` | Quản trị sản phẩm |
| | `RESOURCE /admin/goods_receipts` | `GoodsReceiptController` | `admin.goods_receipts.*` | Quản lý phiếu nhập kho |
| | `RESOURCE /admin/goods_issues` | `GoodsIssueController` | `admin.goods_issues.*` | Quản lý phiếu xuất kho |
| | `GET /admin/qc_inspections` | `QcInspectionController` | `admin.qc_inspections.*` | Kiểm định hàng hoàn về |
| | `GET /admin/customers`, `PATCH .../toggle-lock` | `CustomerController` | `admin.customers.*` | Quản trị khách hàng & CRM |
| | `RESOURCE /admin/roles` | `RoleController` | `admin.roles.*` | Quản trị vai trò & quyền Spatie |
| | `GET /admin/reports` | `ReportController` | `admin.reports.index` | Báo cáo doanh thu & xuất CSV |

---

## 5. Các Lệnh Thao Tác Chuẩn (Standard Commands)

```powershell
# Chạy máy chủ phát triển
php artisan serve
npm run dev

# Biên dịch tài nguyên Frontend (Tailwind CSS v4 + Vite)
npm run build

# Cơ sở dữ liệu: Migrate và nạp dữ liệu mẫu
php artisan migrate
php artisan db:seed --class=LensDataSeeder

# Xóa cache hệ thống khi có thay đổi cấu hình
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Chạy kiểm thử tự động
php artisan test

# Định dạng code chuẩn PSR-12 / Laravel Pint
vendor/bin/pint
```

---

## 6. Bộ Quy Tắc Chuyên Sâu Cần Đọc Thêm

Để đảm bảo code chuẩn xác theo từng khía cạnh kiến trúc, AI Agent vui lòng tham khảo các tài liệu chuyên sâu tại:
1. **[Kiến Trúc & Cấu Trúc Dữ Liệu](file:///.agents/rules/architecture.md)**: Chi tiết mô hình phân tầng, 42 bảng dữ liệu và luồng tích hợp GHN / MoMo.
2. **[Quy Chuẩn Viết Code](file:///.agents/rules/coding-standards.md)**: Chuẩn mực PHP 8 Attributes, Eloquent Casts, Blade UI Tokens, Database Transactions.
3. **[Quy Trình Nghiệp Vụ Đặc Thù](file:///.agents/rules/domain-workflows.md)**: Quy trình Finite State Machine quản lý tồn kho (`stock` / `reserved_stock`), quy trình duyệt hoàn hàng QC, và cơ chế bảo mật RBAC.
