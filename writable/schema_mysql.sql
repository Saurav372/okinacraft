-- Okina Craft E-Commerce — MySQL / MariaDB Schema
-- Optional: Only needed if you switch config.php 'driver' from 'sqlite' to 'mysql'.
-- The app also auto-creates these tables and seeds all products on first visit!

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(120) PRIMARY KEY,
    setting_value LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS policies (
    slug VARCHAR(100) PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body_html LONGTEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    handle VARCHAR(200) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    card_shape VARCHAR(30) DEFAULT 'standard',
    desktop_cols INT DEFAULT 4,
    show_on_home TINYINT DEFAULT 0,
    home_order INT DEFAULT 99,
    is_free_delivery TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    handle VARCHAR(220) NOT NULL UNIQUE,
    title VARCHAR(300) NOT NULL,
    body_html LONGTEXT,
    vendor VARCHAR(120) DEFAULT 'Okina Craft',
    product_type VARCHAR(120) DEFAULT '',
    tags VARCHAR(500) DEFAULT '',
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    compare_at_price DECIMAL(10,2) NULL,
    available TINYINT DEFAULT 1,
    allow_custom_upload TINYINT DEFAULT 0,
    free_delivery TINYINT DEFAULT 0,
    image_ratio VARCHAR(40) DEFAULT '100%',
    options_json LONGTEXT,
    status VARCHAR(30) DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    title VARCHAR(255) NOT NULL DEFAULT 'Default Title',
    option1 VARCHAR(120) NULL,
    option2 VARCHAR(120) NULL,
    option3 VARCHAR(120) NULL,
    sku VARCHAR(120) NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    compare_at_price DECIMAL(10,2) NULL,
    inventory_qty INT DEFAULT 50,
    available TINYINT DEFAULT 1,
    position INT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    src VARCHAR(500) NOT NULL,
    alt VARCHAR(300) DEFAULT '',
    width INT DEFAULT 1000,
    height INT DEFAULT 1000,
    position INT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS collection_products (
    collection_id INT NOT NULL,
    product_id INT NOT NULL,
    position INT DEFAULT 1,
    PRIMARY KEY (collection_id, product_id),
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    customer_name VARCHAR(160) NOT NULL,
    customer_email VARCHAR(190) NOT NULL,
    customer_phone VARCHAR(40) NOT NULL,
    shipping_address LONGTEXT NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_state VARCHAR(100) NOT NULL,
    shipping_pincode VARCHAR(20) NOT NULL,
    payment_method VARCHAR(40) NOT NULL DEFAULT 'cod',
    payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    order_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    customer_note LONGTEXT,
    tracking_number VARCHAR(120) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NULL,
    variant_id INT NULL,
    product_title VARCHAR(300) NOT NULL,
    variant_title VARCHAR(255) DEFAULT '',
    product_handle VARCHAR(220) DEFAULT '',
    image_src VARCHAR(500) DEFAULT '',
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    line_total DECIMAL(10,2) NOT NULL,
    custom_design_path VARCHAR(500) DEFAULT '',
    custom_note LONGTEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(40) DEFAULT '',
    subject VARCHAR(200) DEFAULT '',
    message LONGTEXT NOT NULL,
    status VARCHAR(30) DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
