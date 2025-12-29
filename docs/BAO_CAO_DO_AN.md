# BÁO CÁO ĐỒ ÁN TỐT NGHIỆP

---

## TRANG BÌA

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                         │
│                    BỘ GIÁO DỤC VÀ ĐÀO TẠO                              │
│              TRƯỜNG ĐẠI HỌC [TÊN TRƯỜNG]                               │
│                   KHOA CÔNG NGHỆ THÔNG TIN                              │
│                                                                         │
│                         ─────────────                                   │
│                                                                         │
│                                                                         │
│                                                                         │
│                       ĐỒ ÁN TỐT NGHIỆP                                 │
│                                                                         │
│                                                                         │
│                                                                         │
│     ╔═══════════════════════════════════════════════════════════╗      │
│     ║                                                           ║      │
│     ║    XÂY DỰNG HỆ THỐNG QUẢN LÝ DỰ ÁN VÀ CÔNG VIỆC         ║      │
│     ║                      TASKFLOW                             ║      │
│     ║                                                           ║      │
│     ╚═══════════════════════════════════════════════════════════╝      │
│                                                                         │
│                                                                         │
│                                                                         │
│                                                                         │
│         Sinh viên thực hiện  : [HỌ VÀ TÊN SINH VIÊN]                   │
│         Mã số sinh viên      : [MÃ SỐ SINH VIÊN]                       │
│         Lớp                  : [TÊN LỚP]                               │
│         Ngành                : Công nghệ Thông tin                      │
│                                                                         │
│         Giảng viên hướng dẫn : [HỌ VÀ TÊN GIẢNG VIÊN]                  │
│                                                                         │
│                                                                         │
│                                                                         │
│                                                                         │
│                    [THÀNH PHỐ], Tháng 12 năm 2024                      │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## LỜI CẢM ƠN

Trong quá trình thực hiện đồ án tốt nghiệp "Xây dựng hệ thống quản lý dự án và công việc TaskFlow", em đã nhận được sự hướng dẫn, giúp đỡ tận tình từ nhiều phía. Em xin gửi lời cảm ơn chân thành đến:

**Giảng viên hướng dẫn [Tên GVHD]** đã tận tình chỉ bảo, định hướng và đóng góp những ý kiến quý báu trong suốt quá trình thực hiện đồ án. Thầy/Cô đã giúp em hiểu sâu hơn về quy trình phát triển phần mềm chuyên nghiệp và các kỹ thuật lập trình hiện đại.

**Quý Thầy Cô trong Khoa Công nghệ Thông tin** đã truyền đạt những kiến thức nền tảng vững chắc về lập trình, cơ sở dữ liệu, phân tích thiết kế hệ thống trong suốt thời gian học tập tại trường.

**Gia đình và bạn bè** đã luôn động viên, hỗ trợ về mặt tinh thần và vật chất để em có thể hoàn thành tốt đồ án này.

Mặc dù đã cố gắng hoàn thiện đồ án một cách tốt nhất, nhưng do kiến thức và kinh nghiệm còn hạn chế nên không thể tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý từ Quý Thầy Cô và Hội đồng để đồ án được hoàn thiện hơn.

Em xin chân thành cảm ơn!

*[Thành phố], ngày ... tháng 12 năm 2024*

**Sinh viên thực hiện**

*[Họ và tên sinh viên]*

---

## LỜI CAM ĐOAN

Em xin cam đoan đồ án tốt nghiệp "Xây dựng hệ thống quản lý dự án và công việc TaskFlow" là công trình nghiên cứu của riêng em dưới sự hướng dẫn của [Tên GVHD].

Các số liệu, kết quả trình bày trong đồ án là trung thực và chưa từng được công bố trong bất kỳ công trình nào khác.

Các tài liệu tham khảo đều được trích dẫn đầy đủ nguồn gốc.

Em xin chịu hoàn toàn trách nhiệm về lời cam đoan này.

*[Thành phố], ngày ... tháng 12 năm 2024*

**Sinh viên thực hiện**

*[Họ và tên sinh viên]*


---

## MỤC LỤC

```
LỜI CẢM ƠN .................................................... i
LỜI CAM ĐOAN .................................................. ii
MỤC LỤC ....................................................... iii
DANH MỤC HÌNH ẢNH ............................................. v
DANH MỤC BẢNG BIỂU ............................................ vi
DANH MỤC TỪ VIẾT TẮT .......................................... vii

CHƯƠNG 1: GIỚI THIỆU ĐỀ TÀI ................................... 1
    1.1. Đặt vấn đề ............................................ 1
    1.2. Lý do chọn đề tài ..................................... 2
    1.3. Mục tiêu đề tài ....................................... 3
    1.4. Đối tượng và phạm vi nghiên cứu ....................... 4
    1.5. Phương pháp nghiên cứu ................................ 5
    1.6. Bố cục báo cáo ........................................ 5

CHƯƠNG 2: CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ ........................ 7
    2.1. Tổng quan về hệ thống quản lý dự án ................... 7
    2.2. Kiến trúc MVC ......................................... 9
    2.3. Công nghệ sử dụng ..................................... 11
    2.4. Hệ thống phân quyền RBAC .............................. 15
    2.5. Bảo mật ứng dụng web .................................. 17

CHƯƠNG 3: PHÂN TÍCH HỆ THỐNG .................................. 20
    3.1. Khảo sát hiện trạng ................................... 20
    3.2. Yêu cầu chức năng ..................................... 22
    3.3. Yêu cầu phi chức năng ................................. 25
    3.4. Sơ đồ Use Case ........................................ 26
    3.5. Đặc tả Use Case ....................................... 30

CHƯƠNG 4: THIẾT KẾ HỆ THỐNG ................................... 40
    4.1. Thiết kế kiến trúc .................................... 40
    4.2. Thiết kế cơ sở dữ liệu ................................ 43
    4.3. Thiết kế giao diện .................................... 50
    4.4. Thiết kế API .......................................... 55

CHƯƠNG 5: CÀI ĐẶT VÀ KIỂM THỬ ................................. 60
    5.1. Môi trường phát triển ................................. 60
    5.2. Cài đặt hệ thống ...................................... 61
    5.3. Kết quả cài đặt ....................................... 65
    5.4. Kiểm thử hệ thống ..................................... 75

CHƯƠNG 6: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN ........................ 85
    6.1. Kết quả đạt được ...................................... 85
    6.2. Hạn chế ............................................... 86
    6.3. Hướng phát triển ...................................... 87

TÀI LIỆU THAM KHẢO ............................................ 88
PHỤ LỤC ....................................................... 90
```

---

## DANH MỤC HÌNH ẢNH

| STT | Tên hình | Trang |
|-----|----------|-------|
| Hình 1.1 | Thống kê sử dụng phần mềm quản lý dự án | 2 |
| Hình 2.1 | Mô hình kiến trúc MVC | 9 |
| Hình 2.2 | Cấu trúc thư mục dự án TaskFlow | 10 |
| Hình 2.3 | Mô hình phân quyền RBAC | 15 |
| Hình 3.1 | Sơ đồ Use Case tổng quát | 26 |
| Hình 3.2 | Sơ đồ Use Case quản lý dự án | 27 |
| Hình 3.3 | Sơ đồ Use Case quản lý công việc | 28 |
| Hình 3.4 | Sơ đồ Use Case quản lý tài liệu | 29 |
| Hình 3.5 | Sơ đồ hoạt động đăng nhập | 35 |
| Hình 3.6 | Sơ đồ hoạt động tạo dự án | 36 |
| Hình 3.7 | Sơ đồ hoạt động giao việc | 37 |
| Hình 4.1 | Kiến trúc tổng thể hệ thống | 40 |
| Hình 4.2 | Sơ đồ ERD | 43 |
| Hình 4.3 | Sơ đồ lớp (Class Diagram) | 48 |
| Hình 4.4 | Thiết kế giao diện Dashboard | 50 |
| Hình 4.5 | Thiết kế giao diện Kanban Board | 51 |
| Hình 4.6 | Thiết kế giao diện chi tiết Task | 52 |
| Hình 5.1 | Giao diện đăng nhập | 65 |
| Hình 5.2 | Giao diện Dashboard | 66 |
| Hình 5.3 | Giao diện danh sách dự án | 67 |
| Hình 5.4 | Giao diện Kanban Board | 68 |
| Hình 5.5 | Giao diện chi tiết công việc | 69 |
| Hình 5.6 | Giao diện quản lý tài liệu | 70 |
| Hình 5.7 | Giao diện lịch | 71 |
| Hình 5.8 | Giao diện Admin Panel | 72 |

---

## DANH MỤC BẢNG BIỂU

| STT | Tên bảng | Trang |
|-----|----------|-------|
| Bảng 2.1 | So sánh các ngôn ngữ Backend | 11 |
| Bảng 2.2 | So sánh các hệ quản trị CSDL | 12 |
| Bảng 2.3 | So sánh các CSS Framework | 13 |
| Bảng 2.4 | Ma trận phân quyền | 16 |
| Bảng 3.1 | Danh sách yêu cầu chức năng | 22 |
| Bảng 3.2 | Danh sách yêu cầu phi chức năng | 25 |
| Bảng 3.3 | Đặc tả Use Case UC01 - Đăng nhập | 30 |
| Bảng 3.4 | Đặc tả Use Case UC02 - Quản lý dự án | 31 |
| Bảng 3.5 | Đặc tả Use Case UC03 - Quản lý công việc | 32 |
| Bảng 4.1 | Cấu trúc bảng users | 44 |
| Bảng 4.2 | Cấu trúc bảng projects | 45 |
| Bảng 4.3 | Cấu trúc bảng tasks | 46 |
| Bảng 4.4 | Danh sách API Endpoints | 55 |
| Bảng 5.1 | Môi trường phát triển | 60 |
| Bảng 5.2 | Kết quả kiểm thử chức năng | 75 |
| Bảng 5.3 | Kết quả kiểm thử bảo mật | 80 |

---

## DANH MỤC TỪ VIẾT TẮT

