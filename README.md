# 📸 LensStore — Camera Lenses E-Commerce & Mini-ERP Platform

Hệ thống thương mại điện tử chuyên biệt kinh doanh ống kính máy ảnh chính hãng (Sony, Canon, Nikon, Fujifilm, Sigma, Tamron...), tích hợp trọn gói chuỗi cung ứng, cổng thanh toán MoMo, vận chuyển Giao Hàng Nhanh (GHN v2), quản lý kho Mini-ERP & WMS và hệ thống CRM chăm sóc khách hàng.

---

## 🚀 Công Nghệ Lõi (Tech Stack)

- **Backend**: Laravel 13 (PHP 8.3 / 8.4)
- **Cơ sở dữ liệu**: MySQL 8.4 (Hỗ trợ Local XAMPP & Aiven Cloud MySQL qua SSL `ca.pem`)
- **Frontend Styling**: Tailwind CSS v4 (`@import 'tailwindcss';`)
- **Frontend Bundler**: Vite 8 + Laravel Vite Plugin
- **Phân quyền RBAC**: Spatie Laravel Permission
- **CI/CD & Hosting**: GitHub Actions & GitHub Pages (Static Showcase) / Docker Container (Full-Stack Live)

---

## 🗄️ Cấu Hình Database Aiven Cloud & GitHub Secrets

### 1. Chuỗi kết nối Aiven Cloud MySQL
```env
DB_CONNECTION=mysql
DB_HOST=mysql-248d5c82-hunre-6259.h.aivencloud.com
DB_PORT=27914
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=<YOUR_AIVEN_PASSWORD>
MYSQL_ATTR_SSL_CA=ca.pem
```
Hoặc qua `DB_URL`:
```env
DB_URL=mysql://avnadmin:<YOUR_AIVEN_PASSWORD>@mysql-248d5c82-hunre-6259.h.aivencloud.com:27914/defaultdb?ssl-mode=REQUIRED
MYSQL_ATTR_SSL_CA=ca.pem
```

### 2. Thiết lập GitHub Secrets (Repository Settings)
Trong GitHub Repo, vào **Settings** > **Secrets and variables** > **Actions**, bạn có thể cấu hình theo một trong hai cách:
- **Cách 1 (Khuyến nghị)**: Đặt secret `DB_URL` với giá trị chuỗi URI Aiven đầy đủ.
- **Cách 2**: Đặt các secrets rời: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

Workflow `.github/workflows/deploy-gh-pages.yml` đã được lập trình để tự động nhận diện cả hai cách.

---

## 🌐 Triển Khai Lên GitHub Pages (Static Showcase)

Do **GitHub Pages là môi trường Static Hosting thuần túy** (không hỗ trợ runtime PHP và MySQL server-side trực tiếp trong trình duyệt người dùng), dự án sử dụng quy trình **Automated Static Site Export qua GitHub Actions**:

1. **GitHub Actions Runner (Ubuntu)** khởi chạy với PHP 8.4 & Node.js 20.
2. Kết nối tới **Aiven Cloud MySQL** thông qua chứng chỉ `ca.pem` và chạy migrations để đồng bộ dữ liệu mới nhất.
3. Chạy `npm run build` để đóng gói Vite Tailwind CSS v4.
4. Chạy lệnh xuất tĩnh `php artisan app:export-static` để render toàn bộ các trang Storefront (Trang chủ, Danh mục sản phẩm, Chi tiết từng ống kính, Tin tức, Giới thiệu) thành các tệp `.html` chuẩn SEO và gom tài nguyên vào thư mục `dist/`.
5. Tạo tệp cấu hình `.nojekyll` và tự động xuất bản lên GitHub Pages qua `actions/deploy-pages@v4`.

### Các bước kích hoạt trên GitHub:
1. Đẩy code lên nhánh `main`: `git push origin main`.
2. Vào GitHub Repository > **Settings** > **Pages**.
3. Tại phần **Build and deployment** > **Source**, chọn **GitHub Actions**.
4. Workflow sẽ tự động chạy và cung cấp đường dẫn website: `https://<username>.github.io/<repository-name>/`.

---

## 🛠️ Các Lệnh Thao Tác Nội Bộ (Local Commands)

```powershell
# Chạy máy chủ phát triển
php artisan serve
npm run dev

# Biên dịch tài nguyên Frontend (Tailwind CSS v4 + Vite)
npm run build

# Xuất tĩnh thử nghiệm thư mục dist
php artisan app:export-static

# Kiểm tra cơ sở dữ liệu
php artisan db:show
php artisan migrate
```
