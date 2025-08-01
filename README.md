# Hệ thống Cổng thông tin Sinh viên (Student Portal System)

<p align="center">
<img src="https://img.shields.io/badge/Laravel-v12.x-red.svg" alt="Laravel Version">
<img src="https://img.shields.io/badge/Filament-v3.x-orange.svg" alt="Filament Version">
<img src="https://img.shields.io/badge/PHP-^8.2-blue.svg" alt="PHP Version">
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## Giới thiệu

Hệ thống Cổng thông tin Sinh viên là một ứng dụng web toàn diện được phát triển bằng Laravel Framework và Filament Admin Panel, được thiết kế để quản lý và vận hành các hoạt động học tập của sinh viên trong môi trường giáo dục đại học.

## Tính năng chính

### 🎓 Quản lý Sinh viên
- Quản lý thông tin cá nhân và học tập
- Import/Export dữ liệu hàng loạt
- Theo dõi tiến độ học tập

### 📚 Quản lý Chương trình Đào tạo
- Thiết lập chương trình học/ngành học
- Cấu hình môn tiên quyết và song hành
- Quản lý cấu trúc chương trình

### 🏫 Quản lý Lớp học
- Tạo và quản lý lớp học theo học kỳ
- Kiểm soát sức chứa và đăng ký
- Gán giảng viên và quản lý thời khóa biểu

### 📝 Đăng ký Học phần
- Đăng ký lớp học trực tuyến 24/7
- Kiểm tra điều kiện tự động
- Xem lịch học và danh sách lớp

### 📊 Quản lý Điểm số
- Nhập điểm hàng loạt từ Excel
- Tính toán chữ điểm tự động
- Theo dõi kết quả học tập

### 📋 Hệ thống Yêu cầu
- Yêu cầu chuyển lớp
- Yêu cầu mở lớp mới
- Theo dõi và xử lý yêu cầu

## Kiến trúc Hệ thống

- **Framework**: Laravel 12.x với Eloquent ORM
- **Admin Interface**: Filament 3.x
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Auth với phân quyền theo vai trò

## Giao diện

### 🔧 Admin Panel (`/admin`)
Dành cho quản trị viên với đầy đủ quyền quản lý hệ thống

### 👨‍🎓 Student Portal (`/student`)
Dành cho sinh viên truy cập thông tin và dịch vụ cá nhân

## Tài liệu

### 📖 Tài liệu Nghiệp vụ
- **[Mô tả Nghiệp vụ (Tiếng Việt)](./MO_TA_NGHIEP_VU.md)** - Mô tả chi tiết các chức năng và quy trình nghiệp vụ
- **[Business Documentation (English)](./BUSINESS_DOCUMENTATION.md)** - Comprehensive business functions and features documentation

### 🏗️ Tài liệu Kỹ thuật
- **[Technical Architecture](./TECHNICAL_ARCHITECTURE.md)** - Detailed technical architecture and development guide

## Yêu cầu Hệ thống

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 8.0+ hoặc PostgreSQL 13+

## Cài đặt

### 1. Clone Repository
```bash
git clone <repository-url>
cd stu-portal
```

### 2. Cài đặt Dependencies
```bash
composer install
npm install
```

### 3. Cấu hình Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Cấu hình Database
Chỉnh sửa file `.env` với thông tin database của bạn:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stu_portal
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Chạy Migration
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Khởi động Development Server
```bash
composer dev
# Hoặc
php artisan serve
```

## Sử dụng

### Truy cập Admin Panel
- URL: `http://localhost:8000/admin`
- Tạo tài khoản admin đầu tiên bằng seeder hoặc artisan command

### Truy cập Student Portal
- URL: `http://localhost:8000/student`
- Sinh viên đăng nhập bằng mã sinh viên và mật khẩu

## Development

### Chạy Development Environment
```bash
composer dev
```
Lệnh này sẽ chạy đồng thời:
- Laravel development server
- Queue worker
- Log monitoring
- Vite development server

### Testing
```bash
composer test
```

## Đóng góp

Chúng tôi hoan nghênh các đóng góp từ cộng đồng. Vui lòng đọc hướng dẫn đóng góp trước khi tạo pull request.

## Bảo mật

Nếu bạn phát hiện lỗ hổng bảo mật, vui lòng báo cáo qua email thay vì tạo issue công khai.

## Giấy phép

Dự án này được cấp phép theo [MIT License](https://opensource.org/licenses/MIT).

---

*Được phát triển với ❤️ bằng Laravel và Filament*
