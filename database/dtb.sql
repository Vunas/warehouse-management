-- =============================================
-- WAREHOUSE MANAGEMENT SYSTEM (WMS) SCHEMA
-- Version: 2.0 (Storage Block Logic)
-- Compatible: Laravel Monolithic
-- =============================================

CREATE DATABASE IF NOT EXISTS warehouse_management;
USE warehouse_management;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- 1. AUTHENTICATION & RBAC
-- =============================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE, -- Admin, Manager, Staff
    `guard_name` VARCHAR(50) DEFAULT 'web',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(255),
    `guard_name` VARCHAR(50) DEFAULT 'web',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
    `permission_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`permission_id`, `role_id`),
    CONSTRAINT `fk_pr_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pr_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. WAREHOUSE STRUCTURE (CORE)
-- =============================================

DROP TABLE IF EXISTS `warehouse_types`;
CREATE TABLE `warehouse_types` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type_code` VARCHAR(50) NOT NULL UNIQUE, -- TOTAL, SMALL, TRANSIT
    `rentable` BOOLEAN DEFAULT TRUE,
    `single_contract` BOOLEAN DEFAULT FALSE, -- True for Small WH
    `priority_rule` INT DEFAULT 1, -- 0: FIFO, 1: Total-first
    `description` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `total_blocks` INT DEFAULT 0, -- Total racks/zones
    `total_slots` INT DEFAULT 0,
    `status` ENUM('active', 'maintenance', 'locked') DEFAULT 'active',
    `paired_warehouse_id` INT UNSIGNED NULL, -- For TRANSIT pairs
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_wh_type` FOREIGN KEY (`type_id`) REFERENCES `warehouse_types` (`id`),
    CONSTRAINT `fk_wh_pair` FOREIGN KEY (`paired_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `storage_blocks`;
CREATE TABLE `storage_blocks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `warehouse_id` INT UNSIGNED NOT NULL,
    `block_code` VARCHAR(50) NOT NULL, -- A-01, B-02
    `total_slots` INT NOT NULL,
    `status` ENUM('available', 'rented', 'locked') DEFAULT 'available',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `fk_sb_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `size_conversion_rules`;
CREATE TABLE `size_conversion_rules` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rule_name` VARCHAR(100) NOT NULL,
    `max_length` FLOAT NOT NULL,
    `max_width` FLOAT NOT NULL,
    `max_height` FLOAT NOT NULL,
    `slot_cost` INT NOT NULL,
    `priority_level` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. ACTORS (EMPLOYEES & CUSTOMERS)
-- =============================================

DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `position` VARCHAR(100),
    `warehouse_id` INT UNSIGNED NULL, -- Assigned Warehouse
    `hired_at` DATE,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_emp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_emp_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employee_role`;
CREATE TABLE `employee_role` (
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`employee_id`, `role_id`),
    CONSTRAINT `fk_er_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_er_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `company_name` VARCHAR(255) NOT NULL,
    `tax_code` VARCHAR(50),
    `billing_phone` VARCHAR(20),
    `address` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_cus_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. PRODUCTS & CONTRACTS
-- =============================================

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `sku` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NOT NULL,
    `contract_code` VARCHAR(50) NOT NULL UNIQUE,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `penalty_markup` DECIMAL(5, 2) DEFAULT 0.00,
    `status` ENUM('active', 'expired', 'suspended') DEFAULT 'active',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_con_cus` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contract_blocks`;
CREATE TABLE `contract_blocks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT UNSIGNED NOT NULL,
    `block_id` BIGINT UNSIGNED NOT NULL,
    `slots_committed` INT DEFAULT 0,
    `rented_from` DATETIME NOT NULL,
    `rented_to` DATETIME NOT NULL,
    `rental_price` DECIMAL(15, 2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `fk_cb_con` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cb_block` FOREIGN KEY (`block_id`) REFERENCES `storage_blocks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. INBOUND PROCESS
-- =============================================

DROP TABLE IF EXISTS `inbound_tickets`;
CREATE TABLE `inbound_tickets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT UNSIGNED NOT NULL,
    `expected_date` DATETIME NOT NULL,
    `status` ENUM('pending', 'approved', 'received', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_in_con` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `inbound_details`;
CREATE TABLE `inbound_details` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `inbound_id` BIGINT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `input_length` FLOAT NOT NULL,
    `input_width` FLOAT NOT NULL,
    `input_height` FLOAT NOT NULL,
    `quantity` INT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ind_in` FOREIGN KEY (`inbound_id`) REFERENCES `inbound_tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ind_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `calculated_slots`;
CREATE TABLE `calculated_slots` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `inbound_detail_id` BIGINT UNSIGNED NOT NULL,
    `rule_id` INT UNSIGNED NOT NULL,
    `final_length` FLOAT NOT NULL,
    `final_width` FLOAT NOT NULL,
    `final_height` FLOAT NOT NULL,
    `final_slot_cost` INT NOT NULL,
    `is_violation` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_calc_det` FOREIGN KEY (`inbound_detail_id`) REFERENCES `inbound_details` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_calc_rule` FOREIGN KEY (`rule_id`) REFERENCES `size_conversion_rules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. INVENTORY & TRANSACTIONS (LINKED TO BLOCKS)
-- =============================================

DROP TABLE IF EXISTS `inventory_items`;
CREATE TABLE `inventory_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `block_id` BIGINT UNSIGNED NOT NULL, -- Linked to Block, NOT Warehouse directly
    `product_id` INT UNSIGNED NOT NULL,
    `calc_id` BIGINT UNSIGNED NULL,
    `slot_used` INT NOT NULL DEFAULT 0, -- Slots occupied by this item batch
    `imported_at` DATETIME NOT NULL,
    `current_quantity` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_inv_block` FOREIGN KEY (`block_id`) REFERENCES `storage_blocks` (`id`),
    CONSTRAINT `fk_inv_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
    CONSTRAINT `fk_inv_calc` FOREIGN KEY (`calc_id`) REFERENCES `calculated_slots` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `item_id` BIGINT UNSIGNED NOT NULL,
    `transaction_type` ENUM('inbound', 'outbound', 'transfer', 'adjustment') NOT NULL,
    `quantity` INT NOT NULL,
    `reference_id` BIGINT UNSIGNED NULL,
    `reference_type` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_trans_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. OPERATIONS (OUTBOUND & INTERNAL TRANSFER)
-- =============================================

DROP TABLE IF EXISTS `outbound_tickets`;
CREATE TABLE `outbound_tickets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT UNSIGNED NOT NULL,
    `requested_date` DATETIME NOT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `fk_out_con` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `outbound_details`;
CREATE TABLE `outbound_details` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `outbound_id` BIGINT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_outd_main` FOREIGN KEY (`outbound_id`) REFERENCES `outbound_tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_outd_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `internal_transfers`;
CREATE TABLE `internal_transfers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `from_block_id` BIGINT UNSIGNED NOT NULL,
    `to_block_id` BIGINT UNSIGNED NOT NULL,
    `trigger_reason` VARCHAR(255),
    `status` ENUM('pending', 'in_transit', 'completed') DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `fk_tf_from` FOREIGN KEY (`from_block_id`) REFERENCES `storage_blocks` (`id`),
    CONSTRAINT `fk_tf_to` FOREIGN KEY (`to_block_id`) REFERENCES `storage_blocks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `transfer_items`;
CREATE TABLE `transfer_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `transfer_id` BIGINT UNSIGNED NOT NULL,
    `item_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tfi_main` FOREIGN KEY (`transfer_id`) REFERENCES `internal_transfers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tfi_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;