| Từ viết tắt | Nghĩa đầy đủ |
|-------------|--------------|
| API | Application Programming Interface |
| CRUD | Create, Read, Update, Delete |
| CSS | Cascading Style Sheets |
| CSRF | Cross-Site Request Forgery |
| ERD | Entity Relationship Diagram |
| HTML | HyperText Markup Language |
| HTTP | HyperText Transfer Protocol |
| JSON | JavaScript Object Notation |
| MVC | Model-View-Controller |
| ORM | Object-Relational Mapping |
| PDO | PHP Data Objects |
| PHP | Hypertext Preprocessor |
| RBAC | Role-Based Access Control |
| REST | Representational State Transfer |
| SQL | Structured Query Language |
| UI | User Interface |
| UUID | Universally Unique Identifier |
| UX | User Experience |
| XSS | Cross-Site Scripting |


---

# CHƯƠNG 1: GIỚI THIỆU ĐỀ TÀI

## 1.1. Đặt vấn đề

Trong bối cảnh chuyển đổi số đang diễn ra mạnh mẽ, việc quản lý dự án và công việc hiệu quả trở thành yếu tố then chốt quyết định sự thành công của các tổ chức, doanh nghiệp. Theo khảo sát của Project Management Institute (PMI) năm 2023, có đến 67% các dự án thất bại do thiếu công cụ quản lý phù hợp và quy trình làm việc không rõ ràng.

Tại Việt Nam, đặc biệt là các doanh nghiệp vừa và nhỏ (SMEs), việc quản lý dự án vẫn còn nhiều bất cập:

**Thực trạng hiện tại:**
- Nhiều doanh nghiệp vẫn sử dụng các phương pháp truyền thống như email, Excel, giấy tờ để theo dõi công việc
- Thông tin phân tán, khó tổng hợp và báo cáo
- Thiếu sự minh bạch trong phân công và theo dõi tiến độ
- Khó khăn trong việc cộng tác giữa các thành viên, đặc biệt khi làm việc từ xa
- Chi phí cao khi sử dụng các giải pháp quốc tế như Jira, Asana, Monday.com

**Những vấn đề cần giải quyết:**
- Tập trung hóa thông tin dự án và công việc
- Theo dõi tiến độ trực quan, real-time
- Phân quyền linh hoạt theo vai trò
- Cộng tác hiệu quả giữa các thành viên
- Chi phí hợp lý, phù hợp với doanh nghiệp Việt Nam

## 1.2. Lý do chọn đề tài

Xuất phát từ thực trạng trên, em quyết định thực hiện đề tài "Xây dựng hệ thống quản lý dự án và công việc TaskFlow" với những lý do sau:

**Tính cấp thiết:**
- Nhu cầu số hóa quy trình quản lý dự án ngày càng tăng cao
- Các giải pháp quốc tế có chi phí cao, giao diện tiếng Anh gây khó khăn cho người dùng Việt Nam
- Thiếu các giải pháp self-hosted cho phép doanh nghiệp kiểm soát hoàn toàn dữ liệu

**Tính thực tiễn:**
- Đề tài giải quyết vấn đề thực tế mà nhiều doanh nghiệp đang gặp phải
- Sản phẩm có thể triển khai và sử dụng ngay sau khi hoàn thành
- Phù hợp với quy mô và ngân sách của doanh nghiệp vừa và nhỏ

**Tính học thuật:**
- Áp dụng được nhiều kiến thức đã học: lập trình web, cơ sở dữ liệu, phân tích thiết kế hệ thống
- Thực hành các công nghệ hiện đại: PHP 8, MySQL 8, Tailwind CSS, RESTful API
- Rèn luyện kỹ năng phát triển phần mềm theo quy trình chuyên nghiệp

## 1.3. Mục tiêu đề tài

### 1.3.1. Mục tiêu tổng quát

Xây dựng một hệ thống quản lý dự án và công việc hoàn chỉnh, cho phép các nhóm làm việc cộng tác hiệu quả, theo dõi tiến độ và quản lý tài nguyên một cách trực quan.

### 1.3.2. Mục tiêu cụ thể

| STT | Mục tiêu | Mô tả |
|-----|----------|-------|
| 1 | Quản lý dự án | Tạo, sửa, xóa dự án; theo dõi tiến độ; quản lý thành viên |
| 2 | Quản lý công việc | CRUD tasks; Kanban board; checklist; giao việc |
| 3 | Quản lý tài liệu | Upload, tổ chức thư mục, chia sẻ tài liệu |
| 4 | Hệ thống bình luận | Bình luận đa cấp (nested replies) trên tasks |
| 5 | Lịch và sự kiện | Xem lịch, tạo sự kiện, nhắc nhở deadline |
| 6 | Thông báo | Thông báo real-time các hoạt động |
| 7 | Báo cáo thống kê | Dashboard, biểu đồ tiến độ, export báo cáo |
| 8 | Phân quyền | Hệ thống RBAC 4 cấp: Admin, Manager, Member, Guest |
| 9 | Bảo mật | Chống SQL Injection, XSS, CSRF, Rate Limiting |
| 10 | Responsive | Giao diện tương thích đa thiết bị |

## 1.4. Đối tượng và phạm vi nghiên cứu

### 1.4.1. Đối tượng nghiên cứu

- Quy trình quản lý dự án theo phương pháp Agile/Kanban
- Kiến trúc phần mềm MVC và RESTful API
- Hệ thống phân quyền RBAC
- Các kỹ thuật bảo mật ứng dụng web

### 1.4.2. Đối tượng sử dụng

| Vai trò | Mô tả | Quyền hạn |
|---------|-------|-----------|
| Admin | Quản trị viên hệ thống | Toàn quyền |
| Manager | Quản lý dự án/nhóm | Quản lý dự án, tasks, thành viên |
| Member | Thành viên dự án | Tạo/sửa tasks, documents của mình |
| Guest | Khách | Chỉ xem |

### 1.4.3. Phạm vi đề tài

**Phạm vi thực hiện:**
- Xây dựng ứng dụng web hoàn chỉnh với đầy đủ chức năng quản lý dự án
- Hệ thống self-hosted, có thể triển khai trên server riêng
- Giao diện tiếng Việt, phù hợp người dùng Việt Nam

**Giới hạn:**
- Chưa có ứng dụng mobile native
- Chưa tích hợp real-time notifications (WebSocket)
- Chưa tích hợp với các dịch vụ bên ngoài (Slack, Email marketing)

## 1.5. Phương pháp nghiên cứu

### 1.5.1. Phương pháp nghiên cứu lý thuyết
- Nghiên cứu tài liệu về quản lý dự án, phương pháp Agile/Kanban
- Tìm hiểu kiến trúc MVC, RESTful API, RBAC
- Nghiên cứu các công nghệ: PHP, MySQL, Tailwind CSS, Alpine.js

### 1.5.2. Phương pháp phân tích và thiết kế
- Phân tích yêu cầu từ thực tế sử dụng các phần mềm tương tự
- Thiết kế hệ thống theo mô hình UML
- Thiết kế cơ sở dữ liệu chuẩn hóa 3NF

### 1.5.3. Phương pháp thực nghiệm
- Phát triển phần mềm theo quy trình Agile
- Kiểm thử liên tục trong quá trình phát triển
- Thu thập phản hồi và cải tiến

## 1.6. Bố cục báo cáo

Báo cáo đồ án được tổ chức thành 6 chương:

**Chương 1: Giới thiệu đề tài**
Trình bày bối cảnh, lý do chọn đề tài, mục tiêu, đối tượng và phạm vi nghiên cứu.

**Chương 2: Cơ sở lý thuyết và công nghệ**
Trình bày các lý thuyết nền tảng về quản lý dự án, kiến trúc MVC, các công nghệ sử dụng trong đề tài.

**Chương 3: Phân tích hệ thống**
Phân tích yêu cầu chức năng, phi chức năng; xây dựng các sơ đồ Use Case, Activity Diagram.

**Chương 4: Thiết kế hệ thống**
Thiết kế kiến trúc, cơ sở dữ liệu, giao diện và API.

**Chương 5: Cài đặt và kiểm thử**
Trình bày quá trình cài đặt, kết quả thực hiện và kiểm thử hệ thống.

**Chương 6: Kết luận và hướng phát triển**
Tổng kết kết quả đạt được, hạn chế và đề xuất hướng phát triển.


---

# CHƯƠNG 2: CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ

## 2.1. Tổng quan về hệ thống quản lý dự án

### 2.1.1. Khái niệm

Hệ thống quản lý dự án (Project Management System - PMS) là phần mềm hỗ trợ việc lập kế hoạch, tổ chức, theo dõi và kiểm soát các dự án. Hệ thống giúp các nhóm làm việc cộng tác hiệu quả, quản lý tài nguyên và đảm bảo dự án hoàn thành đúng tiến độ.

### 2.1.2. Các chức năng cốt lõi

- **Quản lý công việc (Task Management)**: Tạo, phân công, theo dõi trạng thái công việc
- **Quản lý tài liệu (Document Management)**: Lưu trữ, tổ chức, chia sẻ tài liệu
- **Quản lý nhóm (Team Management)**: Phân quyền, quản lý thành viên
- **Theo dõi tiến độ (Progress Tracking)**: Dashboard, biểu đồ, báo cáo
- **Lịch và nhắc nhở (Calendar & Reminders)**: Quản lý deadline, sự kiện

### 2.1.3. Phương pháp Kanban

Kanban là phương pháp quản lý công việc trực quan, sử dụng bảng với các cột trạng thái:

```
┌─────────────┬─────────────┬─────────────┬─────────────┬─────────────┐
│   BACKLOG   │    TODO     │ IN PROGRESS │   REVIEW    │    DONE     │
├─────────────┼─────────────┼─────────────┼─────────────┼─────────────┤
│ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │
│ │ Task 1  │ │ │ Task 3  │ │ │ Task 5  │ │ │ Task 7  │ │ │ Task 9  │ │
│ └─────────┘ │ └─────────┘ │ └─────────┘ │ └─────────┘ │ └─────────┘ │
│ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │             │ ┌─────────┐ │
│ │ Task 2  │ │ │ Task 4  │ │ │ Task 6  │ │             │ │ Task 10 │ │
│ └─────────┘ │ └─────────┘ │ └─────────┘ │             │ └─────────┘ │
└─────────────┴─────────────┴─────────────┴─────────────┴─────────────┘
```

**Hình 2.1: Mô hình Kanban Board**

