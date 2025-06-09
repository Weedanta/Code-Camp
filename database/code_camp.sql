-- ============================================
-- CAMPUS HUB DATABASE - IMPROVED VERSION
-- ============================================

-- Membuat database
CREATE DATABASE IF NOT EXISTS campus_hub;
USE campus_hub;

-- ============================================
-- TABEL USERS (No Foreign Key as requested)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    alamat_email VARCHAR(100) NOT NULL UNIQUE, -- Unique key
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(20) DEFAULT NULL, -- Default value
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active', -- Default value
    email_verified TINYINT(1) DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Indexes for better performance
    INDEX idx_users_email (alamat_email),
    INDEX idx_users_status (status),
    INDEX idx_users_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL ADMIN (No Foreign Key as requested)
-- ============================================
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, -- Unique key
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin', -- Default value
    status ENUM('active', 'inactive') DEFAULT 'active', -- Default value
    last_login DATETIME DEFAULT NULL, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Indexes
    INDEX idx_admin_email (email),
    INDEX idx_admin_role (role),
    INDEX idx_admin_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL CATEGORIES
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE, -- Unique key
    slug VARCHAR(100) NOT NULL UNIQUE, -- Unique key untuk SEO
    icon VARCHAR(255) DEFAULT NULL, -- Default value
    description TEXT DEFAULT NULL, -- Default value
    status ENUM('active', 'inactive') DEFAULT 'active', -- Default value
    sort_order INT DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Indexes
    INDEX idx_categories_slug (slug),
    INDEX idx_categories_status (status),
    INDEX idx_categories_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL BOOTCAMPS
-- ============================================
CREATE TABLE IF NOT EXISTS bootcamps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE, -- Unique key untuk SEO
    description TEXT DEFAULT NULL, -- Default value
    category_id INT NOT NULL,
    instructor_name VARCHAR(100) NOT NULL,
    instructor_photo VARCHAR(255) DEFAULT NULL, -- Default value
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Default value
    discount_price DECIMAL(10, 2) DEFAULT NULL, -- Default value
    start_date DATE DEFAULT NULL, -- Default value
    duration VARCHAR(50) DEFAULT NULL, -- Default value
    image VARCHAR(255) DEFAULT NULL, -- Default value
    status ENUM('active', 'upcoming', 'closed', 'draft') DEFAULT 'draft', -- Default value
    featured TINYINT(1) DEFAULT 0, -- Default value
    max_participants INT DEFAULT NULL, -- Default value
    current_participants INT DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign key
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_bootcamps_slug (slug),
    INDEX idx_bootcamps_category (category_id),
    INDEX idx_bootcamps_status (status),
    INDEX idx_bootcamps_featured (featured),
    INDEX idx_bootcamps_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL WISHLISTS
