# Test Booking Flow

## Các bước test flow đặt vé:

### 1. Truy cập trang chọn ghế
- URL: `/booking/seat-selection/{showtime_id}`
- Kiểm tra: Hiển thị thông tin phim, rạp, ngày giờ chiếu
- Kiểm tra: Hiển thị sơ đồ ghế với trạng thái (available, occupied, held)

### 2. Chọn ghế
- Click vào ghế available
- Kiểm tra: Ghế chuyển sang trạng thái selected
- Kiểm tra: Hiển thị ghế đã chọn trong panel bên phải
- Kiểm tra: Cập nhật tổng tiền

### 3. Chọn đồ ăn (tùy chọn)
- Tăng/giảm số lượng đồ ăn
- Kiểm tra: Cập nhật tổng tiền đồ ăn
- Kiểm tra: Hiển thị tóm tắt đơn hàng

### 4. Chọn phương thức thanh toán
- Chọn phương thức thanh toán
- Kiểm tra: Cập nhật tổng tiền cuối cùng

### 5. Submit form
- Click "Đặt vé"
- Kiểm tra: AJAX request gửi dữ liệu đúng format
- Kiểm tra: Redirect đến trang payment

### 6. Trang thanh toán
- URL: `/booking/payment/{showtime_id}`
- Kiểm tra: Hiển thị thông tin đặt vé từ session
- Kiểm tra: Form thanh toán

### 7. Xác nhận thanh toán
- Submit form thanh toán
- Kiểm tra: Tạo booking trong database
- Kiểm tra: Redirect đến trang confirmation

### 8. Trang xác nhận
- URL: `/booking/confirmation/{booking_id}`
- Kiểm tra: Hiển thị thông tin booking
- Kiểm tra: Link đến trang ticket

### 9. Trang vé
- URL: `/booking/ticket/{booking_id}`
- Kiểm tra: Hiển thị vé với barcode
- Kiểm tra: Nút in vé

## Các lỗi có thể gặp:

1. **Lỗi 404**: Route không tồn tại
2. **Lỗi 500**: Lỗi server (database, model, etc.)
3. **Lỗi validation**: Dữ liệu không hợp lệ
4. **Lỗi session**: Dữ liệu session bị mất
5. **Lỗi AJAX**: Request không thành công

## Cách debug:

1. Kiểm tra log Laravel: `storage/logs/laravel.log`
2. Kiểm tra network tab trong browser dev tools
3. Kiểm tra database có dữ liệu không
4. Kiểm tra session có dữ liệu không

## Dữ liệu test cần có:

1. **User**: Đã đăng nhập
2. **Showtime**: Có sẵn với available_seats > 0
3. **Seats**: Có ghế available
4. **FoodItems**: Có đồ ăn available
5. **Screen**: Có rạp chiếu
6. **Movie**: Có phim