**Ưu điểm của Kanban:**
- Trực quan hóa quy trình làm việc
- Giới hạn công việc đang thực hiện (WIP)
- Linh hoạt, dễ thích ứng với thay đổi
- Cải tiến liên tục

## 2.2. Kiến trúc MVC (Model-View-Controller)

### 2.2.1. Khái niệm

MVC là mẫu kiến trúc phần mềm chia ứng dụng thành 3 thành phần:

```
┌─────────────────────────────────────────────────────────────┐
│                        USER REQUEST                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       CONTROLLER                             │
│  • Nhận request từ user                                      │
│  • Xử lý logic điều hướng                                    │
│  • Gọi Model để lấy/cập nhật dữ liệu                        │
│  • Chọn View phù hợp để hiển thị                            │
└─────────────────────────────────────────────────────────────┘
                    │                   │
                    ▼                   ▼
┌──────────────────────────┐  ┌──────────────────────────────┐
│         MODEL            │  │           VIEW               │
│  • Quản lý dữ liệu       │  │  • Hiển thị giao diện        │
│  • Business logic        │  │  • Nhận dữ liệu từ Controller│
│  • Tương tác Database    │  │  • Render HTML/JSON          │
└──────────────────────────┘  └──────────────────────────────┘
```

**Hình 2.2: Mô hình kiến trúc MVC**

### 2.2.2. Áp dụng trong TaskFlow

```
taskflow/
├── app/
│   ├── controllers/          # Controllers
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   └── TaskController.php
│   │
│   ├── models/               # Models
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Project.php
│   │   └── Task.php
│   │
│   ├── views/                # Views
│   │   ├── layouts/
│   │   ├── dashboard/
│   │   ├── projects/
│   │   └── tasks/
│   │
│   └── middleware/           # Middleware
│       ├── AuthMiddleware.php
│       └── PermissionMiddleware.php
│
├── core/                     # Core Framework
│   ├── Database.php
│   ├── Session.php
│   ├── View.php
│   └── Permission.php
│
└── config/                   # Configuration
    ├── app.php
    ├── database.php
    └── permissions.php
```

**Hình 2.3: Cấu trúc thư mục MVC của TaskFlow**

## 2.3. Công nghệ sử dụng

### 2.3.1. So sánh ngôn ngữ Backend

| Tiêu chí | PHP | Node.js | Python | Java |
|----------|-----|---------|--------|------|
| **Hiệu năng** | Trung bình | Cao | Trung bình | Cao |
| **Học tập** | Dễ | Trung bình | Dễ | Khó |
| **Hosting** | Rất phổ biến | Phổ biến | Phổ biến | Đắt |
| **Chi phí** | Thấp | Trung bình | Trung bình | Cao |

**Bảng 2.1: So sánh các ngôn ngữ Backend**

**Lý do chọn PHP:**
- Phổ biến, dễ triển khai trên hầu hết hosting
- Chi phí thấp, phù hợp doanh nghiệp nhỏ
- Cộng đồng lớn, nhiều tài liệu
- Tích hợp tốt với MySQL

### 2.3.2. So sánh hệ quản trị CSDL

| Tiêu chí | MySQL | PostgreSQL | MongoDB | SQLite |
|----------|-------|------------|---------|--------|
| **Loại** | Relational | Relational | NoSQL | Relational |
| **Hiệu năng** | Cao | Cao | Rất cao | Trung bình |
| **Tính năng** | Đầy đủ | Rất đầy đủ | Linh hoạt | Cơ bản |
| **Phù hợp** | Web app | Enterprise | Big data | Mobile |

**Bảng 2.2: So sánh các hệ quản trị CSDL**

**Lý do chọn MySQL:**
- Phổ biến nhất trong web development
- Tích hợp native với PHP
- Hiệu năng tốt cho ứng dụng web
- Có phpMyAdmin và nhiều công cụ GUI

### 2.3.3. So sánh CSS Framework

| Tiêu chí | Tailwind CSS | Bootstrap | Bulma |
|----------|--------------|-----------|-------|
| **Approach** | Utility-first | Component-based | Component-based |
| **Customization** | Rất cao | Trung bình | Cao |
| **File size** | Nhỏ (purge) | Lớn | Trung bình |
| **Flexibility** | Rất cao | Trung bình | Trung bình |

**Bảng 2.3: So sánh các CSS Framework**

**Lý do chọn Tailwind CSS:**
- Utility-first: viết CSS trực tiếp trong HTML
- Highly customizable theo design
- Hỗ trợ responsive và dark mode native
- File size nhỏ sau khi purge

### 2.3.4. Alpine.js

Alpine.js là framework JavaScript nhẹ (15KB), phù hợp cho tương tác đơn giản:

- Cú pháp đơn giản, giống Vue.js
- Không cần build, sử dụng qua CDN
- Kết hợp tốt với server-side rendering

## 2.4. Hệ thống phân quyền RBAC

### 2.4.1. Khái niệm RBAC

RBAC (Role-Based Access Control) là mô hình kiểm soát truy cập dựa trên vai trò. Quyền được gán cho vai trò, người dùng được gán vai trò.

```
┌─────────────────────────────────────────────────────────────┐
│                         USERS                                │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐        │
│  │  Admin  │  │ Manager │  │ Member  │  │  Guest  │        │
│  └────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘        │
└───────┼────────────┼────────────┼────────────┼──────────────┘
        │            │            │            │
        ▼            ▼            ▼            ▼
┌─────────────────────────────────────────────────────────────┐
│                         ROLES                                │
│  Admin: Toàn quyền hệ thống                                 │
│  Manager: Quản lý dự án, nhóm                               │
│  Member: Tạo/sửa task, document của mình                    │
│  Guest: Chỉ xem                                             │
└─────────────────────────────────────────────────────────────┘
        │            │            │            │
        ▼            ▼            ▼            ▼
┌─────────────────────────────────────────────────────────────┐
│                      PERMISSIONS                             │
│  users.*, projects.*, tasks.*, documents.*, admin.*         │
└─────────────────────────────────────────────────────────────┘
```

**Hình 2.4: Mô hình RBAC trong TaskFlow**

### 2.4.2. Ma trận phân quyền

| Quyền | Admin | Manager | Member | Guest |
|-------|:-----:|:-------:|:------:|:-----:|
| users.view | ✅ | ✅ | ✅ | ❌ |
| users.create | ✅ | ❌ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ | ❌ |
| projects.view | ✅ | ✅ | ✅ | ✅ |
| projects.create | ✅ | ✅ | ✅ | ❌ |
| projects.edit | ✅ | ✅ | ❌ | ❌ |
| projects.delete | ✅ | ❌ | ❌ | ❌ |
| tasks.view | ✅ | ✅ | ✅ | ✅ |
| tasks.create | ✅ | ✅ | ✅ | ❌ |
| tasks.edit | ✅ | ✅ | ✅ | ❌ |
| tasks.delete | ✅ | ✅ | ❌ | ❌ |
| admin.access | ✅ | ❌ | ❌ | ❌ |

**Bảng 2.4: Ma trận phân quyền chi tiết**

## 2.5. Bảo mật ứng dụng web

### 2.5.1. Các mối đe dọa phổ biến (OWASP Top 10)

| Mối đe dọa | Mô tả | Giải pháp |
|------------|-------|-----------|
| SQL Injection | Chèn mã SQL độc hại | Prepared Statements |
| XSS | Chèn script độc hại | Output escaping |
| CSRF | Giả mạo request | CSRF tokens |
| Broken Auth | Xác thực yếu | Bcrypt, session management |

### 2.5.2. Các biện pháp áp dụng trong TaskFlow

**1. Chống SQL Injection:**
```php
// Prepared Statement với PDO
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
```

**2. Chống XSS:**
```php
// Output escaping
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

**3. Password Hashing:**
```php
// Bcrypt với cost 12
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

**4. Session Security:**
```php
session_set_cookie_params([
    'secure' => true,      // HTTPS only
    'httponly' => true,    // Chống XSS
    'samesite' => 'Lax',   // Chống CSRF
]);
```


---

# CHƯƠNG 3: PHÂN TÍCH HỆ THỐNG

## 3.1. Khảo sát hiện trạng

### 3.1.1. Khảo sát các giải pháp hiện có

Qua khảo sát các phần mềm quản lý dự án phổ biến trên thị trường:

| Phần mềm | Ưu điểm | Nhược điểm |
|----------|---------|------------|
| **Trello** | Giao diện đơn giản, miễn phí cơ bản | Giới hạn tính năng, không self-hosted |
| **Asana** | Nhiều tính năng, tích hợp tốt | Chi phí cao, phức tạp |
| **Jira** | Mạnh mẽ, phù hợp Agile | Phức tạp, chi phí cao |
| **Monday.com** | Giao diện đẹp, linh hoạt | Chi phí rất cao |

### 3.1.2. Nhu cầu thực tế

Từ khảo sát, xác định các nhu cầu chính:
- Giao diện tiếng Việt, dễ sử dụng
- Chi phí hợp lý hoặc miễn phí
- Self-hosted để kiểm soát dữ liệu
- Đầy đủ tính năng cơ bản: tasks, projects, documents
- Phân quyền linh hoạt

## 3.2. Yêu cầu chức năng

### 3.2.1. Danh sách yêu cầu chức năng

| Mã | Tên chức năng | Mô tả | Độ ưu tiên |
|----|---------------|-------|------------|
| **FR01** | Đăng nhập/Đăng xuất | Xác thực người dùng | Cao |
| **FR02** | Đăng ký tài khoản | Tạo tài khoản mới | Cao |
| **FR03** | Quên mật khẩu | Reset password qua email | Trung bình |
| **FR04** | Quản lý dự án | CRUD dự án | Cao |
| **FR05** | Quản lý thành viên dự án | Thêm/xóa/phân quyền thành viên | Cao |
| **FR06** | Quản lý công việc | CRUD tasks | Cao |
| **FR07** | Kanban Board | Kéo thả tasks giữa các cột | Cao |
| **FR08** | Checklist | Tạo/quản lý checklist trong task | Trung bình |
| **FR09** | Giao việc | Assign task cho thành viên | Cao |
| **FR10** | Bình luận | Comment trên tasks | Trung bình |
| **FR11** | Nested Replies | Trả lời bình luận đa cấp | Trung bình |
| **FR12** | Quản lý tài liệu | Upload/download files | Cao |
| **FR13** | Thư mục | Tổ chức tài liệu theo thư mục | Trung bình |
| **FR14** | Chia sẻ tài liệu | Share documents với thành viên | Trung bình |
| **FR15** | Lịch | Xem lịch tháng/tuần | Trung bình |
| **FR16** | Sự kiện | Tạo/quản lý events | Trung bình |
| **FR17** | Thông báo | Notifications cho các hoạt động | Trung bình |
| **FR18** | Dashboard | Thống kê tổng quan | Cao |
| **FR19** | Báo cáo | Biểu đồ tiến độ, export | Trung bình |
| **FR20** | Tìm kiếm | Tìm kiếm toàn cục | Trung bình |
| **FR21** | Quản lý users (Admin) | CRUD users | Cao |
| **FR22** | Cài đặt hệ thống | Cấu hình hệ thống | Trung bình |
| **FR23** | Activity Logs | Lịch sử hoạt động | Thấp |
| **FR24** | Backup | Sao lưu dữ liệu | Thấp |

