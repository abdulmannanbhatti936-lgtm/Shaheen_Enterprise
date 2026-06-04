-- herbal.sql
-- Shaheen Enterprise - Premium Wellness Database Schema
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
-- 1. Database Creation
-- CREATE DATABASE IF NOT EXISTS herbal_db;
-- USE herbal_db;
-- 2. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 3. Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 4. Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    ingredients TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    stock INT DEFAULT 50,
    -- Added for Stock Management
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 5. Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 6. Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 7. Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT UNSIGNED CHECK (
        rating BETWEEN 1 AND 5
    ),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 8. Coupons Table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_percent INT NOT NULL,
    expiry_date DATE NOT NULL,
    status BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 9. Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 10. Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 11. Performance Optimization Indexes
ALTER TABLE products ADD FULLTEXT INDEX idx_search (name, description);
CREATE INDEX idx_product_category ON products(category);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_user_orders ON orders(user_id);
CREATE INDEX idx_order_items ON order_items(order_id);

-- 12. Wellness Journal (Blog)
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Shaheen Wellness Team',
    status ENUM('Draft', 'Published') DEFAULT 'Published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Sample Data Inserts
...
INSERT INTO site_settings (setting_key, setting_value)
VALUES ('hero_title', 'Nature’s Healing Wisely Crafted'),
    ('hero_subtitle', 'PREMIUM BOTANICALS'),
    (
        'hero_description',
        'Discover a curated collection of organic essentials designed to nurture your skin and spirit.'
    );
INSERT INTO products (
        name,
        description,
        ingredients,
        price,
        image,
        category,
        is_featured
    )
VALUES (
        'Eternal Youth Night Cream',
        'Revitalize your skin overnight with our blend of essential herbs.',
        'Retinol, Jojoba Oil, Aloe Vera, Jasmine Extract.',
        45.00,
        'cream.jpg',
        'Face Care',
        TRUE
    ),
    (
        'Golden Roots Hair Oil',
        'Traditional Ayurvedic hair oil for ultimate strength and shine.',
        'Amla, Coconut Oil, Rosemary Essential Oil.',
        18.00,
        'oil.jpg',
        'Hair Care',
        FALSE
    ),
    (
        'Forest Rain Organic Soap',
        'Handcrafted soap bar with scents of the deep woods.',
        'Charcoal, Olive Oil, Pine Resin, Glycerin.',
        12.00,
        'soap.jpg',
        'Cleansers',
        FALSE
    );
COMMIT;