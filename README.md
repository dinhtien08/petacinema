# PETACINEMA Admin MVC - Inline CRUD

Bản này giữ giao diện admin của PETACINEMA và cách tổ chức MVC đơn giản theo PHP1.

## Module
- Rooms
- Room Types
- Seat Types

## CRUD tại cùng màn hình
- Mỗi module chỉ có `views/admin/<module>/list.php`.
- Thêm và sửa mở bằng Bootstrap Modal ngay trên trang danh sách.
- Xóa xử lý trực tiếp qua route rồi quay lại trang danh sách.
- Không dùng `add.php` hoặc `edit.php`.

## Cấu trúc xử lý
`index.php -> routes/index.php -> Controller -> Model -> View`

## Cài đặt
1. Import database `movie_booking`.
2. Đặt thư mục `petacinema` vào `htdocs`.
3. Kiểm tra thông tin DB và `BASE_URL` trong `configs/env.php`.
4. Truy cập:
   - `?action=rooms`
   - `?action=room-types`
   - `?action=seat-types`
