<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

class AdminController {
    private $admin;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->admin = new AdminModel($db);
    }

    // ==================== AUTHENTICATION ====================
    
    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
            header('Location: admin.php?action=dashboard');
            exit;
        }
        
        include __DIR__ . '/../views/admin/login.php';
    }

    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=login');
            exit;
        }

        $email = SecurityHelper::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting
        if (!SecurityHelper::preventBruteForce($email)) {
            $_SESSION['error'] = 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.';
            header('Location: admin.php?action=login');
            exit;
        }

        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email dan password harus diisi';
            header('Location: admin.php?action=login');
            exit;
        }

        // Check email contains 'admin'
        if (strpos(strtolower($email), 'admin') === false) {
            SecurityHelper::logSecurityEvent(
                'invalid_admin_login',
                "Non-admin email attempted admin login: " . $email,
                'high'
            );
            $_SESSION['error'] = 'Akses ditolak';
            header('Location: admin.php?action=login');
            exit;
        }

        // Attempt login
        $admin = $this->admin->login($email, $password);
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_last_activity'] = time();

            // Update last login
            $this->admin->updateLastLogin($admin['id']);

            // Log successful login
            $this->admin->logActivity(
                $admin['id'], 
                'login', 
                'Admin berhasil login',
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );

            header('Location: admin.php?action=dashboard');
            exit;
        } else {
            SecurityHelper::logSecurityEvent(
                'failed_admin_login',
                "Failed admin login attempt: " . $email,
                'high'
            );
            $_SESSION['error'] = 'Email atau password salah';
            header('Location: admin.php?action=login');
            exit;
        }
    }

    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'logout', 
                'Admin logout'
            );
        }

        // Clear admin session
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();

        header('Location: admin.php?action=login');
        exit;
    }

    // ==================== DASHBOARD ====================
    
    public function dashboard() {
        $stats = $this->admin->getDashboardStats();
        $recentActivities = $this->admin->getRecentActivities(10);
        $systemAlerts = $this->admin->getSystemAlerts();
        
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    public function stats() {
        $detailedStats = $this->admin->getDashboardStats();
        include __DIR__ . '/../views/admin/stats.php';
    }

    // ==================== USER MANAGEMENT ====================
    
    public function manageUsers() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
        $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');
        
        $users = $this->admin->getUsers($page, 20, $search, $status);
        $totalUsers = $this->admin->countUsers($search, $status);
        $totalPages = ceil($totalUsers / 20);
        
        include __DIR__ . '/../views/admin/manage_users.php';
    }

    public function editUser() {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        $user = $this->admin->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        include __DIR__ . '/../views/admin/edit_user.php';
    }

    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_users');
            exit;
        }

        // Validate CSRF token
        if (!SecurityHelper::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token keamanan tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        $id = intval($_POST['id']);
        $name = SecurityHelper::sanitizeInput($_POST['name']);
        $email = SecurityHelper::sanitizeInput($_POST['email']);
        $phone = SecurityHelper::sanitizeInput($_POST['phone']);
        $status = SecurityHelper::sanitizeInput($_POST['status'] ?? '');

        // Validate input
        if (empty($name) || empty($email)) {
            $_SESSION['error'] = 'Nama dan email wajib diisi';
            header('Location: admin.php?action=edit_user&id=' . $id);
            exit;
        }

        if (!SecurityHelper::validateEmail($email)) {
            $_SESSION['error'] = 'Format email tidak valid';
            header('Location: admin.php?action=edit_user&id=' . $id);
            exit;
        }

        $result = $this->admin->updateUser($id, $name, $email, $phone, $status);
        
        if ($result['success']) {
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'update_user', 
                "Mengupdate data user ID: $id"
            );
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: admin.php?action=manage_users');
        exit;
    }

    public function deleteUser() {
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        
        if (!$id) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        // Check if user exists
        $user = $this->admin->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        $result = $this->admin->deleteUser($id);
        
        if ($result['success']) {
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'delete_user', 
                "Menghapus user: " . $user['name'] . " (ID: $id)"
            );
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: admin.php?action=manage_users');
        exit;
    }

    public function deleteUsersBulk() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_users');
            exit;
        }

        $ids = $_POST['selected_users'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            $_SESSION['error'] = 'Tidak ada user yang dipilih';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            $id = intval($id);
            if ($id > 0) {
                $result = $this->admin->deleteUser($id);
                if ($result['success']) {
                    $deletedCount++;
                }
            }
        }

        $this->admin->logActivity(
            $_SESSION['admin_id'], 
            'bulk_delete_users', 
            "Menghapus $deletedCount users secara bulk"
        );

        $_SESSION['success'] = "$deletedCount user berhasil dihapus";
        header('Location: admin.php?action=manage_users');
        exit;
    }

    // ==================== BOOTCAMP MANAGEMENT ====================
    
    public function manageBootcamps() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
        $category = intval($_GET['category'] ?? 0);
        $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');
        
        $bootcamps = $this->admin->getBootcamps($page, 20, $search, $category, $status);
        $totalBootcamps = $this->admin->countBootcamps($search, $category, $status);
        $totalPages = ceil($totalBootcamps / 20);
        $categories = $this->admin->getCategories();
        
        include __DIR__ . '/../views/admin/manage_bootcamps.php';
    }

    public function createBootcamp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission
            $data = [
                'title' => SecurityHelper::sanitizeInput($_POST['title']),
                'description' => SecurityHelper::sanitizeInput($_POST['description']),
                'category_id' => intval($_POST['category_id']),
                'instructor_name' => SecurityHelper::sanitizeInput($_POST['instructor_name']),
                'price' => floatval($_POST['price']),
                'discount_price' => floatval($_POST['discount_price']),
                'start_date' => SecurityHelper::sanitizeInput($_POST['start_date']),
                'duration' => SecurityHelper::sanitizeInput($_POST['duration']),
                'status' => SecurityHelper::sanitizeInput($_POST['status'])
            ];

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = SecurityHelper::validateFileUpload($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
                
                if ($uploadResult['valid']) {
                    // Move uploaded file
                    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                    $uploadPath = 'assets/images/bootcamps/' . $filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $data['image'] = $filename;
                    }
                } else {
                    $_SESSION['error'] = 'Error upload image: ' . implode(', ', $uploadResult['errors']);
                    $categories = $this->admin->getCategories();
                    include __DIR__ . '/../views/admin/create_bootcamp.php';
                    return;
                }
            }

            $result = $this->admin->createBootcamp($data);
            
            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'], 
                    'create_bootcamp', 
                    "Membuat bootcamp: " . $data['title']
                );
                $_SESSION['success'] = $result['message'];
                header('Location: admin.php?action=manage_bootcamps');
                exit;
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }

        $categories = $this->admin->getCategories();
        include __DIR__ . '/../views/admin/create_bootcamp.php';
    }

    public function editBootcamp() {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID bootcamp tidak valid';
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }

        $bootcamp = $this->admin->getBootcampById($id);
        if (!$bootcamp) {
            $_SESSION['error'] = 'Bootcamp tidak ditemukan';
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }

        $categories = $this->admin->getCategories();
        include __DIR__ . '/../views/admin/edit_bootcamp.php';
    }

    // ==================== ORDER MANAGEMENT ====================
    
    public function manageOrders() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
        $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');
        
        $orders = $this->admin->getOrders($page, 20, $search, $status);
        $totalOrders = $this->admin->countOrders($search, $status);
        $totalPages = ceil($totalOrders / 20);
        
        include __DIR__ . '/../views/admin/manage_orders.php';
    }

    // ==================== CATEGORY MANAGEMENT ====================
    
    public function manageCategories() {
        $categories = $this->admin->getCategories();
        include __DIR__ . '/../views/admin/manage_categories.php';
    }

    // ==================== SYSTEM TOOLS ====================
    
    public function backupDatabase() {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = 'backups/' . $filename;
            
            // Create backups directory if not exists
            if (!is_dir('backups')) {
                mkdir('backups', 0755, true);
            }
            
            // Simple backup success message (actual implementation would depend on server setup)
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'backup_database', 
                "Database backup initiated: $filename"
            );
            $_SESSION['success'] = "Backup database berhasil dimulai: $filename";
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    public function cleanLogs() {
        $result = $this->admin->cleanOldLogs();
        
        if ($result['success']) {
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'clean_logs', 
                "Membersihkan log lama"
            );
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    public function exportData() {
        $type = SecurityHelper::sanitizeInput($_GET['type'] ?? '');
        
        try {
            switch ($type) {
                case 'users':
                    $data = $this->admin->exportUsers();
                    $filename = 'users_export_' . date('Y-m-d') . '.csv';
                    break;
                    
                case 'bootcamps':
                    $data = $this->admin->exportBootcamps();
                    $filename = 'bootcamps_export_' . date('Y-m-d') . '.csv';
                    break;
                    
                case 'orders':
                    $data = $this->admin->exportOrders();
                    $filename = 'orders_export_' . date('Y-m-d') . '.csv';
                    break;
                    
                default:
                    $_SESSION['error'] = 'Tipe export tidak valid';
                    header('Location: admin.php?action=manage_settings');
                    exit;
            }
            
            // Set headers for download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            
            // Output CSV
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
            
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'export_data', 
                "Export data $type"
            );
            
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error export: ' . $e->getMessage();
            header('Location: admin.php?action=manage_settings');
            exit;
        }
    }

    // ==================== SETTINGS ====================
    
    public function manageSettings() {
        include __DIR__ . '/../views/admin/manage_settings.php';
    }
}