**Bảng 3.1: Danh sách yêu cầu chức năng**

## 3.3. Yêu cầu phi chức năng

| Mã | Yêu cầu | Mô tả |
|----|---------|-------|
| **NFR01** | Hiệu năng | Thời gian phản hồi < 2 giây |
| **NFR02** | Bảo mật | Chống SQL Injection, XSS, CSRF |
| **NFR03** | Khả năng mở rộng | Hỗ trợ 100+ users đồng thời |
| **NFR04** | Tương thích | Hoạt động trên Chrome, Firefox, Safari, Edge |
| **NFR05** | Responsive | Tương thích mobile, tablet, desktop |
| **NFR06** | Khả dụng | Uptime 99% |
| **NFR07** | Bảo trì | Code clean, có documentation |
| **NFR08** | Ngôn ngữ | Giao diện tiếng Việt |

**Bảng 3.2: Danh sách yêu cầu phi chức năng**

## 3.4. Sơ đồ Use Case

### 3.4.1. Use Case tổng quát

```
                              ┌─────────────────────────────────────┐
                              │           HỆ THỐNG TASKFLOW         │
                              │                                     │
    ┌───────┐                 │  ┌─────────────────────────────┐   │
    │       │                 │  │      Quản lý tài khoản      │   │
    │ Guest │─────────────────┼──│  • Đăng nhập                │   │
    │       │                 │  │  • Đăng ký                  │   │
    └───────┘                 │  │  • Quên mật khẩu            │   │
                              │  └─────────────────────────────┘   │
    ┌───────┐                 │                                     │
    │       │                 │  ┌─────────────────────────────┐   │
    │Member │─────────────────┼──│      Quản lý dự án          │   │
    │       │                 │  │  • Xem danh sách dự án      │   │
    └───────┘                 │  │  • Tạo dự án mới            │   │
        │                     │  │  • Xem chi tiết dự án       │   │
        │                     │  └─────────────────────────────┘   │
        │                     │                                     │
        │                     │  ┌─────────────────────────────┐   │
        │                     │  │      Quản lý công việc      │   │
        └─────────────────────┼──│  • Xem Kanban Board         │   │
                              │  │  • Tạo/sửa/xóa task         │   │
    ┌───────┐                 │  │  • Kéo thả task             │   │
    │       │                 │  │  • Checklist                │   │
    │Manager│─────────────────┼──│  • Bình luận                │   │
    │       │                 │  └─────────────────────────────┘   │
    └───────┘                 │                                     │
        │                     │  ┌─────────────────────────────┐   │
        │                     │  │      Quản lý tài liệu       │   │
        │                     │  │  • Upload/Download          │   │
        └─────────────────────┼──│  • Tạo thư mục              │   │
                              │  │  • Chia sẻ                  │   │
    ┌───────┐                 │  └─────────────────────────────┘   │
    │       │                 │                                     │
    │ Admin │─────────────────┼──┌─────────────────────────────┐   │
    │       │                 │  │      Quản trị hệ thống      │   │
    └───────┘                 │  │  • Quản lý users            │   │
                              │  │  • Cài đặt hệ thống         │   │
                              │  │  • Xem logs                 │   │
                              │  │  • Backup                   │   │
                              │  └─────────────────────────────┘   │
                              │                                     │
                              └─────────────────────────────────────┘
```

**Hình 3.1: Sơ đồ Use Case tổng quát**

### 3.4.2. Use Case quản lý công việc

```
                    ┌─────────────────────────────────────────────┐
                    │         QUẢN LÝ CÔNG VIỆC                   │
                    │                                             │
                    │    ┌─────────────────────┐                  │
                    │    │   Xem danh sách     │                  │
    ┌───────┐       │    │      tasks          │                  │
    │       │───────┼───>│                     │                  │
    │ User  │       │    └─────────────────────┘                  │
    │       │       │              │                              │
    └───────┘       │              │ <<include>>                  │
        │           │              ▼                              │
        │           │    ┌─────────────────────┐                  │
        │           │    │   Xem Kanban Board  │                  │
        │───────────┼───>│                     │                  │
        │           │    └─────────────────────┘                  │
        │           │                                             │
        │           │    ┌─────────────────────┐                  │
        │───────────┼───>│    Tạo task mới     │                  │
        │           │    └─────────────────────┘                  │
        │           │              │                              │
        │           │              │ <<extend>>                   │
        │           │              ▼                              │
        │           │    ┌─────────────────────┐                  │
        │           │    │   Giao việc         │                  │
        │───────────┼───>│                     │                  │
        │           │    └─────────────────────┘                  │
        │           │                                             │
        │           │    ┌─────────────────────┐                  │
        │───────────┼───>│   Cập nhật task     │                  │
        │           │    └─────────────────────┘                  │
        │           │              │                              │
        │           │              │ <<extend>>                   │
        │           │              ▼                              │
        │           │    ┌─────────────────────┐                  │
        │           │    │   Kéo thả (Kanban)  │                  │
        │───────────┼───>│                     │                  │
        │           │    └─────────────────────┘                  │
        │           │                                             │
        │           │    ┌─────────────────────┐                  │
        │───────────┼───>│   Quản lý Checklist │                  │
        │           │    └─────────────────────┘                  │
        │           │                                             │
        │           │    ┌─────────────────────┐                  │
        └───────────┼───>│   Bình luận         │                  │
                    │    └─────────────────────┘                  │
                    │              │                              │
                    │              │ <<extend>>                   │
                    │              ▼                              │
                    │    ┌─────────────────────┐                  │
                    │    │   Trả lời (Nested)  │                  │
                    │    └─────────────────────┘                  │
                    │                                             │
                    └─────────────────────────────────────────────┘
```

**Hình 3.2: Sơ đồ Use Case quản lý công việc**

## 3.5. Đặc tả Use Case

### 3.5.1. UC01 - Đăng nhập

| Thuộc tính | Mô tả |
|------------|-------|
| **Mã Use Case** | UC01 |
| **Tên** | Đăng nhập |
| **Actor** | Guest |
| **Mô tả** | Người dùng đăng nhập vào hệ thống |
| **Tiền điều kiện** | Người dùng đã có tài khoản |
| **Hậu điều kiện** | Người dùng được xác thực và chuyển đến Dashboard |

**Luồng chính:**
1. Người dùng truy cập trang đăng nhập
2. Hệ thống hiển thị form đăng nhập
3. Người dùng nhập email và mật khẩu
4. Người dùng nhấn nút "Đăng nhập"
5. Hệ thống xác thực thông tin
6. Hệ thống tạo session và chuyển đến Dashboard

**Luồng thay thế:**
- 5a. Email không tồn tại: Hiển thị thông báo lỗi
- 5b. Mật khẩu sai: Hiển thị thông báo lỗi
- 5c. Tài khoản bị khóa: Hiển thị thông báo tài khoản bị khóa

**Bảng 3.3: Đặc tả Use Case UC01 - Đăng nhập**

### 3.5.2. UC04 - Quản lý dự án

| Thuộc tính | Mô tả |
|------------|-------|
| **Mã Use Case** | UC04 |
| **Tên** | Quản lý dự án |
| **Actor** | Member, Manager, Admin |
| **Mô tả** | Người dùng tạo, xem, sửa, xóa dự án |
| **Tiền điều kiện** | Người dùng đã đăng nhập |
| **Hậu điều kiện** | Dự án được tạo/cập nhật/xóa thành công |

**Luồng chính (Tạo dự án):**
1. Người dùng nhấn nút "Tạo dự án mới"
2. Hệ thống hiển thị form tạo dự án
3. Người dùng nhập thông tin: tên, mô tả, màu sắc, ngày bắt đầu/kết thúc
4. Người dùng nhấn "Lưu"
5. Hệ thống validate dữ liệu
6. Hệ thống tạo dự án và thêm người tạo làm Owner
7. Hệ thống chuyển đến trang chi tiết dự án

**Bảng 3.4: Đặc tả Use Case UC04 - Quản lý dự án**

### 3.5.3. Sơ đồ hoạt động - Đăng nhập

```
┌─────────┐                              ┌─────────┐
│  User   │                              │ System  │
└────┬────┘                              └────┬────┘
     │                                        │
     │  1. Truy cập trang đăng nhập          │
     │───────────────────────────────────────>│
     │                                        │
     │  2. Hiển thị form đăng nhập           │
     │<───────────────────────────────────────│
     │                                        │
     │  3. Nhập email, password              │
     │───────────────────────────────────────>│
     │                                        │
     │                    ┌───────────────────┴───────────────────┐
     │                    │         Validate dữ liệu              │
     │                    └───────────────────┬───────────────────┘
     │                                        │
     │                    ┌───────────────────┴───────────────────┐
     │                    │      Email tồn tại?                   │
     │                    └───────────────────┬───────────────────┘
     │                              │                   │
     │                           Có │                   │ Không
     │                              ▼                   ▼
     │                    ┌─────────────────┐  ┌─────────────────┐
     │                    │ Verify password │  │ Báo lỗi email   │
     │                    └────────┬────────┘  └────────┬────────┘
     │                             │                    │
     │                    ┌────────┴────────┐          │
     │                    │ Password đúng?  │          │
     │                    └────────┬────────┘          │
     │                      │             │            │
     │                   Đúng│          Sai│           │
     │                      ▼             ▼            │
     │              ┌─────────────┐ ┌─────────────┐    │
     │              │Tạo session  │ │Báo lỗi pass │    │
     │              │Regenerate ID│ └──────┬──────┘    │
     │              └──────┬──────┘        │           │
     │                     │               │           │
     │  4. Redirect Dashboard              │           │
     │<────────────────────┘               │           │
     │                                     │           │
     │  5. Hiển thị lỗi                   │           │
     │<────────────────────────────────────┴───────────┘
     │                                        │
     ▼                                        ▼
```

