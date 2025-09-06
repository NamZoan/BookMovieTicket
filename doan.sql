# Thiết kế Database Trang Web Đặt Vé Xem Phim

## 1. Danh sách các bảng chính

### 1.1 Bảng Users (Người dùng)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    loyalty_points INT DEFAULT 0,
    user_type ENUM('Customer', 'Admin', 'Staff') DEFAULT 'Customer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

### 1.2 Bảng Movies (Phim)
CREATE TABLE movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    original_title VARCHAR(200),
    description TEXT,
    duration INT NOT NULL, -- Thời lượng (phút)
    release_date DATE,
    director VARCHAR(100),
    cast TEXT, -- Diễn viên chính
    genre VARCHAR(100), -- Thể loại
    language VARCHAR(50),
    country VARCHAR(50),
    rating DECIMAL(2,1), -- Đánh giá (1.0-10.0)
    age_rating VARCHAR(10), -- P, K, T13, T16, T18, C
    poster_url VARCHAR(500),
    trailer_url VARCHAR(500),
    status ENUM('Coming Soon', 'Now Showing', 'Ended') DEFAULT 'Coming Soon',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

### 1.3 Bảng Cinemas (Rạp chiếu phim)
CREATE TABLE cinemas (
    cinema_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

### 1.4 Bảng Screens (Phòng chiếu)
CREATE TABLE screens (
    screen_id INT PRIMARY KEY AUTO_INCREMENT,
    cinema_id INT NOT NULL,
    screen_name VARCHAR(50) NOT NULL,
    total_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

    FOREIGN KEY (cinema_id) REFERENCES cinemas(cinema_id)
);

### 1.5 Bảng Seats (Ghế ngồi)
CREATE TABLE seats (
    seat_id INT PRIMARY KEY AUTO_INCREMENT,
    screen_id INT NOT NULL,
    row_name VARCHAR(5) NOT NULL, -- A, B, C...
    seat_number INT NOT NULL, -- 1, 2, 3...
    seat_type ENUM('Normal', 'VIP', 'Couple','Disabled') DEFAULT 'Normal',
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (screen_id) REFERENCES screens(screen_id),
    UNIQUE KEY unique_seat (screen_id, row_name, seat_number)
);

### 1.6 Bảng Showtimes (Suất chiếu)
CREATE TABLE showtimes (
    showtime_id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,
    screen_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    end_time TIME NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    available_seats INT NOT NULL,
    status ENUM('Active', 'Cancelled', 'Full') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id),
    FOREIGN KEY (screen_id) REFERENCES screens(screen_id)
);

### 1.7 Bảng Pricing (Bảng giá)
CREATE TABLE pricing (
    pricing_id INT PRIMARY KEY AUTO_INCREMENT,
    seat_type ENUM('Regular', 'VIP', 'Couple') NOT NULL,
    day_type ENUM('Weekday', 'Weekend', 'Holiday') NOT NULL,
    time_slot ENUM('Morning', 'Afternoon', 'Evening', 'Late Night') NOT NULL,
    price_multiplier DECIMAL(3,2) DEFAULT 1.00, -- Hệ số nhân với base_price
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

### 1.8 Bảng Bookings (Đặt vé)
CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Cash', 'Credit Card', 'Banking', 'E-Wallet', 'Loyalty Points'),
    payment_status ENUM('Pending', 'Paid', 'Failed', 'Refunded') DEFAULT 'Pending',
    booking_status ENUM('Confirmed', 'Cancelled', 'Used') DEFAULT 'Confirmed',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_date TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(showtime_id)
);

### 1.9 Bảng Booking_Seats (Ghế đã đặt)
CREATE TABLE booking_seats (
    booking_seat_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    seat_id INT NOT NULL,
    seat_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (seat_id) REFERENCES seats(seat_id),
    UNIQUE KEY unique_booking_seat (booking_id, seat_id)
);

### 1.10 Bảng Food_Items (Đồ ăn, nước uống)
CREATE TABLE food_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('Popcorn', 'Drinks', 'Snacks', 'Combo') NOT NULL,
    image_url VARCHAR(500),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

### 1.11 Bảng Booking_Food (Đồ ăn đã đặt)
CREATE TABLE booking_food (
    booking_food_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (item_id) REFERENCES food_items(item_id)
);

### 1.12 Bảng Promotions (Khuyến mãi)
CREATE TABLE promotions (
    promotion_id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_type ENUM('Percentage', 'Fixed Amount') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2),
    usage_limit INT,
    used_count INT DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

### 1.13 Bảng Reviews (Đánh giá phim)
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 10),
    comment TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id),
    UNIQUE KEY unique_user_movie (user_id, movie_id)
);

