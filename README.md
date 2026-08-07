# PETACINEMA - Hệ thống đặt vé xem phim đơn rạp

PETACINEMA là đồ án xây dựng website quản lý và đặt vé xem phim cho **một rạp chiếu phim**, phát triển bằng **PHP thuần theo mô hình MVC**, MySQL và Bootstrap.

Hệ thống hỗ trợ ba nhóm người dùng chính:

- **Khách hàng (User):** xem phim, chọn suất chiếu, chọn ghế, mua combo, thanh toán VNPay Sandbox và xem vé đã mua.
- **Nhân viên (Staff):** theo dõi phim, suất chiếu, phòng, sơ đồ ghế, booking, thanh toán và các thông tin phục vụ vận hành rạp.
- **Quản trị viên (Admin):** quản lý toàn bộ dữ liệu và nghiệp vụ của hệ thống.

---

## 1. Công nghệ sử dụng

- **Backend:** PHP 8+
- **Database:** MySQL
- **Database Access:** PDO
- **Frontend:** HTML5, CSS3, Bootstrap 5, Bootstrap Icons, JavaScript
- **Kiến trúc:** MVC (Model - View - Controller)
- **Thanh toán:** VNPay Sandbox
- **Web server local:** Laragon / Apache

> PHP 8+ được khuyến nghị vì project sử dụng biểu thức `match` trong routing.

---

## 2. Chức năng chính

### 2.1. Khách hàng

- Đăng ký tài khoản.
- Đăng nhập / đăng xuất.
- Xem danh sách phim đang chiếu và sắp chiếu.
- Xem thông tin chi tiết phim.
- Xem lịch chiếu theo phim.
- Chọn ngày và suất chiếu.
- Xem sơ đồ ghế theo phòng chiếu.
- Chọn ghế Standard, VIP hoặc Couple.
- Kiểm tra trạng thái ghế trước khi đặt.
- Giữ ghế tạm thời trong quá trình thanh toán.
- Chọn đồ ăn / combo đi kèm booking.
- Thanh toán trực tuyến qua VNPay Sandbox.
- Xem kết quả thanh toán.
- Xem danh sách **Vé của tôi**.
- Xem mã booking, phim, suất chiếu, phòng, ghế và số tiền.
- Theo dõi trạng thái check-in của booking.

### 2.2. Quản trị viên

- Dashboard thống kê tổng quan.
- Quản lý phim.
- Quản lý suất chiếu.
- Quản lý phòng chiếu.
- Quản lý loại phòng.
- Quản lý loại ghế.
- Sinh sơ đồ ghế cho phòng chiếu.
- Thay đổi trạng thái ghế: khả dụng, hỏng, bảo trì.
- Xem sơ đồ ghế theo suất chiếu.
- Quản lý người dùng và phân quyền.
- Quản lý đồ ăn.
- Quản lý biến thể / kích cỡ đồ ăn.
- Quản lý đơn đồ ăn.
- Theo dõi booking.
- Xem chi tiết booking.
- Theo dõi thanh toán.
- Xem chi tiết giao dịch thanh toán.

### 2.3. Nhân viên

Nhân viên có giao diện riêng và chủ yếu được cấp quyền **xem / theo dõi nghiệp vụ**, không được phép chỉnh sửa các dữ liệu quản trị quan trọng.

Các chức năng gồm:

- Dashboard nhân viên.
- Xem danh sách phim và chi tiết phim.
- Xem suất chiếu.
- Xem phòng và sơ đồ ghế.
- Xem loại phòng và loại ghế.
- Xem booking và chi tiết booking.
- Xem danh sách thanh toán và chi tiết giao dịch.
- Xem đồ ăn, biến thể đồ ăn và đơn đồ ăn.

---

## 3. Luồng đặt vé

Luồng đặt vé chính của hệ thống:

```text
Trang chủ
    ↓
Chi tiết phim
    ↓
Chọn ngày / suất chiếu
    ↓
Chọn ghế
    ↓
Chọn combo
    ↓
Tạo booking pending
    ↓
VNPay Sandbox
    ↓
Thanh toán thành công
    ↓
Booking = paid
    ↓
Vé của tôi
```