**Hình 3.5: Sơ đồ hoạt động đăng nhập**


---

# CHƯƠNG 4: THIẾT KẾ HỆ THỐNG

## 4.1. Thiết kế kiến trúc

### 4.1.1. Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT LAYER                                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │   Browser   │  │   Mobile    │  │   Tablet    │  │   Desktop   │    │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘    │
└─────────┼────────────────┼────────────────┼────────────────┼────────────┘
          └────────────────┴────────────────┴────────────────┘
                                    │ HTTP/HTTPS
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                                │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                    Tailwind CSS + Alpine.js                      │    │
│  │  • Responsive Design    • Dark Mode    • Interactive Components  │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        APPLICATION LAYER (PHP 8.x)                       │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐               │
│  │  Controllers  │  │   Middleware  │  │     Core      │               │
│  │  • Auth       │  │  • Auth       │  │  • Database   │               │
│  │  • Project    │  │  • Permission │  │  • Session    │               │
│  │  • Task       │  │               │  │  • View       │               │
│  │  • Document   │  │               │  │  • Permission │               │
│  │  • Admin      │  │               │  │  • Validator  │               │
│  └───────────────┘  └───────────────┘  └───────────────┘               │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐               │
│  │    Models     │  │     Views     │  │      API      │               │
│  │  • User       │  │  • Layouts    │  │  • REST API   │               │
│  │  • Project    │  │  • Components │  │  • JSON       │               │
│  │  • Task       │  │  • Pages      │  │  • AJAX       │               │
│  │  • Document   │  │               │  │               │               │
│  └───────────────┘  └───────────────┘  └───────────────┘               │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          DATA LAYER                                      │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                      MySQL 8.0 Database                          │    │
│  │  • 16 Tables    • UUID Primary Keys    • Foreign Key Constraints │    │
│  │  • Indexes      • Triggers             • Stored Procedures       │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 4.1: Kiến trúc tổng thể hệ thống TaskFlow**

### 4.1.2. Luồng xử lý Request

```
┌─────────┐     ┌───────────┐     ┌────────────┐     ┌──────────┐     ┌──────────┐
│ Browser │────>│ bootstrap │────>│ Middleware │────>│Controller│────>│  Model   │
└─────────┘     └───────────┘     └────────────┘     └──────────┘     └──────────┘
                                        │                 │                │
                                        │                 │                │
                                   Auth Check        Business          Database
                                   Permission         Logic             Query
                                        │                 │                │
                                        ▼                 ▼                ▼
┌─────────┐     ┌───────────┐     ┌────────────┐     ┌──────────┐     ┌──────────┐
│ Browser │<────│   View    │<────│ Controller │<────│  Model   │<────│ Database │
└─────────┘     └───────────┘     └────────────┘     └──────────┘     └──────────┘
                   Render            Response           Data              Result
                   HTML/JSON
```

**Hình 4.2: Luồng xử lý Request**

## 4.2. Thiết kế cơ sở dữ liệu

### 4.2.1. Sơ đồ ERD

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     USERS       │       │    PROJECTS     │       │     TASKS       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK, UUID)   │◄──┐   │ id (PK, UUID)   │◄──┐   │ id (PK, UUID)   │
│ email (UNIQUE)  │   │   │ name            │   │   │ title           │
│ password_hash   │   │   │ description     │   │   │ description     │
│ full_name       │   │   │ color           │   │   │ status          │
│ avatar_url      │   │   │ status          │   │   │ priority        │
│ role            │   │   │ priority        │   │   │ position        │
│ department      │   │   │ progress        │   │   │ due_date        │
│ is_active       │   │   │ start_date      │   │   │ completed_at    │
│ created_at      │   │   │ end_date        │   │   │ project_id (FK) │──┐
│ updated_at      │   │   │ created_by (FK) │───┘   │ created_by (FK) │──┤
└────────┬────────┘   │   │ created_at      │       │ created_at      │  │
         │            │   └────────┬────────┘       └────────┬────────┘  │
         │            │            │                         │           │
         ▼            │            ▼                         ▼           │
┌─────────────────────┴───┐  ┌─────────────────┐  ┌─────────────────────┴─┐
│   PROJECT_MEMBERS       │  │   DOCUMENTS     │  │   TASK_ASSIGNEES      │
├─────────────────────────┤  ├─────────────────┤  ├───────────────────────┤
│ project_id (PK, FK)     │  │ id (PK, UUID)   │  │ task_id (PK, FK)      │
│ user_id (PK, FK)        │  │ name            │  │ user_id (PK, FK)      │
│ role                    │  │ type            │  │ assigned_by (FK)      │
│ joined_at               │  │ file_path       │  │ assigned_at           │
└─────────────────────────┘  │ project_id (FK) │  └───────────────────────┘
                             │ uploaded_by(FK) │
                             └─────────────────┘  ┌─────────────────────────┐
                                                  │   TASK_CHECKLISTS      │
┌─────────────────┐       ┌─────────────────┐     ├─────────────────────────┤
│    COMMENTS     │       │  NOTIFICATIONS  │     │ id (PK, UUID)          │
├─────────────────┤       ├─────────────────┤     │ task_id (FK)           │
│ id (PK, UUID)   │       │ id (PK, UUID)   │     │ title                  │
│ entity_type     │       │ user_id (FK)    │     │ is_completed           │
│ entity_id       │       │ type            │     │ position               │
│ content         │       │ title           │     │ completed_at           │
│ parent_id (FK)  │◄──┐   │ message         │     └─────────────────────────┘
│ created_by (FK) │   │   │ is_read         │
│ created_at      │   │   │ created_at      │     ┌─────────────────────────┐
└────────┬────────┘   │   └─────────────────┘     │   CALENDAR_EVENTS      │
         │            │                           ├─────────────────────────┤
         └────────────┘                           │ id (PK, UUID)          │
         (Self-referencing                        │ title                  │
          for nested replies)                     │ type                   │
                                                  │ start_time             │
┌─────────────────┐       ┌─────────────────┐     │ end_time               │
│  ACTIVITY_LOGS  │       │  USER_SETTINGS  │     │ project_id (FK)        │
├─────────────────┤       ├─────────────────┤     │ created_by (FK)        │
│ id (PK, UUID)   │       │ user_id (PK,FK) │     └─────────────────────────┘
│ user_id (FK)    │       │ theme           │
│ entity_type     │       │ language        │
│ entity_id       │       │ timezone        │
│ action          │       │ notification_*  │
│ old_values      │       └─────────────────┘
│ new_values      │
│ ip_address      │
│ created_at      │
└─────────────────┘
```

**Hình 4.3: Sơ đồ ERD của TaskFlow**

### 4.2.2. Chi tiết các bảng

**Bảng USERS:**

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | VARCHAR(36) | PRIMARY KEY | UUID |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email đăng nhập |
| password_hash | VARCHAR(255) | NOT NULL | Mật khẩu mã hóa bcrypt |
| full_name | VARCHAR(100) | NOT NULL | Họ tên |
| avatar_url | VARCHAR(500) | NULL | URL ảnh đại diện |
| role | ENUM | NOT NULL | admin/manager/member/guest |
| department | VARCHAR(100) | NULL | Phòng ban |
| position | VARCHAR(100) | NULL | Chức vụ |
| phone | VARCHAR(20) | NULL | Số điện thoại |
| is_active | TINYINT(1) | DEFAULT 1 | Trạng thái hoạt động |
| last_login_at | DATETIME | NULL | Lần đăng nhập cuối |
| created_at | DATETIME | DEFAULT NOW | Ngày tạo |
| updated_at | DATETIME | ON UPDATE | Ngày cập nhật |

**Bảng 4.1: Cấu trúc bảng users**

**Bảng TASKS:**

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | VARCHAR(36) | PRIMARY KEY | UUID |
| project_id | VARCHAR(36) | FOREIGN KEY | ID dự án |
| title | VARCHAR(500) | NOT NULL | Tiêu đề |
| description | TEXT | NULL | Mô tả chi tiết |
| status | ENUM | NOT NULL | backlog/todo/in_progress/in_review/done |
| priority | ENUM | NOT NULL | low/medium/high/urgent |
| position | INT | DEFAULT 0 | Vị trí trong Kanban |
| due_date | DATE | NULL | Ngày hết hạn |
| completed_at | DATETIME | NULL | Ngày hoàn thành |
| estimated_hours | DECIMAL(6,2) | NULL | Giờ ước tính |
| actual_hours | DECIMAL(6,2) | NULL | Giờ thực tế |
| created_by | VARCHAR(36) | FOREIGN KEY | Người tạo |
| created_at | DATETIME | DEFAULT NOW | Ngày tạo |

**Bảng 4.2: Cấu trúc bảng tasks**

**Bảng COMMENTS (hỗ trợ nested replies):**

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | VARCHAR(36) | PRIMARY KEY | UUID |
| entity_type | ENUM | NOT NULL | task/document/project |
| entity_id | VARCHAR(36) | NOT NULL | ID của entity |
| content | TEXT | NOT NULL | Nội dung bình luận |
| parent_id | VARCHAR(36) | FOREIGN KEY (self) | ID comment cha (nested) |
| created_by | VARCHAR(36) | FOREIGN KEY | Người tạo |
| created_at | DATETIME | DEFAULT NOW | Ngày tạo |

**Bảng 4.3: Cấu trúc bảng comments**

## 4.3. Thiết kế giao diện

### 4.3.1. Layout chính

```
┌─────────────────────────────────────────────────────────────────────────┐
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │ HEADER                                                          │    │
│  │ [Logo] [Search...                    ] [🔔] [👤 User ▼]         │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│  ┌──────────┐  ┌───────────────────────────────────────────────────┐    │
│  │ SIDEBAR  │  │                                                   │    │
│  │          │  │                    CONTENT                        │    │
│  │ 🏠 Home  │  │                                                   │    │
│  │ 📋 Tasks │  │  ┌─────────────────────────────────────────────┐  │    │
│  │ 📁 Proj. │  │  │                                             │  │    │
│  │ 📄 Docs  │  │  │         Main Content Area                   │  │    │
│  │ 📅 Cal.  │  │  │                                             │  │    │
│  │ 👥 Team  │  │  │                                             │  │    │
│  │ 📊 Report│  │  │                                             │  │    │
│  │          │  │  │                                             │  │    │
│  │ ──────── │  │  └─────────────────────────────────────────────┘  │    │
│  │ ⚙️ Admin │  │                                                   │    │
│  │          │  │                                                   │    │
│  └──────────┘  └───────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 4.4: Thiết kế Layout chính**

