# Mô tả Nghiệp vụ và Chức năng Hệ thống Cổng thông tin Sinh viên

## Tổng quan Hệ thống

**Hệ thống Cổng thông tin Sinh viên (Student Portal)** là một ứng dụng web được phát triển bằng Laravel Framework, sử dụng Filament Admin Panel để quản lý và vận hành các hoạt động học tập của sinh viên trong môi trường giáo dục đại học.

### Kiến trúc Hệ thống
- **Framework**: Laravel 12.x
- **Admin Interface**: Filament 3.x
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates + Tailwind CSS
- **Authentication**: Laravel Auth với phân quyền

### Hai Giao diện Chính
1. **Admin Panel** (`/admin`) - Dành cho quản trị viên
2. **Student Portal** (`/student`) - Dành cho sinh viên

---

## Các Thực thể Nghiệp vụ Chính

### 1. Sinh viên (Student)
**Mô tả**: Quản lý thông tin cá nhân và học tập của sinh viên
- Mã sinh viên (student_code)
- Thông tin cá nhân: tên, giới tính, địa chỉ, ngày sinh
- Chương trình đào tạo (training_program_id)
- Lớp và khoa
- Liên kết với tài khoản người dùng (User)

### 2. Chương trình Đào tạo (Training Program)
**Mô tả**: Định nghĩa các chương trình học/ngành học
- Mã chương trình (code)
- Tên chương trình
- Tổng số tín chỉ (total_credits)
- Thời gian đào tạo (duration_years)
- Loại bằng (degree_type)
- Chuyên ngành (specialization)
- Trạng thái hoạt động

### 3. Môn học (Subject)
**Mô tả**: Quản lý danh mục các môn học
- Mã môn học (code)
- Tên môn học
- Số tín chỉ (credits)
- Mô tả chi tiết
- Trạng thái hoạt động

### 4. Môn học trong Chương trình (Program Subject)
**Mô tả**: Liên kết môn học với chương trình đào tạo
- Thuộc chương trình đào tạo nào
- Môn học tương ứng
- Học kỳ dự kiến (semester)
- Tính chất: bắt buộc/tự chọn (is_required)
- **Các mối quan hệ môn học**:
  - Môn tiên quyết (prerequisites): Môn phải học trước
  - Môn song hành (corequisites): Môn phải học cùng lúc

### 5. Lớp học (Class Room)
**Mô tả**: Lớp học cụ thể cho môn học trong học kỳ
- Mã lớp (code)
- Môn học (subject_id)
- Học kỳ (semester_id)
- Sức chứa tối đa (capacity)
- Trạng thái mở đăng ký (is_open)
- Giảng viên phụ trách (user_id)
- **Tính năng thông minh**:
  - Tự động kiểm tra điều kiện đăng ký
  - Kiểm tra môn tiên quyết
  - Kiểm tra sức chứa lớp

### 6. Học kỳ (Semester)
**Mô tả**: Quản lý các học kỳ/kỳ học
- Tên học kỳ
- Ngày bắt đầu và kết thúc
- Tự động xác định học kỳ hiện tại

### 7. Kết quả Học tập (Student Subject)
**Mô tả**: Theo dõi kết quả học tập của sinh viên
- Điểm số (grade) theo thang điểm 4.0
- Chữ điểm tự động (A+, A, B+, B, C+, C, D+, D, F)
- Trạng thái: đậu/rớt (passed/failed)
- Tự động cập nhật trạng thái dựa trên điểm

---

## Chức năng Nghiệp vụ Chính

### A. QUẢN LÝ SINH VIÊN (Admin Panel)

#### 1. Quản lý Danh sách Sinh viên
- **Thêm/Sửa/Xóa** thông tin sinh viên
- **Import hàng loạt** từ file Excel
- **Tìm kiếm và lọc** theo nhiều tiêu chí
- Gán chương trình đào tạo cho sinh viên

#### 2. Quản lý Chương trình Đào tạo
- Tạo và quản lý các chương trình đào tạo
- Thiết lập môn học cho từng chương trình
- Cấu hình môn tiên quyết và song hành
- Quản lý quan hệ giữa các môn học

#### 3. Quản lý Môn học
- **CRUD** danh mục môn học
- **Import hàng loạt** từ file Excel
- Thiết lập mối quan hệ phụ thuộc giữa các môn

#### 4. Quản lý Lớp học
- Tạo lớp học cho từng môn trong học kỳ
- Thiết lập sức chứa và giảng viên
- Quản lý trạng thái mở/đóng đăng ký
- Xem danh sách sinh viên đăng ký

#### 5. Quản lý Học kỳ
- Tạo và quản lý các học kỳ
- Thiết lập thời gian bắt đầu/kết thúc
- Tự động xác định học kỳ hiện tại

#### 6. Quản lý Điểm số
- **Import điểm** hàng loạt từ file Excel
- Cập nhật kết quả học tập sinh viên
- Tự động tính toán chữ điểm và trạng thái

#### 7. Xử lý Yêu cầu
- **Yêu cầu chuyển lớp**: Duyệt/từ chối đơn chuyển lớp
- **Yêu cầu mở lớp**: Xem xét và quyết định mở lớp mới

