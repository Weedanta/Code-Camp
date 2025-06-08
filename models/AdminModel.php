<?php
// models/AdminModel.php - Complete Admin Model Implementation

class AdminModel {
    private $conn;

    public function __construct($database) {
        $this->conn = $database;
    }

    // ==================== AUTHENTICATION ====================
    
    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                return $admin;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }

    public function updateLastLogin($adminId) {
        try {
            $stmt = $this->conn->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            return $stmt->execute([$adminId]);
        } catch (PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
            return false;
        }
    }

    // ==================== DASHBOARD STATS ====================
    
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total users
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users");
            $stats['total_users'] = $stmt->fetchColumn();
            
            // New users this month
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
            $stats['new_users_month'] = $stmt->fetchColumn();
            
            // Total bootcamps
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM bootcamps");
            $stats['total_bootcamps'] = $stmt->fetchColumn();
            
            // Active bootcamps
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM bootcamps WHERE status = 'active'");
            $stats['active_bootcamps'] = $stmt->fetchColumn();
            
            // Total orders
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM orders");
            $stats['total_orders'] = $stmt->fetchColumn();
            
            // Orders this month
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
            $stats['orders_month'] = $stmt->fetchColumn();
            
            // Total revenue
            $stmt = $this->conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'completed'");
            $stats['total_revenue'] = $stmt->fetchColumn();
            
            // Revenue this month
            $stmt = $this->conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'completed' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
            $stats['revenue_month'] = $stmt->fetchColumn();
            
            // Pending reviews
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM reviews WHERE status = 'pending'");
            $stats['pending_reviews'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return [
                'total_users' => 0,
                'new_users_month' => 0,
                'total_bootcamps' => 0,
                'active_bootcamps' => 0,
                'total_orders' => 0,
                'orders_month' => 0,
                'total_revenue' => 0,
                'revenue_month' => 0,
                'pending_reviews' => 0
            ];
        }
    }

    public function getDetailedStats() {
        try {
            $stats = $this->getDashboardStats();
            
            // Additional detailed stats
            $stmt = $this->conn->query("SELECT COALESCE(AVG(total_amount), 0) as avg FROM orders WHERE payment_status = 'completed'");
            $stats['avg_order_value'] = $stmt->fetchColumn();
            
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM enrollments");
            $stats['total_enrollments'] = $stmt->fetchColumn();
            
            // Top bootcamps by revenue
            $stmt = $this->conn->query("
                SELECT b.id, b.title, COUNT(o.id) as enrollments, COALESCE(SUM(o.total_amount), 0) as revenue 
                FROM bootcamps b 
                LEFT JOIN order_items oi ON b.id = oi.bootcamp_id 
                LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'completed'
                GROUP BY b.id, b.title 
                ORDER BY revenue DESC 
                LIMIT 10
            ");
            $stats['top_bootcamps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get detailed stats error: " . $e->getMessage());
            return [];
        }
    }

    public function getSystemAlerts() {
        $alerts = [];
        
        try {
            // Check for pending reviews
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'");
            $pendingReviews = $stmt->fetchColumn();
            
            if ($pendingReviews > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "Ada $pendingReviews review yang menunggu moderasi"
                ];
            }
            
            // Check for failed orders
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM orders WHERE payment_status = 'failed' AND DATE(created_at) = CURDATE()");
            $failedOrders = $stmt->fetchColumn();
            
            if ($failedOrders > 5) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "Banyak order gagal hari ini: $failedOrders orders"
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Get system alerts error: " . $e->getMessage());
        }
        
        return $alerts;
    }

    // ==================== USER MANAGEMENT ====================
    
    public function getUsers($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (name LIKE ? OR alamat_email LIKE ? OR no_telepon LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT u.*, 
                           COALESCE(COUNT(o.id), 0) as total_orders,
                           COALESCE(SUM(o.total_amount), 0) as total_spent
                    FROM users u 
                    LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'completed'
                    $where 
                    GROUP BY u.id
                    ORDER BY u.created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get users error: " . $e->getMessage());
            return [];
        }
    }

    public function countUsers($search = '', $status = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (name LIKE ? OR alamat_email LIKE ? OR no_telepon LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count users error: " . $e->getMessage());
            return 0;
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user by id error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $name, $email, $phone, $status) {
        try {
            // Check if email already exists for other users
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE alamat_email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah digunakan oleh user lain'];
            }
            
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, alamat_email = ?, no_telepon = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$name, $email, $phone, $status, $id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Data user berhasil diupdate'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate data user'];
            }
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteUser($id) {
        try {
            // Check if user has orders
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $stmt->execute([$id]);
            $orderCount = $stmt->fetchColumn();
            
            if ($orderCount > 0) {
                return ['success' => false, 'message' => 'User tidak dapat dihapus karena memiliki riwayat order'];
            }
            
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'User berhasil dihapus'];
            } else {
                return ['success' => false, 'message' => 'Gagal menghapus user'];
            }
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function resetUserPassword($id) {
        try {
            $newPassword = 'password123';
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$hashedPassword, $id]);
            
            if ($success) {
                return ['success' => true, 'message' => "Password berhasil direset ke: $newPassword"];
            } else {
                return ['success' => false, 'message' => 'Gagal mereset password'];
            }
        } catch (PDOException $e) {
            error_log("Reset user password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== BOOTCAMP MANAGEMENT ====================
    
    public function getBootcamps($page = 1, $limit = 20, $search = '', $category = 0, $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (b.title LIKE ? OR b.description LIKE ? OR b.instructor_name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($category) {
                $where .= " AND b.category_id = ?";
                $params[] = $category;
            }
            
            if ($status) {
                $where .= " AND b.status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT b.*, c.name as category_name,
                           COALESCE(COUNT(DISTINCT e.id), 0) as total_enrollments,
                           COALESCE(AVG(r.rating), 0) as avg_rating,
                           COALESCE(COUNT(DISTINCT r.id), 0) as review_count
                    FROM bootcamps b 
                    LEFT JOIN categories c ON b.category_id = c.id
                    LEFT JOIN enrollments e ON b.id = e.bootcamp_id
                    LEFT JOIN reviews r ON b.id = r.bootcamp_id AND r.status = 'published'
                    $where 
                    GROUP BY b.id
                    ORDER BY b.created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get bootcamps error: " . $e->getMessage());
            return [];
        }
    }

    public function countBootcamps($search = '', $category = 0, $status = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (title LIKE ? OR description LIKE ? OR instructor_name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($category) {
                $where .= " AND category_id = ?";
                $params[] = $category;
            }
            
            if ($status) {
                $where .= " AND status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM bootcamps $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count bootcamps error: " . $e->getMessage());
            return 0;
        }
    }

    public function getBootcampById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT b.*, c.name as category_name,
                       COALESCE(COUNT(DISTINCT e.id), 0) as total_enrollments,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COALESCE(COUNT(DISTINCT r.id), 0) as review_count
                FROM bootcamps b 
                LEFT JOIN categories c ON b.category_id = c.id
                LEFT JOIN enrollments e ON b.id = e.bootcamp_id
                LEFT JOIN reviews r ON b.id = r.bootcamp_id AND r.status = 'published'
                WHERE b.id = ?
                GROUP BY b.id
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get bootcamp by id error: " . $e->getMessage());
            return false;
        }
    }

    public function createBootcamp($data) {
        try {
            // Check if slug already exists
            if (!empty($data['slug'])) {
                $stmt = $this->conn->prepare("SELECT id FROM bootcamps WHERE slug = ?");
                $stmt->execute([$data['slug']]);
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Slug sudah digunakan'];
                }
            }
            
            $stmt = $this->conn->prepare("
                INSERT INTO bootcamps (title, slug, description, category_id, instructor_name, price, discount_price, 
                                     start_date, duration, status, featured, max_participants, image, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $data['title'],
                $data['slug'],
                $data['description'],
                $data['category_id'],
                $data['instructor_name'],
                $data['price'],
                $data['discount_price'],
                $data['start_date'],
                $data['duration'],
                $data['status'],
                $data['featured'],
                $data['max_participants'],
                $data['image'] ?? null
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Bootcamp berhasil dibuat'];
            } else {
                return ['success' => false, 'message' => 'Gagal membuat bootcamp'];
            }
        } catch (PDOException $e) {
            error_log("Create bootcamp error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function updateBootcamp($id, $data) {
        try {
            // Check if slug already exists for other bootcamps
            if (!empty($data['slug'])) {
                $stmt = $this->conn->prepare("SELECT id FROM bootcamps WHERE slug = ? AND id != ?");
                $stmt->execute([$data['slug'], $id]);
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Slug sudah digunakan'];
                }
            }
            
            $sql = "UPDATE bootcamps SET title = ?, slug = ?, description = ?, category_id = ?, instructor_name = ?, 
                    price = ?, discount_price = ?, start_date = ?, duration = ?, status = ?, featured = ?, 
                    max_participants = ?, updated_at = NOW()";
            $params = [
                $data['title'],
                $data['slug'],
                $data['description'],
                $data['category_id'],
                $data['instructor_name'],
                $data['price'],
                $data['discount_price'],
                $data['start_date'],
                $data['duration'],
                $data['status'],
                $data['featured'],
                $data['max_participants']
            ];
            
            if (isset($data['image'])) {
                $sql .= ", image = ?";
                $params[] = $data['image'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute($params);
            
            if ($success) {
                return ['success' => true, 'message' => 'Bootcamp berhasil diupdate'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate bootcamp'];
            }
        } catch (PDOException $e) {
            error_log("Update bootcamp error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteBootcamp($id) {
        try {
            // Check if bootcamp has enrollments
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM enrollments WHERE bootcamp_id = ?");
            $stmt->execute([$id]);
            $enrollmentCount = $stmt->fetchColumn();
            
            if ($enrollmentCount > 0) {
                return ['success' => false, 'message' => 'Bootcamp tidak dapat dihapus karena sudah memiliki peserta'];
            }
            
            $stmt = $this->conn->prepare("DELETE FROM bootcamps WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Bootcamp berhasil dihapus'];
            } else {
                return ['success' => false, 'message' => 'Gagal menghapus bootcamp'];
            }
        } catch (PDOException $e) {
            error_log("Delete bootcamp error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function toggleBootcampFeatured($id, $featured) {
        try {
            $stmt = $this->conn->prepare("UPDATE bootcamps SET featured = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$featured, $id]);
            
            if ($success) {
                $status = $featured ? 'featured' : 'unfeatured';
                return ['success' => true, 'message' => "Bootcamp berhasil di-$status"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah status featured'];
            }
        } catch (PDOException $e) {
            error_log("Toggle bootcamp featured error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== CATEGORY MANAGEMENT ====================
    
    public function getCategories() {
        try {
            $stmt = $this->conn->query("
                SELECT c.*, COUNT(b.id) as bootcamp_count 
                FROM categories c 
                LEFT JOIN bootcamps b ON c.id = b.category_id 
                WHERE c.status = 'active'
                GROUP BY c.id 
                ORDER BY c.sort_order, c.name
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get categories error: " . $e->getMessage());
            return [];
        }
    }

    public function createCategory($data) {
        try {
            // Check if name already exists
            $stmt = $this->conn->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$data['name']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Nama kategori sudah digunakan'];
            }
            
            $stmt = $this->conn->prepare("
                INSERT INTO categories (name, slug, description, status, sort_order, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['status'],
                $data['sort_order']
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Kategori berhasil dibuat'];
            } else {
                return ['success' => false, 'message' => 'Gagal membuat kategori'];
            }
        } catch (PDOException $e) {
            error_log("Create category error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function updateCategory($id, $data) {
        try {
            // Check if name already exists for other categories
            $stmt = $this->conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
            $stmt->execute([$data['name'], $id]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Nama kategori sudah digunakan'];
            }
            
            $stmt = $this->conn->prepare("
                UPDATE categories SET name = ?, description = ?, sort_order = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $success = $stmt->execute([
                $data['name'],
                $data['description'],
                $data['sort_order'],
                $id
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Kategori berhasil diupdate'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate kategori'];
            }
        } catch (PDOException $e) {
            error_log("Update category error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteCategory($id) {
        try {
            // Check if category has bootcamps
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM bootcamps WHERE category_id = ?");
            $stmt->execute([$id]);
            $bootcampCount = $stmt->fetchColumn();
            
            if ($bootcampCount > 0) {
                return ['success' => false, 'message' => 'Kategori tidak dapat dihapus karena masih memiliki bootcamp'];
            }
            
            $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Kategori berhasil dihapus'];
            } else {
                return ['success' => false, 'message' => 'Gagal menghapus kategori'];
            }
        } catch (PDOException $e) {
            error_log("Delete category error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== ORDER MANAGEMENT ====================
    
    public function getOrders($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (o.id LIKE ? OR u.name LIKE ? OR u.alamat_email LIKE ? OR o.transaction_id LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND o.payment_status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT o.*, u.name as user_name, u.alamat_email as user_email, u.no_telepon as user_phone,
                           COUNT(oi.id) as item_count
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    $where 
                    GROUP BY o.id
                    ORDER BY o.created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get orders error: " . $e->getMessage());
            return [];
        }
    }

    public function countOrders($search = '', $status = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (o.id LIKE ? OR u.name LIKE ? OR u.alamat_email LIKE ? OR o.transaction_id LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND o.payment_status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count orders error: " . $e->getMessage());
            return 0;
        }
    }

    public function getOrderById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT o.*, u.name as user_name, u.alamat_email as user_email, u.no_telepon as user_phone
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get order by id error: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderItems($orderId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT oi.*, b.title as bootcamp_title, b.image as bootcamp_image
                FROM order_items oi 
                LEFT JOIN bootcamps b ON oi.bootcamp_id = b.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get order items error: " . $e->getMessage());
            return [];
        }
    }

    public function updateOrderStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$status, $id]);
            
            if ($success) {
                return ['success' => true, 'message' => "Status order berhasil diubah menjadi $status"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah status order'];
            }
        } catch (PDOException $e) {
            error_log("Update order status error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== REVIEW MANAGEMENT ====================
    
    public function getReviews($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (r.review_text LIKE ? OR u.name LIKE ? OR b.title LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND r.status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT r.*, u.name as user_name, b.title as bootcamp_title
                    FROM reviews r 
                    LEFT JOIN users u ON r.user_id = u.id
                    LEFT JOIN bootcamps b ON r.bootcamp_id = b.id
                    $where 
                    ORDER BY r.created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get reviews error: " . $e->getMessage());
            return [];
        }
    }

    public function countReviews($search = '', $status = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (r.review_text LIKE ? OR u.name LIKE ? OR b.title LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND r.status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reviews r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN bootcamps b ON r.bootcamp_id = b.id $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count reviews error: " . $e->getMessage());
            return 0;
        }
    }

    public function updateReviewStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$status, $id]);
            
            if ($success) {
                return ['success' => true, 'message' => "Review berhasil di-$status"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah status review'];
            }
        } catch (PDOException $e) {
            error_log("Update review status error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteReview($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Review berhasil dihapus'];
            } else {
                return ['success' => false, 'message' => 'Gagal menghapus review'];
            }
        } catch (PDOException $e) {
            error_log("Delete review error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function bulkApproveReviews() {
        try {
            $stmt = $this->conn->prepare("UPDATE reviews SET status = 'published', updated_at = NOW() WHERE status = 'pending'");
            $success = $stmt->execute();
            
            if ($success) {
                $count = $stmt->rowCount();
                return ['success' => true, 'message' => "$count review berhasil disetujui", 'count' => $count];
            } else {
                return ['success' => false, 'message' => 'Gagal menyetujui review'];
            }
        } catch (PDOException $e) {
            error_log("Bulk approve reviews error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== FORUM MANAGEMENT ====================
    
    public function getForumPosts($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (fp.title LIKE ? OR fp.content LIKE ? OR u.name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND fp.status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT fp.*, u.name as user_name,
                           COUNT(fpr.id) as reply_count
                    FROM forum_posts fp 
                    LEFT JOIN users u ON fp.user_id = u.id
                    LEFT JOIN forum_post_replies fpr ON fp.id = fpr.post_id
                    $where 
                    GROUP BY fp.id
                    ORDER BY fp.is_pinned DESC, fp.updated_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get forum posts error: " . $e->getMessage());
            return [];
        }
    }

    public function countForumPosts($search = '', $status = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (fp.title LIKE ? OR fp.content LIKE ? OR u.name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($status) {
                $where .= " AND fp.status = ?";
                $params[] = $status;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM forum_posts fp LEFT JOIN users u ON fp.user_id = u.id $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count forum posts error: " . $e->getMessage());
            return 0;
        }
    }

    public function pinForumPost($id, $pinned) {
        try {
            $stmt = $this->conn->prepare("UPDATE forum_posts SET is_pinned = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$pinned, $id]);
            
            if ($success) {
                $status = $pinned ? 'pinned' : 'unpinned';
                return ['success' => true, 'message' => "Post berhasil $status"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah status pin'];
            }
        } catch (PDOException $e) {
            error_log("Pin forum post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function lockForumPost($id, $locked) {
        try {
            $stmt = $this->conn->prepare("UPDATE forum_posts SET is_locked = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$locked, $id]);
            
            if ($success) {
                $status = $locked ? 'locked' : 'unlocked';
                return ['success' => true, 'message' => "Post berhasil $status"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah status lock'];
            }
        } catch (PDOException $e) {
            error_log("Lock forum post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteForumPost($id) {
        try {
            $this->conn->beginTransaction();
            
            // Delete replies first
            $stmt = $this->conn->prepare("DELETE FROM forum_post_replies WHERE post_id = ?");
            $stmt->execute([$id]);
            
            // Delete post
            $stmt = $this->conn->prepare("DELETE FROM forum_posts WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Post dan balasan berhasil dihapus'];
            } else {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Gagal menghapus post'];
            }
        } catch (PDOException $e) {
            $this->conn->rollback();
            error_log("Delete forum post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== SETTINGS MANAGEMENT ====================
    
    public function getSettings() {
        try {
            $stmt = $this->conn->query("SELECT * FROM settings ORDER BY setting_key");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get settings error: " . $e->getMessage());
            return [];
        }
    }

    public function updateSetting($key, $value) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
            ");
            $success = $stmt->execute([$key, $value, $value]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Setting berhasil diupdate'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate setting'];
            }
        } catch (PDOException $e) {
            error_log("Update setting error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== SYSTEM TOOLS ====================
    
    public function createDatabaseBackup($filename) {
        try {
            // This is a simplified backup - in production, use mysqldump or similar
            $tables = ['users', 'admins', 'bootcamps', 'categories', 'orders', 'order_items', 'reviews', 'settings'];
            $backup = "-- Database Backup " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                $stmt = $this->conn->query("SELECT * FROM $table");
                $backup .= "-- Table: $table\n";
                $backup .= "DELETE FROM $table;\n";
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $values = array_map(function($v) { return "'" . addslashes($v) . "'"; }, array_values($row));
                    $backup .= "INSERT INTO $table VALUES (" . implode(", ", $values) . ");\n";
                }
                $backup .= "\n";
            }
            
            if (file_put_contents($filename, $backup)) {
                return ['success' => true, 'message' => 'Backup berhasil dibuat'];
            } else {
                return ['success' => false, 'message' => 'Gagal membuat backup'];
            }
        } catch (Exception $e) {
            error_log("Create database backup error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function cleanOldLogs() {
        try {
            $stmt = $this->conn->prepare("DELETE FROM admin_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)");
            $success = $stmt->execute();
            
            if ($success) {
                $count = $stmt->rowCount();
                return ['success' => true, 'message' => "$count log lama berhasil dibersihkan"];
            } else {
                return ['success' => false, 'message' => 'Gagal membersihkan log'];
            }
        } catch (PDOException $e) {
            error_log("Clean old logs error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function optimizeDatabase() {
        try {
            $tables = ['users', 'admins', 'bootcamps', 'categories', 'orders', 'order_items', 'reviews', 'settings', 'admin_activity_logs'];
            
            foreach ($tables as $table) {
                $stmt = $this->conn->query("OPTIMIZE TABLE $table");
            }
            
            return ['success' => true, 'message' => 'Database berhasil dioptimasi'];
        } catch (PDOException $e) {
            error_log("Optimize database error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function checkSystemHealth() {
        try {
            $health = [
                'database' => 'OK',
                'disk_space' => 'OK',
                'memory' => 'OK',
                'errors' => []
            ];
            
            // Check database connection
            $stmt = $this->conn->query("SELECT 1");
            if (!$stmt) {
                $health['database'] = 'ERROR';
                $health['errors'][] = 'Database connection failed';
            }
            
            // Check disk space (simplified)
            $totalSpace = disk_total_space('.');
            $freeSpace = disk_free_space('.');
            $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
            
            if ($usedPercent > 90) {
                $health['disk_space'] = 'WARNING';
                $health['errors'][] = 'Disk space usage over 90%';
            }
            
            return $health;
        } catch (Exception $e) {
            error_log("Check system health error: " . $e->getMessage());
            return [
                'database' => 'ERROR',
                'disk_space' => 'UNKNOWN',
                'memory' => 'UNKNOWN',
                'errors' => ['System health check failed']
            ];
        }
    }

    // ==================== EXPORT FUNCTIONS ====================
    
    public function exportUsers() {
        try {
            $stmt = $this->conn->query("
                SELECT id, name, alamat_email, no_telepon, status, created_at, updated_at 
                FROM users 
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Export users error: " . $e->getMessage());
            return [];
        }
    }

    public function exportBootcamps() {
        try {
            $stmt = $this->conn->query("
                SELECT b.id, b.title, b.slug, b.instructor_name, b.price, b.discount_price, 
                       b.start_date, b.duration, b.status, b.featured, c.name as category,
                       b.created_at, b.updated_at
                FROM bootcamps b 
                LEFT JOIN categories c ON b.category_id = c.id
                ORDER BY b.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Export bootcamps error: " . $e->getMessage());
            return [];
        }
    }

    public function exportOrders() {
        try {
            $stmt = $this->conn->query("
                SELECT o.id, o.transaction_id, u.name as user_name, u.alamat_email as user_email,
                       o.total_amount, o.payment_status, o.payment_method, o.created_at, o.updated_at
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Export orders error: " . $e->getMessage());
            return [];
        }
    }

    // ==================== ADMIN PROFILE ====================
    
    public function getAdminById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin by id error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAdminProfile($id, $data) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE admins SET name = ?, email = ?, phone = ?, department = ?, 
                       timezone = ?, language = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $success = $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['department'],
                $data['timezone'],
                $data['language'],
                $id
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Profile berhasil diupdate'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate profile'];
            }
        } catch (PDOException $e) {
            error_log("Update admin profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function changeAdminPassword($id, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $stmt = $this->conn->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin || !password_verify($currentPassword, $admin['password'])) {
                return ['success' => false, 'message' => 'Password lama tidak benar'];
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE admins SET password = ?, updated_at = NOW() WHERE id = ?");
            $success = $stmt->execute([$hashedPassword, $id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Password berhasil diubah'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah password'];
            }
        } catch (PDOException $e) {
            error_log("Change admin password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== ACTIVITY LOG ====================
    
    public function logActivity($adminId, $activityType, $description, $ipAddress = null, $userAgent = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO admin_activity_logs (admin_id, activity_type, description, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            return $stmt->execute([
                $adminId,
                $activityType,
                $description,
                $ipAddress,
                $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("Log activity error: " . $e->getMessage());
            return false;
        }
    }

    public function getActivityLog($page = 1, $limit = 50, $search = '', $activityType = '', $adminId = 0, $dateFrom = '', $dateTo = '') {
        try {
            $offset = ($page - 1) * $limit;
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (aal.description LIKE ? OR a.name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($activityType) {
                $where .= " AND aal.activity_type = ?";
                $params[] = $activityType;
            }
            
            if ($adminId) {
                $where .= " AND aal.admin_id = ?";
                $params[] = $adminId;
            }
            
            if ($dateFrom) {
                $where .= " AND DATE(aal.created_at) >= ?";
                $params[] = $dateFrom;
            }
            
            if ($dateTo) {
                $where .= " AND DATE(aal.created_at) <= ?";
                $params[] = $dateTo;
            }
            
            $sql = "SELECT aal.*, a.name as admin_name
                    FROM admin_activity_logs aal 
                    LEFT JOIN admins a ON aal.admin_id = a.id
                    $where 
                    ORDER BY aal.created_at DESC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get activity log error: " . $e->getMessage());
            return [];
        }
    }

    public function countActivityLog($search = '', $activityType = '', $adminId = 0, $dateFrom = '', $dateTo = '') {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            if ($search) {
                $where .= " AND (aal.description LIKE ? OR a.name LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if ($activityType) {
                $where .= " AND aal.activity_type = ?";
                $params[] = $activityType;
            }
            
            if ($adminId) {
                $where .= " AND aal.admin_id = ?";
                $params[] = $adminId;
            }
            
            if ($dateFrom) {
                $where .= " AND DATE(aal.created_at) >= ?";
                $params[] = $dateFrom;
            }
            
            if ($dateTo) {
                $where .= " AND DATE(aal.created_at) <= ?";
                $params[] = $dateTo;
            }
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM admin_activity_logs aal LEFT JOIN admins a ON aal.admin_id = a.id $where");
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count activity log error: " . $e->getMessage());
            return 0;
        }
    }

    public function getRecentActivities($limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT aal.*, a.name as admin_name
                FROM admin_activity_logs aal 
                LEFT JOIN admins a ON aal.admin_id = a.id
                ORDER BY aal.created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent activities error: " . $e->getMessage());
            return [];
        }
    }
}
?>