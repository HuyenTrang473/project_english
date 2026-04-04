# Project English

Tài liệu tổng quan cho dự án học tiếng Anh gồm:

- `BE/`: Backend Laravel (API, xác thực, xử lý dữ liệu)
- `FE/`: Frontend Vue 3 + Vite (giao diện người dùng)

## 1. Yêu cầu môi trường

- PHP `>= 8.2`
- Composer
- Node.js `>= 18`
- MySQL/MariaDB (hoặc cấu hình DB phù hợp trong `.env`)
- XAMPP (khuyến nghị cho môi trường Windows)

## 2. Cài đặt Backend (Laravel)

Mở terminal tại thư mục `BE/`:

```bash
cd BE
composer install
npm install
```

Tạo file môi trường và key ứng dụng:

```bash
copy .env.example .env
php artisan key:generate
```

Cập nhật cấu hình DB trong `.env`, sau đó migrate:

```bash
php artisan migrate
```

Nếu có seed dữ liệu ban đầu:

```bash
php artisan db:seed
```

## 3. Cài đặt Frontend (Vue)

Mở terminal tại thư mục `FE/`:

```bash
cd FE
npm install
```

## 4. Chạy dự án khi phát triển

Bạn cần chạy BE và FE ở 2 terminal riêng.

### Cách A: Chạy BE từng dịch vụ

Tại `BE/`:

```bash
php artisan serve
npm run dev
```

### Cách B: Chạy BE bằng lệnh tổng hợp

Tại `BE/`:

```bash
composer run dev
```

Lệnh này sẽ chạy đồng thời:

- Laravel server
- Queue listener
- Vite

### Chạy FE

Tại `FE/`:

```bash
npm run dev
```

## 5. Build production

### Backend assets

```bash
cd BE
npm run build
```

### Frontend

```bash
cd FE
npm run build
npm run preview
```

## 6. Kiểm thử

Tại `BE/`:

```bash
php artisan test
```

Hoặc:

```bash
composer test
```

## 7. Cấu trúc thư mục chính

```text
Project_English/
|- BE/                  # Laravel backend
|  |- app/
|  |- config/
|  |- database/
|  |- routes/
|  |- tests/
|  \- ...
|- FE/                  # Vue frontend
|  |- src/
|  |- public/
|  \- ...
\- AUDIO_FEATURE_GUIDE.md
```

## 8. Ghi chú

- `BE/README.md`: Tài liệu mặc định của Laravel (chưa tùy biến theo dự án).
- `FE/README.md`: Tài liệu mẫu Vue/Vite (chưa tùy biến theo dự án).
- README này là tài liệu chính để onboarding và chạy toàn bộ hệ thống.