### 4.3.2. Giao diện Kanban Board

```
┌─────────────────────────────────────────────────────────────────────────┐
│  Kanban Board                                        [+ Tạo task mới]   │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐ │
│  │ BACKLOG   │ │   TODO    │ │IN PROGRESS│ │ IN REVIEW │ │   DONE    │ │
│  │    (5)    │ │    (8)    │ │    (4)    │ │    (2)    │ │   (25)    │ │
│  ├───────────┤ ├───────────┤ ├───────────┤ ├───────────┤ ├───────────┤ │
│  │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │
│  │ │🔴 High│ │ │ │🟡 Med │ │ │ │🟢 Low │ │ │ │🔴 High│ │ │ │✅ Done│ │ │
│  │ │Task 1 │ │ │ │Task 6 │ │ │ │Task 14│ │ │ │Task 18│ │ │ │Task 20│ │ │
│  │ │       │ │ │ │       │ │ │ │       │ │ │ │       │ │ │ │       │ │ │
│  │ │📅 Dec │ │ │ │📅 Dec │ │ │ │👤 Nam │ │ │ │👤 Lan │ │ │ │       │ │ │
│  │ │   25  │ │ │ │   28  │ │ │ │       │ │ │ │       │ │ │ │       │ │ │
│  │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │
│  │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │           │ │ ┌───────┐ │ │
│  │ │🟡 Med │ │ │ │🔴 High│ │ │ │🟡 Med │ │ │           │ │ │✅ Done│ │ │
│  │ │Task 2 │ │ │ │Task 7 │ │ │ │Task 15│ │ │           │ │ │Task 21│ │ │
│  │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │           │ │ └───────┘ │ │
│  │           │ │           │ │           │ │           │ │           │ │
│  │  [+ Add]  │ │  [+ Add]  │ │  [+ Add]  │ │  [+ Add]  │ │           │ │
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘ └───────────┘ │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 4.5: Thiết kế giao diện Kanban Board**

## 4.4. Thiết kế API

### 4.4.1. Danh sách API Endpoints

| Endpoint | Method | Mô tả | Auth |
|----------|--------|-------|------|
| `/api/create-project.php` | POST | Tạo dự án mới | ✅ |
| `/api/update-project.php` | POST | Cập nhật dự án | ✅ |
| `/api/create-task.php` | POST | Tạo công việc | ✅ |
| `/api/update-task.php` | POST/DELETE | Cập nhật/xóa task | ✅ |
| `/api/comments.php` | GET/POST/PUT/DELETE | CRUD comments | ✅ |
| `/api/checklist.php` | POST/PUT/DELETE | Quản lý checklist | ✅ |
| `/api/upload-document.php` | POST | Upload tài liệu | ✅ |
| `/api/calendar.php` | GET/POST/PUT/DELETE | Quản lý sự kiện | ✅ |
| `/api/notifications.php` | GET/PUT | Quản lý thông báo | ✅ |
| `/api/search.php` | GET | Tìm kiếm toàn cục | ✅ |
| `/api/reports.php` | GET | Lấy báo cáo | ✅ |
| `/api/project-members.php` | GET/POST/DELETE | Quản lý thành viên | ✅ |
| `/api/admin-users.php` | GET/POST/PUT/DELETE | Quản lý users | ✅ |

**Bảng 4.4: Danh sách API Endpoints**

### 4.4.2. Cấu trúc Response

```json
// Success Response
{
    "success": true,
    "data": {
        "id": "uuid-123",
        "title": "Task name",
        "status": "in_progress"
    },
    "message": "Tạo task thành công"
}

// Error Response
{
    "success": false,
    "error": "Validation failed",
    "errors": {
        "title": "Tiêu đề là bắt buộc",
        "due_date": "Ngày không hợp lệ"
    }
}

// Paginated Response
{
    "success": true,
    "data": [...],
    "pagination": {
        "total": 100,
        "per_page": 20,
        "current_page": 1,
        "last_page": 5
    }
}
```


---

# CHƯƠNG 5: CÀI ĐẶT VÀ KIỂM THỬ

## 5.1. Môi trường phát triển

### 5.1.1. Yêu cầu hệ thống

| Thành phần | Yêu cầu | Phiên bản sử dụng |
|------------|---------|-------------------|
| Hệ điều hành | Windows/Linux/macOS | Windows 11 |
| Web Server | Apache/Nginx | Apache 2.4 |
| PHP | >= 8.0 | PHP 8.2 |
| MySQL | >= 8.0 | MySQL 8.0 |
| Composer | >= 2.0 | Composer 2.5 |
| Node.js | >= 16 (optional) | Node.js 18 |
| RAM | >= 4GB | 16GB |
| Disk | >= 1GB | SSD 256GB |

**Bảng 5.1: Môi trường phát triển**

### 5.1.2. Công cụ phát triển

- **IDE**: Visual Studio Code với extensions PHP, Tailwind CSS
- **Database Tool**: phpMyAdmin, MySQL Workbench
- **API Testing**: Postman
- **Version Control**: Git, GitHub
- **Browser**: Chrome DevTools

## 5.2. Cài đặt hệ thống

### 5.2.1. Cài đặt môi trường

**Bước 1: Cài đặt XAMPP/WAMP**
```bash
# Download và cài đặt XAMPP từ https://www.apachefriends.org/
# Hoặc sử dụng Laragon: https://laragon.org/
```

**Bước 2: Clone source code**
```bash
git clone https://github.com/username/taskflow.git
cd taskflow
```

**Bước 3: Cấu hình database**
```bash
# Tạo database
mysql -u root -p
CREATE DATABASE taskflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
mysql -u root -p taskflow < database/taskflow2.sql

# Import dữ liệu mẫu (optional)
mysql -u root -p taskflow < database/seed.sql
```

**Bước 4: Cấu hình ứng dụng**
```bash
# Copy file cấu hình
cp .env.example .env

# Chỉnh sửa .env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Bước 5: Cấu hình Virtual Host (Apache)**
```apache
<VirtualHost *:80>
    ServerName taskflow.local
    DocumentRoot "C:/xampp/htdocs/taskflow"
    
    <Directory "C:/xampp/htdocs/taskflow">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Bước 6: Chạy ứng dụng**
```bash
# Truy cập http://taskflow.local hoặc http://localhost/taskflow
# Đăng nhập với tài khoản mặc định:
# Email: admin@taskflow.com
# Password: Admin@123
```

### 5.2.2. Cấu trúc thư mục sau cài đặt

```
taskflow/
├── app/                    # Application code
│   ├── controllers/        # 10 controllers
│   ├── models/             # 6 models
│   ├── views/              # 35+ views
│   └── middleware/         # 2 middleware
├── api/                    # 30+ API endpoints
├── core/                   # 8 core classes
├── config/                 # 3 config files
├── database/               # SQL scripts
├── public/                 # Public assets
├── storage/                # Logs, cache
├── uploads/                # User uploads
├── bootstrap.php           # Bootstrap
├── index.php               # Entry point
└── .env                    # Environment config
```

## 5.3. Kết quả cài đặt

### 5.3.1. Giao diện đăng nhập

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                         │
│                         ┌─────────────────────┐                         │
│                         │                     │                         │
│                         │    🚀 TaskFlow      │                         │
│                         │                     │                         │
│                         │  ┌───────────────┐  │                         │
│                         │  │ Email         │  │                         │
│                         │  │ admin@...     │  │                         │
│                         │  └───────────────┘  │                         │
│                         │                     │                         │
│                         │  ┌───────────────┐  │                         │
│                         │  │ Mật khẩu      │  │                         │
│                         │  │ ••••••••      │  │                         │
│                         │  └───────────────┘  │                         │
│                         │                     │                         │
│                         │  ☐ Ghi nhớ đăng nhập│                         │
│                         │                     │                         │
│                         │  ┌───────────────┐  │                         │
│                         │  │  ĐĂNG NHẬP    │  │                         │
│                         │  └───────────────┘  │                         │
│                         │                     │                         │
│                         │  Quên mật khẩu?     │                         │
│                         │  Chưa có tài khoản? │                         │
│                         │                     │                         │
│                         └─────────────────────┘                         │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 5.1: Giao diện đăng nhập**

### 5.3.2. Giao diện Dashboard

```
┌─────────────────────────────────────────────────────────────────────────┐
│  TaskFlow    [🔍 Tìm kiếm...]                    [🔔 3] [👤 Admin ▼]   │
├──────────┬──────────────────────────────────────────────────────────────┤
│          │  Dashboard                                                   │
│ 🏠 Home  │                                                              │
│ 📋 Tasks │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │
│ 📁 Proj. │  │    8    │ │   44    │ │   22    │ │  156    │            │
│ 📄 Docs  │  │ Dự án   │ │Công việc│ │Thành viên│ │Tài liệu │            │
│ 📅 Cal.  │  │ hoạt động│ │đang làm │ │         │ │         │            │
│ 👥 Team  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘            │
│ 📊 Report│                                                              │
│          │  ┌─────────────────────────────────────────────────────┐    │
│ ──────── │  │  📈 Tiến độ tuần này                                │    │
│ ⚙️ Admin │  │  ████████████████████████████████████████████████   │    │
│          │  │  Mon  Tue  Wed  Thu  Fri  Sat  Sun                  │    │
│          │  └─────────────────────────────────────────────────────┘    │
│          │                                                              │
│          │  ┌─────────────────────┐  ┌─────────────────────┐          │
│          │  │ 📋 Công việc sắp hạn│  │ 🕐 Hoạt động gần đây│          │
│          │  │ • Task 1 - Hôm nay  │  │ • Nam tạo task mới  │          │
│          │  │ • Task 2 - Ngày mai │  │ • Lan comment       │          │
│          │  │ • Task 3 - 3 ngày   │  │ • Minh upload file  │          │
│          │  └─────────────────────┘  └─────────────────────┘          │
│          │                                                              │
└──────────┴──────────────────────────────────────────────────────────────┘
```

**Hình 5.2: Giao diện Dashboard**

### 5.3.3. Giao diện chi tiết Task

```
┌─────────────────────────────────────────────────────────────────────────┐
│  ← Quay lại                              [Chỉnh sửa] [Xóa]             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Thiết kế giao diện Dashboard                                          │
│  ┌──────────┐ ┌──────────┐ ┌────────────────────┐                      │
│  │🟡 Medium │ │⏳ Đang làm│ │📁 Website Redesign │                      │
│  └──────────┘ └──────────┘ └────────────────────┘                      │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ Mô tả                                                           │   │
│  │ Thiết kế lại giao diện Dashboard với các widget thống kê,      │   │
│  │ biểu đồ tiến độ và danh sách công việc sắp đến hạn.            │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ Checklist                                              3/5      │   │
│  │ ████████████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 60%     │   │
│  │ ☑ Thiết kế wireframe                                           │   │
│  │ ☑ Chọn màu sắc                                                 │   │
│  │ ☑ Tạo components                                               │   │
│  │ ☐ Responsive mobile                                            │   │
│  │ ☐ Dark mode                                                    │   │
│  │ [+ Thêm mục mới...]                                            │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ 💬 Bình luận (3)                                               │   │
│  │                                                                 │   │
│  │ ┌─────────────────────────────────────────────────────────┐    │   │
│  │ │ [Viết bình luận...]                          [Gửi]      │    │   │
│  │ └─────────────────────────────────────────────────────────┘    │   │
│  │                                                                 │   │
│  │ 👤 Nguyễn Văn Nam • 2 giờ trước                                │   │
│  │ ┌─────────────────────────────────────────────────────────┐    │   │
│  │ │ Đã hoàn thành phần wireframe, mọi người review giúp nhé │    │   │
│  │ └─────────────────────────────────────────────────────────┘    │   │
│  │ [Trả lời]                                                      │   │
│  │                                                                 │   │
│  │   └─ 👤 Trần Thị Lan • 1 giờ trước                            │   │
│  │      ┌─────────────────────────────────────────────────────┐   │   │
│  │      │ @Nam Looks good! Chỉ cần chỉnh lại spacing một chút │   │   │
│  │      └─────────────────────────────────────────────────────┘   │   │
│  │      [Trả lời]                                                 │   │
│  │                                                                 │   │
│  │        └─ 👤 Nguyễn Văn Nam • 30 phút trước                   │   │
│  │           ┌─────────────────────────────────────────────────┐  │   │
│  │           │ @Lan OK, mình sẽ fix ngay!                      │  │   │
│  │           └─────────────────────────────────────────────────┘  │   │
│  │                                                                 │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 5.3: Giao diện chi tiết Task với Nested Comments**

