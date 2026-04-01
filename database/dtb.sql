-- ==============================================================================
-- HỆ THỐNG QUẢN LÝ KHO VÀ BÁN HÀNG (WMS & E-COMMERCE)
-- ==============================================================================

DROP DATABASE IF EXISTS warehouse_management;
CREATE DATABASE warehouse_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warehouse_management;

-- Tắt kiểm tra khóa ngoại để tạo bảng dễ dàng
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================
-- 0. USERS (Bảng giả định để các FK hoạt động)
-- =====================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- =====================================
-- 1. ADDRESSES (Địa chỉ)
-- =====================================
CREATE TABLE cities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE districts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
);

CREATE TABLE wards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    district_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE CASCADE
);

CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    ward_id BIGINT UNSIGNED NOT NULL,
    detail VARCHAR(255) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ward_id) REFERENCES wards(id)
);

-- =====================================
-- 2. PRODUCTS (Sản phẩm & Cấu hình)
-- =====================================
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    brand_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX (name),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

CREATE TABLE product_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_code VARCHAR(255) NULL,
    expiry_date DATE NULL,
    manufacture_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    UNIQUE KEY (product_id, batch_code),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 3. SUPPLIERS (Nhà cung cấp)
-- =====================================
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE supplier_products (
    supplier_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (supplier_id, product_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 4. WAREHOUSES (Kho hàng & Tồn kho)
-- =====================================
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    type ENUM('zone','shelf','pallet','bin','rack') NOT NULL,
    is_store BOOLEAN DEFAULT TRUE,
    picking_priority INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES locations(id) ON DELETE SET NULL
);

CREATE TABLE inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    quantity INT DEFAULT 0,
    reserved_quantity INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY (product_id, location_id, batch_id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id)
);

-- =====================================
-- 5. INBOUND (Nhập kho)
-- =====================================
CREATE TABLE inbound_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE inbound_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inbound_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    location_id BIGINT UNSIGNED NULL,
    quantity INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (inbound_id) REFERENCES inbound_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- =====================================
-- 6. ORDERS & PAYMENTS (Đơn hàng Bán ra)
-- =====================================
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    address_id BIGINT UNSIGNED NULL,
    total_price DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'paid', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    customer_note TEXT NULL,
    order_date TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (address_id) REFERENCES addresses(id)
);

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('cash', 'vnpay', 'bank_transfer', 'momo') NOT NULL,
    transaction_id VARCHAR(255) NULL,
    amount DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- =====================================
-- 7. OUTBOUND (Xuất kho)
-- =====================================
CREATE TABLE outbound_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NULL,
    type ENUM('sales', 'internal', 'adjustment', 'return_to_supplier') DEFAULT 'sales',
    reason TEXT NULL,
    status ENUM('pending', 'picking', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (staff_id) REFERENCES users(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

CREATE TABLE outbound_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    outbound_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    location_id BIGINT UNSIGNED NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (outbound_id) REFERENCES outbound_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- =====================================
-- 8. STOCK TRANSFERS (Chuyển kho)
-- =====================================
CREATE TABLE stock_transfers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_warehouse_id BIGINT UNSIGNED NOT NULL,
    to_warehouse_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'in_transit', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE transfer_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transfer_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    from_location_id BIGINT UNSIGNED NULL,
    to_location_id BIGINT UNSIGNED NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (from_location_id) REFERENCES locations(id),
    FOREIGN KEY (to_location_id) REFERENCES locations(id)
);

-- =====================================
-- 9. INVENTORY TRANSACTIONS (Sổ kho / Log)
-- =====================================
CREATE TABLE inventory_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    transaction_type ENUM('inbound', 'outbound', 'transfer', 'adjustment') NOT NULL,
    reference_id BIGINT UNSIGNED NULL,
    quantity_change INT NOT NULL,
    balance_after INT NOT NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    note TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

-- =====================================
-- 10. CART ITEMS (Giỏ hàng)
-- =====================================
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 11. PRODUCT ALERTS (Cảnh báo hàng hóa)
-- =====================================
CREATE TABLE product_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL UNIQUE,
    stock_threshold INT DEFAULT 10,
    expiry_threshold_days INT DEFAULT 90,
    is_active BOOLEAN DEFAULT TRUE,
    last_stock_alert_at TIMESTAMP NULL,
    last_expiry_alert_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 12. STOCK TAKES (Kiểm kê kho)
-- =====================================
CREATE TABLE stock_takes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('draft', 'counting', 'reviewing', 'completed', 'cancelled') DEFAULT 'draft',
    notes TEXT NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE stock_take_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stock_take_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NULL,
    expected_quantity INT NOT NULL,
    counted_quantity INT NULL,
    variance INT NULL,
    reason TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (stock_take_id) REFERENCES stock_takes(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id)
);

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;