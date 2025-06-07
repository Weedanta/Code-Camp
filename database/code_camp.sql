-- ============================================
-- CAMPUS HUB DATABASE - IMPROVED VERSION
-- ============================================

-- Membuat database
CREATE DATABASE IF NOT EXISTS code_camp;
USE code_camp;

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
-- INSERT SAMPLE DATA
-- ============================================

-- Insert sample categories
INSERT INTO categories (name, slug, icon, description, status, sort_order) VALUES
('Web Development', 'web-development', 'web.png', 'Learn modern web development technologies', 'active', 1),
('Data Science', 'data-science', 'data-science.png', 'Master data analysis and machine learning', 'active', 2),
('UI/UX Design', 'ui-ux-design', 'uiux.png', 'Create beautiful and user-friendly interfaces', 'active', 3),
('Mobile Development', 'mobile-development', 'mobile.png', 'Build mobile applications for iOS and Android', 'active', 4),
('DevOps', 'devops', 'devops.png', 'Learn deployment and infrastructure management', 'active', 5);

-- Insert default admin accounts
INSERT INTO admin (name, email, password, role, status) VALUES
('Super Admin', 'superadmin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active'),
('Admin User', 'admin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'Campus Hub', 'string', 'Name of the website', 1),
('site_description', 'Platform pembelajaran online terbaik', 'string', 'Site description for SEO', 1),
('max_file_upload', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 0),
('enable_registration', 'true', 'boolean', 'Allow new user registrations', 0),
('default_currency', 'IDR', 'string', 'Default currency for pricing', 1),
('timezone', 'Asia/Jakarta', 'string', 'Default timezone', 0);

-- Insert sample users
INSERT INTO users (name, alamat_email, password, no_telepon, status, email_verified) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', 'active', 1),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08987654321', 'active', 1),
('Bob Wilson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555666777', 'active', 0);

-- Insert sample bootcamps
INSERT INTO bootcamps (title, slug, description, category_id, instructor_name, instructor_photo, price, discount_price, start_date, duration, image, status, featured, max_participants) VALUES
('Digital Marketing Intensive Bootcamp', 'digital-marketing-intensive', 'Belajar strategi & implementasi digital marketing dari dasar hingga advanced dengan mentor berpengalaman di industri.', 1, 'Sarah Johnson', 'sarah.jpg', 650000.00, 500000.00, '2025-07-23', '21+ Sesi', 'digital-marketing.jpg', 'active', 1, 30),
('Microsoft Excel Basic to Advanced', 'excel-basic-advanced', 'Kuasai Microsoft Excel dari dasar hingga advanced, termasuk formula, pivot table, dan analisis data.', 2, 'Michael Lee', 'michael.jpg', 450000.00, 350000.00, '2025-05-24', '15+ Sesi', 'excel.jpg', 'active', 0, 25),
('Figma UI/UX Design Masterclass', 'figma-uiux-masterclass', 'Belajar UI/UX design menggunakan Figma dengan mentor berpengalaman, dari konsep dasar hingga project kompleks.', 3, 'John Doe', 'john.jpg', 550000.00, 400000.00, '2025-03-20', '12+ Sesi', 'figma-uiux.jpg', 'active', 1, 20);

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