## 5.4. Kiểm thử hệ thống

### 5.4.1. Kiểm thử chức năng

| STT | Chức năng | Test Case | Đầu vào | Kết quả mong đợi | Kết quả | Trạng thái |
|-----|-----------|-----------|---------|------------------|---------|------------|
| 1 | Đăng nhập | TC01 | Email, password đúng | Chuyển đến Dashboard | Đúng | ✅ Pass |
| 2 | Đăng nhập | TC02 | Email sai | Hiển thị lỗi | Đúng | ✅ Pass |
| 3 | Đăng nhập | TC03 | Password sai | Hiển thị lỗi | Đúng | ✅ Pass |
| 4 | Tạo dự án | TC04 | Tên, mô tả hợp lệ | Tạo thành công | Đúng | ✅ Pass |
| 5 | Tạo dự án | TC05 | Tên trống | Hiển thị lỗi validation | Đúng | ✅ Pass |
| 6 | Tạo task | TC06 | Tiêu đề hợp lệ | Tạo thành công | Đúng | ✅ Pass |
| 7 | Kéo thả task | TC07 | Kéo từ TODO sang IN_PROGRESS | Cập nhật status | Đúng | ✅ Pass |
| 8 | Thêm checklist | TC08 | Tiêu đề item | Thêm thành công | Đúng | ✅ Pass |
| 9 | Toggle checklist | TC09 | Click checkbox | Đổi trạng thái | Đúng | ✅ Pass |
| 10 | Bình luận | TC10 | Nội dung comment | Thêm thành công | Đúng | ✅ Pass |
| 11 | Trả lời comment | TC11 | Reply nội dung | Hiển thị nested | Đúng | ✅ Pass |
| 12 | Upload file | TC12 | File PDF 5MB | Upload thành công | Đúng | ✅ Pass |
| 13 | Upload file | TC13 | File EXE | Từ chối | Đúng | ✅ Pass |
| 14 | Tìm kiếm | TC14 | Keyword "task" | Hiển thị kết quả | Đúng | ✅ Pass |
| 15 | Phân quyền | TC15 | Member xóa project | Từ chối | Đúng | ✅ Pass |

**Bảng 5.2: Kết quả kiểm thử chức năng**

### 5.4.2. Kiểm thử bảo mật

| STT | Loại tấn công | Test Case | Payload | Kết quả mong đợi | Kết quả | Trạng thái |
|-----|---------------|-----------|---------|------------------|---------|------------|
| 1 | SQL Injection | SEC01 | `' OR '1'='1` | Không bị inject | Đúng | ✅ Pass |
| 2 | SQL Injection | SEC02 | `'; DROP TABLE users;--` | Không bị inject | Đúng | ✅ Pass |
| 3 | XSS | SEC03 | `<script>alert(1)</script>` | Escape HTML | Đúng | ✅ Pass |
| 4 | XSS | SEC04 | `<img onerror=alert(1)>` | Escape HTML | Đúng | ✅ Pass |
| 5 | CSRF | SEC05 | Request không có token | Từ chối | Đúng | ✅ Pass |
| 6 | Brute Force | SEC06 | 10 lần login sai | Rate limit | Đúng | ✅ Pass |
| 7 | File Upload | SEC07 | File .php | Từ chối | Đúng | ✅ Pass |
| 8 | Path Traversal | SEC08 | `../../../etc/passwd` | Từ chối | Đúng | ✅ Pass |

**Bảng 5.3: Kết quả kiểm thử bảo mật**

### 5.4.3. Kiểm thử hiệu năng

| Metric | Mục tiêu | Kết quả | Trạng thái |
|--------|----------|---------|------------|
| Thời gian load trang | < 2s | 1.2s | ✅ Pass |
| Thời gian API response | < 500ms | 150ms | ✅ Pass |
| Concurrent users | 100 | 120 | ✅ Pass |
| Memory usage | < 256MB | 128MB | ✅ Pass |
| Database queries/page | < 20 | 8 | ✅ Pass |


---

# CHƯƠNG 6: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

## 6.1. Kết quả đạt được

### 6.1.1. Về mặt chức năng

Đồ án đã hoàn thành xây dựng hệ thống quản lý dự án TaskFlow với đầy đủ các chức năng đề ra:

| Module | Tính năng | Trạng thái |
|--------|-----------|------------|
| **Authentication** | Đăng nhập, đăng ký, quên mật khẩu, remember me | ✅ Hoàn thành |
| **Projects** | CRUD dự án, quản lý thành viên, theo dõi tiến độ | ✅ Hoàn thành |
| **Tasks** | CRUD tasks, Kanban board, drag & drop, checklist, giao việc | ✅ Hoàn thành |
| **Comments** | Bình luận, nested replies (đa cấp), mention | ✅ Hoàn thành |
| **Documents** | Upload, thư mục, chia sẻ, đánh dấu sao | ✅ Hoàn thành |
| **Calendar** | Xem lịch, tạo sự kiện, nhắc nhở | ✅ Hoàn thành |
| **Notifications** | Thông báo hệ thống, đánh dấu đã đọc | ✅ Hoàn thành |
| **Reports** | Dashboard thống kê, biểu đồ, export | ✅ Hoàn thành |
| **Admin** | Quản lý users, cài đặt, logs, backup | ✅ Hoàn thành |
| **UI/UX** | Responsive, dark mode, tìm kiếm toàn cục | ✅ Hoàn thành |

**Tổng cộng: 40+ tính năng đã hoàn thành**

### 6.1.2. Về mặt kỹ thuật

- **Kiến trúc MVC**: Tổ chức code rõ ràng, dễ bảo trì với 100+ files
- **RESTful API**: 30+ endpoints với chuẩn JSON response
- **Cơ sở dữ liệu**: 16 bảng với quan hệ đầy đủ, indexes tối ưu
- **Bảo mật**: Áp dụng đầy đủ các biện pháp chống SQL Injection, XSS, CSRF
- **Responsive**: Giao diện hoạt động tốt trên mọi thiết bị
- **Performance**: Thời gian load < 2s, hỗ trợ 100+ users đồng thời

### 6.1.3. Thống kê mã nguồn

| Thành phần | Số file | Số dòng code |
|------------|---------|--------------|
| Controllers | 10 | ~1,500 |
| Models | 6 | ~800 |
| Views | 35+ | ~5,000 |
| API Endpoints | 30+ | ~3,000 |
| Core Framework | 8 | ~1,200 |
| JavaScript | - | ~2,000 |
| SQL Scripts | 15+ | ~800 |
| **Tổng cộng** | **100+** | **~15,000** |

## 6.2. Hạn chế

Mặc dù đã hoàn thành các mục tiêu đề ra, đồ án vẫn còn một số hạn chế:

### 6.2.1. Hạn chế về tính năng

- **Chưa có real-time**: Thông báo chưa real-time, cần refresh để thấy thay đổi
- **Chưa có mobile app**: Chỉ có web interface, chưa có ứng dụng mobile native
- **Chưa tích hợp bên ngoài**: Chưa tích hợp với Slack, Email marketing, Google Calendar
- **Chưa có AI**: Chưa có tính năng gợi ý task, dự đoán deadline

