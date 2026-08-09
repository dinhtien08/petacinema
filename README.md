PETACINEMA

PETACINEMA là hệ thống đặt vé xem phim cho một rạp chiếu phim, xây dựng bằng PHP thuần theo mô hình MVC, MySQL và Bootstrap.

Công nghệ

PHP 8+

MySQL / PDO

HTML, CSS, JavaScript

Bootstrap 5 + Bootstrap Icons

MVC (Model - View - Controller)

VNPay Sandbox

Laragon / Apache

Vai trò người dùng

Khách hàng

Đăng ký, đăng nhập và quản lý tài khoản.

Xem phim, lịch chiếu và chi tiết phim.

Chọn suất chiếu, ghế và combo đồ ăn.

Thanh toán trực tuyến qua VNPay.

Xem vé đã mua và trạng thái check-in.

Nhân viên

Xem phim, suất chiếu, phòng và sơ đồ ghế.

Theo dõi booking và thanh toán.

Check-in khách theo mã booking.

Xác nhận giao đồ ăn.

Quản trị viên

Dashboard thống kê.

Quản lý phim, suất chiếu, phòng, loại phòng và loại ghế.

Quản lý sơ đồ ghế và trạng thái ghế.

Quản lý người dùng, đồ ăn và biến thể đồ ăn.

Theo dõi booking và giao dịch thanh toán.

Nghiệp vụ chính

Đặt vé

Chọn phim
→ Chọn suất chiếu
→ Chọn ghế
→ Chọn combo
→ Tạo booking pending
→ Thanh toán VNPay
→ Booking paid
→ Xem vé

Booking pending giữ ghế trong 5 phút.

Ghế được kiểm tra và khóa trong transaction để hạn chế đặt trùng.

Hỗ trợ ghế Standard, VIP và Couple.

Suất chiếu

Mỗi suất chiếu thuộc một phim và một phòng.

Thời gian kết thúc được tính từ thời lượng phim và thời gian dọn phòng.

Hệ thống kiểm tra xung đột lịch chiếu trong cùng phòng.

Suất chiếu có booking đang hiệu lực không được chỉnh sửa tùy tiện.

Check-in

Booking đã thanh toán mới được check-in.

Cho phép check-in đến 30 phút sau giờ bắt đầu phim.

Sau thời điểm này booking được hiển thị là Quá hạn check-in.

Giao đồ ăn

Đồ ăn được xác nhận giao từ booking đã thanh toán.

Có thể giao đến hết thời gian của suất chiếu.

Sau end_time, đơn chưa giao được hiển thị là Quá hạn giao.

Cấu trúc thư mục

petacinema/
├── assets/
├── configs/
├── controllers/
├── middlewares/
├── models/
├── routes/
├── views/
│ ├── account/
│ ├── admin/
│ └── staff/
├── index.php
├── vnpay_ipn.php
└── vnpay_return.php

Chạy project

Cài PHP 8+, MySQL và Apache/Laragon.

Đặt project vào thư mục web server, ví dụ C:/laragon/www/petacinema.

Chuẩn bị database và cấu hình kết nối phù hợp với môi trường local.

Khởi động Apache và MySQL.

Truy cập:

http://localhost/petacinema/

Ghi chú

Project được xây dựng phục vụ mục đích học tập và mô phỏng quy trình đặt vé của một rạp chiếu phim đơn.