-- ============================================
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bootcamp_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Unique constraint untuk mencegah duplikasi
    UNIQUE KEY unique_wishlist (user_id, bootcamp_id), -- Unique key
    
    -- Indexes
    INDEX idx_wishlists_user (user_id),
    INDEX idx_wishlists_bootcamp (bootcamp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL ORDERS
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE, -- Unique key
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Default value
    discount_amount DECIMAL(10, 2) DEFAULT 0.00, -- Default value
    final_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Default value
    payment_status ENUM('pending', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending', -- Default value
    payment_method VARCHAR(50) DEFAULT NULL, -- Default value
    transaction_id VARCHAR(100) DEFAULT NULL, -- Default value
    notes TEXT DEFAULT NULL, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_orders_number (order_number),
    INDEX idx_orders_user (user_id),
    INDEX idx_orders_status (payment_status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL ORDER ITEMS
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    bootcamp_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Default value
    discount_price DECIMAL(10, 2) DEFAULT 0.00, -- Default value
    final_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign keys
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Unique constraint untuk mencegah duplikasi item dalam satu order
    UNIQUE KEY unique_order_item (order_id, bootcamp_id), -- Unique key
    
    -- Indexes
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_bootcamp (bootcamp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL REVIEWS
-- ============================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bootcamp_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT DEFAULT NULL, -- Default value
    status ENUM('published', 'pending', 'rejected') DEFAULT 'published', -- Default value
    helpful_count INT DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign keys
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Unique constraint untuk mencegah review ganda per user per bootcamp
    UNIQUE KEY unique_user_bootcamp_review (user_id, bootcamp_id), -- Unique key
    
    -- Indexes
    INDEX idx_reviews_bootcamp (bootcamp_id),
    INDEX idx_reviews_user (user_id),
    INDEX idx_reviews_rating (rating),
    INDEX idx_reviews_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL CV DATA
-- ============================================
CREATE TABLE IF NOT EXISTS cv_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE, -- Unique key - satu user satu CV
    template_id INT DEFAULT 1, -- Default value
    personal_info JSON DEFAULT NULL, -- Default value
    experience JSON DEFAULT NULL, -- Default value
    education JSON DEFAULT NULL, -- Default value
    skills JSON DEFAULT NULL, -- Default value
    projects JSON DEFAULT NULL, -- Default value
    certifications JSON DEFAULT NULL, -- Default value
    languages JSON DEFAULT NULL, -- Default value
    status ENUM('draft', 'completed', 'archived') DEFAULT 'draft', -- Default value
    is_public TINYINT(1) DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_cv_user (user_id),
    INDEX idx_cv_status (status),
    INDEX idx_cv_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL TODO LISTS
-- ============================================
CREATE TABLE IF NOT EXISTS todo_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL, -- Default value
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending', -- Default value
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium', -- Default value
    due_date DATE DEFAULT NULL, -- Default value
    reminder_date DATETIME DEFAULT NULL, -- Default value
    completed_at DATETIME DEFAULT NULL, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_todos_user (user_id),
    INDEX idx_todos_status (status),
    INDEX idx_todos_priority (priority),
    INDEX idx_todos_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL FORUM POSTS (No Foreign Key as requested - artikel)
-- ============================================
CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE, -- Unique key untuk SEO
    content TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'general', -- Default value
    status ENUM('published', 'draft', 'archived') DEFAULT 'published', -- Default value
    is_pinned TINYINT(1) DEFAULT 0, -- Default value
    is_locked TINYINT(1) DEFAULT 0, -- Default value
    view_count INT DEFAULT 0, -- Default value
    reply_count INT DEFAULT 0, -- Default value
    last_reply_at DATETIME DEFAULT NULL, -- Default value
    last_reply_by INT DEFAULT NULL, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    is_deleted TINYINT(1) DEFAULT 0, -- Default value
    
    -- Note: No foreign key as requested for articles
    -- But we keep user_id for reference
    
    -- Indexes
    INDEX idx_forum_posts_user (user_id),
    INDEX idx_forum_posts_slug (slug),
    INDEX idx_forum_posts_category (category),
    INDEX idx_forum_posts_status (status),
    INDEX idx_forum_posts_created (created_at),
    INDEX idx_forum_posts_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL FORUM REPLIES
-- ============================================
CREATE TABLE IF NOT EXISTS forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL, -- Default value untuk nested replies
    content TEXT NOT NULL,
    status ENUM('published', 'pending', 'rejected') DEFAULT 'published', -- Default value
    is_helpful TINYINT(1) DEFAULT 0, -- Default value
    helpful_count INT DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    is_deleted TINYINT(1) DEFAULT 0, -- Default value
    
    -- Foreign keys
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES forum_replies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_forum_replies_post (post_id),
    INDEX idx_forum_replies_user (user_id),
    INDEX idx_forum_replies_parent (parent_id),
    INDEX idx_forum_replies_status (status),
    INDEX idx_forum_replies_created (created_at),
    INDEX idx_forum_replies_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL ADMIN ACTIVITY LOG
-- ============================================
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT DEFAULT NULL, -- Default value
    ip_address VARCHAR(45) DEFAULT NULL, -- Default value
    user_agent TEXT DEFAULT NULL, -- Default value
    status ENUM('success', 'failed', 'warning') DEFAULT 'success', -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    
    -- Foreign key
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_admin_log_admin (admin_id),
    INDEX idx_admin_log_activity (activity_type),
    INDEX idx_admin_log_status (status),
    INDEX idx_admin_log_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL SETTINGS (Tambahan untuk konfigurasi sistem)
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE, -- Unique key
    setting_value TEXT DEFAULT NULL, -- Default value
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string', -- Default value
    description TEXT DEFAULT NULL, -- Default value
    is_public TINYINT(1) DEFAULT 0, -- Default value
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Default value
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Default value
    
    -- Indexes
    INDEX idx_settings_key (setting_key),
    INDEX idx_settings_type (setting_type),
    INDEX idx_settings_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT COMPREHENSIVE DUMMY DATA
-- ============================================

-- Insert categories (lebih banyak kategori)
INSERT INTO categories (name, slug, icon, description, status, sort_order) VALUES
('Web Development', 'web-development', 'web.png', 'Learn modern web development technologies like React, Vue, Angular', 'active', 1),
('Data Science', 'data-science', 'data-science.png', 'Master data analysis, machine learning, and artificial intelligence', 'active', 2),
('UI/UX Design', 'ui-ux-design', 'uiux.png', 'Create beautiful and user-friendly interfaces and experiences', 'active', 3),
('Mobile Development', 'mobile-development', 'mobile.png', 'Build mobile applications for iOS and Android platforms', 'active', 4),
('DevOps', 'devops', 'devops.png', 'Learn deployment, CI/CD, and infrastructure management', 'active', 5),
('Digital Marketing', 'digital-marketing', 'marketing.png', 'Master online marketing strategies and social media', 'active', 6),
('Cybersecurity', 'cybersecurity', 'security.png', 'Learn ethical hacking and security best practices', 'active', 7),
('Cloud Computing', 'cloud-computing', 'cloud.png', 'Master AWS, Azure, and Google Cloud platforms', 'active', 8),
('Artificial Intelligence', 'artificial-intelligence', 'ai.png', 'Deep learning, neural networks, and AI applications', 'active', 9),
('Business Analysis', 'business-analysis', 'business.png', 'Learn business processes and requirements analysis', 'active', 10);

-- Insert admin accounts
INSERT INTO admin (name, email, password, role, status, last_login) VALUES
('Super Admin', 'superadmin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active', NOW()),
('Admin User', 'admin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('Content Admin', 'content@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Support Admin', 'support@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'inactive', DATE_SUB(NOW(), INTERVAL 1 WEEK));

-- Insert system settings
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'Campus Hub', 'string', 'Name of the website', 1),
('site_description', 'Platform pembelajaran online terbaik untuk mengembangkan skill digital', 'string', 'Site description for SEO', 1),
('site_logo', 'assets/images/logo.png', 'string', 'Site logo path', 1),
('max_file_upload', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 0),
('enable_registration', 'true', 'boolean', 'Allow new user registrations', 0),
('enable_forum', 'true', 'boolean', 'Enable forum functionality', 0),
('enable_cv_builder', 'true', 'boolean', 'Enable CV builder feature', 0),
('default_currency', 'IDR', 'string', 'Default currency for pricing', 1),
('currency_symbol', 'Rp', 'string', 'Currency symbol', 1),
('timezone', 'Asia/Jakarta', 'string', 'Default timezone', 0),
('contact_email', 'hello@campus-hub.com', 'string', 'Contact email address', 1),
('contact_phone', '+62-21-1234-5678', 'string', 'Contact phone number', 1),
('social_facebook', 'https://facebook.com/campushub', 'string', 'Facebook page URL', 1),
('social_instagram', 'https://instagram.com/campushub', 'string', 'Instagram page URL', 1),
('social_twitter', 'https://twitter.com/campushub', 'string', 'Twitter page URL', 1),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode', 0),
('google_analytics', '', 'string', 'Google Analytics tracking ID', 0),
('smtp_host', 'smtp.gmail.com', 'string', 'SMTP server host', 0),
('smtp_port', '587', 'number', 'SMTP server port', 0),
('payment_gateway', 'midtrans', 'string', 'Default payment gateway', 0);

-- Insert users (30 users untuk testing yang lebih realistis)
INSERT INTO users (name, alamat_email, password, no_telepon, status, email_verified, created_at) VALUES
('Ahmad Wijaya', 'ahmad.wijaya@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', 'active', 1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
('Siti Nurhaliza', 'siti.nurhaliza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08987654321', 'active', 1, DATE_SUB(NOW(), INTERVAL 85 DAY)),
('Budi Santoso', 'budi.santoso@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555666777', 'active', 1, DATE_SUB(NOW(), INTERVAL 80 DAY)),
('Dewi Kartika', 'dewi.kartika@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08111222333', 'active', 1, DATE_SUB(NOW(), INTERVAL 75 DAY)),
('Rahman Hakim', 'rahman.hakim@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08444555666', 'active', 1, DATE_SUB(NOW(), INTERVAL 70 DAY)),
('Indira Sari', 'indira.sari@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08777888999', 'active', 1, DATE_SUB(NOW(), INTERVAL 65 DAY)),
('Fajar Pratama', 'fajar.pratama@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08222333444', 'active', 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
('Maya Anggraini', 'maya.anggraini@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08666777888', 'active', 1, DATE_SUB(NOW(), INTERVAL 55 DAY)),
('Andi Permana', 'andi.permana@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08333444555', 'active', 1, DATE_SUB(NOW(), INTERVAL 50 DAY)),
('Rika Novita', 'rika.novita@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08888999000', 'active', 1, DATE_SUB(NOW(), INTERVAL 45 DAY)),
('Dimas Setiawan', 'dimas.setiawan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555666777', 'active', 1, DATE_SUB(NOW(), INTERVAL 40 DAY)),
('Putri Melati', 'putri.melati@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08999000111', 'active', 1, DATE_SUB(NOW(), INTERVAL 35 DAY)),
('Agus Supriadi', 'agus.supriadi@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08444333222', 'active', 1, DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Lestari Wulandari', 'lestari.wulandari@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08777666555', 'active', 1, DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Hendro Gunawan', 'hendro.gunawan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08222111000', 'active', 1, DATE_SUB(NOW(), INTERVAL 20 DAY)),
('Sari Rejeki', 'sari.rejeki@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08666555444', 'active', 1, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('Irfan Maulana', 'irfan.maulana@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08333222111', 'active', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Nur Azizah', 'nur.azizah@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08888777666', 'active', 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Rio Ramadhan', 'rio.ramadhan@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555444333', 'active', 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Fia Kusuma', 'fia.kusuma@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08999888777', 'active', 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),
('Toni Hartono', 'toni.hartono@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08444333222', 'active', 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
('Winda Sari', 'winda.sari@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08777666555', 'active', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
('Bayu Wijaya', 'bayu.wijaya@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08222111000', 'active', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Ratna Dewi', 'ratna.dewi@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08666555444', 'active', 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
('Eko Prasetyo', 'eko.prasetyo@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08333222111', 'active', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Lina Hartati', 'lina.hartati@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08888777666', 'active', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Joko Susilo', 'joko.susilo@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555444333', 'active', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Nina Marlina', 'nina.marlina@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08999888777', 'active', 1, NOW()),
('Teguh Saputra', 'teguh.saputra@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08444333222', 'active', 0, NOW()),
('Sinta Maharani', 'sinta.maharani@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08777666555', 'inactive', 0, NOW());

-- Insert comprehensive bootcamps (50 bootcamps across all categories)
INSERT INTO bootcamps (title, slug, description, category_id, instructor_name, instructor_photo, price, discount_price, start_date, duration, image, status, featured, max_participants, current_participants, created_at) VALUES
-- Web Development (Category 1)
('Full Stack Web Development Bootcamp', 'fullstack-web-development', 'Pelajari JavaScript, React, Node.js, dan MongoDB untuk menjadi Full Stack Developer yang handal', 1, 'Sarah Johnson', 'sarah.jpg', 2500000.00, 1999000.00, '2025-07-15', '16 Minggu', 'fullstack-web.jpg', 'active', 1, 25, 15, DATE_SUB(NOW(), INTERVAL 30 DAY)),
('React.js Advanced Masterclass', 'reactjs-advanced-masterclass', 'Master React.js dengan hooks, context, dan state management modern', 1, 'Michael Chen', 'michael.jpg', 1800000.00, 1440000.00, '2025-07-20', '12 Minggu', 'react-advanced.jpg', 'active', 1, 20, 12, DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Vue.js Complete Guide', 'vuejs-complete-guide', 'Belajar Vue.js dari dasar hingga advanced dengan project nyata', 1, 'David Kim', 'david.jpg', 1500000.00, 1200000.00, '2025-08-01', '10 Minggu', 'vuejs-guide.jpg', 'active', 0, 30, 8, DATE_SUB(NOW(), INTERVAL 20 DAY)),
('Node.js Backend Development', 'nodejs-backend-development', 'Bangun REST API dan microservices dengan Node.js dan Express', 1, 'Alex Thompson', 'alex.jpg', 1600000.00, 1280000.00, '2025-08-10', '8 Minggu', 'nodejs-backend.jpg', 'active', 0, 25, 5, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Laravel PHP Framework', 'laravel-php-framework', 'Master Laravel untuk pengembangan web aplikasi modern', 1, 'Ryan Martinez', 'ryan.jpg', 1400000.00, 1120000.00, '2025-08-15', '10 Minggu', 'laravel-php.jpg', 'active', 0, 20, 3, DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Data Science (Category 2)
('Data Science Complete Bootcamp', 'data-science-complete', 'Python, Pandas, Machine Learning, dan Data Visualization untuk karir Data Scientist', 2, 'Dr. Lisa Wang', 'lisa.jpg', 3000000.00, 2400000.00, '2025-07-25', '20 Minggu', 'data-science.jpg', 'active', 1, 20, 18, DATE_SUB(NOW(), INTERVAL 35 DAY)),
('Machine Learning with Python', 'machine-learning-python', 'Implementasi algoritma ML menggunakan scikit-learn dan TensorFlow', 2, 'Prof. James Anderson', 'james.jpg', 2200000.00, 1760000.00, '2025-08-05', '14 Minggu', 'ml-python.jpg', 'active', 1, 15, 10, DATE_SUB(NOW(), INTERVAL 28 DAY)),
('Data Visualization Mastery', 'data-visualization-mastery', 'Buat visualisasi data yang menarik dengan Matplotlib, Seaborn, dan Plotly', 2, 'Emma Davis', 'emma.jpg', 1200000.00, 960000.00, '2025-08-12', '6 Minggu', 'data-viz.jpg', 'active', 0, 25, 7, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('SQL for Data Analysis', 'sql-data-analysis', 'Master SQL untuk analisis data dan business intelligence', 2, 'Robert Garcia', 'robert.jpg', 900000.00, 720000.00, '2025-08-20', '4 Minggu', 'sql-analysis.jpg', 'active', 0, 30, 12, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Excel Advanced for Data Analysis', 'excel-advanced-data', 'Kuasai Excel untuk analisis data dengan pivot tables, formulas, dan VBA', 2, 'Maria Lopez', 'maria.jpg', 800000.00, 640000.00, '2025-09-01', '6 Minggu', 'excel-data.jpg', 'active', 0, 35, 15, DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- UI/UX Design (Category 3)
('UI/UX Design Complete Course', 'uiux-design-complete', 'Pelajari prinsip design, wireframing, prototyping dengan Figma dan Adobe XD', 3, 'Jennifer Smith', 'jennifer.jpg', 1800000.00, 1440000.00, '2025-07-30', '12 Minggu', 'uiux-complete.jpg', 'active', 1, 20, 16, DATE_SUB(NOW(), INTERVAL 22 DAY)),
('Figma Masterclass for Designers', 'figma-masterclass', 'Master Figma untuk UI design dan prototyping professional', 3, 'Carlos Rodriguez', 'carlos.jpg', 1000000.00, 800000.00, '2025-08-08', '8 Minggu', 'figma-master.jpg', 'active', 0, 25, 11, DATE_SUB(NOW(), INTERVAL 16 DAY)),
('User Experience Research', 'user-experience-research', 'Metodologi UX research dan user testing untuk better design decisions', 3, 'Dr. Sarah Mitchell', 'sarah_m.jpg', 1500000.00, 1200000.00, '2025-08-18', '10 Minggu', 'ux-research.jpg', 'active', 0, 15, 6, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Mobile App Design Workshop', 'mobile-app-design', 'Design mobile applications yang user-friendly dan modern', 3, 'Tom Wilson', 'tom.jpg', 1300000.00, 1040000.00, '2025-09-05', '8 Minggu', 'mobile-design.jpg', 'upcoming', 0, 20, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('Design System & Component Library', 'design-system-components', 'Bangun design system yang konsisten dan scalable', 3, 'Anna Parker', 'anna.jpg', 1400000.00, 1120000.00, '2025-09-10', '6 Minggu', 'design-system.jpg', 'upcoming', 0, 18, 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Mobile Development (Category 4)
('Flutter Mobile Development', 'flutter-mobile-development', 'Bangun aplikasi mobile cross-platform dengan Flutter dan Dart', 4, 'Kevin Lee', 'kevin.jpg', 2000000.00, 1600000.00, '2025-08-01', '14 Minggu', 'flutter-mobile.jpg', 'active', 1, 20, 13, DATE_SUB(NOW(), INTERVAL 20 DAY)),
('React Native Complete Guide', 'react-native-complete', 'Develop mobile apps dengan React Native untuk iOS dan Android', 4, 'Jessica Brown', 'jessica.jpg', 1900000.00, 1520000.00, '2025-08-15', '12 Minggu', 'react-native.jpg', 'active', 0, 18, 8, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('iOS Development with Swift', 'ios-development-swift', 'Native iOS app development menggunakan Swift dan Xcode', 4, 'Mark Johnson', 'mark.jpg', 2200000.00, 1760000.00, '2025-09-01', '16 Minggu', 'ios-swift.jpg', 'upcoming', 0, 15, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),
('Android Development with Kotlin', 'android-kotlin-development', 'Native Android development dengan Kotlin dan Android Studio', 4, 'Diana Chen', 'diana.jpg', 2100000.00, 1680000.00, '2025-09-08', '14 Minggu', 'android-kotlin.jpg', 'upcoming', 0, 17, 0, DATE_SUB(NOW(), INTERVAL 6 DAY)),

-- DevOps (Category 5)
('DevOps Engineer Bootcamp', 'devops-engineer-bootcamp', 'Docker, Kubernetes, CI/CD, dan cloud infrastructure untuk DevOps career', 5, 'Steven Clark', 'steven.jpg', 2800000.00, 2240000.00, '2025-08-10', '18 Minggu', 'devops-bootcamp.jpg', 'active', 1, 15, 9, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('Docker & Kubernetes Mastery', 'docker-kubernetes-mastery', 'Container orchestration dan microservices deployment', 5, 'Rachel Green', 'rachel.jpg', 1800000.00, 1440000.00, '2025-08-20', '10 Minggu', 'docker-k8s.jpg', 'active', 0, 20, 6, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('AWS Cloud Practitioner', 'aws-cloud-practitioner', 'Amazon Web Services fundamentals dan certification prep', 5, 'Chris Taylor', 'chris.jpg', 1500000.00, 1200000.00, '2025-09-01', '8 Minggu', 'aws-cloud.jpg', 'upcoming', 0, 25, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Digital Marketing (Category 6)
('Digital Marketing Complete Strategy', 'digital-marketing-strategy', 'SEO, SEM, Social Media, Content Marketing untuk business growth', 6, 'Michelle Adams', 'michelle.jpg', 1600000.00, 1280000.00, '2025-07-28', '12 Minggu', 'digital-marketing.jpg', 'active', 1, 30, 22, DATE_SUB(NOW(), INTERVAL 24 DAY)),
('Social Media Marketing Mastery', 'social-media-marketing', 'Instagram, Facebook, TikTok, LinkedIn marketing strategies', 6, 'Daniel White', 'daniel.jpg', 1200000.00, 960000.00, '2025-08-12', '8 Minggu', 'social-media.jpg', 'active', 0, 25, 14, DATE_SUB(NOW(), INTERVAL 16 DAY)),
('Google Ads & Facebook Ads', 'google-facebook-ads', 'Paid advertising campaigns yang profitable dan scalable', 6, 'Lisa Thompson', 'lisa_t.jpg', 1400000.00, 1120000.00, '2025-08-25', '6 Minggu', 'paid-ads.jpg', 'active', 0, 20, 7, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Content Marketing & SEO', 'content-marketing-seo', 'Create engaging content dan optimize untuk search engines', 6, 'Andrew Miller', 'andrew.jpg', 1100000.00, 880000.00, '2025-09-02', '8 Minggu', 'content-seo.jpg', 'upcoming', 0, 28, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),

-- Cybersecurity (Category 7)
('Ethical Hacking Complete Course', 'ethical-hacking-complete', 'Penetration testing, vulnerability assessment, dan security analysis', 7, 'Robert Black', 'robert_b.jpg', 2500000.00, 2000000.00, '2025-08-05', '16 Minggu', 'ethical-hacking.jpg', 'active', 1, 15, 11, DATE_SUB(NOW(), INTERVAL 20 DAY)),
('Cybersecurity Fundamentals', 'cybersecurity-fundamentals', 'Network security, encryption, dan security best practices', 7, 'Sandra Davis', 'sandra.jpg', 1700000.00, 1360000.00, '2025-08-18', '10 Minggu', 'cybersecurity.jpg', 'active', 0, 18, 8, DATE_SUB(NOW(), INTERVAL 14 DAY)),
('Security Analysis & Incident Response', 'security-analysis-response', 'Threat detection, malware analysis, dan incident handling', 7, 'Michael Foster', 'michael_f.jpg', 2000000.00, 1600000.00, '2025-09-05', '12 Minggu', 'security-analysis.jpg', 'upcoming', 0, 12, 0, DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Cloud Computing (Category 8)
('Multi-Cloud Architecture', 'multi-cloud-architecture', 'AWS, Azure, Google Cloud integration dan best practices', 8, 'Patricia Wilson', 'patricia.jpg', 2400000.00, 1920000.00, '2025-08-08', '14 Minggu', 'multi-cloud.jpg', 'active', 1, 16, 10, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('Microsoft Azure Fundamentals', 'azure-fundamentals', 'Azure services, deployment, dan management untuk enterprise', 8, 'James Rodriguez', 'james_r.jpg', 1600000.00, 1280000.00, '2025-08-22', '10 Minggu', 'azure-fund.jpg', 'active', 0, 20, 6, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('Google Cloud Platform Mastery', 'gcp-mastery', 'GCP services, BigQuery, Cloud Functions, dan machine learning', 8, 'Catherine Lee', 'catherine.jpg', 1800000.00, 1440000.00, '2025-09-10', '12 Minggu', 'gcp-mastery.jpg', 'upcoming', 0, 18, 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Artificial Intelligence (Category 9)
('AI & Machine Learning Engineer', 'ai-ml-engineer', 'Deep learning, neural networks, dan AI applications development', 9, 'Dr. Alan Turing Jr', 'alan.jpg', 3500000.00, 2800000.00, '2025-08-12', '22 Minggu', 'ai-ml-engineer.jpg', 'active', 1, 12, 8, DATE_SUB(NOW(), INTERVAL 16 DAY)),
('Computer Vision with OpenCV', 'computer-vision-opencv', 'Image processing, object detection, dan facial recognition', 9, 'Dr. Emily Watson', 'emily.jpg', 2200000.00, 1760000.00, '2025-08-28', '14 Minggu', 'computer-vision.jpg', 'active', 0, 15, 4, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Natural Language Processing', 'natural-language-processing', 'Text analysis, sentiment analysis, dan chatbot development', 9, 'Dr. Richard Stone', 'richard.jpg', 2000000.00, 1600000.00, '2025-09-15', '12 Minggu', 'nlp-course.jpg', 'upcoming', 0, 14, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),

-- Business Analysis (Category 10)
('Business Analyst Complete Guide', 'business-analyst-guide', 'Requirements gathering, process mapping, dan stakeholder management', 10, 'Margaret Johnson', 'margaret.jpg', 1800000.00, 1440000.00, '2025-08-15', '12 Minggu', 'business-analyst.jpg', 'active', 1, 22, 15, DATE_SUB(NOW(), INTERVAL 14 DAY)),
('Project Management Professional', 'project-management-pro', 'PMP certification prep, agile methodology, dan team leadership', 10, 'David Anderson', 'david_a.jpg', 2000000.00, 1600000.00, '2025-08-30', '10 Minggu', 'project-mgmt.jpg', 'active', 0, 18, 9, DATE_SUB(NOW(), INTERVAL 8 DAY)),
('Digital Transformation Strategy', 'digital-transformation', 'Change management, technology adoption, dan business innovation', 10, 'Dr. Susan Miller', 'susan.jpg', 2200000.00, 1760000.00, '2025-09-12', '8 Minggu', 'digital-transform.jpg', 'upcoming', 0, 16, 0, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Insert Orders dengan order_number yang unik
INSERT INTO orders (order_number, user_id, total_amount, discount_amount, final_amount, payment_status, payment_method, transaction_id, created_at) VALUES
('ORD-2025-000001', 1, 2500000.00, 501000.00, 1999000.00, 'completed', 'credit_card', 'TXN-001-2025', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('ORD-2025-000002', 2, 1800000.00, 360000.00, 1440000.00, 'completed', 'bank_transfer', 'TXN-002-2025', DATE_SUB(NOW(), INTERVAL 28 DAY)),
('ORD-2025-000003', 3, 3000000.00, 600000.00, 2400000.00, 'completed', 'credit_card', 'TXN-003-2025', DATE_SUB(NOW(), INTERVAL 25 DAY)),
('ORD-2025-000004', 4, 1800000.00, 360000.00, 1440000.00, 'completed', 'e_wallet', 'TXN-004-2025', DATE_SUB(NOW(), INTERVAL 23 DAY)),
('ORD-2025-000005', 5, 1600000.00, 320000.00, 1280000.00, 'completed', 'credit_card', 'TXN-005-2025', DATE_SUB(NOW(), INTERVAL 22 DAY)),
('ORD-2025-000006', 6, 2200000.00, 440000.00, 1760000.00, 'completed', 'bank_transfer', 'TXN-006-2025', DATE_SUB(NOW(), INTERVAL 20 DAY)),
('ORD-2025-000007', 7, 1000000.00, 200000.00, 800000.00, 'completed', 'e_wallet', 'TXN-007-2025', DATE_SUB(NOW(), INTERVAL 18 DAY)),
('ORD-2025-000008', 8, 2800000.00, 560000.00, 2240000.00, 'completed', 'credit_card', 'TXN-008-2025', DATE_SUB(NOW(), INTERVAL 16 DAY)),
('ORD-2025-000009', 9, 1200000.00, 240000.00, 960000.00, 'completed', 'bank_transfer', 'TXN-009-2025', DATE_SUB(NOW(), INTERVAL 15 DAY)),
('ORD-2025-000010', 10, 1600000.00, 320000.00, 1280000.00, 'completed', 'credit_card', 'TXN-010-2025', DATE_SUB(NOW(), INTERVAL 14 DAY)),
('ORD-2025-000011', 11, 1800000.00, 360000.00, 1440000.00, 'completed', 'e_wallet', 'TXN-011-2025', DATE_SUB(NOW(), INTERVAL 12 DAY)),
('ORD-2025-000012', 12, 1900000.00, 380000.00, 1520000.00, 'completed', 'credit_card', 'TXN-012-2025', DATE_SUB(NOW(), INTERVAL 10 DAY)),
('ORD-2025-000013', 13, 2500000.00, 500000.00, 2000000.00, 'completed', 'bank_transfer', 'TXN-013-2025', DATE_SUB(NOW(), INTERVAL 8 DAY)),
('ORD-2025-000014', 14, 1400000.00, 280000.00, 1120000.00, 'completed', 'e_wallet', 'TXN-014-2025', DATE_SUB(NOW(), INTERVAL 6 DAY)),
('ORD-2025-000015', 15, 2400000.00, 480000.00, 1920000.00, 'completed', 'credit_card', 'TXN-015-2025', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('ORD-2025-000016', 16, 1800000.00, 360000.00, 1440000.00, 'pending', 'bank_transfer', 'TXN-016-2025', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('ORD-2025-000017', 17, 2000000.00, 400000.00, 1600000.00, 'pending', 'credit_card', 'TXN-017-2025', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('ORD-2025-000018', 18, 1500000.00, 300000.00, 1200000.00, 'failed', 'e_wallet', 'TXN-018-2025', NOW());

-- Insert Order Items
INSERT INTO order_items (order_id, bootcamp_id, price, discount_price, final_price) VALUES
(1, 1, 2500000.00, 501000.00, 1999000.00),
(2, 2, 1800000.00, 360000.00, 1440000.00),
(3, 6, 3000000.00, 600000.00, 2400000.00),
(4, 11, 1800000.00, 360000.00, 1440000.00),
(5, 24, 1600000.00, 320000.00, 1280000.00),
(6, 7, 2200000.00, 440000.00, 1760000.00),
(7, 12, 1000000.00, 200000.00, 800000.00),
(8, 19, 2800000.00, 560000.00, 2240000.00),
(9, 25, 1200000.00, 240000.00, 960000.00),
(10, 24, 1600000.00, 320000.00, 1280000.00),
(11, 20, 1800000.00, 360000.00, 1440000.00),
(12, 14, 1900000.00, 380000.00, 1520000.00),
(13, 28, 2500000.00, 500000.00, 2000000.00),
(14, 27, 1400000.00, 280000.00, 1120000.00),
(15, 30, 2400000.00, 480000.00, 1920000.00),
(16, 31, 1800000.00, 360000.00, 1440000.00),
(17, 34, 2000000.00, 400000.00, 1600000.00),
(18, 17, 1500000.00, 300000.00, 1200000.00);

-- Insert Wishlists
INSERT INTO wishlists (user_id, bootcamp_id, created_at) VALUES
(1, 3, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 8, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 15, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 22, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(3, 9, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(4, 13, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(5, 16, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(6, 18, DATE_SUB(NOW(), INTERVAL 9 DAY)),
(7, 21, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(8, 26, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(9, 29, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(10, 32, NOW()),
(11, 35, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(12, 4, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(13, 10, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(14, 23, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(15, 33, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Insert Reviews
INSERT INTO reviews (bootcamp_id, user_id, rating, review_text, status, created_at) VALUES
(1, 1, 5, 'Bootcamp yang sangat comprehensive! Instructor sangat berpengalaman dan materi up-to-date. Project akhirnya sangat membantu untuk portfolio.', 'published', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(1, 2, 4, 'Materi bagus tapi pace-nya agak cepat. Overall worth it untuk yang serius belajar web development.', 'published', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(2, 3, 5, 'React masterclass yang benar-benar mendalam. Setelah ikut ini jadi lebih percaya diri develop dengan React.', 'published', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(6, 4, 5, 'Data science bootcamp terbaik yang pernah saya ikuti. Dari teori sampai praktek semuanya lengkap.', 'published', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(7, 5, 4, 'Machine learning dengan Python dijelaskan dengan sangat baik. Tugas-tugasnya challenging tapi bermanfaat.', 'published', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(11, 6, 5, 'UI/UX course yang sangat praktis. Belajar langsung dengan case study real project.', 'published', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(12, 7, 4, 'Figma masterclass yang worth it. Jadi lebih mahir menggunakan Figma untuk prototyping.', 'published', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(19, 8, 5, 'DevOps bootcamp yang sangat comprehensive. Docker dan Kubernetes explained dengan excellent.', 'published', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(24, 9, 4, 'Digital marketing strategy yang up-to-date dengan tren terkini. Instructor berpengalaman di industri.', 'published', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(25, 10, 5, 'Social media marketing course yang actionable. Langsung bisa diapply untuk business.', 'published', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(28, 13, 5, 'Ethical hacking course yang sangat detail. Security mindset jadi lebih terasah.', 'published', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 14, 3, 'Materi bagus tapi butuh background programming yang kuat. Agak overwhelming untuk beginner.', 'published', NOW()),
(6, 15, 5, 'Perfect introduction to data science. Step by step explanation yang mudah diikuti.', 'published', NOW()),
(1, 16, 4, 'Full stack bootcamp yang intensive. Banyak materi yang dipelajari dalam waktu singkat.', 'published', NOW()),
(7, 17, 5, 'ML with Python course yang excellent. Hands-on projects sangat membantu pemahaman.', 'published', NOW());

-- Insert comprehensive Forum Posts
INSERT INTO forum_posts (user_id, title, slug, content, category, status, is_pinned, view_count, created_at) VALUES
(1, 'Tips Belajar Programming untuk Pemula', 'tips-belajar-programming-pemula', 'Halo teman-teman! Saya ingin share beberapa tips belajar programming untuk yang baru mulai:\n\n1. Pilih satu bahasa dulu dan fokus\n2. Praktek coding setiap hari minimal 1 jam\n3. Jangan takut error, itu bagian dari learning process\n4. Join community dan aktif bertanya\n5. Build project kecil-kecil untuk portfolio\n\nApa tips kalian yang lain?', 'programming', 'published', 1, 156, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(2, 'Career Path Data Scientist vs Data Analyst', 'career-path-data-scientist-analyst', 'Guys, saya bingung mau pilih career path sebagai Data Scientist atau Data Analyst. Bisa share pengalaman dan perbedaannya gak?\n\nSetahu saya:\n- Data Analyst lebih fokus ke reporting dan insight\n- Data Scientist lebih ke machine learning dan predictive modeling\n\nTapi di real world gimana ya? Thanks!', 'career', 'published', 0, 89, DATE_SUB(NOW(), INTERVAL 18 DAY)),
(3, 'Review Bootcamp Full Stack Web Development', 'review-bootcamp-fullstack-web', 'Barusan selesai ikut bootcamp full stack web development di Campus Hub. Overall experience sangat positif!\n\n**Pros:**\n- Instructor sangat knowledgeable\n- Materi up-to-date dengan industry standard\n- Project-based learning\n- Career support setelah bootcamp\n\n**Cons:**\n- Pace agak cepat, butuh dedikasi tinggi\n- Assignment cukup challenging\n\nOverall rating: 4.5/5. Recommended banget untuk yang serius career switch ke tech!', 'review', 'published', 0, 234, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(4, 'UI/UX Design: Figma vs Adobe XD', 'uiux-design-figma-vs-adobe-xd', 'Designer UI/UX di sini, kalian lebih prefer Figma atau Adobe XD? Apa alasannya?\n\nPersonally saya lebih suka Figma karena:\n- Collaboration feature yang excellent\n- Web-based, gak perlu install software\n- Community resources yang banyak\n- Auto-layout yang powerful\n\nTapi Adobe XD juga punya kelebihan di integration dengan Creative Suite.\n\nShare opinion kalian dong!', 'design', 'published', 0, 67, DATE_SUB(NOW(), INTERVAL 12 DAY)),
(5, 'Belajar React: Functional vs Class Components', 'belajar-react-functional-vs-class', 'Newbie React here! Masih bingung kapan harus pakai functional components dan kapan pakai class components.\n\nSetelah baca-baca, katanya functional components dengan hooks adalah future of React. Tapi masih banyak legacy code yang pakai class components.\n\nBuat yang experienced, gimana best practice-nya? Fokus belajar functional + hooks aja atau perlu tahu keduanya?', 'programming', 'published', 0, 145, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(6, 'Machine Learning untuk Beginner: Mulai dari Mana?', 'machine-learning-beginner-mulai-mana', 'Halo ML enthusiasts! Saya background non-tech tapi tertarik banget sama machine learning. Kira-kira roadmap belajarnya gimana ya?\n\nYang sudah saya prepare:\n- Basic Python programming\n- Basic statistics dan matematika\n\nNext step apa yang harus saya ambil? Library apa yang harus dipelajari pertama? Thanks untuk guidance-nya!', 'data-science', 'published', 0, 198, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(7, 'Docker vs Virtual Machines: Kapan Pakai Yang Mana?', 'docker-vs-virtual-machines', 'DevOps beginners here! Masih confused tentang kapan harus pakai Docker dan kapan pakai Virtual Machines.\n\nSejauh ini yang saya tahu:\n- Docker lebih lightweight\n- VM provides better isolation\n- Docker better for microservices\n- VM better for running different OS\n\nTapi di real-world scenarios gimana ya? Share experience kalian dong!', 'devops', 'published', 0, 112, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(8, 'Cybersecurity: Sertifikasi Apa Yang Worth It?', 'cybersecurity-sertifikasi-worth-it', 'Para cybersecurity professionals, sertifikasi apa yang paling worth it untuk career advancement?\n\nSaya lihat banyak pilihan:\n- CISSP\n- CEH (Certified Ethical Hacker)\n- CISM\n- Security+\n- CISCP\n\nUntuk yang baru mulai di cybersecurity field, mana yang sebaiknya diambil pertama? Budget dan time investment juga jadi consideration.', 'cybersecurity', 'published', 0, 87, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(9, 'Mobile Development: Native vs Cross-Platform', 'mobile-development-native-vs-crossplatform', 'Mobile developers, mau tanya nih. Untuk startup yang budget terbatas, lebih baik develop native (iOS + Android separately) atau pakai cross-platform framework?\n\nCross-platform options:\n- React Native\n- Flutter\n- Xamarin\n\nNative obviously gives better performance dan access ke platform-specific features, tapi development time dan cost jadi 2x lipat.\n\nPengalaman kalian gimana?', 'mobile', 'published', 0, 156, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(10, 'Digital Marketing di Era AI: Gimana Adaptasinya?', 'digital-marketing-era-ai-adaptasi', 'Digital marketers, dengan makin banyaknya AI tools like ChatGPT, automation platforms, dll. Gimana cara kita adapt dan stay relevant?\n\nBeberapa areas yang saya lihat terimpact:\n- Content creation (AI bisa nulis copy)\n- Ad optimization (algorithm makin smart)\n- Customer service (chatbots everywhere)\n- Data analysis (AI bisa process big data)\n\nSkill apa yang harus kita develop untuk tetap competitive?', 'marketing', 'published', 0, 201, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 'AWS vs Azure vs Google Cloud: Perbandingan Cost', 'aws-azure-gcp-perbandingan-cost', 'Cloud engineers, ada yang punya experience pakai ketiga cloud providers ini? Dari segi cost gimana perbandingannya?\n\nSpecifically untuk:\n- Compute instances (VM)\n- Storage\n- Database services\n- Networking\n- ML/AI services\n\nDan biasanya pricing model mana yang paling predictable untuk budgeting?', 'cloud', 'published', 0, 78, NOW()),
(12, 'Freelance vs Full-time: Programmer Edition', 'freelance-vs-fulltime-programmer', 'Programmers yang sudah experienced, gimana perbandingan freelance vs full-time employment?\n\n**Freelance pros:**\n- Higher hourly rate\n- Flexibility\n- Diverse projects\n- Location independence\n\n**Full-time pros:**\n- Stable income\n- Benefits (health insurance, etc)\n- Team collaboration\n- Career progression path\n\nYang mana yang lebih sustainable long-term? Terutama untuk yang sudah berkeluarga.', 'career', 'published', 0, 167, NOW());

-- Insert Forum Replies
INSERT INTO forum_replies (post_id, user_id, content, status, created_at) VALUES
(1, 5, 'Great tips! Saya tambahkan: jangan skip fundamentals. Algoritma dan data structures itu penting banget untuk foundation yang kuat.', 'published', DATE_SUB(NOW(), INTERVAL 19 DAY)),
(1, 8, 'Setuju sama poin praktek setiap hari! Consistency is key. Saya dulu commit 100 days of code challenge dan hasilnya amazing.', 'published', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(1, 12, 'Tip tambahan: build project yang solve real problem, jangan cuma tutorial projects. Portfolio akan lebih impressive.', 'published', DATE_SUB(NOW(), INTERVAL 17 DAY)),
(2, 6, 'Data Scientist salary ceiling lebih tinggi, tapi competition juga fierce. Data Analyst path lebih straightforward dan job market lebih stable.', 'published', DATE_SUB(NOW(), INTERVAL 17 DAY)),
(2, 9, 'Saya career switch dari analyst ke scientist. Key difference: analyst lebih descriptive (what happened), scientist lebih predictive (what will happen).', 'published', DATE_SUB(NOW(), INTERVAL 16 DAY)),
(3, 7, 'Thanks for the review! Saya lagi consider bootcamp ini juga. Untuk background non-tech, kira-kira preparation apa yang dibutuhkan?', 'published', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(3, 11, 'Saya alumni batch sebelumnya. Career support mereka emang bagus, ada job placement assistance dan interview prep.', 'published', DATE_SUB(NOW(), INTERVAL 13 DAY)),
(4, 2, 'Figma FTW! Web-based collaboration adalah game changer. Team remote jadi lebih mudah sync design.', 'published', DATE_SUB(NOW(), INTERVAL 11 DAY)),
(4, 10, 'Adobe XD masih strong di integration sama Photoshop/Illustrator. Kalau workflow kalian heavy di Creative Suite, XD masih worth it.', 'published', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(5, 1, 'Fokus ke functional components + hooks aja. Class components slowly being phased out. Functional lebih readable dan performant.', 'published', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(5, 14, 'Tapi tetap perlu understand class components for legacy code maintenance. Di real world masih banyak yang pakai class.', 'published', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(6, 13, 'Start dengan scikit-learn untuk basic ML algorithms. Pandas untuk data manipulation. Matplotlib/Seaborn untuk visualization.', 'published', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(6, 4, 'Jangan skip statistics! Machine learning tanpa statistical understanding seperti driving tanpa tahu traffic rules.', 'published', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(7, 15, 'Docker untuk containerization same application across different environments. VM untuk running completely different OS or legacy systems.', 'published', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 3, 'In microservices architecture, Docker is the way to go. VM too heavy untuk per-service deployment.', 'published', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(8, 16, 'CompTIA Security+ good entry level. CEH lebih advanced tapi industry recognition tinggi. CISSP untuk management level.', 'published', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(8, 1, 'Start dengan Security+ dulu. Foundation yang solid sebelum ke specialized certifications.', 'published', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(9, 18, 'Flutter performance mendekati native dan single codebase. React Native good ecosystem tapi performance gap masih ada.', 'published', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(9, 7, 'Untuk MVP dan quick market validation, cross-platform adalah no-brainer. Optimize later setelah product-market fit.', 'published', NOW()),
(10, 12, 'AI is tool, not replacement. Focus on strategic thinking, creativity, dan human insight yang gak bisa di-automate AI.', 'published', NOW()),
(11, 5, 'AWS generally more expensive tapi feature set paling comprehensive. GCP cheapest untuk compute-heavy workloads. Azure balanced.', 'published', NOW()),
(12, 20, 'Freelance income volatile tapi ceiling tinggi. Full-time lebih predictable. Hybrid approach: part-time + freelance projects.', 'published', NOW());

-- Insert Todo Lists
INSERT INTO todo_lists (user_id, title, description, status, priority, due_date, completed_at, created_at) VALUES
(1, 'Selesaikan Final Project Bootcamp', 'Complete full-stack web application untuk portfolio', 'in_progress', 'high', '2025-07-30', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'Apply Job sebagai Frontend Developer', 'Submit aplikasi ke 10 companies dalam seminggu', 'pending', 'high', '2025-07-15', NULL, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'Update LinkedIn Profile', 'Add recent bootcamp experience dan skills', 'completed', 'medium', '2025-06-20', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY)),
(2, 'Learn Advanced React Hooks', 'Deep dive into useContext, useReducer, custom hooks', 'in_progress', 'medium', '2025-07-25', NULL, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 'Build Personal Portfolio Website', 'Create responsive portfolio using React dan Tailwind CSS', 'pending', 'high', '2025-08-01', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'Complete Machine Learning Course', 'Finish all modules dan assignments', 'in_progress', 'high', '2025-07-28', NULL, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 'Practice SQL Queries Daily', 'Solve 5 SQL problems per day untuk interview prep', 'pending', 'medium', '2025-07-20', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'Design System Documentation', 'Create comprehensive design system untuk team', 'in_progress', 'high', '2025-07-22', NULL, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(4, 'User Research for Mobile App', 'Conduct user interviews dan usability testing', 'pending', 'medium', '2025-07-18', NULL, NOW()),
(5, 'Deploy Application to AWS', 'Setup CI/CD pipeline dan production environment', 'pending', 'high', '2025-07-16', NULL, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 'Learn Kubernetes Orchestration', 'Complete Kubernetes course dan hands-on labs', 'in_progress', 'medium', '2025-08-05', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(6, 'Prepare for Technical Interview', 'Practice coding challenges dan system design', 'pending', 'urgent', '2025-07-12', NULL, NOW()),
(7, 'Create Social Media Strategy', 'Plan content calendar untuk Q3 2025', 'completed', 'medium', '2025-06-30', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
(8, 'Security Audit for Web Application', 'Perform penetration testing dan vulnerability assessment', 'in_progress', 'high', '2025-07-20', NULL, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(9, 'Mobile App Performance Optimization', 'Improve app loading time dan reduce memory usage', 'pending', 'medium', '2025-07-25', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(10, 'Data Pipeline Architecture Design', 'Design scalable ETL pipeline untuk big data processing', 'in_progress', 'high', '2025-07-30', NULL, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(11, 'UX Research Report Completion', 'Analyze user feedback dan create actionable insights', 'pending', 'medium', '2025-07-18', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(12, 'API Documentation Update', 'Update REST API documentation dengan latest endpoints', 'completed', 'low', '2025-06-25', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));

-- Insert CV Data (sample data untuk beberapa users)
INSERT INTO cv_data (user_id, template_id, personal_info, experience, education, skills, projects, certifications, status, is_public, created_at) VALUES
(1, 1, 
'{"full_name":"Ahmad Wijaya","email":"ahmad.wijaya@email.com","phone":"08123456789","address":"Jakarta, Indonesia","summary":"Passionate Full Stack Developer dengan 2+ tahun experience di web development. Expertise dalam React, Node.js, dan modern web technologies."}',
'[{"title":"Frontend Developer","company":"PT Tech Startup","location":"Jakarta","start_date":"2023-01","end_date":"2025-06","description":"Develop responsive web applications menggunakan React, TypeScript, dan Tailwind CSS. Collaborate dengan design team untuk implement pixel-perfect UI."},{"title":"Junior Web Developer","company":"CV Digital Agency","location":"Jakarta","start_date":"2022-06","end_date":"2022-12","description":"Maintain dan develop company websites menggunakan HTML, CSS, JavaScript, dan PHP. Handle client requests dan bug fixes."}]',
'[{"degree":"S1 Teknik Informatika","institution":"Universitas Indonesia","location":"Depok","start_date":"2018-08","end_date":"2022-07","gpa":"3.65"},{"degree":"Full Stack Web Development Bootcamp","institution":"Campus Hub","location":"Online","start_date":"2025-01","end_date":"2025-06","description":"Intensive bootcamp covering React, Node.js, MongoDB, dan modern web development practices."}]',
'{"technical":["JavaScript","TypeScript","React","Node.js","Express.js","MongoDB","PostgreSQL","Git","Docker","AWS"],"soft":["Problem Solving","Team Collaboration","Communication","Project Management","Adaptability"]}',
'[{"name":"E-Commerce Platform","description":"Full-stack e-commerce application dengan React frontend dan Node.js backend. Features: user authentication, payment integration, admin dashboard.","technologies":["React","Node.js","MongoDB","Stripe API"],"url":"https://github.com/ahmad/ecommerce","demo":"https://ecommerce-demo.com"},{"name":"Task Management App","description":"Collaborative task management application dengan real-time updates menggunakan Socket.io.","technologies":["React","Socket.io","Express.js","PostgreSQL"],"url":"https://github.com/ahmad/taskmanager"}]',
'[{"name":"AWS Cloud Practitioner","issuer":"Amazon Web Services","date":"2024-03","credential_id":"AWS-12345"},{"name":"JavaScript Algorithms and Data Structures","issuer":"freeCodeCamp","date":"2023-11","credential_id":"FCC-67890"}]',
'completed', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),

(2, 1,
'{"full_name":"Siti Nurhaliza","email":"siti.nurhaliza@email.com","phone":"08987654321","address":"Bandung, Indonesia","summary":"UI/UX Designer dengan passion untuk creating user-centered design solutions. Experience dalam design thinking, user research, dan prototyping."}',
'[{"title":"UI/UX Designer","company":"Design Studio ABC","location":"Bandung","start_date":"2024-01","end_date":"present","description":"Lead design untuk mobile applications dan web platforms. Conduct user research, create wireframes, prototypes, dan design systems."},{"title":"Junior Graphic Designer","company":"Creative Agency XYZ","location":"Bandung","start_date":"2023-03","end_date":"2023-12","description":"Create visual designs untuk marketing materials, social media content, dan brand identity projects."}]',
'[{"degree":"S1 Desain Komunikasi Visual","institution":"Institut Teknologi Bandung","location":"Bandung","start_date":"2019-08","end_date":"2023-07","gpa":"3.78"},{"degree":"UI/UX Design Bootcamp","institution":"Campus Hub","location":"Online","start_date":"2024-10","end_date":"2025-02","description":"Comprehensive UI/UX design training covering user research, wireframing, prototyping, dan usability testing."}]',
'{"technical":["Figma","Adobe XD","Sketch","Adobe Photoshop","Adobe Illustrator","Principle","InVision","HTML/CSS","JavaScript (Basic)"],"soft":["User Empathy","Creative Thinking","Attention to Detail","Communication","Presentation Skills"]}',
'[{"name":"Mobile Banking App Redesign","description":"Complete redesign untuk mobile banking application focusing on improved user experience dan accessibility.","technologies":["Figma","User Research","Prototyping"],"url":"https://figma.com/banking-redesign"},{"name":"E-Learning Platform UI","description":"Design system dan interface untuk online learning platform dengan focus pada student engagement.","technologies":["Adobe XD","Design System","Usability Testing"],"url":"https://behance.net/elearning-ui"}]',
'[{"name":"Google UX Design Certificate","issuer":"Google","date":"2024-08","credential_id":"GOOGLE-UX-123"},{"name":"Adobe Certified Expert - Photoshop","issuer":"Adobe","date":"2023-06","credential_id":"ADOBE-PS-456"}]',
'completed', 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),

(3, 1,
'{"full_name":"Budi Santoso","email":"budi.santoso@email.com","phone":"08555666777","address":"Surabaya, Indonesia","summary":"Data Scientist dengan strong background di statistics dan machine learning. Experience dalam data analysis, predictive modeling, dan business intelligence."}',
'[{"title":"Data Scientist","company":"Fintech Company DEF","location":"Surabaya","start_date":"2024-06","end_date":"present","description":"Develop machine learning models untuk fraud detection dan risk assessment. Analyze customer behavior data untuk business insights."},{"title":"Data Analyst","company":"Retail Corporation GHI","location":"Surabaya","start_date":"2023-08","end_date":"2024-05","description":"Create automated reports dan dashboards menggunakan Python dan SQL. Perform statistical analysis untuk sales forecasting."}]',
'[{"degree":"S1 Statistika","institution":"Institut Teknologi Sepuluh Nopember","location":"Surabaya","start_date":"2019-08","end_date":"2023-07","gpa":"3.82"},{"degree":"Data Science Bootcamp","institution":"Campus Hub","location":"Online","start_date":"2024-01","end_date":"2024-05","description":"Intensive data science training covering Python, machine learning, deep learning, dan data visualization."}]',
'{"technical":["Python","R","SQL","Pandas","NumPy","Scikit-learn","TensorFlow","Tableau","Power BI","Jupyter Notebook"],"soft":["Analytical Thinking","Problem Solving","Statistical Reasoning","Communication","Business Acumen"]}',
'[{"name":"Customer Churn Prediction Model","description":"Machine learning model untuk prediksi customer churn dengan accuracy 87% menggunakan ensemble methods.","technologies":["Python","Scikit-learn","Pandas","XGBoost"],"url":"https://github.com/budi/churn-prediction"},{"name":"Sales Forecasting Dashboard","description":"Interactive dashboard untuk sales forecasting dengan time series analysis dan trend visualization.","technologies":["Python","Streamlit","Plotly","Prophet"],"url":"https://github.com/budi/sales-forecast"}]',
'[{"name":"TensorFlow Developer Certificate","issuer":"Google","date":"2024-09","credential_id":"TF-CERT-789"},{"name":"Microsoft Certified: Azure Data Scientist Associate","issuer":"Microsoft","date":"2024-04","credential_id":"MS-AZURE-101"}]',
'completed', 1, DATE_SUB(NOW(), INTERVAL 8 DAY));

-- Insert Admin Activity Logs
INSERT INTO admin_activity_log (admin_id, activity_type, description, ip_address, user_agent, status, created_at) VALUES
(1, 'login', 'Super admin berhasil login ke sistem', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success', NOW()),
(1, 'user_management', 'Melihat daftar users', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
(2, 'login', 'Admin user login', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 'bootcamp_management', 'Update bootcamp information', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'system_settings', 'Update site settings', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 'login', 'Content admin login', '192.168.1.102', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'content_management', 'Approve forum posts', '192.168.1.102', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'user_management', 'Update user profile', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'backup', 'Database backup completed', '192.168.1.100', 'Automated System', 'success', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'security_audit', 'Security audit log review', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 'failed_login', 'Failed login attempt', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'failed', DATE_SUB(NOW(), INTERVAL 1 WEEK)),
(4, 'login', 'Support admin login attempt', '192.168.1.103', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'failed', DATE_SUB(NOW(), INTERVAL 1 WEEK));

-- ============================================
-- VIEWS FOR REPORTING
-- ============================================

-- View for bootcamp statistics
CREATE OR REPLACE VIEW bootcamp_stats AS
SELECT 
    b.id,
    b.title,
    b.price,
    b.discount_price,
    COUNT(DISTINCT o.user_id) as enrolled_users,
    AVG(r.rating) as avg_rating,
    COUNT(r.id) as review_count,
    COUNT(w.id) as wishlist_count
FROM bootcamps b
LEFT JOIN order_items oi ON b.id = oi.bootcamp_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'completed'
LEFT JOIN reviews r ON b.id = r.bootcamp_id AND r.status = 'published'
LEFT JOIN wishlists w ON b.id = w.bootcamp_id
WHERE b.status = 'active'
GROUP BY b.id;

-- View for user activity summary
CREATE OR REPLACE VIEW user_activity_summary AS
SELECT 
    u.id,
    u.name,
    u.alamat_email,
    u.status,
    COUNT(DISTINCT o.id) as total_orders,
    COUNT(DISTINCT w.id) as wishlist_count,
    COUNT(DISTINCT r.id) as review_count,
    COUNT(DISTINCT fp.id) as forum_posts,
    COUNT(DISTINCT fr.id) as forum_replies,
    u.created_at as join_date
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'completed'
LEFT JOIN wishlists w ON u.id = w.user_id
LEFT JOIN reviews r ON u.id = r.user_id
LEFT JOIN forum_posts fp ON u.id = fp.user_id AND fp.is_deleted = 0
LEFT JOIN forum_replies fr ON u.id = fr.user_id AND fr.is_deleted = 0
GROUP BY u.id;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure untuk membersihkan data lama
DELIMITER //
CREATE PROCEDURE CleanOldData()
BEGIN
    -- Clean old admin activity logs (older than 6 months)
    DELETE FROM admin_activity_log 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Archive old completed todos (older than 1 year)
    UPDATE todo_lists 
    SET status = 'archived' 
    WHERE status = 'completed' 
    AND completed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
    
    -- Clean old draft forum posts (older than 30 days)
    UPDATE forum_posts 
    SET is_deleted = 1 
    WHERE status = 'draft' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //
DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger untuk update reply count di forum posts
DELIMITER //
CREATE TRIGGER update_post_reply_count_insert
AFTER INSERT ON forum_replies
FOR EACH ROW
BEGIN
    UPDATE forum_posts 
    SET reply_count = reply_count + 1,
        last_reply_at = NEW.created_at,
        last_reply_by = NEW.user_id
    WHERE id = NEW.post_id;
END //

CREATE TRIGGER update_post_reply_count_delete
AFTER UPDATE ON forum_replies
FOR EACH ROW
BEGIN
    IF NEW.is_deleted = 1 AND OLD.is_deleted = 0 THEN
        UPDATE forum_posts 
        SET reply_count = reply_count - 1
        WHERE id = NEW.post_id;
    END IF;
END //
DELIMITER ;

-- Trigger untuk update current_participants di bootcamps
DELIMITER //
CREATE TRIGGER update_bootcamp_participants
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE order_status VARCHAR(20);
    
    SELECT payment_status INTO order_status 
    FROM orders 
    WHERE id = NEW.order_id;
    
    IF order_status = 'completed' THEN
        UPDATE bootcamps 
        SET current_participants = current_participants + 1
        WHERE id = NEW.bootcamp_id;
    END IF;
END //
DELIMITER ;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

-- Additional composite indexes
CREATE INDEX idx_orders_user_status ON orders(user_id, payment_status);
CREATE INDEX idx_reviews_bootcamp_status ON reviews(bootcamp_id, status);
CREATE INDEX idx_forum_posts_status_created ON forum_posts(status, created_at);
CREATE INDEX idx_forum_replies_post_status ON forum_replies(post_id, status);

-- Full-text search indexes
ALTER TABLE bootcamps ADD FULLTEXT(title, description);
ALTER TABLE forum_posts ADD FULLTEXT(title, content);

-- ============================================
-- SUMMARY OF IMPROVEMENTS
-- ============================================

/*
✅ PRIMARY KEYS: Setiap tabel memiliki AUTO_INCREMENT PRIMARY KEY
✅ UNIQUE KEYS: 
   - users.alamat_email
   - admin.email
   - categories.name, categories.slug
   - bootcamps.slug
   - wishlists.unique_wishlist (user_id, bootcamp_id)
   - orders.order_number
   - order_items.unique_order_item (order_id, bootcamp_id)
   - reviews.unique_user_bootcamp_review (user_id, bootcamp_id)
   - cv_data.user_id
   - forum_posts.slug
   - settings.setting_key

✅ DEFAULT VALUES: Hampir setiap kolom memiliki DEFAULT value yang sesuai
   - CURRENT_TIMESTAMP untuk created_at/updated_at
   - ENUM values untuk status fields
   - Numeric defaults (0) untuk counters
   - NULL untuk optional fields

✅ FOREIGN KEYS: Semua relasi tabel memiliki foreign key yang tepat
   - CASCADE untuk data yang saling bergantung
   - RESTRICT untuk data yang perlu dijaga integritasnya
   - Tidak ada FK untuk users dan forum_posts (artikel) sesuai permintaan

✅ ADDITIONAL IMPROVEMENTS:
   - Proper indexing untuk performance
   - Views untuk reporting
   - Stored procedures untuk maintenance
   - Triggers untuk data consistency
   - CHECK constraints untuk data validation
   - Full-text search capabilities
*/



-- Tambahkan ke database/code_camp.sql

-- Tabel untuk room chat
CREATE TABLE chat_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    admin_id INT NULL,
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_status (status)
);

-- Tabel untuk pesan chat
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    sender_type ENUM('user', 'admin') NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    INDEX idx_room_id (room_id),
    INDEX idx_sender (sender_type, sender_id),
    INDEX idx_created_at (created_at),
    INDEX idx_is_read (is_read)
);

-- Tabel untuk typing indicator (optional)
CREATE TABLE chat_typing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    sender_type ENUM('user', 'admin') NOT NULL,
    sender_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_typing (room_id, sender_type, sender_id)
);