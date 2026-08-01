FROM php:8.2-cli

# 1. Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Cài đặt các extension PHP bắt buộc cho Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy toàn bộ mã nguồn vào trong container
WORKDIR /app
COPY . .

# 5. Cài đặt thư viện PHP (Vendor) và thư viện Node (Node_modules)
RUN composer install --optimize-autoloader --no-dev
RUN npm install

# 6. Build giao diện Vue 3 / Inertia
RUN npm run build

# 7. Expose port cho Render
EXPOSE $PORT

# 8. Cấp quyền và khởi chạy ứng dụng qua start.sh
RUN chmod +x start.sh
CMD ["./start.sh"]