### 6.2.2. Hạn chế về kỹ thuật

- **Chưa có caching**: Chưa implement Redis/Memcached cho caching
- **Chưa có queue**: Chưa có job queue cho các tác vụ nặng
- **Chưa có testing tự động**: Chưa có unit tests, integration tests
- **Chưa có CI/CD**: Chưa setup pipeline tự động deploy

## 6.3. Hướng phát triển

### 6.3.1. Ngắn hạn (3-6 tháng)

| Ưu tiên | Tính năng | Mô tả |
|---------|-----------|-------|
| Cao | Real-time notifications | Sử dụng WebSocket/Pusher |
| Cao | Unit testing | PHPUnit cho backend |
| Trung bình | Caching | Redis cho session và data caching |
| Trung bình | Email notifications | Gửi email khi có sự kiện quan trọng |

### 6.3.2. Trung hạn (6-12 tháng)

| Ưu tiên | Tính năng | Mô tả |
|---------|-----------|-------|
| Cao | Mobile app | React Native hoặc Flutter |
| Cao | API documentation | Swagger/OpenAPI |
| Trung bình | Integration | Slack, Google Calendar, Trello import |
| Trung bình | Advanced reports | Biểu đồ Gantt, burndown chart |

### 6.3.3. Dài hạn (12+ tháng)

| Ưu tiên | Tính năng | Mô tả |
|---------|-----------|-------|
| Trung bình | AI features | Gợi ý task, dự đoán deadline, auto-assign |
| Trung bình | Multi-tenant | Hỗ trợ nhiều tổ chức trên cùng hệ thống |
| Thấp | Marketplace | Plugin/extension marketplace |
| Thấp | White-label | Cho phép rebrand cho khách hàng |

## 6.4. Kết luận

Đồ án "Xây dựng hệ thống quản lý dự án và công việc TaskFlow" đã hoàn thành các mục tiêu đề ra:

1. **Xây dựng thành công** hệ thống quản lý dự án hoàn chỉnh với 40+ tính năng
2. **Áp dụng** kiến trúc MVC, RESTful API, RBAC theo chuẩn công nghiệp
3. **Đảm bảo bảo mật** với các biện pháp chống SQL Injection, XSS, CSRF
4. **Giao diện** responsive, hiện đại, hỗ trợ dark mode
5. **Có thể triển khai** ngay cho các doanh nghiệp vừa và nhỏ

Hệ thống TaskFlow là một giải pháp thay thế khả thi cho các phần mềm quản lý dự án quốc tế, với ưu điểm:
- Giao diện tiếng Việt, phù hợp người dùng Việt Nam
- Self-hosted, kiểm soát hoàn toàn dữ liệu
- Chi phí thấp, phù hợp doanh nghiệp nhỏ
- Mã nguồn mở, có thể tùy chỉnh theo nhu cầu

Qua quá trình thực hiện đồ án, em đã học hỏi và rèn luyện được nhiều kỹ năng quan trọng trong phát triển phần mềm chuyên nghiệp, từ phân tích yêu cầu, thiết kế hệ thống đến cài đặt và kiểm thử.

---

# TÀI LIỆU THAM KHẢO

## Sách và tài liệu học thuật

[1] Fowler, M. (2002). *Patterns of Enterprise Application Architecture*. Addison-Wesley Professional.

[2] Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). *Design Patterns: Elements of Reusable Object-Oriented Software*. Addison-Wesley.

[3] Fielding, R. T. (2000). *Architectural Styles and the Design of Network-based Software Architectures*. Doctoral dissertation, University of California, Irvine.

[4] Elmasri, R., & Navathe, S. B. (2015). *Fundamentals of Database Systems* (7th ed.). Pearson.

[5] Sommerville, I. (2015). *Software Engineering* (10th ed.). Pearson.

## Tài liệu kỹ thuật

[6] PHP Documentation. (2024). *PHP Manual*. https://www.php.net/manual/

[7] MySQL Documentation. (2024). *MySQL 8.0 Reference Manual*. https://dev.mysql.com/doc/refman/8.0/en/

[8] Tailwind CSS. (2024). *Tailwind CSS Documentation*. https://tailwindcss.com/docs

[9] Alpine.js. (2024). *Alpine.js Documentation*. https://alpinejs.dev/

[10] OWASP Foundation. (2024). *OWASP Top Ten Web Application Security Risks*. https://owasp.org/Top10/

[11] OWASP. (2024). *OWASP Cheat Sheet Series*. https://cheatsheetseries.owasp.org/

## Bài báo và nghiên cứu

[12] Project Management Institute. (2023). *Pulse of the Profession 2023*. PMI.

[13] Schwaber, K., & Sutherland, J. (2020). *The Scrum Guide*. Scrum.org.


---

# PHỤ LỤC

## Phụ lục A: Hướng dẫn cài đặt chi tiết

### A.1. Yêu cầu hệ thống

```
- PHP >= 8.0 với extensions: pdo_mysql, mbstring, json, openssl
- MySQL >= 8.0
- Apache/Nginx với mod_rewrite
- Composer >= 2.0 (optional)
```

### A.2. Các bước cài đặt

```bash
# 1. Clone repository
git clone https://github.com/username/taskflow.git
cd taskflow

# 2. Tạo database
mysql -u root -p
CREATE DATABASE taskflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 3. Import schema
mysql -u root -p taskflow < database/taskflow2.sql

# 4. Import dữ liệu mẫu (optional)
mysql -u root -p taskflow < database/seed.sql

# 5. Cấu hình
cp .env.example .env
# Chỉnh sửa .env với thông tin database

# 6. Cấu hình permissions
chmod -R 755 storage/
chmod -R 755 uploads/

# 7. Truy cập
# http://localhost/taskflow
# hoặc cấu hình virtual host
```

### A.3. Tài khoản mặc định

```
Admin:
- Email: admin@taskflow.com
- Password: Admin@123

Manager:
- Email: manager@taskflow.com
- Password: Manager@123

Member:
- Email: member@taskflow.com
- Password: Member@123
```

## Phụ lục B: Cấu trúc Database

### B.1. Script tạo bảng users

```sql
CREATE TABLE `users` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `avatar_url` VARCHAR(500) NULL,
    `role` ENUM('admin', 'manager', 'member', 'guest') NOT NULL DEFAULT 'member',
    `department` VARCHAR(100) NULL,
    `position` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `email_verified_at` DATETIME NULL,
    `remember_token` VARCHAR(64) NULL,
    `remember_token_expiry` DATETIME NULL,
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### B.2. Script tạo bảng tasks

```sql
CREATE TABLE `tasks` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `project_id` VARCHAR(36) NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('backlog', 'todo', 'in_progress', 'in_review', 'done') NOT NULL DEFAULT 'todo',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `start_date` DATE NULL,
    `due_date` DATE NULL,
    `completed_at` DATETIME NULL,
    `estimated_hours` DECIMAL(6, 2) NULL,
    `actual_hours` DECIMAL(6, 2) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tasks_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_tasks_project` (`project_id`),
    INDEX `idx_tasks_status` (`status`),
    INDEX `idx_tasks_priority` (`priority`),
    INDEX `idx_tasks_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### B.3. Script tạo bảng comments (nested replies)

```sql
CREATE TABLE `comments` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `entity_type` ENUM('task', 'document', 'project') NOT NULL DEFAULT 'task',
    `entity_id` VARCHAR(36) NOT NULL,
    `content` TEXT NOT NULL,
    `parent_id` VARCHAR(36) NULL,
    `created_by` VARCHAR(36) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) 
        REFERENCES `comments`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_comments_entity` (`entity_type`, `entity_id`),
    INDEX `idx_comments_parent` (`parent_id`),
    INDEX `idx_comments_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Phụ lục C: Mã nguồn quan trọng

### C.1. Database Singleton Pattern

```php
<?php
namespace Core;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct() {}

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $config = require BASE_PATH . '/config/database.php';
            $dsn = sprintf("%s:host=%s;dbname=%s;charset=%s",
                $config['driver'], $config['host'], 
                $config['database'], $config['charset']);
            
            $this->connection = new PDO($dsn, 
                $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return $this->connection;
    }
}
```

### C.2. Authentication Middleware

```php
<?php
namespace App\Middleware;

use Core\Session;

class AuthMiddleware
{
    public static function handle(): bool
    {
        Session::start();
        
        if (!Session::has('user_id')) {
            if (self::isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }
            
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            header('Location: /php/login.php');
            exit;
        }
        
        return true;
    }
}
```

### C.3. Permission Check

```php
<?php
namespace Core;

class Permission
{
    public static function can(string $role, string $permission): bool
    {
        $config = require BASE_PATH . '/config/permissions.php';
        $permissions = $config['permissions'][$role] ?? [];
        return in_array($permission, $permissions);
    }
}
```

## Phụ lục D: API Documentation

### D.1. Create Task API

```
POST /api/create-task.php

Headers:
  Content-Type: application/json
  Cookie: PHPSESSID=xxx

Request Body:
{
    "title": "Task title",
    "description": "Task description",
    "project_id": "uuid-xxx",
    "priority": "medium",
    "due_date": "2024-12-31",
    "assignee_id": "uuid-xxx"
}

Response (Success):
{
    "success": true,
    "message": "Tạo công việc thành công",
    "task_id": "uuid-xxx"
}

Response (Error):
{
    "success": false,
    "error": "Tiêu đề công việc là bắt buộc"
}
```

### D.2. Comments API

```
GET /api/comments.php?entity_type=task&entity_id=xxx

Response:
{
    "success": true,
    "data": [
        {
            "id": "uuid-xxx",
            "content": "Comment content",
            "parent_id": null,
            "created_by": "uuid-xxx",
            "full_name": "Nguyễn Văn A",
            "created_at": "2024-12-22 10:00:00",
            "replies": [
                {
                    "id": "uuid-yyy",
                    "content": "Reply content",
                    "parent_id": "uuid-xxx",
                    ...
                }
            ]
        }
    ]
}

POST /api/comments.php

Request Body:
{
    "entity_type": "task",
    "entity_id": "uuid-xxx",
    "content": "Comment content",
    "parent_id": "uuid-xxx"  // optional, for replies
}
```

---

*Kết thúc báo cáo*