Hệ thống có kiểm tra ghế trước khi tạo booking nhằm hạn chế trường hợp nhiều khách hàng cùng đặt một ghế.

Booking ở trạng thái `pending` chỉ giữ ghế trong một khoảng thời gian nhất định. Thời gian mặc định hiện tại được cấu hình là **5 phút**.

---

## 4. Nghiệp vụ suất chiếu

Mỗi suất chiếu liên kết với:

- một bộ phim;
- một phòng chiếu;
- thời gian bắt đầu;
- thời gian kết thúc;
- giá vé cơ bản.

Thời gian kết thúc được xác định từ thời lượng phim và thời gian dọn phòng.

Thời gian dọn phòng mặc định:

```php
SHOWTIME_CLEANING_TIME = 20; // phút
```

Hệ thống kiểm tra xung đột lịch để hạn chế tạo hai suất chiếu bị trùng thời gian trong cùng một phòng.

---

## 5. Phòng và ghế

Một phòng thuộc một loại phòng (`room_types`).

Một ghế thuộc:

- một phòng;
- một loại ghế;
- một hàng;
- một cột;
- một trạng thái.

Các loại ghế có thể bao gồm:

- Standard
- VIP
- Couple

Trạng thái ghế được sử dụng để quản lý các ghế đang khả dụng, bị hỏng hoặc đang bảo trì.

Đối với ghế Couple, hệ thống có xử lý kiểm tra cặp ghế khi khách hàng thực hiện đặt vé.

---

## 6. Booking và chống đặt trùng ghế

Booking có các trạng thái nghiệp vụ như:

- `pending`
- `paid`
- `cancelled`

Khi khách đặt vé, hệ thống sử dụng transaction và kiểm tra dữ liệu ghế trước khi hoàn tất booking.

Một số xử lý quan trọng:

- Transaction bằng PDO.
- Lock dữ liệu ghế trong quá trình xử lý booking.
- Kiểm tra ghế đã được booking khác giữ hay chưa.
- Booking pending quá hạn có thể bị hủy.
- Hoàn tồn kho combo khi booking không hoàn tất theo luồng xử lý tương ứng.
- Giá booking được kiểm tra lại ở backend.

---

## 7. Check-in

Trạng thái check-in được quản lý ở **bảng `bookings`**, không lưu riêng trên từng ticket.

Các trường liên quan:

```text
checkin_status
checked_in_at
checked_in_by
```

Ví dụ trạng thái chưa check-in:

```text
checkin_status = pending
checked_in_at  = NULL
checked_in_by  = NULL
```

Ví dụ booking đã check-in:

```text
checkin_status = checked_in
checked_in_at  = thời gian check-in
checked_in_by  = ID nhân viên
```

Trang **Vé của tôi** sử dụng thông tin trên booking để hiển thị trạng thái check-in cho khách hàng.

---

## 8. Thanh toán VNPay Sandbox

PETACINEMA tích hợp VNPay ở môi trường Sandbox.

Luồng thanh toán:

```text
Booking pending
    ↓
Tạo URL thanh toán VNPay
    ↓
Khách thực hiện thanh toán
    ↓
VNPay Return / IPN
    ↓
Kiểm tra chữ ký và số tiền
    ↓
Cập nhật payment
    ↓
Cập nhật booking
```

Các file chính:

```text
models/VnpayService.php
controllers/PaymentController.php
vnpay_return.php
vnpay_ipn.php
```

Thông tin VNPay được cấu hình trong:

```text
configs/env.php
```

Không nên commit hoặc chia sẻ **VNPay Secret Key thật** lên repository công khai.

---

## 9. Cơ sở dữ liệu

Database mặc định:

```text
movie_booking
```

File SQL đi kèm project:

```text
movie_booking.sql
```

Database hiện gồm **13 bảng chính**:

| STT | Bảng | Chức năng |
|---:|---|---|
| 1 | `users` | Tài khoản và phân quyền |
| 2 | `movies` | Thông tin phim |
| 3 | `room_types` | Loại phòng chiếu |
| 4 | `rooms` | Phòng chiếu |
| 5 | `seat_types` | Loại ghế |
| 6 | `seats` | Ghế trong phòng |
| 7 | `showtimes` | Suất chiếu |
| 8 | `bookings` | Đơn đặt vé |
| 9 | `tickets` | Chi tiết vé / ghế của booking |
| 10 | `payments` | Giao dịch thanh toán |
| 11 | `foods` | Đồ ăn / sản phẩm |
| 12 | `food_variants` | Biến thể đồ ăn |
| 13 | `food_orders` | Combo / đồ ăn thuộc booking |

### Quan hệ tổng quát

```text
users
  │
  └── bookings
       ├── tickets ── seats
       │               └── seat_types
       │
       ├── payments
       │
       └── food_orders
              └── food_variants
                     └── foods

showtimes
 ├── movies
 └── rooms
      └── room_types
```

---

## 10. Cấu trúc thư mục

```text
petacinema/
│
├── assets/
│   ├── css/
│   └── uploads/
│
├── configs/
│   ├── env.php
│   └── helper.php
│
├── controllers/
│   ├── AuthController.php
│   ├── BookingController.php
│   ├── DashboardController.php
│   ├── FoodController.php
│   ├── HomeController.php
│   ├── MovieController.php
│   ├── PaymentController.php
│   ├── RoomController.php
│   ├── ShowtimeController.php
│   ├── UserController.php
│   └── Staff...Controller.php
│
├── middlewares/
│   ├── AuthMiddleware.php
│   ├── GuestMiddleware.php
│   └── RoleMiddleware.php
│
├── models/
│   ├── BaseModel.php
│   ├── BookingModel.php
│   ├── MovieModel.php
│   ├── PaymentModel.php
│   ├── RoomModel.php
│   ├── SeatModel.php
│   ├── ShowtimeModel.php
│   ├── UserModel.php
│   └── ...
│
├── routes/
│   └── index.php
│
├── views/
│   ├── admin/
│   ├── staff/
│   ├── home.php
│   ├── movie_detail.php
│   ├── booking_date.php
│   ├── booking_combo.php
│   ├── my_tickets.php
│   ├── login.php
│   └── register.php
│
├── index.php
├── movie_booking.sql
├── vnpay_return.php
├── vnpay_ipn.php
└── README.md
```

---

## 11. Mô hình xử lý MVC

Luồng xử lý cơ bản:

```text
Browser
   ↓
index.php
   ↓
routes/index.php
   ↓
Middleware
   ↓
Controller
   ↓
Model ↔ MySQL
   ↓
Controller
   ↓
View
   ↓
Browser
```

Các middleware hiện tại:

- `GuestMiddleware`: xử lý các route dành cho khách chưa đăng nhập.
- `AuthMiddleware`: kiểm tra trạng thái đăng nhập.
- `RoleMiddleware`: kiểm tra quyền `admin`, `staff`, `user`.

---

## 12. Yêu cầu môi trường

Khuyến nghị sử dụng:

- Laragon
- Apache
- PHP 8.0 trở lên
- MySQL 8.x
- phpMyAdmin
- Trình duyệt Chrome / Edge

Không cần Composer cho cấu trúc project hiện tại.

---

## 13. Hướng dẫn cài đặt

### Bước 1: Đưa source vào Laragon

Copy thư mục:

```text
petacinema
```

vào:

```text
C:\laragon\www\
```

Kết quả:

```text
C:\laragon\www\petacinema\
```

### Bước 2: Khởi động Laragon

Khởi động:

- Apache
- MySQL

### Bước 3: Import database

Mở phpMyAdmin và import file:

```text
movie_booking.sql
```

Database sử dụng:

```text
movie_booking
```

### Bước 4: Cấu hình database

Mở:

```text
configs/env.php
```

Kiểm tra:

```php
DB_HOST     = localhost
DB_PORT     = 3306
DB_USERNAME = root
DB_PASSWORD =
DB_NAME     = movie_booking
```

Nếu MySQL trên máy có mật khẩu khác thì thay đổi `DB_PASSWORD` tương ứng.

### Bước 5: Kiểm tra BASE_URL

Mặc định:

```text
http://localhost/petacinema/
```

Nếu đổi tên thư mục project, cần cập nhật `BASE_URL` trong `configs/env.php`.

