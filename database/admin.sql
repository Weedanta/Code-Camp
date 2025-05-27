-- database/admin_update.sql
-- Update untuk melengkapi sistem admin

USE campus_hub;

-- Pastikan tabel admin sudah ada dengan struktur yang benar
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pastikan tabel admin_activity_log sudah ada
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin accounts (hanya jika belum ada)
INSERT IGNORE INTO admin (name, email, password, role, created_at) VALUES
('Super Admin', 'superadmin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NOW()),
('Admin User', 'admin@campus-hub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW()),
('Test Admin', 'testadmin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Update tabel users jika diperlukan (pastikan struktur sesuai)
-- ALTER TABLE users MODIFY COLUMN alamat_email VARCHAR(100) NOT NULL UNIQUE;

-- Index untuk performa yang lebih baik
CREATE INDEX idx_admin_email ON admin(email);
CREATE INDEX idx_admin_activity_admin_id ON admin_activity_log(admin_id);
CREATE INDEX idx_admin_activity_created_at ON admin_activity_log(created_at);
CREATE INDEX idx_users_email ON users(alamat_email);

-- View untuk statistik admin (opsional)
CREATE OR REPLACE VIEW admin_stats AS
SELECT 
    COUNT(DISTINCT u.id) as total_users,
    COUNT(DISTINCT a.id) as total_admins,
    COUNT(DISTINCT al.id) as total_activities,
    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as users_today
FROM users u
CROSS JOIN admin a
CROSS JOIN admin_activity_log al;

-- Stored procedure untuk membersihkan log lama (opsional)
DELIMITER //
CREATE PROCEDURE CleanOldLogs()
BEGIN
    DELETE FROM admin_activity_log 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
END //
DELIMITER ;

-- Event untuk menjalankan pembersihan otomatis (opsional)
-- CREATE EVENT IF NOT EXISTS clean_logs_event
-- ON SCHEDULE EVERY 1 MONTH
-- DO CALL CleanOldLogs();

-- Trigger untuk mencatat perubahan pada tabel users (opsional)
DELIMITER //
CREATE TRIGGER user_update_log 
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.name != NEW.name OR OLD.alamat_email != NEW.alamat_email OR OLD.no_telepon != NEW.no_telepon THEN
        INSERT INTO admin_activity_log (admin_id, activity_type, description, created_at)
        VALUES (
            IFNULL(@current_admin_id, 1), 
            'user_modified', 
            CONCAT('User ID ', NEW.id, ' data changed'),
            NOW()
        );
    END IF;
END //
DELIMITER ;

-- Trigger untuk mencatat penghapusan user
DELIMITER //
CREATE TRIGGER user_delete_log 
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO admin_activity_log (admin_id, activity_type, description, created_at)
    VALUES (
        IFNULL(@current_admin_id, 1), 
        'user_deleted', 
        CONCAT('User ID ', OLD.id, ' (', OLD.name, ') deleted'),
        NOW()
    );
END //
DELIMITER ;

-- Contoh data untuk testing (hapus jika tidak diperlukan)
INSERT IGNORE INTO users (name, alamat_email, password, no_telepon, created_at) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', NOW()),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08987654321', NOW()),
('Bob Wilson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08555666777', NOW());

-- Verifikasi data
SELECT 'Admin accounts created:' as Info;
SELECT id, name, email, role, created_at FROM admin;

SELECT 'Sample users created:' as Info;
SELECT id, name, alamat_email, no_telepon, created_at FROM users LIMIT 5;