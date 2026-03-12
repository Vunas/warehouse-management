DROP DATABASE IF EXISTS warehouse_management;
CREATE DATABASE warehouse_management;
USE warehouse_management;

SET FOREIGN_KEY_CHECKS=0;

-- =====================================
-- 1. USERS & AUTH
-- =====================================

CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    full_name VARCHAR(150),
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) UNIQUE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_code VARCHAR(100) UNIQUE,
    description VARCHAR(255),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE user_roles (
    user_id BIGINT,
    role_id INT,
    PRIMARY KEY (user_id, role_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE role_permissions (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),

    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- =====================================
-- 2. ADDRESS
-- =====================================

CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT,
    name VARCHAR(150),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE wards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    district_id INT,
    name VARCHAR(150),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (district_id) REFERENCES districts(id)
);

CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    ward_id INT,
    detail VARCHAR(255),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (ward_id) REFERENCES wards(id)
);

-- =====================================
-- 3. PRODUCT
-- =====================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) UNIQUE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) UNIQUE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand_id INT,

    name VARCHAR(150),
    description TEXT,
    price DECIMAL(12,2),

    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(255),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 4. SUPPLIERS
-- =====================================

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    phone VARCHAR(20),
    email VARCHAR(150),
    address VARCHAR(255),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE supplier_products (
    supplier_id INT,
    product_id INT,
    PRIMARY KEY (supplier_id, product_id),

    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- 5. WAREHOUSE
-- =====================================

CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    location VARCHAR(255),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT,
    name VARCHAR(100),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
);

CREATE TABLE shelves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_id INT,
    shelf_code VARCHAR(100),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (zone_id) REFERENCES zones(id)
);

CREATE TABLE inventory (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    product_id INT,
    shelf_id INT,
    quantity INT DEFAULT 0,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    UNIQUE(product_id, shelf_id),

    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (shelf_id) REFERENCES shelves(id)
);

-- =====================================
-- 6. INBOUND (NHẬP KHO)
-- =====================================

CREATE TABLE inbound_orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    staff_id BIGINT,

    status ENUM('pending','completed','cancelled'),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE inbound_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    inbound_id BIGINT,
    product_id INT,

    quantity INT,
    price DECIMAL(12,2),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (inbound_id) REFERENCES inbound_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =====================================
-- 7. SALES ORDER
-- =====================================

CREATE TABLE orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT,
    address_id INT,

    total_price DECIMAL(12,2),

    status ENUM('pending','paid','shipping','completed','cancelled'),

    order_date TIMESTAMP,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (address_id) REFERENCES addresses(id)
);

CREATE TABLE order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    order_id BIGINT,
    product_id INT,

    quantity INT,
    price DECIMAL(12,2),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE payments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    order_id BIGINT UNIQUE,

    payment_method ENUM('cash','vnpay','bank_transfer'),
    amount DECIMAL(12,2),

    status ENUM('pending','paid','failed'),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- =====================================
-- 8. OUTBOUND (XUẤT KHO)
-- =====================================

CREATE TABLE outbound_orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    order_id BIGINT,
    staff_id BIGINT,

    status ENUM('pending','completed','cancelled'),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE outbound_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    outbound_id BIGINT,
    product_id INT,
    quantity INT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (outbound_id) REFERENCES outbound_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =====================================
-- 9. STOCK TRANSFER
-- =====================================

CREATE TABLE stock_transfers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    from_shelf_id INT,
    to_shelf_id INT,

    staff_id BIGINT,

    status ENUM('pending','completed','cancelled'),

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (from_shelf_id) REFERENCES shelves(id),
    FOREIGN KEY (to_shelf_id) REFERENCES shelves(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE transfer_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    transfer_id BIGINT,
    inventory_id BIGINT,

    product_id INT,
    quantity INT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =====================================
-- 10. CART
-- =====================================

CREATE TABLE cart_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT,
    product_id INT,

    quantity INT,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

SET FOREIGN_KEY_CHECKS=1;
