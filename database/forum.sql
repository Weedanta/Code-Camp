-- Tabel untuk menyimpan post forum
CREATE TABLE forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- Tabel untuk menyimpan reply post
CREATE TABLE forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- Insert sample data
INSERT INTO forum_posts (user_id, title, content) VALUES
(1, 'Pertanyaan tentang Web Development', 'Halo teman-teman, saya baru belajar web development. Ada yang bisa kasih saran framework mana yang bagus untuk pemula?'),
(1, 'Tips Belajar Programming Efektif', 'Mau share tips belajar programming yang efektif berdasarkan pengalaman saya selama ini. Silakan diskusi juga di komentar!'),
(1, 'Career Path di Tech Industry', 'Bingung mau ambil career path yang mana di tech industry. Ada yang bisa sharing pengalaman?');