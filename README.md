# 📘 HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN LARAVEL FULLSTACK

Tài liệu này hướng dẫn **cài đặt, làm sạch dữ liệu và chạy dự án Laravel Fullstack (Blade + Tailwind + Vite)** theo đúng quy trình.

---

## 1️ YÊU CẦU MÔI TRƯỜNG

Trước khi chạy dự án, **bắt buộc cài XAMPP** và các công cụ sau:

* **XAMPP** (Apache + MySQL)
* **PHP >= 8.2** (sử dụng PHP đi kèm XAMPP)
* **Composer**
* **Node.js >= 18** (khuyến nghị)
* **NPM** (đi kèm Node.js)

👉 **Lưu ý quan trọng:**

* Phải bật **MySQL** trong XAMPP trước khi chạy migrate
* Database phải có tên đúng như cấu hình bên dưới

---

## 2️ CÀI ĐẶT BACKEND (LARAVEL)

### 🔹 Bước 1: Cài dependencies PHP

```bash
composer install
```

➡️ Cài toàn bộ package PHP theo `composer.json`

---

### 🔹 Bước 2: Tạo file môi trường (.env)

```bash
cp .env.example .env
```

Sau đó **mở file `.env` và cấu hình database đúng như sau** (BẮT BUỘC):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_management
DB_USERNAME=root
DB_PASSWORD=
```

👉 Database `warehouse_management` **phải được tạo sẵn trong phpMyAdmin (XAMPP)** trước khi chạy migrate.

---

### 🔹 Bước 3: Generate application key

```bash
php artisan key:generate
```

---

## 3️ RESET & LÀM SẠCH HỆ THỐNG (KHI CẦN)

> Dùng khi mới clone project hoặc gặp lỗi cache

```bash
composer dump-autoload

php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 4️ DATABASE & MIGRATION

### 🔹 Cách 1: Reset toàn bộ DB & seed lại (khuyến nghị)

```bash
php artisan migrate:fresh --seed
```

➡️ Xóa toàn bộ bảng → tạo lại → seed dữ liệu mẫu

---

### 🔹 Cách 2: Chạy từng bước (nếu cần debug)

```bash
php artisan session:table
php artisan migrate
php artisan db:seed
```

---

## 5️ CÀI ĐẶT FRONTEND (VITE + TAILWIND)

### 🔹 Bước 1: Cài package frontend

```bash
npm install
```

---

### 🔹 Bước 2: Chạy Vite dev server

```bash
npm run dev
```

➡️ Dùng cho **dev local (hot reload CSS/JS)**

---

## 6️ CHẠY SERVER LARAVEL

Mở **terminal khác** và chạy:

```bash
php artisan serve
```

Mặc định chạy tại:

```
http://127.0.0.1:8000
```

---

## 7️ TÓM TẮT LỆNH CHẠY CHUẨN (DEV)

```bash
composer install
npm install

php artisan migrate:fresh --seed

php artisan serve
npm run dev
```

---

## 8 TÀI KHOẢN MẪU (NẾU CÓ SEED)

| Role     | Username             | Password |
| -----    | --------             | -------- |
| Admin    | admin@gmail.com      | 123456   |
| Staff    | staff@example.com    | 123456   |
| Customer | customer@example.com | 123456   |

*(Có thể chỉnh trong DatabaseSeeder)*

---



## ✅ KẾT LUẬN

> Dự án Laravel Fullstack yêu cầu **PHP server + Vite dev server** để hoạt động đúng.

---

📌 *Tài liệu dùng cho sinh viên / đồ án / nội bộ team*
