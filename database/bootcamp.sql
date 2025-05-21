-- Extend the existing database with new tables
USE campus_hub;

-- Bootcamp Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(255),
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bootcamps Table
CREATE TABLE IF NOT EXISTS bootcamps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    instructor_name VARCHAR(100) NOT NULL,
    instructor_photo VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    start_date DATE,
    duration VARCHAR(50),
    image VARCHAR(255),
    status ENUM('active', 'upcoming', 'closed') DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Wishlist Table
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bootcamp_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, bootcamp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items (Bootcamps purchased in each order)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    bootcamp_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bootcamp Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bootcamp_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    review_text TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bootcamp_id) REFERENCES bootcamps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO categories (name, icon, created_at) VALUES
('Web Dev', 'Web.png', NOW()),
('Data Science', 'DataScience.png', NOW()),
('UI/UX Design', 'UIUX.png', NOW()),
('Mobile Dev', 'Mobile.png', NOW());

-- Insert sample bootcamps
INSERT INTO bootcamps (title, description, category_id, instructor_name, instructor_photo, price, discount_price, start_date, duration, image, status, created_at) VALUES
('Digital Marketing: Fullstack Intensive Bootcamp', 'Belajar strategi & implementasi digital marketing dari dasar hingga advanced dengan mentor berpengalaman di industri.', 1, 'Sarah Johnson', 'sarah.jpg', 650000, 1000000, '2025-07-23', '21+ Sesi', 'digital-marketing.jpg', 'active', NOW()),
('Microsoft Excel Basic to Advanced', 'Kuasai Microsoft Excel dari dasar hingga advanced, termasuk formula, pivot table, dan analisis data.', 2, 'Michael Lee', 'michael.jpg', 450000, 1000000, '2025-05-24', '15+ Sesi', 'excel.jpg', 'active', NOW()),
('Data Analysis: Fullstack Intensive Bootcamp', 'Belajar analisis data dari dasar hingga visualisasi, termasuk penggunaan tools populer seperti Python, SQL, dan Tableau.', 2, 'Alex Chen', 'alex.jpg', 650000, 1000000, '2025-06-04', '14+ Sesi', 'data-analysis.jpg', 'active', NOW()),
('Human Capital Staff BNSP Certified', 'Program pelatihan intensif untuk menjadi Human Capital Staff bersertifikasi BNSP dengan materi komprehensif dan praktis.', 1, 'Dr. Budi Handoyo', 'budi.jpg', 2250000, 5000000, '2025-06-17', '10+ Sesi', 'hr-staff.jpg', 'active', NOW()),
('Figma UI/UX Design', 'Belajar UI/UX design menggunakan Figma dengan mentor berpengalaman, dari konsep dasar hingga project kompleks.', 3, 'John Doe', 'john.jpg', 550000, 800000, '2025-03-20', '12+ Sesi', 'figma-uiux.jpg', 'active', NOW()),
('Pengoptimalan IoT', 'Pelajari cara mengembangkan solusi Internet of Things dari awal hingga implementasi!', 1, 'Michael Lee', 'michael.jpg', 750000, 900000, '2025-03-20', '4 bulan', 'iot.jpg', 'active', NOW()),
('Maximize Our Foreign Language Skills', 'Tingkatkan kemampuan bahasa asing Anda dengan bootcamp intensif yang praktis dan interaktif!', 4, 'Sukarman Widiyanto', 'sukarman.jpg', 450000, 600000, '2025-03-08', '2 bulan', 'language.jpg', 'active', NOW()),
('Capture The Flag: To The Moon!', 'Jelajahi dunia cybersecurity melalui kompetisi Capture The Flag yang seru dan informatif!', 1, 'Haris Pratama', 'haris.jpg', 350000, 500000, '2025-03-20', '1 bulan', 'ctf.jpg', 'active', NOW());

-- Insert sample reviews
INSERT INTO reviews (bootcamp_id, user_id, rating, review_text, created_at) VALUES
(1, 1, 5, 'Bootcamp yang sangat bagus dan informatif! Saya belajar banyak tentang digital marketing.', NOW()),
(2, 1, 4, 'Materi Excel disajikan dengan cara yang mudah dipahami. Sangat membantu untuk pekerjaan saya.', NOW()),
(5, 1, 5, 'Instruktur sangat berpengalaman dan materi UI/UX sangat update dengan tren terkini.', NOW());