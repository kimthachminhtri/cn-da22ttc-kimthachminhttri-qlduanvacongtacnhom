# Hướng Dẫn Đóng Góp cho TaskFlow

🎉 **Cảm ơn bạn đã quan tâm đến việc đóng góp cho TaskFlow!** 🎉

Chúng tôi hoan nghênh mọi đóng góp từ cộng đồng. Tài liệu này cung cấp hướng dẫn và thông tin về cách đóng góp cho dự án TaskFlow.

## 📋 Mục Lục

- [Quy Tắc Ứng Xử](#quy-tắc-ứng-xử)
- [Bắt Đầu](#bắt-đầu)
- [Cài Đặt Môi Trường](#cài-đặt-môi-trường)
- [Cách Đóng Góp](#cách-đóng-góp)
- [Tiêu Chuẩn Code](#tiêu-chuẩn-code)
- [Quy Tắc Commit](#quy-tắc-commit)
- [Quy Trình Pull Request](#quy-trình-pull-request)
- [Báo Cáo Lỗi](#báo-cáo-lỗi)
- [Đề Xuất Tính Năng](#đề-xuất-tính-năng)

---

## 🤝 Quy Tắc Ứng Xử

Khi tham gia dự án này, bạn đồng ý tuân thủ Quy tắc Ứng xử:

- **Tôn trọng** và hòa nhập với tất cả người đóng góp
- **Xây dựng** trong các cuộc thảo luận và phản hồi
- **Kiên nhẫn** với người mới và các câu hỏi
- **Tập trung vào dự án** và tránh công kích cá nhân
- **Giúp tạo môi trường thân thiện** cho mọi người

---

## 🚀 Bắt Đầu

### Yêu Cầu Hệ Thống

Trước khi đóng góp, hãy đảm bảo bạn có:

- **PHP 8.0+** đã cài đặt
- **MySQL 8.0+** hoặc MariaDB
- **Git** để quản lý phiên bản
- **Trình soạn thảo code** (VS Code, PhpStorm, v.v.)
- Kiến thức cơ bản về **PHP**, **MySQL**, **HTML/CSS**, **JavaScript**

### Các Bước Đầu Tiên

1. **Fork** repository trên GitHub
2. **Clone** fork của bạn về máy
3. **Cài đặt** môi trường phát triển
4. **Tạo branch** cho đóng góp của bạn
5. **Thực hiện** các thay đổi
6. **Kiểm tra** các thay đổi
7. **Gửi** pull request

---

## 💻 Cài Đặt Môi Trường

### 1. Clone Repository

```bash
# Clone fork của bạn
git clone https://github.com/YOUR_USERNAME/taskflow.git
cd taskflow

# Thêm upstream remote
git remote add upstream https://github.com/ORIGINAL_OWNER/taskflow.git
```

### 2. Cài Đặt Database

```bash
# Tạo database
mysql -u root -p
CREATE DATABASE taskflow_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
mysql -u root -p taskflow_dev < database/taskflow2.sql
```

### 3. Cấu Hình

```php
// Cập nhật thông tin database trong includes/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow_dev');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Chạy Server Phát Triển

```bash
# Sử dụng PHP built-in server
php -S localhost:8000

# Hoặc sử dụng XAMPP/WAMP/MAMP
# Truy cập: http://localhost/taskflow
```

---

## 🛠️ Cách Đóng Góp

### Các Loại Đóng Góp

Chúng tôi hoan nghênh nhiều loại đóng góp:

- 🐛 **Sửa lỗi**
- ✨ **Tính năng mới**
- 📚 **Cải thiện tài liệu**
- 🎨 **Cải thiện UI/UX**
- ⚡ **Tối ưu hiệu suất**
- 🧪 **Kiểm thử và đảm bảo chất lượng**
- 🌐 **Dịch thuật và bản địa hóa**

### Quy Trình Đóng Góp

1. **Kiểm tra issues hiện có** để tránh trùng lặp
2. **Tạo issue** để thảo luận (với các thay đổi lớn)
3. **Fork và clone** repository
4. **Tạo feature branch**
5. **Thực hiện thay đổi**
6. **Kiểm tra kỹ lưỡng**
7. **Commit với message rõ ràng**
8. **Push lên fork của bạn**
9. **Tạo pull request**

---

## 📝 Tiêu Chuẩn Code

### Tiêu Chuẩn PHP

#### Tiêu Chuẩn PSR
- Tuân theo **PSR-1** (Tiêu chuẩn Code Cơ bản)
- Tuân theo **PSR-12** (Phong cách Code Mở rộng)
- Sử dụng **PSR-4** autoloading khi có thể

#### Phong Cách Code
```php
<?php
/**
 * Mô tả class
 */
class ExampleClass
{
    private string $property;
    
    public function methodName(string $parameter): string
    {
        if ($condition) {
            return $this->doSomething($parameter);
        }
        
        return 'default';
    }
}
```

#### Hướng Dẫn Bảo Mật
- **Luôn sử dụng prepared statements** cho truy vấn database
- **Escape output** với `htmlspecialchars()` hoặc helper `e()`
- **Validate và sanitize** tất cả input từ người dùng
- **Sử dụng CSRF tokens** cho forms
- **Không bao giờ hiển thị thông tin nhạy cảm** trong thông báo lỗi

### Hướng Dẫn Frontend

#### HTML
- Sử dụng **semantic HTML5** elements
- Bao gồm **thuộc tính accessibility** phù hợp
- Tuân theo **BEM methodology** cho CSS classes khi cần

#### CSS (Tailwind)
- Sử dụng **Tailwind utility classes** là chính
- Tạo **custom components** cho các pattern lặp lại
- Tuân theo thiết kế **mobile-first** responsive

#### JavaScript
- Sử dụng cú pháp **ES6+** hiện đại
- Tuân theo patterns của **Alpine.js** cho reactivity
- Viết **code sạch, dễ đọc** với comments

---

## 📋 Quy Tắc Commit

### Định Dạng Commit Message

```
type(scope): subject

body (tùy chọn)

footer (tùy chọn)
```

### Các Loại Type
- **feat**: Tính năng mới
- **fix**: Sửa lỗi
- **docs**: Thay đổi tài liệu
- **style**: Thay đổi phong cách code (formatting, v.v.)
- **refactor**: Tái cấu trúc code
- **test**: Thêm hoặc cập nhật tests
- **chore**: Công việc bảo trì

### Ví Dụ

```bash
# Commit message tốt
feat(auth): thêm chức năng ghi nhớ đăng nhập
fix(tasks): sửa lỗi kéo thả kanban board
docs(api): cập nhật tài liệu endpoint
style(ui): cải thiện hover states cho button
refactor(database): tối ưu hiệu suất truy vấn

# Commit message không tốt
fix bug
update stuff
đang làm feature
```

---

## 🔄 Quy Trình Pull Request

### Trước Khi Gửi

1. **Cập nhật branch** với các thay đổi mới nhất từ upstream
2. **Kiểm tra kỹ** các thay đổi của bạn
3. **Chạy linting** và sửa các vấn đề
4. **Cập nhật tài liệu** nếu cần
5. **Thêm tests** cho tính năng mới

### Mẫu PR

```markdown
## Mô Tả
Mô tả ngắn gọn về các thay đổi

## Loại Thay Đổi
- [ ] Sửa lỗi
- [ ] Tính năng mới
- [ ] Cập nhật tài liệu
- [ ] Cải thiện hiệu suất

## Kiểm Thử
Đã kiểm thử như thế nào?

## Checklist
- [ ] Code tuân theo hướng dẫn phong cách của dự án
- [ ] Đã tự review code
- [ ] Đã cập nhật tài liệu
- [ ] Không có warnings mới
```

### Quy Trình Review

1. Maintainers sẽ review PR của bạn
2. Giải quyết các yêu cầu thay đổi
3. Sau khi được approve, PR sẽ được merge
4. Đóng góp của bạn sẽ được ghi nhận

---

## 🐛 Báo Cáo Lỗi

### Trước Khi Báo Cáo

1. **Tìm kiếm issues hiện có** để tránh trùng lặp
2. **Cố gắng tái tạo** lỗi một cách nhất quán
3. **Kiểm tra xem đã được sửa chưa** trong phiên bản mới nhất

### Mẫu Báo Cáo Lỗi

```markdown
## Mô Tả Lỗi
Mô tả rõ ràng về lỗi

## Các Bước Tái Tạo
1. Vào '...'
2. Click vào '...'
3. Thấy lỗi

## Hành Vi Mong Đợi
Điều gì nên xảy ra

## Hành Vi Thực Tế
Điều gì thực sự xảy ra

## Môi Trường
- Phiên bản PHP: 
- Phiên bản MySQL:
- Trình duyệt:
- Hệ điều hành:

## Screenshots
Nếu có
```

---

## ✨ Đề Xuất Tính Năng

### Trước Khi Đề Xuất

1. **Kiểm tra issues hiện có** cho các đề xuất tương tự
2. **Xem xét xem có phù hợp** với phạm vi dự án không
3. **Suy nghĩ về độ phức tạp** của việc triển khai

### Mẫu Đề Xuất Tính Năng

```markdown
## Mô Tả Tính Năng
Mô tả rõ ràng về tính năng

## Trường Hợp Sử Dụng
Tại sao cần tính năng này?

## Giải Pháp Đề Xuất
Nó nên hoạt động như thế nào?

## Các Phương Án Khác
Các cách tiếp cận khác bạn đã nghĩ đến

## Thông Tin Bổ Sung
Bất kỳ thông tin nào khác
```

---

## 📞 Nhận Hỗ Trợ

- **Tài liệu**: Xem thư mục `/docs/`
- **Issues**: Mở GitHub issue
- **Thảo luận**: Sử dụng GitHub Discussions cho câu hỏi

---

## 🙏 Ghi Nhận Đóng Góp

Người đóng góp sẽ được:
- Liệt kê trong CONTRIBUTORS.md
- Đề cập trong release notes
- Ghi nhận trong lịch sử commit

Cảm ơn bạn đã đóng góp cho TaskFlow! 🚀