### 1.14 Bảng News (Tin tức)
CREATE TABLE news (
    news_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    summary TEXT,
    image_url VARCHAR(500),
    author VARCHAR(100),
    category ENUM('Movie News', 'Cinema News', 'Promotion', 'Event'),
    is_published BOOLEAN DEFAULT FALSE,
    published_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

### 1.15 Bảng System_Settings (Cài đặt hệ thống)
CREATE TABLE system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

## 2. Indexes quan trọng

-- Indexes cho hiệu suất truy vấn
CREATE INDEX idx_movies_status ON movies(status);
CREATE INDEX idx_movies_release_date ON movies(release_date);
CREATE INDEX idx_showtimes_date_time ON showtimes(show_date, show_time);
CREATE INDEX idx_showtimes_movie ON showtimes(movie_id);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_status ON bookings(booking_status);
CREATE INDEX idx_bookings_date ON bookings(booking_date);

## 3. Views hữu ích

### 3.1 View thông tin suất chiếu chi tiết
CREATE VIEW showtime_details AS
SELECT
    s.showtime_id,
    m.title as movie_title,
    m.duration,
    m.age_rating,
    c.name as cinema_name,
    c.address as cinema_address,
    sc.screen_name,
    s.show_date,
    s.show_time,
    s.base_price,
    s.available_seats,
    s.status
FROM showtimes s
JOIN movies m ON s.movie_id = m.movie_id
JOIN screens sc ON s.screen_id = sc.screen_id
JOIN cinemas c ON sc.cinema_id = c.cinema_id;

### 3.2 View thống kê doanh thu
CREATE VIEW revenue_stats AS
SELECT
    DATE(b.booking_date) as booking_date,
    c.name as cinema_name,
    m.title as movie_title,
    COUNT(b.booking_id) as total_bookings,
    SUM(b.final_amount) as revenue
FROM bookings b
JOIN showtimes s ON b.showtime_id = s.showtime_id
JOIN movies m ON s.movie_id = m.movie_id
JOIN screens sc ON s.screen_id = sc.screen_id
JOIN cinemas c ON sc.cinema_id = c.cinema_id
WHERE b.payment_status = 'Paid'
GROUP BY DATE(b.booking_date), c.cinema_id, m.movie_id;

## 4. Stored Procedures quan trọng

### 4.1 Procedure đặt vé
DELIMITER //
CREATE PROCEDURE BookTickets(
    IN p_user_id INT,
    IN p_showtime_id INT,
    IN p_seat_ids TEXT, -- Danh sách seat_id ngăn cách bởi dấu phẩy
    IN p_promotion_code VARCHAR(20)
)
BEGIN
    DECLARE v_booking_id INT;
    DECLARE v_total_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_discount_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_final_amount DECIMAL(10,2);
    DECLARE v_booking_code VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Tạo mã booking
    SET v_booking_code = CONCAT('BK', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));

    -- Tính tổng tiền vé
    -- ... Logic tính toán phức tạp ...

    -- Tạo booking
    INSERT INTO bookings (user_id, showtime_id, booking_code, total_amount, discount_amount, final_amount)
    VALUES (p_user_id, p_showtime_id, v_booking_code, v_total_amount, v_discount_amount, v_final_amount);

    SET v_booking_id = LAST_INSERT_ID();

    -- Thêm ghế đã đặt
    -- ... Logic thêm ghế ...

    -- Cập nhật số ghế còn trống
    UPDATE showtimes
    SET available_seats = available_seats - (LENGTH(p_seat_ids) - LENGTH(REPLACE(p_seat_ids, ',', '')) + 1)
    WHERE showtime_id = p_showtime_id;

    COMMIT;

    SELECT v_booking_id as booking_id, v_booking_code as booking_code;
END //
DELIMITER ;

## 5. Triggers

### 5.1 Trigger cập nhật điểm thưởng
DELIMITER //
CREATE TRIGGER update_loyalty_points
AFTER UPDATE ON bookings
FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'Paid' AND OLD.payment_status != 'Paid' THEN
        UPDATE users
        SET loyalty_points = loyalty_points + FLOOR(NEW.final_amount / 1000)
        WHERE user_id = NEW.user_id;
    END IF;
END //
DELIMITER ;

## 6. Dữ liệu mẫu

### 6.1 Thêm dữ liệu cài đặt hệ thống
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('booking_expiry_minutes', '15', 'Thời gian hết hạn booking (phút)'),
('max_seats_per_booking', '8', 'Số ghế tối đa mỗi lần đặt'),
('loyalty_points_rate', '1000', 'Số tiền để được 1 điểm thưởng'),
('refund_deadline_hours', '2', 'Thời hạn hủy vé trước giờ chiếu'),
('maintenance_mode', 'false', 'Chế độ bảo trì hệ thống');

### 6.2 Thêm bảng giá mẫu
INSERT INTO pricing (seat_type, day_type, time_slot, price_multiplier) VALUES
('Regular', 'Weekday', 'Morning', 0.8),
('Regular', 'Weekday', 'Afternoon', 1.0),
('Regular', 'Weekday', 'Evening', 1.2),
('Regular', 'Weekend', 'Morning', 1.0),
('Regular', 'Weekend', 'Afternoon', 1.3),
('Regular', 'Weekend', 'Evening', 1.5),
('VIP', 'Weekday', 'Morning', 1.5),
('VIP', 'Weekday', 'Afternoon', 1.8),
('VIP', 'Weekday', 'Evening', 2.0),
('Couple', 'Weekday', 'Evening', 2.5),
('Couple', 'Weekend', 'Evening', 3.0);

## 7. Lưu ý quan trọng khi triển khai

1. **Bảo mật**: Hash password, mã hóa thông tin thanh toán
2. **Hiệu suất**: Sử dụng Redis cache cho dữ liệu thường xuyên truy cập
3. **Backup**: Lên lịch backup định kỳ
4. **Monitoring**: Theo dõi hiệu suất và log lỗi
5. **Scalability**: Thiết kế để có thể mở rộng theo chiều ngang

