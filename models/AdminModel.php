<?php
// models/AdminModel.php - Enhanced Admin Model

class AdminModel {
    private $conn;
    private $admin_table = 'admin';
    private $users_table = 'users';
    private $bootcamps_table = 'bootcamps';
    private $categories_table = 'categories';
    private $orders_table = 'orders';
    private $reviews_table = 'reviews';
    private $forum_posts_table = 'forum_posts';
    private $settings_table = 'settings';
    private $activity_log_table = 'admin_activity_log';

    public function __construct($db) {
        $this->conn = $db;
    }

    // ==================== AUTHENTICATION ====================
    
    public function login($email, $password) {
        try {
            $query = "SELECT * FROM " . $this->admin_table . " WHERE email = :email AND status = 'active' LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $admin['password'])) {
                    return $admin;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updateLastLogin($admin_id) {
        try {
            $query = "UPDATE " . $this->admin_table . " SET last_login = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $admin_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==================== DASHBOARD STATS ====================
    
    public function getDashboardStats() {
        try {
            $stats = [];
            
            // Total users
            $query = "SELECT COUNT(*) as total FROM " . $this->users_table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // New users this month
            $query = "SELECT COUNT(*) as total FROM " . $this->users_table . " WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['new_users_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total bootcamps
            $query = "SELECT COUNT(*) as total FROM " . $this->bootcamps_table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_bootcamps'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Active bootcamps
            $query = "SELECT COUNT(*) as total FROM " . $this->bootcamps_table . " WHERE status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['active_bootcamps'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total orders
            $query = "SELECT COUNT(*) as total FROM " . $this->orders_table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Orders this month
            $query = "SELECT COUNT(*) as total FROM " . $this->orders_table . " WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['orders_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total revenue
            $query = "SELECT SUM(final_amount) as total FROM " . $this->orders_table . " WHERE payment_status = 'completed'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Revenue this month
            $query = "SELECT SUM(final_amount) as total FROM " . $this->orders_table . " WHERE payment_status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['revenue_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Pending reviews
            $query = "SELECT COUNT(*) as total FROM " . $this->reviews_table . " WHERE status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['pending_reviews'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total forum posts
            $query = "SELECT COUNT(*) as total FROM " . $this->forum_posts_table . " WHERE is_deleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['total_forum_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getRecentActivities($limit = 10) {
        try {
            $query = "SELECT al.*, a.name as admin_name 
                     FROM " . $this->activity_log_table . " al
                     LEFT JOIN " . $this->admin_table . " a ON al.admin_id = a.id
                     ORDER BY al.created_at DESC LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getSystemAlerts() {
        $alerts = [];
        
        try {
            // Check for failed orders
            $query = "SELECT COUNT(*) as total FROM " . $this->orders_table . " WHERE payment_status = 'failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $failed_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($failed_orders > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "$failed_orders order gagal dalam 24 jam terakhir"
                ];
            }
            
            // Check for pending reviews
            $query = "SELECT COUNT(*) as total FROM " . $this->reviews_table . " WHERE status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pending_reviews = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($pending_reviews > 10) {
                $alerts[] = [
                    'type' => 'info',
                    'message' => "$pending_reviews review menunggu persetujuan"
                ];
            }
            
            return $alerts;
        } catch (PDOException $e) {
            return [];
        }
    }

    // ==================== USER MANAGEMENT ====================
    
    public function getUsers($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(u.name LIKE :search OR u.alamat_email LIKE :search OR u.no_telepon LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "u.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT u.*, 
                            COUNT(DISTINCT o.id) as total_orders,
                            SUM(CASE WHEN o.payment_status = 'completed' THEN o.final_amount ELSE 0 END) as total_spent
                     FROM " . $this->users_table . " u
                     LEFT JOIN " . $this->orders_table . " o ON u.id = o.user_id
                     $whereClause
                     GROUP BY u.id
                     ORDER BY u.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function countUsers($search = '', $status = '') {
        try {
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(name LIKE :search OR alamat_email LIKE :search OR no_telepon LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM " . $this->users_table . " $whereClause";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM " . $this->users_table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updateUser($id, $name, $email, $phone, $status) {
        try {
            // Check if email already exists for other users
            $checkQuery = "SELECT id FROM " . $this->users_table . " WHERE alamat_email = :email AND id != :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email sudah digunakan oleh user lain'];
            }

            $query = "UPDATE " . $this->users_table . " 
                     SET name = :name, alamat_email = :email, no_telepon = :phone, status = :status, updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Data user berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate data user'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }
    
    public function deleteUser($id) {
        try {
            $query = "DELETE FROM " . $this->users_table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User berhasil dihapus'];
            }
            return ['success' => false, 'message' => 'Gagal menghapus user'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== BOOTCAMP MANAGEMENT ====================
    
    public function getBootcamps($page = 1, $limit = 20, $search = '', $category = 0, $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(b.title LIKE :search OR b.description LIKE :search OR b.instructor_name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($category > 0) {
                $conditions[] = "b.category_id = :category";
                $params[':category'] = $category;
            }
            
            if (!empty($status)) {
                $conditions[] = "b.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT b.*, c.name as category_name,
                            COUNT(DISTINCT oi.order_id) as total_enrollments,
                            AVG(r.rating) as avg_rating,
                            COUNT(DISTINCT r.id) as review_count
                     FROM " . $this->bootcamps_table . " b
                     LEFT JOIN " . $this->categories_table . " c ON b.category_id = c.id
                     LEFT JOIN order_items oi ON b.id = oi.bootcamp_id
                     LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'completed'
                     LEFT JOIN " . $this->reviews_table . " r ON b.id = r.bootcamp_id AND r.status = 'published'
                     $whereClause
                     GROUP BY b.id
                     ORDER BY b.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function countBootcamps($search = '', $category = 0, $status = '') {
        try {
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(title LIKE :search OR description LIKE :search OR instructor_name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($category > 0) {
                $conditions[] = "category_id = :category";
                $params[':category'] = $category;
            }
            
            if (!empty($status)) {
                $conditions[] = "status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM " . $this->bootcamps_table . " $whereClause";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getCategories() {
        try {
            $query = "SELECT * FROM " . $this->categories_table . " ORDER BY sort_order ASC, name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getBootcampById($id) {
        try {
            $query = "SELECT b.*, c.name as category_name FROM " . $this->bootcamps_table . " b 
                     LEFT JOIN " . $this->categories_table . " c ON b.category_id = c.id 
                     WHERE b.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createBootcamp($data) {
        try {
            $query = "INSERT INTO " . $this->bootcamps_table . " 
                     (title, slug, description, category_id, instructor_name, price, discount_price, 
                      start_date, duration, image, status, featured, max_participants, created_at) 
                     VALUES (:title, :slug, :description, :category_id, :instructor_name, :price, 
                             :discount_price, :start_date, :duration, :image, :status, :featured, 
                             :max_participants, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Bootcamp berhasil dibuat'];
            }
            return ['success' => false, 'message' => 'Gagal membuat bootcamp'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function updateBootcamp($id, $data) {
        try {
            $setParts = [];
            $params = [':id' => $id];
            
            foreach ($data as $key => $value) {
                $setParts[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            
            $query = "UPDATE " . $this->bootcamps_table . " SET " . implode(', ', $setParts) . ", updated_at = NOW() WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Bootcamp berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate bootcamp'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function deleteBootcamp($id) {
        try {
            $query = "DELETE FROM " . $this->bootcamps_table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Bootcamp berhasil dihapus'];
            }
            return ['success' => false, 'message' => 'Gagal menghapus bootcamp'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== ACTIVITY LOGGING ====================
    
    public function logActivity($admin_id, $activity_type, $description, $ip_address = null, $user_agent = null) {
        try {
            $query = "INSERT INTO " . $this->activity_log_table . " 
                     (admin_id, activity_type, description, ip_address, user_agent, created_at) 
                     VALUES (:admin_id, :activity_type, :description, :ip_address, :user_agent, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':admin_id', $admin_id);
            $stmt->bindParam(':activity_type', $activity_type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->execute();
        } catch (PDOException $e) {
            // Log error silently
        }
    }

    // ==================== ORDERS ====================
    
    public function getOrders($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(o.order_number LIKE :search OR u.name LIKE :search OR u.alamat_email LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "o.payment_status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT o.*, u.name as user_name, u.alamat_email as user_email,
                            COUNT(oi.id) as item_count
                     FROM " . $this->orders_table . " o
                     LEFT JOIN " . $this->users_table . " u ON o.user_id = u.id
                     LEFT JOIN order_items oi ON o.id = oi.order_id
                     $whereClause
                     GROUP BY o.id
                     ORDER BY o.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countOrders($search = '', $status = '') {
        try {
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(o.order_number LIKE :search OR u.name LIKE :search OR u.alamat_email LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "o.payment_status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM " . $this->orders_table . " o
                     LEFT JOIN " . $this->users_table . " u ON o.user_id = u.id
                     $whereClause";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getOrderById($id) {
        try {
            $query = "SELECT o.*, u.name as user_name, u.alamat_email as user_email, u.no_telepon as user_phone
                     FROM " . $this->orders_table . " o
                     LEFT JOIN " . $this->users_table . " u ON o.user_id = u.id
                     WHERE o.id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getOrderItems($order_id) {
        try {
            $query = "SELECT oi.*, b.title as bootcamp_title, b.image as bootcamp_image
                     FROM order_items oi
                     LEFT JOIN " . $this->bootcamps_table . " b ON oi.bootcamp_id = b.id
                     WHERE oi.order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateOrderStatus($id, $status) {
        try {
            $query = "UPDATE " . $this->orders_table . " SET payment_status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Status order berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate status order'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== REVIEWS ====================
    
    public function getReviews($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(b.title LIKE :search OR u.name LIKE :search OR r.review_text LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "r.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT r.*, u.name as user_name, b.title as bootcamp_title
                     FROM " . $this->reviews_table . " r
                     LEFT JOIN " . $this->users_table . " u ON r.user_id = u.id
                     LEFT JOIN " . $this->bootcamps_table . " b ON r.bootcamp_id = b.id
                     $whereClause
                     ORDER BY r.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countReviews($search = '', $status = '') {
        try {
            $conditions = [];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(b.title LIKE :search OR u.name LIKE :search OR r.review_text LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "r.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT COUNT(*) as total FROM " . $this->reviews_table . " r
                     LEFT JOIN " . $this->users_table . " u ON r.user_id = u.id
                     LEFT JOIN " . $this->bootcamps_table . " b ON r.bootcamp_id = b.id
                     $whereClause";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function updateReviewStatus($id, $status) {
        try {
            $query = "UPDATE " . $this->reviews_table . " SET status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Status review berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate status review'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== FORUM ====================
    
    public function getForumPosts($page = 1, $limit = 20, $search = '', $status = '') {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = ['fp.is_deleted = 0'];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(fp.title LIKE :search OR fp.content LIKE :search OR u.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "fp.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = "WHERE " . implode(" AND ", $conditions);
            
            $query = "SELECT fp.*, u.name as user_name
                     FROM " . $this->forum_posts_table . " fp
                     LEFT JOIN " . $this->users_table . " u ON fp.user_id = u.id
                     $whereClause
                     ORDER BY fp.created_at DESC
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countForumPosts($search = '', $status = '') {
        try {
            $conditions = ['fp.is_deleted = 0'];
            $params = [];
            
            if (!empty($search)) {
                $conditions[] = "(fp.title LIKE :search OR fp.content LIKE :search OR u.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if (!empty($status)) {
                $conditions[] = "fp.status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = "WHERE " . implode(" AND ", $conditions);
            
            $query = "SELECT COUNT(*) as total FROM " . $this->forum_posts_table . " fp
                     LEFT JOIN " . $this->users_table . " u ON fp.user_id = u.id
                     $whereClause";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function pinForumPost($id, $pin) {
        try {
            $query = "UPDATE " . $this->forum_posts_table . " SET is_pinned = :pin WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':pin', $pin, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Post berhasil ' . ($pin ? 'di-pin' : 'di-unpin')];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate post'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    public function lockForumPost($id, $lock) {
        try {
            $query = "UPDATE " . $this->forum_posts_table . " SET is_locked = :lock WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lock', $lock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Post berhasil ' . ($lock ? 'dikunci' : 'dibuka')];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate post'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== SETTINGS ====================
    
    public function getSettings() {
        try {
            $query = "SELECT * FROM " . $this->settings_table . " ORDER BY setting_key ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateSetting($key, $value) {
        try {
            $query = "UPDATE " . $this->settings_table . " SET setting_value = :value, updated_at = NOW() WHERE setting_key = :key";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':key', $key);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Setting berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate setting'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== SYSTEM TOOLS ====================
    
    public function cleanOldLogs() {
        try {
            $query = "DELETE FROM " . $this->activity_log_table . " WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $deleted = $stmt->rowCount();
            
            return ['success' => true, 'message' => "Berhasil menghapus $deleted log lama"];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function exportUsers() {
        try {
            $query = "SELECT id, name, alamat_email, no_telepon, status, email_verified, created_at FROM " . $this->users_table . " ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function exportBootcamps() {
        try {
            $query = "SELECT b.*, c.name as category_name FROM " . $this->bootcamps_table . " b 
                     LEFT JOIN " . $this->categories_table . " c ON b.category_id = c.id 
                     ORDER BY b.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function exportOrders() {
        try {
            $query = "SELECT o.*, u.name as user_name, u.alamat_email as user_email FROM " . $this->orders_table . " o 
                     LEFT JOIN " . $this->users_table . " u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ==================== CATEGORIES ====================
    
    public function createCategory($data) {
        try {
            $query = "INSERT INTO " . $this->categories_table . " 
                     (name, slug, description, status, sort_order, created_at) 
                     VALUES (:name, :slug, :description, :status, :sort_order, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $data['slug']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':sort_order', $data['sort_order']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Kategori berhasil dibuat'];
            }
            return ['success' => false, 'message' => 'Gagal membuat kategori'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // ==================== DETAILED STATS ====================
    
    public function getDetailedStats() {
        try {
            $stats = [];
            
            // Revenue by month (last 12 months)
            $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                            SUM(final_amount) as revenue,
                            COUNT(*) as orders
                     FROM " . $this->orders_table . " 
                     WHERE payment_status = 'completed' 
                     AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                     GROUP BY month 
                     ORDER BY month ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['revenue_by_month'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top bootcamps by enrollment
            $query = "SELECT b.title, COUNT(DISTINCT oi.order_id) as enrollments
                     FROM " . $this->bootcamps_table . " b
                     LEFT JOIN order_items oi ON b.id = oi.bootcamp_id
                     LEFT JOIN " . $this->orders_table . " o ON oi.order_id = o.id AND o.payment_status = 'completed'
                     GROUP BY b.id
                     ORDER BY enrollments DESC
                     LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['top_bootcamps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // User registration by month
            $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as users
                     FROM " . $this->users_table . " 
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                     GROUP BY month 
                     ORDER BY month ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['users_by_month'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>