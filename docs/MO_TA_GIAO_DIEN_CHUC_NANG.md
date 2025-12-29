# MÔ TẢ GIAO DIỆN, CHỨC NĂNG VÀ ĐÁNH GIÁ HỆ THỐNG TASKFLOW

## CHƯƠNG 1: TỔNG QUAN GIAO DIỆN

### 1.1. Thiết kế tổng thể

Hệ thống TaskFlow được thiết kế với giao diện hiện đại, sử dụng bố cục hai cột truyền thống phổ biến trong các ứng dụng quản lý. Phần bên trái là thanh điều hướng (sidebar) có chiều rộng cố định 240px trên màn hình desktop, chứa logo ứng dụng và các menu chính. Phần bên phải chiếm phần còn lại của màn hình, hiển thị nội dung chính của từng trang.

Phía trên cùng của giao diện là thanh header chạy ngang toàn bộ chiều rộng màn hình. Thanh header này chứa ô tìm kiếm toàn cục ở giữa, cho phép người dùng tìm kiếm nhanh dự án, công việc và tài liệu. Bên phải header là khu vực hiển thị thông báo với biểu tượng chuông và avatar người dùng kèm menu dropdown để truy cập cài đặt hoặc đăng xuất.

Về màu sắc, hệ thống sử dụng tông màu chủ đạo là xanh dương (primary color #3b82f6) kết hợp với nền trắng và xám nhạt tạo cảm giác chuyên nghiệp, dễ nhìn. Hệ thống hỗ trợ chế độ tối (dark mode) với nền xám đậm và chữ sáng, giúp giảm mỏi mắt khi làm việc trong điều kiện ánh sáng yếu.

### 1.2. Thiết kế đáp ứng (Responsive Design)

Giao diện TaskFlow được thiết kế để hiển thị tốt trên nhiều kích thước màn hình khác nhau. Trên màn hình desktop từ 1280px trở lên, sidebar hiển thị đầy đủ với tên các menu item. Khi màn hình thu nhỏ xuống kích thước laptop (1024px-1279px), sidebar vẫn hiển thị nhưng với chiều rộng nhỏ hơn.

Trên thiết bị tablet (768px-1023px), sidebar được thu gọn thành dạng icon, chỉ hiển thị đầy đủ khi người dùng click vào nút hamburger menu. Nội dung chính chiếm toàn bộ chiều rộng màn hình. Trên điện thoại di động (dưới 768px), sidebar hoàn toàn ẩn đi và chỉ xuất hiện dưới dạng drawer khi người dùng mở menu. Các grid layout tự động chuyển từ nhiều cột sang một cột để phù hợp với màn hình nhỏ.

### 1.3. Công nghệ giao diện

TaskFlow sử dụng Tailwind CSS làm framework CSS chính. Đây là framework utility-first cho phép xây dựng giao diện nhanh chóng bằng cách kết hợp các class tiện ích. Tailwind CSS giúp đảm bảo tính nhất quán về spacing, typography và màu sắc trong toàn bộ ứng dụng.

Về JavaScript, hệ thống sử dụng Alpine.js - một framework nhẹ chỉ khoảng 15KB sau khi minify. Alpine.js cung cấp khả năng reactive data binding tương tự Vue.js nhưng không cần bước build phức tạp. Các tương tác như mở/đóng modal, dropdown menu, toggle switch đều được xử lý bằng Alpine.js.

Biểu tượng trong ứng dụng sử dụng bộ Lucide Icons - một bộ icon mã nguồn mở với hơn 1000 biểu tượng thiết kế theo phong cách outline hiện đại. Các biểu đồ thống kê được vẽ bằng Chart.js, hỗ trợ nhiều loại biểu đồ như doughnut, bar, line với khả năng tùy biến cao.


---

## CHƯƠNG 2: MÔ TẢ CHI TIẾT CÁC MÀN HÌNH

### 2.1. Màn hình Đăng nhập

Khi người dùng truy cập hệ thống TaskFlow mà chưa đăng nhập, họ sẽ được chuyển hướng đến trang đăng nhập. Trang này có thiết kế đơn giản với một form đăng nhập đặt ở giữa màn hình trên nền xám nhạt.

Phía trên form là logo TaskFlow kèm tiêu đề "Đăng nhập" và dòng chào mừng "Chào mừng bạn quay lại TaskFlow". Form đăng nhập gồm hai trường nhập liệu: Email và Mật khẩu. Trường email yêu cầu định dạng email hợp lệ, trường mật khẩu hiển thị dạng ký tự ẩn để bảo mật.

Bên dưới các trường nhập liệu là checkbox "Ghi nhớ đăng nhập" cho phép người dùng duy trì phiên đăng nhập lâu hơn, và liên kết "Quên mật khẩu?" dẫn đến trang khôi phục mật khẩu. Nút "Đăng nhập" màu xanh dương nổi bật chiếm toàn bộ chiều rộng form. Cuối cùng là dòng text hướng dẫn người dùng chưa có tài khoản đến trang đăng ký.

Khi đăng nhập thất bại, hệ thống hiển thị thông báo lỗi màu đỏ phía trên form, giải thích rõ lý do như "Email hoặc mật khẩu không đúng" hoặc "Tài khoản đã bị khóa". Khi đăng nhập thành công, người dùng được chuyển hướng đến trang Dashboard.

### 2.2. Màn hình Dashboard

Dashboard là trang chủ của hệ thống sau khi đăng nhập, cung cấp cái nhìn tổng quan về tình trạng công việc. Đặc biệt, giao diện Dashboard được thiết kế khác nhau tùy theo vai trò của người dùng.

**Đối với thành viên thường (Member)**, Dashboard hiển thị bốn thẻ thống kê nhanh ở đầu trang: Tổng số dự án tham gia, Số công việc được giao, Số thành viên trong các dự án, và Tỷ lệ hoàn thành công việc cá nhân. Mỗi thẻ có biểu tượng minh họa và số liệu lớn dễ đọc.

Phía dưới là phần "Dự án của tôi" hiển thị các dự án mà thành viên đang tham gia dưới dạng lưới các thẻ. Mỗi thẻ dự án hiển thị tên dự án, trạng thái, mô tả ngắn, thanh tiến độ và tỷ lệ phần trăm hoàn thành. Người dùng có thể click vào thẻ để xem chi tiết dự án.

Bên dưới nữa là hai cột song song: cột trái hiển thị "Công việc của tôi" - danh sách các task được giao với checkbox để đánh dấu hoàn thành, độ ưu tiên được thể hiện bằng màu sắc (đỏ cho khẩn cấp, cam cho cao, xanh cho trung bình, xám cho thấp). Cột phải hiển thị "Hoạt động gần đây" - timeline các hoạt động mới nhất trong các dự án như ai đã hoàn thành task nào, ai đã comment, ai đã tạo dự án mới.

**Đối với quản lý (Manager)**, Dashboard được mở rộng với nhiều thông tin hơn. Phần thống kê nhanh có năm thẻ: Số dự án đang quản lý, Tổng số thành viên, Tổng số công việc, Số công việc quá hạn (hiển thị nổi bật màu đỏ nếu có), và Số công việc đến hạn trong tuần.

Phần "Khối lượng công việc" hiển thị danh sách các thành viên kèm thanh progress bar thể hiện workload của từng người. Những thành viên có task quá hạn được đánh dấu cảnh báo. Bên cạnh là phần "Thành viên xuất sắc" xếp hạng top 3 người có tỷ lệ hoàn thành cao nhất với huy chương vàng, bạc, đồng.

Phần "Tiến độ dự án" hiển thị tất cả dự án mà manager đang quản lý với thanh tiến độ, số thành viên và cảnh báo nếu có task quá hạn. Cuối cùng là hai cột: "Đến hạn tuần này" liệt kê các task sắp đến deadline và "Tasks quá hạn" nhóm theo từng thành viên để manager dễ theo dõi và nhắc nhở.

### 2.3. Màn hình Quản lý Dự án

Trang quản lý dự án hiển thị tất cả các dự án mà người dùng có quyền truy cập. Phía trên là thanh filter với các tab lọc theo trạng thái: Tất cả, Đang hoạt động, Lên kế hoạch, Tạm dừng, và Hoàn thành. Tab đang được chọn có màu nền xanh dương nổi bật. Bên phải là nút "Tạo dự án" (chỉ hiển thị với người có quyền).

Danh sách dự án được hiển thị dưới dạng lưới các thẻ (card). Mỗi thẻ dự án bao gồm: biểu tượng thư mục với màu sắc tùy chỉnh của dự án, tên dự án, badge trạng thái (active màu xanh lá, planning màu xám, on_hold màu cam, completed màu xanh dương), mô tả ngắn gọn, thanh tiến độ với tỷ lệ phần trăm, số thành viên và số task quá hạn nếu có.

Khi hover vào thẻ dự án, thẻ sẽ có hiệu ứng đổ bóng nhẹ để người dùng biết có thể click. Những dự án có task quá hạn sẽ có viền màu đỏ nhạt để cảnh báo. Click vào thẻ sẽ chuyển đến trang chi tiết dự án.

Khi click nút "Tạo dự án", một modal popup xuất hiện với form tạo dự án mới. Form bao gồm các trường: Tên dự án (bắt buộc), Mô tả, Độ ưu tiên (dropdown chọn Thấp/Trung bình/Cao), Màu sắc (color picker), Ngày bắt đầu và Ngày kết thúc dự kiến. Sau khi submit, hệ thống tạo dự án và chuyển người dùng đến trang chi tiết dự án vừa tạo.

### 2.4. Màn hình Chi tiết Dự án

Trang chi tiết dự án cung cấp cái nhìn toàn diện về một dự án cụ thể. Header của trang hiển thị tên dự án với biểu tượng, trạng thái, tên người tạo và ngày tạo. Bên phải header có các nút hành động: đánh dấu sao (favorite), cài đặt và chỉnh sửa (với người có quyền).

Bên dưới header là thanh tab cho phép chuyển đổi giữa các phần: Tổng quan, Công việc, Tài liệu, Thành viên và Hoạt động.

**Tab Tổng quan** hiển thị thông tin cơ bản của dự án bên trái (mô tả, ngày bắt đầu, ngày kết thúc, độ ưu tiên) và thống kê tiến độ bên phải (thanh progress bar, số task hoàn thành/đang làm/quá hạn).

**Tab Công việc** là phần quan trọng nhất, hiển thị Kanban board với 5 cột tương ứng 5 trạng thái: Backlog, To Do, In Progress, In Review và Done. Mỗi cột có header hiển thị tên trạng thái và số lượng task. Các task được hiển thị dưới dạng thẻ nhỏ trong từng cột, có thể kéo thả (drag & drop) giữa các cột để thay đổi trạng thái.

Mỗi thẻ task trên Kanban hiển thị tiêu đề task, badge độ ưu tiên với màu sắc tương ứng, avatar của người được giao và ngày hết hạn nếu có. Task quá hạn có viền đỏ cảnh báo. Click vào thẻ task sẽ mở trang chi tiết task.

**Tab Tài liệu** hiển thị các file và thư mục liên quan đến dự án với khả năng upload, download và tổ chức theo cấu trúc thư mục.

**Tab Thành viên** liệt kê tất cả thành viên trong dự án với vai trò của họ (Owner, Manager, Member). Người có quyền có thể thêm/xóa thành viên hoặc thay đổi vai trò từ đây.

**Tab Hoạt động** hiển thị timeline tất cả các hoạt động trong dự án theo thứ tự thời gian: ai đã làm gì, lúc nào.


### 2.5. Màn hình Quản lý Công việc

Trang quản lý công việc tập trung hiển thị tất cả các task mà người dùng liên quan, bao gồm task được giao và task do mình tạo. Giao diện được thiết kế dạng danh sách để dễ dàng quét qua nhiều task cùng lúc.

Phía trên danh sách là hai dropdown filter: một để lọc theo trạng thái (Tất cả, Cần làm, Đang làm, Hoàn thành) và một để lọc theo độ ưu tiên (Tất cả, Khẩn cấp, Cao, Trung bình, Thấp). Bên phải là nút "Tạo công việc" màu xanh dương.

Danh sách task được hiển thị trong một container màu trắng với các dòng phân cách nhẹ. Mỗi dòng task bao gồm: checkbox ở đầu để đánh dấu hoàn thành nhanh, tiêu đề task có thể click để xem chi tiết, và một hàng thông tin phụ gồm badge độ ưu tiên, tên dự án với chấm màu đại diện, và ngày hết hạn với biểu tượng lịch.

Khi người dùng tick vào checkbox, hệ thống gửi request AJAX để cập nhật trạng thái task thành "done" và tự động reload trang để cập nhật giao diện. Task đã hoàn thành có tiêu đề bị gạch ngang và màu nhạt đi.

Khi click nút "Tạo công việc", modal popup hiện ra với form đầy đủ các trường: Tiêu đề (bắt buộc), Mô tả (textarea), Dự án (dropdown chọn từ danh sách dự án), Độ ưu tiên (dropdown), Ngày hết hạn (date picker), và Giao cho (dropdown chọn thành viên). Form sử dụng AJAX để submit, sau khi tạo thành công sẽ chuyển đến trang chi tiết task vừa tạo.

### 2.6. Màn hình Chi tiết Công việc

Trang chi tiết công việc cung cấp đầy đủ thông tin và công cụ để làm việc với một task cụ thể. Bố cục chia làm hai phần chính: phần nội dung bên trái chiếm khoảng 60% chiều rộng và phần thông tin bên phải chiếm 40%.

Header của trang hiển thị checkbox lớn để đánh dấu hoàn thành, tiêu đề task, và các nút hành động (đánh dấu sao, chỉnh sửa, xóa). Dòng phụ hiển thị badge độ ưu tiên, tên dự án và tên người tạo.

**Phần nội dung bên trái** bắt đầu với mô tả chi tiết của task. Tiếp theo là phần Checklist - danh sách các bước nhỏ cần hoàn thành trong task. Mỗi item checklist có checkbox riêng, người dùng có thể tick để đánh dấu hoàn thành từng bước. Có nút để thêm item mới vào checklist. Thanh progress nhỏ hiển thị tỷ lệ hoàn thành checklist (ví dụ: 2/4 items).

Bên dưới checklist là phần Bình luận, cho phép các thành viên thảo luận về task. Mỗi comment hiển thị avatar người viết, tên, thời gian và nội dung. Có textarea ở cuối để viết comment mới. Comment được load và submit bằng AJAX để không cần reload trang.

**Phần thông tin bên phải** hiển thị các metadata của task trong các dòng riêng biệt: Trạng thái (có thể click để thay đổi), Độ ưu tiên, Ngày hết hạn, Người giao việc, Danh sách người được giao (có thể có nhiều người), Dự án thuộc về, Thời gian tạo và cập nhật.

Phía dưới là phần Tệp đính kèm hiển thị các file được upload kèm task. Mỗi file hiển thị icon theo loại file, tên file và nút download. Có nút để upload thêm file mới.

### 2.7. Màn hình Lịch

Trang Lịch cung cấp hai chế độ xem: Calendar truyền thống và Gantt chart. Người dùng chuyển đổi giữa hai chế độ bằng toggle button ở góc trên trái.

**Chế độ Calendar** hiển thị lịch tháng với 7 cột tương ứng 7 ngày trong tuần (từ Thứ 2 đến Chủ nhật). Mỗi ô ngày có chiều cao tối thiểu để chứa được vài sự kiện. Ngày hiện tại được highlight bằng vòng tròn màu xanh dương quanh số ngày.

Trong mỗi ô ngày, các sự kiện và task đến hạn được hiển thị dưới dạng thanh nhỏ với màu sắc khác nhau: sự kiện calendar có màu tùy chỉnh, task có màu đỏ. Nếu một ngày có quá nhiều item, chỉ hiển thị 3 item đầu và dòng "+X khác" để không làm vỡ layout.

Thanh điều hướng phía trên cho phép chuyển tháng trước/sau bằng nút mũi tên, nút "Hôm nay" để quay về tháng hiện tại, và nút "Thêm sự kiện" để tạo sự kiện mới. Click vào một ngày bất kỳ cũng mở form tạo sự kiện với ngày đó được điền sẵn.

**Chế độ Gantt** hiển thị timeline theo chiều ngang. Cột bên trái liệt kê tên các task và sự kiện, phần còn lại là lưới với mỗi cột là một ngày trong tháng. Các task và sự kiện được biểu diễn bằng thanh ngang có độ dài tương ứng với thời gian thực hiện, màu sắc theo dự án hoặc loại sự kiện.

Ngày hiện tại được đánh dấu bằng cột có nền màu nhạt. Cuối tuần (Thứ 7, Chủ nhật) có nền xám nhạt để phân biệt. Hover vào thanh Gantt hiển thị tooltip với thông tin chi tiết về task/sự kiện đó.

Bên dưới calendar/gantt là hai panel song song: "Sự kiện sắp tới" liệt kê các event trong 14 ngày tới và "Công việc sắp đến hạn" liệt kê các task đến hạn trong 7 ngày tới, mỗi item có badge hiển thị còn bao nhiêu ngày.

### 2.8. Màn hình Quản lý Tài liệu

Trang tài liệu cho phép người dùng quản lý và chia sẻ file trong các dự án. Giao diện được thiết kế giống như file explorer quen thuộc.

Header trang có tiêu đề "Tài liệu" kèm mô tả ngắn, và hai nút hành động: "Thư mục mới" và "Tải lên". Bên dưới là breadcrumb navigation hiển thị đường dẫn thư mục hiện tại, cho phép click vào bất kỳ cấp nào để quay lại.

Thanh công cụ gồm ô tìm kiếm để tìm file theo tên, dropdown lọc theo loại file (Tất cả, Thư mục, Tệp tin), và toggle chuyển đổi giữa Grid view và List view.

**Grid view** hiển thị các file và thư mục dưới dạng lưới các thẻ. Mỗi thẻ có icon lớn ở giữa (icon thư mục màu vàng cho folder, icon file với màu theo loại: đỏ cho PDF, xanh dương cho Word, xanh lá cho Excel, tím cho ảnh), tên file bên dưới, tên người upload và kích thước file. Khi hover, các nút hành động xuất hiện: đánh dấu sao, download (với file), xóa.

**List view** hiển thị dạng bảng với các cột: Tên (có icon), Người tạo, Kích thước, Ngày cập nhật, và Thao tác. Dạng này phù hợp khi cần xem nhiều file cùng lúc và so sánh thông tin.

Click vào thư mục sẽ mở thư mục đó và cập nhật breadcrumb. Click vào file sẽ mở preview nếu là loại file hỗ trợ (ảnh, PDF) hoặc download nếu không.

Modal "Tải lên" có vùng drag & drop với hướng dẫn "Click để chọn file hoặc kéo thả vào đây". Hỗ trợ upload nhiều file cùng lúc, giới hạn 50MB mỗi file. Có dropdown để chọn dự án liên quan (tùy chọn). Sau khi chọn file, danh sách file được chọn hiển thị với tên và kích thước trước khi upload.


### 2.9. Màn hình Báo cáo và Thống kê

Trang báo cáo cung cấp cái nhìn phân tích về hiệu suất công việc thông qua các biểu đồ và số liệu thống kê. Giao diện được thiết kế khác nhau cho Member và Manager để phù hợp với nhu cầu của từng vai trò.

Header trang hiển thị tiêu đề và mô tả ngắn về phạm vi báo cáo (cá nhân hoặc team). Bên phải là thanh filter thời gian với 4 tab: Tuần, Tháng, Quý, Năm - cho phép người dùng xem thống kê theo các khoảng thời gian khác nhau. Nút "Xuất báo cáo" (với người có quyền) mở dropdown cho phép export dữ liệu ra file CSV.

Phần thống kê tổng quan gồm 5 thẻ ngang hàng: Tổng dự án, Tổng công việc, Tỷ lệ hoàn thành (hiển thị phần trăm), Số task quá hạn (thẻ này có nền đỏ nhạt nếu có task quá hạn), và Số task đến hạn tuần này. Mỗi thẻ có icon minh họa và số liệu lớn dễ đọc.

Phần biểu đồ được chia làm hai cột. Cột trái hiển thị biểu đồ Doughnut (hình donut) thể hiện phân bố task theo trạng thái với 5 màu: xám cho Backlog, xanh dương cho To Do, vàng cho In Progress, tím cho In Review, và xanh lá cho Done. Bên cạnh biểu đồ là chú thích với số lượng cụ thể của từng trạng thái.

Cột phải hiển thị biểu đồ Bar (cột đứng) thể hiện phân bố task theo độ ưu tiên. Bốn cột tương ứng với Thấp (xám), Trung bình (xanh dương), Cao (cam), và Khẩn cấp (đỏ). Chiều cao cột thể hiện số lượng task.

Đối với Manager, có thêm biểu đồ Line (đường) hiển thị xu hướng hoàn thành công việc theo thời gian. Hai đường: một cho số task hoàn thành (màu xanh lá) và một cho số task tạo mới (màu xanh dương), giúp manager đánh giá velocity của team.

Phần "Tiến độ dự án" liệt kê tất cả dự án với thanh progress bar ngang. Mỗi dự án hiển thị tên, số task, thanh tiến độ với màu sắc theo mức độ (xanh lá cho >80%, xanh dương cho 50-80%, vàng cho 25-50%, xám cho <25%), và tỷ lệ phần trăm.

Hai panel cuối trang: "Năng suất thành viên" xếp hạng các thành viên theo tỷ lệ hoàn thành với thanh progress, số task đang làm và đã xong. Top 3 có badge huy chương. "Công việc quá hạn" liệt kê các task đã quá deadline với thông tin ngày hết hạn và số ngày quá hạn, được highlight màu đỏ theo mức độ nghiêm trọng.

### 2.10. Màn hình Cài đặt

Trang cài đặt cho phép người dùng tùy chỉnh thông tin cá nhân và các preferences. Giao diện sử dụng tab navigation với 4 tab: Hồ sơ, Bảo mật, Thông báo, và Giao diện.

**Tab Hồ sơ** hiển thị form cập nhật thông tin cá nhân. Phía trên là khu vực avatar với ảnh đại diện hiện tại (hoặc chữ cái đầu tên nếu chưa có ảnh) và nút "Thay đổi ảnh". Click vào nút này mở file picker để chọn ảnh mới, hỗ trợ JPG, PNG, WebP với giới hạn 2MB. Ảnh được upload bằng AJAX và cập nhật preview ngay lập tức.

Bên dưới là các trường thông tin: Họ và tên, Email, Chức danh, Phòng ban. Mỗi trường có label rõ ràng và input với giá trị hiện tại được điền sẵn. Nút "Lưu thay đổi" ở cuối form gửi request cập nhật thông tin.

**Tab Bảo mật** chứa form đổi mật khẩu với 3 trường: Mật khẩu hiện tại, Mật khẩu mới (yêu cầu tối thiểu 6 ký tự), và Xác nhận mật khẩu mới. Hệ thống validate mật khẩu mới phải khớp với xác nhận trước khi cho phép submit. Thông báo thành công hoặc lỗi hiển thị sau khi submit.

**Tab Thông báo** cho phép bật/tắt các loại thông báo khác nhau. Mỗi loại thông báo hiển thị trên một dòng với tiêu đề, mô tả ngắn và toggle switch bên phải. Các loại thông báo bao gồm: Được giao công việc mới, Sắp đến hạn, Có bình luận mới, Được nhắc đến (@mention). Toggle switch có animation mượt khi chuyển trạng thái.

**Tab Giao diện** cung cấp tùy chọn về theme và ngôn ngữ. Phần chọn theme có 3 option dạng radio card: Sáng (icon mặt trời), Tối (icon mặt trăng), và Hệ thống (icon màn hình - tự động theo cài đặt OS). Card đang được chọn có viền màu primary. Khi chọn theme, giao diện thay đổi ngay lập tức không cần reload.

Phần chọn ngôn ngữ là dropdown với các option: Tiếng Việt và English. Thay đổi ngôn ngữ sẽ reload trang để áp dụng.

---

## CHƯƠNG 3: THAO TÁC NGƯỜI DÙNG

### 3.1. Quy trình đăng nhập và xác thực

Khi người dùng mở ứng dụng TaskFlow, hệ thống kiểm tra session để xác định trạng thái đăng nhập. Nếu chưa đăng nhập, người dùng được chuyển hướng đến trang login. Tại đây, người dùng nhập email và mật khẩu rồi nhấn nút Đăng nhập.

Hệ thống xác thực thông tin bằng cách so sánh email trong database và verify mật khẩu đã được hash bằng thuật toán bcrypt. Nếu thông tin đúng, hệ thống tạo session mới, lưu user_id và user_role vào session, sau đó chuyển hướng đến Dashboard. Nếu người dùng đã tick "Ghi nhớ đăng nhập", session sẽ có thời hạn dài hơn (30 ngày thay vì 24 giờ mặc định).

Trường hợp quên mật khẩu, người dùng click vào link "Quên mật khẩu?" và nhập email. Hệ thống gửi email chứa link reset password với token có thời hạn 1 giờ. Click vào link trong email sẽ mở trang đặt mật khẩu mới.

### 3.2. Quy trình tạo và quản lý dự án

Để tạo dự án mới, người dùng có quyền (Admin hoặc Manager) vào trang Projects và click nút "Tạo dự án". Modal form xuất hiện, người dùng điền các thông tin cần thiết: tên dự án là bắt buộc, các trường khác như mô tả, độ ưu tiên, màu sắc, ngày bắt đầu/kết thúc là tùy chọn.

Sau khi submit, hệ thống tạo record mới trong bảng projects với người tạo là owner. Đồng thời tạo record trong bảng project_members để gán người tạo làm thành viên với role "owner". Người dùng được chuyển đến trang chi tiết dự án vừa tạo.

Để thêm thành viên vào dự án, owner hoặc manager của dự án vào tab Thành viên trong trang chi tiết dự án, click nút "Thêm thành viên". Modal hiển thị danh sách người dùng trong hệ thống, có thể tìm kiếm theo tên hoặc email. Chọn người cần thêm và chọn role (Manager hoặc Member), sau đó confirm.

Để chỉnh sửa thông tin dự án, người có quyền click nút Edit trên header trang chi tiết. Form chỉnh sửa xuất hiện với các giá trị hiện tại được điền sẵn. Sau khi sửa và submit, thông tin được cập nhật và trang refresh.

### 3.3. Quy trình tạo và quản lý công việc

Công việc (task) có thể được tạo từ nhiều nơi: từ trang Tasks chung, từ Kanban board trong chi tiết dự án, hoặc từ nút quick-add trên header. Khi tạo task, người dùng cần điền tiêu đề (bắt buộc), có thể thêm mô tả, chọn dự án, đặt độ ưu tiên, ngày hết hạn và giao cho thành viên.

Task mới được tạo với trạng thái mặc định là "todo". Để thay đổi trạng thái, người dùng có thể kéo thả task giữa các cột trên Kanban board, hoặc click vào task để mở chi tiết và chọn trạng thái mới từ dropdown.

Tính năng checklist cho phép chia nhỏ task thành các bước. Trong trang chi tiết task, người dùng click "Thêm checklist item", nhập nội dung và Enter. Mỗi item có checkbox riêng, tick vào để đánh dấu hoàn thành. Thanh progress tự động cập nhật theo số item đã hoàn thành.

Để giao task cho người khác, người có quyền mở chi tiết task và click vào phần "Người thực hiện". Dropdown hiển thị danh sách thành viên trong dự án, có thể chọn một hoặc nhiều người. Những người được giao sẽ nhận thông báo.

### 3.4. Quy trình tương tác với lịch và sự kiện

Người dùng có thể tạo sự kiện calendar bằng cách click nút "Thêm sự kiện" hoặc click vào một ngày cụ thể trên lịch. Form tạo sự kiện yêu cầu tiêu đề, có thể thêm mô tả, chọn ngày giờ bắt đầu và kết thúc, loại sự kiện (Họp, Deadline, Nhắc nhở, Khác), màu sắc và cài đặt nhắc nhở trước.

Sự kiện sau khi tạo sẽ hiển thị trên calendar view và Gantt chart. Click vào sự kiện để xem chi tiết hoặc chỉnh sửa. Nếu có cài đặt nhắc nhở, hệ thống sẽ tạo notification trước thời gian sự kiện theo số phút đã chọn.

Trên Gantt chart, người dùng có thể nhìn thấy timeline của tất cả task và sự kiện trong tháng. Các thanh Gantt có màu sắc theo dự án hoặc loại sự kiện, giúp dễ dàng phân biệt. Hover vào thanh để xem tooltip với thông tin chi tiết.

### 3.5. Quy trình upload và quản lý tài liệu

Để upload tài liệu, người dùng vào trang Documents và click nút "Tải lên". Có thể chọn file từ máy tính hoặc kéo thả file vào vùng upload. Hỗ trợ upload nhiều file cùng lúc với các định dạng phổ biến: PDF, Word, Excel, PowerPoint, ảnh và file nén.

File sau khi upload được lưu vào thư mục uploads trên server với tên file được hash để tránh trùng lặp. Metadata của file (tên gốc, kích thước, loại, người upload, thời gian) được lưu vào database. File hiển thị trong danh sách với icon theo loại file.

Để tổ chức file, người dùng có thể tạo thư mục bằng nút "Thư mục mới". Nhập tên thư mục và chọn dự án liên quan (tùy chọn). Sau đó có thể upload file vào thư mục hoặc di chuyển file có sẵn.

Tính năng đánh dấu sao (star) giúp người dùng đánh dấu các file quan trọng để dễ tìm lại. Click vào icon ngôi sao trên file để toggle trạng thái star. File được star sẽ hiển thị icon ngôi sao vàng.


---

## CHƯƠNG 4: ĐÁNH GIÁ HỆ THỐNG

### 4.1. Đánh giá về giao diện người dùng

Giao diện TaskFlow được đánh giá cao về tính thẩm mỹ và sự hiện đại. Việc sử dụng Tailwind CSS đảm bảo tính nhất quán về spacing, typography và màu sắc trong toàn bộ ứng dụng. Color palette được chọn lọc kỹ lưỡng với màu primary xanh dương tạo cảm giác chuyên nghiệp, các màu semantic (đỏ cho lỗi/khẩn cấp, xanh lá cho thành công, vàng cho cảnh báo) được sử dụng đúng ngữ cảnh giúp người dùng dễ dàng nhận biết trạng thái.

Thiết kế responsive hoạt động tốt trên các kích thước màn hình khác nhau. Trên desktop, giao diện tận dụng tối đa không gian với sidebar cố định và nội dung rộng rãi. Trên tablet và mobile, các element tự động sắp xếp lại để phù hợp với màn hình nhỏ hơn mà không làm mất đi tính năng. Tuy nhiên, một số trang phức tạp như Kanban board và Gantt chart có thể khó sử dụng trên màn hình quá nhỏ.

Hệ thống icon Lucide được sử dụng xuyên suốt tạo nên sự đồng nhất về visual language. Mỗi chức năng đều có icon đại diện trực quan, giúp người dùng nhanh chóng nhận biết mà không cần đọc text. Dark mode được implement đầy đủ với màu sắc được điều chỉnh phù hợp, không gây chói mắt khi sử dụng trong điều kiện ánh sáng yếu.

Điểm cần cải thiện về giao diện bao gồm: cần thêm animation và transition mượt mà hơn cho các tương tác, một số form có thể được tối ưu về layout để giảm số bước nhập liệu, và cần có hướng dẫn trực quan (tooltip, hint) cho người dùng mới.

### 4.2. Đánh giá về trải nghiệm người dùng

Trải nghiệm người dùng của TaskFlow được thiết kế theo hướng đơn giản và trực quan. Navigation chính thông qua sidebar với các menu item rõ ràng, người dùng có thể truy cập bất kỳ tính năng nào chỉ với một click. Breadcrumb navigation trong các trang có cấu trúc phân cấp (như Documents) giúp người dùng luôn biết mình đang ở đâu và dễ dàng quay lại.

Hệ thống feedback được implement tốt với toast notifications xuất hiện khi có action thành công hoặc lỗi. Các thông báo lỗi được viết rõ ràng, giải thích nguyên nhân và gợi ý cách khắc phục. Loading states với spinner hoặc skeleton loading giúp người dùng biết hệ thống đang xử lý.

Tính năng tìm kiếm toàn cục trên header cho phép tìm nhanh dự án, task và tài liệu từ bất kỳ trang nào. Kết quả tìm kiếm được nhóm theo loại và hiển thị trong dropdown, click vào kết quả sẽ chuyển đến trang tương ứng.

Tuy nhiên, hệ thống chưa có onboarding flow cho người dùng mới. Khi đăng ký tài khoản và đăng nhập lần đầu, người dùng được đưa thẳng vào Dashboard trống mà không có hướng dẫn bắt đầu từ đâu. Cần bổ sung tour guide hoặc wizard để hướng dẫn tạo dự án đầu tiên, mời thành viên và tạo task.

Về accessibility, hệ thống đã có một số cải thiện như contrast ratio đủ cho text, focus states cho các interactive elements. Tuy nhiên cần bổ sung thêm ARIA labels cho screen readers, keyboard navigation đầy đủ cho tất cả các tính năng, và skip links cho người dùng keyboard.

### 4.3. Đánh giá về chức năng

Về mặt chức năng, TaskFlow đáp ứng đầy đủ các yêu cầu cơ bản của một hệ thống quản lý dự án. Module Authentication hoạt động ổn định với đầy đủ các tính năng: đăng ký, đăng nhập, quên mật khẩu, đổi mật khẩu. Session management được xử lý tốt với timeout hợp lý và remember me option.

Module Projects cung cấp đầy đủ CRUD operations, Kanban board với drag & drop hoạt động mượt mà, quản lý thành viên với phân quyền rõ ràng. Tính năng transfer ownership cho phép chuyển quyền sở hữu dự án khi cần thiết.

Module Tasks là module phong phú nhất với nhiều tính năng: tạo/sửa/xóa task, checklist, comments, file attachments, multiple assignees, priority levels, due dates. Kanban board cho phép kéo thả task giữa các trạng thái một cách trực quan.

Module Calendar tích hợp tốt với tasks, hiển thị cả events và task deadlines trên cùng một view. Gantt chart cung cấp cái nhìn timeline hữu ích cho việc lập kế hoạch. Tuy nhiên, chưa có tính năng recurring events và chưa tích hợp với Google Calendar hay Outlook.

Module Documents đáp ứng nhu cầu cơ bản về lưu trữ và chia sẻ file. Hỗ trợ nhiều định dạng file phổ biến, có tổ chức theo thư mục và tìm kiếm. Điểm cần cải thiện là chưa có preview cho các loại file, chưa có version control và chưa có tính năng share link public.

Module Reports cung cấp các biểu đồ thống kê hữu ích với Chart.js. Phân biệt view cho Member và Manager là điểm cộng lớn. Tính năng export CSV hoạt động tốt. Tuy nhiên, cần bổ sung thêm các loại báo cáo chi tiết hơn và khả năng tùy chỉnh dashboard.

### 4.4. Đánh giá về kỹ thuật

Về kiến trúc, TaskFlow được xây dựng theo mô hình MVC (Model-View-Controller) rõ ràng. Code được tổ chức theo cấu trúc thư mục logic với sự phân tách giữa các layer. Việc sử dụng namespace và autoloading theo chuẩn PSR-4 giúp code dễ maintain và mở rộng.

Về bảo mật, hệ thống đã implement các biện pháp bảo vệ cơ bản: CSRF protection cho tất cả các form, prepared statements để chống SQL injection, output escaping để chống XSS, password hashing với bcrypt. Rate limiting được áp dụng cho các API endpoints nhạy cảm như login và forgot password.

Về performance, hệ thống sử dụng AJAX cho hầu hết các tương tác để tránh reload toàn trang. Tuy nhiên, một số queries phức tạp (như lấy dashboard data cho Manager) có thể chậm khi dữ liệu lớn. Cần implement caching layer (Redis hoặc Memcached) và tối ưu database indexes.

Điểm yếu lớn nhất về kỹ thuật là thiếu automated testing. Hiện tại không có unit tests hay integration tests, điều này làm tăng rủi ro khi refactor hoặc thêm tính năng mới. Cần bổ sung PHPUnit cho backend testing và có thể Cypress cho end-to-end testing.

Documentation cũng cần được cải thiện. Mặc dù có một số file markdown mô tả kiến trúc và hướng dẫn cài đặt, nhưng chưa có API documentation chi tiết (nên dùng Swagger/OpenAPI) và inline code comments còn thiếu ở nhiều nơi.

### 4.5. Tổng kết và đề xuất

Nhìn chung, TaskFlow là một hệ thống quản lý dự án hoàn chỉnh với giao diện hiện đại và chức năng đầy đủ cho nhu cầu cơ bản. Hệ thống phù hợp cho các team nhỏ đến trung bình (5-50 người) trong việc quản lý dự án phần mềm, theo dõi tiến độ công việc và cộng tác nhóm.

Điểm mạnh nổi bật của hệ thống bao gồm giao diện người dùng được thiết kế chuyên nghiệp với Tailwind CSS, hệ thống phân quyền chi tiết với 4 roles và permissions matrix rõ ràng, Dashboard được tùy biến theo vai trò người dùng, và Kanban board trực quan cho quản lý công việc.

Các điểm cần ưu tiên cải thiện trong tương lai gần bao gồm việc bổ sung automated testing để đảm bảo chất lượng code, implement caching để cải thiện performance, thêm email notifications cho các sự kiện quan trọng, và xây dựng onboarding flow cho người dùng mới.

Về lâu dài, hệ thống có thể được mở rộng với các tính năng như real-time updates sử dụng WebSocket, tích hợp với các công cụ bên ngoài (Slack, Microsoft Teams, Google Calendar), phát triển mobile app native, và API public cho third-party integrations.

---

*Tài liệu được biên soạn: 27/12/2024*
*Phiên bản hệ thống: 1.0*