### Bước 6: Cấu hình VNPay Sandbox

Trong `configs/env.php`, cấu hình các giá trị Sandbox được VNPay cung cấp:

```php
VNPAY_TMN_CODE
VNPAY_HASH_SECRET
VNPAY_PAYMENT_URL
VNPAY_RETURN_URL
```

Không sử dụng Secret Key production cho project demo.

### Bước 7: Truy cập website

```text
http://localhost/petacinema/
```

---

## 14. Tài khoản dữ liệu mẫu

File SQL hiện có một số tài khoản phục vụ kiểm thử local.

| Vai trò | Email | Mật khẩu dữ liệu mẫu |
|---|---|---|
| Admin | `admin@petacinema.com` | `123456` |
| Staff | `staff1@petacinema.com` | `123456` |
| Staff | `staff2@petacinema.com` | `123456` |
| User | `binh@gmail.com` | `123456` |

> Các tài khoản trên chỉ dùng cho môi trường local / đồ án. Khi triển khai thực tế, tất cả mật khẩu phải được lưu bằng `password_hash()` và kiểm tra bằng `password_verify()`.

---

## 15. Một số route chính

### Client

```text
?action=/
?action=movie_detail
?action=booking_date
?action=booking_checkout
?action=my_tickets
?action=login
?action=register
```

### Admin

```text
?action=dashboard
?action=movies
?action=showtimes
?action=rooms
?action=room-types
?action=seat-types
?action=bookings
?action=payment_list
?action=food_list
?action=food_variant_list
?action=food_order_list
?action=users
```

### Staff

```text
?action=staff_dashboard
?action=staff_movies
?action=staff_showtimes
?action=staff_rooms
?action=staff_bookings
?action=staff_payment_list
?action=staff_food_list
```

---

## 16. Kiểm thử luồng chính

Khi chạy project lần đầu, nên test theo thứ tự:

1. Import database thành công.
2. Đăng nhập Admin.
3. Kiểm tra phim.
4. Kiểm tra phòng và sơ đồ ghế.
5. Kiểm tra suất chiếu.
6. Đăng nhập / đăng ký tài khoản User.
7. Chọn một phim.
8. Chọn suất chiếu.
9. Chọn ghế.
10. Chọn combo nếu cần.
11. Thanh toán VNPay Sandbox.
12. Kiểm tra booking chuyển sang `paid`.
13. Mở **Vé của tôi**.
14. Kiểm tra ghế đã đặt trên sơ đồ suất chiếu.
15. Kiểm tra trạng thái check-in.
16. Đăng nhập Staff và kiểm tra booking / thanh toán.

---

## 17. Phạm vi đồ án

PETACINEMA được xây dựng cho mô hình **một rạp chiếu phim**, tập trung vào các nghiệp vụ chính:

- quản lý phim;
- quản lý phòng và ghế;
- lập lịch suất chiếu;
- đặt vé online;
- chống đặt trùng ghế;
- quản lý combo;
- thanh toán online bằng VNPay Sandbox;
- quản lý booking và vé;
- theo dõi check-in;
- phân quyền Admin / Staff / User.

Project phục vụ mục đích **học tập và báo cáo đồ án**, chưa được thiết kế như một hệ thống production quy mô lớn.

---

## 18. Lưu ý bảo mật

Khi đưa source lên GitHub hoặc chia sẻ công khai:

- Không commit Secret Key VNPay thật.
- Không commit mật khẩu database production.
- Không lưu mật khẩu người dùng dưới dạng plain text.
- Nên bổ sung CSRF token cho các thao tác thay đổi dữ liệu.
- Nên sử dụng POST/DELETE thay cho GET đối với thao tác xóa.
- Nên tách biến môi trường nhạy cảm khỏi source code.
- Không nên đưa thư mục `.git` vào file ZIP khi nộp source.

---

## 19. Tác giả

**Đồ án:** PETACINEMA - Hệ thống đặt vé xem phim đơn rạp  
**Mục đích:** Học tập / báo cáo đồ án

---

## 20. License

Project được xây dựng phục vụ mục đích học tập. Không sử dụng thông tin cấu hình, tài khoản hoặc Secret Key trong source cho môi trường production.