### B. DỊCH VỤ SINH VIÊN (Student Portal)

#### 1. Thông tin Cá nhân
- **Dashboard cá nhân** với thông tin tổng quan
- Xem thông tin chương trình đào tạo
- Theo dõi tiến độ học tập

#### 2. Đăng ký Học phần
- **Xem danh sách lớp học** có thể đăng ký
- **Đăng ký lớp học** với kiểm tra điều kiện tự động:
  - Kiểm tra môn tiên quyết đã qua
  - Kiểm tra môn song hành
  - Kiểm tra sức chứa lớp
  - Kiểm tra trùng lịch học
- **Xem lớp đã đăng ký** trong học kỳ hiện tại

#### 3. Xem Kết quả Học tập
- Xem điểm các môn đã học
- Theo dõi tiến độ tích lũy tín chỉ
- Xem bảng điểm chi tiết

#### 4. Gửi Yêu cầu
- **Yêu cầu chuyển lớp**: Đề xuất chuyển từ lớp này sang lớp khác
- **Yêu cầu mở lớp**: Đề xuất mở lớp mới cho môn học
- Theo dõi trạng thái xử lý yêu cầu

---

## Luồng Nghiệp vụ Chính

### 1. Luồng Đăng ký Học phần
```
Sinh viên đăng nhập → Xem danh sách lớp học khả dụng → 
Chọn lớp muốn đăng ký → Hệ thống kiểm tra điều kiện →
Nếu đạt: Đăng ký thành công / Nếu không: Hiển thị lý do từ chối
```

**Điều kiện kiểm tra tự động:**
- Đã qua các môn tiên quyết
- Đã học hoặc đang học các môn song hành  
- Lớp còn chỗ trống
- Chưa đăng ký lớp này trước đó
- Môn nằm trong chương trình đào tạo

### 2. Luồng Yêu cầu Chuyển lớp
```
Sinh viên tạo yêu cầu → Chọn lớp hiện tại và lớp muốn chuyển →
Nhập lý do → Gửi yêu cầu → Admin xem xét → Duyệt/Từ chối
```

### 3. Luồng Yêu cầu Mở lớp
```
Sinh viên tạo yêu cầu → Chọn môn học muốn mở lớp →
Các sinh viên khác tham gia yêu cầu → Admin xem xét số lượng →
Quyết định mở lớp mới nếu đủ điều kiện
```

### 4. Luồng Nhập điểm
```
Admin import file Excel → Hệ thống xử lý và validate dữ liệu →
Cập nhật vào database → Tự động tính chữ điểm và trạng thái
```

---

## Tính năng Nâng cao

### 1. Hệ thống Kiểm tra Điều kiện Thông minh
- **Kiểm tra môn tiên quyết**: Tự động kiểm tra sinh viên đã qua các môn tiên quyết
- **Kiểm tra môn song hành**: Đảm bảo sinh viên học các môn song hành cùng lúc
- **Kiểm tra chương trình đào tạo**: Chỉ cho phép đăng ký môn thuộc chương trình của sinh viên

### 2. Import/Export Dữ liệu
- **Import sinh viên** từ file Excel với validation
- **Import môn học** hàng loạt
- **Import điểm** với tự động tính toán

### 3. Dashboard và Widget
- **Widget thông tin học kỳ hiện tại**
- **Widget tiến độ chương trình đào tạo**
- **Widget thông tin tài khoản sinh viên**

### 4. Hệ thống Phân quyền
- **Admin**: Toàn quyền quản lý hệ thống
- **Student**: Chỉ truy cập thông tin và dịch vụ của mình

---

## Lợi ích Nghiệp vụ

### Cho Nhà trường
- **Tự động hóa** quy trình đăng ký học phần
- **Giảm thiểu sai sót** trong quản lý học tập
- **Tiết kiệm thời gian** xử lý thủ tục hành chính
- **Báo cáo và thống kê** chính xác

### Cho Sinh viên  
- **Đăng ký học phần 24/7** qua internet
- **Kiểm tra điều kiện tự động** tránh đăng ký sai
- **Theo dõi tiến độ học tập** trực tuyến
- **Gửi yêu cầu và theo dõi** xử lý

### Cho Giảng viên
- **Quản lý danh sách lớp** dễ dàng
- **Nhập điểm** nhanh chóng và chính xác

---

## Công nghệ và Kiến trúc

### Backend
- **Laravel Framework**: MVC pattern, Eloquent ORM
- **Filament Admin**: Modern admin interface
- **Spatie Roles & Permissions**: Phân quyền
- **Laravel Excel**: Import/Export Excel

### Frontend
- **Filament UI**: Component-based interface
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework

### Database
- **Eloquent ORM**: Object-Relational Mapping
- **Migration system**: Version control cho database
- **Soft deletes**: Xóa mềm dữ liệu quan trọng

---

*Tài liệu này mô tả đầy đủ các nghiệp vụ và chức năng của Hệ thống Cổng thông tin Sinh viên, được thiết kế để phục vụ hiệu quả các hoạt động quản lý học tập trong môi trường giáo dục đại học.*