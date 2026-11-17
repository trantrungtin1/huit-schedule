Markdown
# HUIT Schedule Hub 🎓

Hệ thống quản lý lịch giảng dạy nội bộ, hỗ trợ sắp xếp và gợi ý phân công giảng viên tự động.

## 🚀 Chức năng chính
- **Quản lý danh sách lịch dạy:** Hiển thị chi tiết lịch dạy theo tuần.
- **Bộ lọc thông minh (Lọc kết hợp):** Tìm kiếm lịch dạy theo Cơ sở hoặc theo Giảng viên.
- **Thêm lịch dạy mới & Gợi ý tự động:** 
  - Kiểm tra tính hợp lệ của thời gian.
  - **Thuật toán thông minh:** Tự động loại bỏ các giảng viên đang kẹt lịch và chỉ gợi ý các giảng viên trống lịch vào đúng Thứ/Ca học đó.
- **CI/CD Pipeline:** Tích hợp GitHub Actions tự động kiểm tra lỗi cú pháp PHP khi push code.

## 💻 Công nghệ sử dụng
- **Backend:** PHP 8 (Thuần), PDO (PHP Data Objects).
- **Database:** MySQL.
- **Frontend:** HTML5, Bootstrap 5.
- **Công cụ & Thư viện:** Laragon, Git/GitHub, Composer, FakerPHP (Sinh dữ liệu mẫu).

## ⚙️ Hướng dẫn cài đặt (Dành cho Giảng viên/Người chấm)

1. **Clone dự án về thư mục www của Laragon/XAMPP:**
bash
git clone https://github.com/trantrungtin1/huit-schedule.git

2. **Cài đặt thư viện:**
bash
composer install
3. **Cấu hình Database:**
- Tạo một database mới tên là `huit_db` (Collation: utf8mb4_unicode_ci).
- Đổi tên file `.env.example` thành `.env` và kiểm tra thông tin kết nối (mặc định user `root`, pass rỗng).

4. **Tạo bảng và dữ liệu mẫu:**
- Chạy file `seed.php` trong Terminal để tự động khởi tạo bảng và bơm dữ liệu Faker:
bash
php seed.php
3. **Cấu hình Database:**
- Tạo một database mới tên là `huit_db` (Collation: utf8mb4_unicode_ci).
- Đổi tên file `.env.example` thành `.env` và kiểm tra thông tin kết nối (mặc định user `root`, pass rỗng).

4. **Tạo bảng và dữ liệu mẫu:**
- Chạy file `seed.php` trong Terminal để tự động khởi tạo bảng và bơm dữ liệu Faker:
bash
php seed.php