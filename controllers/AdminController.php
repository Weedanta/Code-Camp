<?php
// controllers/AdminController.php - Enhanced Admin Controller

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../helper/SecurityHelper.php';

class AdminController
{
    private $admin;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->admin = new AdminModel($db);
    }

    // Helper method untuk include view dengan error handling
    private function includeView($viewPath, $data = [])
    {
        // Extract data ke variable
        if (is_array($data)) {
            extract($data);
        }

        // Path relatif dari root directory (admin.php)
        $fullPath = $viewPath;
        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            // Fallback jika file tidak ada
            echo "View file not found: " . $viewPath;
            exit;
        }
    }

    // ==================== AUTHENTICATION ====================

    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
            header('Location: admin.php?action=dashboard');
            exit;
        }

        $this->includeView('views/admin/login.php');
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=login');
            exit;
        }

        $email = SecurityHelper::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting
        if (!SecurityHelper::preventBruteForce($email, 1000)) {
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
            // Clear failed attempts on successful login
            SecurityHelper::clearFailedAttempts($email);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_last_activity'] = time();
            $_SESSION['admin_session_regenerated'] = time();

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
            // Record failed attempt SEBELUM log security event
            SecurityHelper::recordFailedAttempt($email);

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

    public function logout()
    {
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
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();

        header('Location: admin.php?action=login');
        exit;
    }

    // ==================== DASHBOARD ====================

    public function dashboard()
    {
        try {
            $stats = $this->admin->getDashboardStats();
            $recentActivities = $this->admin->getRecentActivities(10);
            $systemAlerts = $this->admin->getSystemAlerts();

            $this->includeView('views/admin/dashboard.php', compact('stats', 'recentActivities', 'systemAlerts'));
        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading dashboard';
            $stats = [];
            $recentActivities = [];
            $systemAlerts = [];
            $this->includeView('views/admin/dashboard.php', compact('stats', 'recentActivities', 'systemAlerts'));
        }
    }

    public function detailedStats()
    {
        try {
            $detailedStats = $this->admin->getDetailedStats();
            $this->includeView('views/admin/stats.php', compact('detailedStats'));
        } catch (Exception $e) {
            error_log("Stats error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading statistics';
            $detailedStats = [];
            $this->includeView('views/admin/stats.php', compact('detailedStats'));
        }
    }

    // ==================== USER MANAGEMENT ====================

    public function manageUsers()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');

            $users = $this->admin->getUsers($page, 20, $search, $status);
            $totalUsers = $this->admin->countUsers($search, $status);
            $totalPages = ceil($totalUsers / 20);

            $this->includeView('views/admin/manage_user.php', compact('users', 'totalUsers', 'totalPages', 'page', 'search', 'status'));
        } catch (Exception $e) {
            error_log("Manage users error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading users data';
            $users = [];
            $totalUsers = 0;
            $totalPages = 1;
            $page = 1;
            $search = '';
            $status = '';
            $this->includeView('views/admin/manage_user.php', compact('users', 'totalUsers', 'totalPages', 'page', 'search', 'status'));
        }
    }

    public function editUser()
    {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        try {
            $user = $this->admin->getUserById($id);
            if (!$user) {
                $_SESSION['error'] = 'User tidak ditemukan';
                header('Location: admin.php?action=manage_users');
                exit;
            }

            $this->includeView('views/admin/edit_user.php', compact('user'));
        } catch (Exception $e) {
            error_log("Edit user error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading user data';
            header('Location: admin.php?action=manage_users');
            exit;
        }
    }

    public function updateUser()
    {
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

        $id = intval($_POST['id'] ?? 0);
        $name = SecurityHelper::sanitizeInput($_POST['name'] ?? '');
        $email = SecurityHelper::sanitizeInput($_POST['email'] ?? '');
        $phone = SecurityHelper::sanitizeInput($_POST['phone'] ?? '');
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

        try {
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
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating user data';
        }

        header('Location: admin.php?action=manage_users');
        exit;
    }

    public function deleteUser()
    {
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        try {
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
        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting user';
        }

        header('Location: admin.php?action=manage_users');
        exit;
    }

    public function deleteUsersBulk()
    {
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

        try {
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
        } catch (Exception $e) {
            error_log("Bulk delete users error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting users';
        }

        header('Location: admin.php?action=manage_users');
        exit;
    }

    public function resetUserPassword()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: admin.php?action=manage_users');
            exit;
        }

        try {
            $result = $this->admin->resetUserPassword($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'reset_password',
                    "Reset password user ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Reset password error: " . $e->getMessage());
            $_SESSION['error'] = 'Error resetting password';
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'admin.php?action=manage_users';
        header('Location: ' . $referer);
        exit;
    }

    // ==================== BOOTCAMP MANAGEMENT ====================

    public function manageBootcamps()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $category = intval($_GET['category'] ?? 0);
            $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');

            $bootcamps = $this->admin->getBootcamps($page, 20, $search, $category, $status);
            $totalBootcamps = $this->admin->countBootcamps($search, $category, $status);
            $totalPages = ceil($totalBootcamps / 20);
            $categories = $this->admin->getCategories();

            $this->includeView('views/admin/manage_bootcamps.php', compact('bootcamps', 'totalBootcamps', 'totalPages', 'categories', 'page', 'search', 'category', 'status'));
        } catch (Exception $e) {
            error_log("Manage bootcamps error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading bootcamps data';
            $bootcamps = [];
            $totalBootcamps = 0;
            $totalPages = 1;
            $categories = [];
            $page = 1;
            $search = '';
            $category = 0;
            $status = '';
            $this->includeView('views/admin/manage_bootcamps.php', compact('bootcamps', 'totalBootcamps', 'totalPages', 'categories', 'page', 'search', 'category', 'status'));
        }
    }

    public function createBootcamp()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission
            $data = [
                'title' => SecurityHelper::sanitizeInput($_POST['title'] ?? ''),
                'slug' => SecurityHelper::generateSlug($_POST['title'] ?? ''),
                'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? ''),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'instructor_name' => SecurityHelper::sanitizeInput($_POST['instructor_name'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'discount_price' => floatval($_POST['discount_price'] ?? 0),
                'start_date' => SecurityHelper::sanitizeInput($_POST['start_date'] ?? ''),
                'duration' => SecurityHelper::sanitizeInput($_POST['duration'] ?? ''),
                'status' => SecurityHelper::sanitizeInput($_POST['status'] ?? 'draft'),
                'featured' => intval($_POST['featured'] ?? 0),
                'max_participants' => intval($_POST['max_participants'] ?? 0),
                'image' => '' // Default value
            ];

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = SecurityHelper::validateFileUpload($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);

                if ($uploadResult['valid']) {
                    // Create upload directory if not exists
                    $uploadDir = 'assets/images/bootcamps/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                    $uploadPath = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $data['image'] = $filename;
                    }
                } else {
                    $_SESSION['error'] = 'Error upload image: ' . implode(', ', $uploadResult['errors']);
                    $categories = $this->admin->getCategories();
                    $this->includeView('views/admin/create_bootcamp.php', compact('categories'));
                    return;
                }
            }

            try {
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
            } catch (Exception $e) {
                error_log("Create bootcamp error: " . $e->getMessage());
                $_SESSION['error'] = 'Error creating bootcamp';
            }
        }

        $categories = $this->admin->getCategories();
        $this->includeView('views/admin/create_bootcamp.php', compact('categories'));
    }

    public function editBootcamp()
    {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID bootcamp tidak valid';
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }

        try {
            $bootcamp = $this->admin->getBootcampById($id);
            if (!$bootcamp) {
                $_SESSION['error'] = 'Bootcamp tidak ditemukan';
                header('Location: admin.php?action=manage_bootcamps');
                exit;
            }

            $categories = $this->admin->getCategories();
            $this->includeView('views/admin/edit_bootcamps.php', compact('bootcamp', 'categories'));
        } catch (Exception $e) {
            error_log("Edit bootcamp error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading bootcamp data';
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }
    }

    public function updateBootcamp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $data = [
            'title' => SecurityHelper::sanitizeInput($_POST['title'] ?? ''),
            'slug' => SecurityHelper::generateSlug($_POST['title'] ?? ''),
            'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? ''),
            'category_id' => intval($_POST['category_id'] ?? 0),
            'instructor_name' => SecurityHelper::sanitizeInput($_POST['instructor_name'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'discount_price' => floatval($_POST['discount_price'] ?? 0),
            'start_date' => SecurityHelper::sanitizeInput($_POST['start_date'] ?? ''),
            'duration' => SecurityHelper::sanitizeInput($_POST['duration'] ?? ''),
            'status' => SecurityHelper::sanitizeInput($_POST['status'] ?? 'draft'),
            'featured' => intval($_POST['featured'] ?? 0),
            'max_participants' => intval($_POST['max_participants'] ?? 0)
        ];

        // Handle file upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = SecurityHelper::validateFileUpload($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);

            if ($uploadResult['valid']) {
                $uploadDir = 'assets/images/bootcamps/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = $filename;
                }
            }
        }

        try {
            $result = $this->admin->updateBootcamp($id, $data);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'update_bootcamp',
                    "Mengupdate bootcamp ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Update bootcamp error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating bootcamp';
        }

        header('Location: admin.php?action=manage_bootcamps');
        exit;
    }

    public function deleteBootcamp()
    {
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID bootcamp tidak valid';
            header('Location: admin.php?action=manage_bootcamps');
            exit;
        }

        try {
            $bootcamp = $this->admin->getBootcampById($id);
            if (!$bootcamp) {
                $_SESSION['error'] = 'Bootcamp tidak ditemukan';
                header('Location: admin.php?action=manage_bootcamps');
                exit;
            }

            $result = $this->admin->deleteBootcamp($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_bootcamp',
                    "Menghapus bootcamp: " . $bootcamp['title'] . " (ID: $id)"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete bootcamp error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting bootcamp';
        }

        header('Location: admin.php?action=manage_bootcamps');
        exit;
    }

    public function toggleFeaturedBootcamp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = intval($_GET['id'] ?? 0);
        $featured = $_GET['featured'] === 'true' ? 1 : 0;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        try {
            $result = $this->admin->toggleBootcampFeatured($id, $featured);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'toggle_featured',
                    "Toggle featured bootcamp ID: $id menjadi " . ($featured ? 'featured' : 'not featured')
                );
            }

            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Toggle featured error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error updating featured status']);
        }
        exit;
    }

    // ==================== CATEGORY MANAGEMENT ====================

    public function manageCategories()
    {
        try {
            $categories = $this->admin->getCategories();
            $this->includeView('views/admin/manage_categories.php', compact('categories'));
        } catch (Exception $e) {
            error_log("Manage categories error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading categories';
            $categories = [];
            $this->includeView('views/admin/manage_categories.php', compact('categories'));
        }
    }

    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => SecurityHelper::sanitizeInput($_POST['name'] ?? ''),
                'slug' => SecurityHelper::generateSlug($_POST['name'] ?? ''),
                'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? ''),
                'status' => 'active',
                'sort_order' => intval($_POST['sort_order'] ?? 0)
            ];

            if (empty($data['name'])) {
                $_SESSION['error'] = 'Nama kategori wajib diisi';
                header('Location: admin.php?action=manage_categories');
                exit;
            }

            try {
                $result = $this->admin->createCategory($data);

                if ($result['success']) {
                    $this->admin->logActivity(
                        $_SESSION['admin_id'],
                        'create_category',
                        "Membuat kategori: " . $data['name']
                    );
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            } catch (Exception $e) {
                error_log("Create category error: " . $e->getMessage());
                $_SESSION['error'] = 'Error creating category';
            }

            header('Location: admin.php?action=manage_categories');
            exit;
        }

        $this->includeView('views/admin/create_category.php');
    }

    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_categories');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $data = [
            'name' => SecurityHelper::sanitizeInput($_POST['name'] ?? ''),
            'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0)
        ];

        if (empty($data['name'])) {
            $_SESSION['error'] = 'Nama kategori wajib diisi';
            header('Location: admin.php?action=manage_categories');
            exit;
        }

        try {
            $result = $this->admin->updateCategory($id, $data);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'update_category',
                    "Mengupdate kategori ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Update category error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating category';
        }

        header('Location: admin.php?action=manage_categories');
        exit;
    }

    public function deleteCategory()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID kategori tidak valid';
            header('Location: admin.php?action=manage_categories');
            exit;
        }

        try {
            $result = $this->admin->deleteCategory($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_category',
                    "Menghapus kategori ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete category error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting category';
        }

        header('Location: admin.php?action=manage_categories');
        exit;
    }

    // ==================== ORDER MANAGEMENT ====================

    public function manageOrders()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');

            $orders = $this->admin->getOrders($page, 20, $search, $status);
            $totalOrders = $this->admin->countOrders($search, $status);
            $totalPages = ceil($totalOrders / 20);

            $this->includeView('views/admin/manage_order.php', compact('orders', 'totalOrders', 'totalPages', 'page', 'search', 'status'));
        } catch (Exception $e) {
            error_log("Manage orders error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading orders data';
            $orders = [];
            $totalOrders = 0;
            $totalPages = 1;
            $page = 1;
            $search = '';
            $status = '';
            $this->includeView('views/admin/manage_order.php', compact('orders', 'totalOrders', 'totalPages', 'page', 'search', 'status'));
        }
    }

    public function viewOrder()
    {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID order tidak valid';
            header('Location: admin.php?action=manage_orders');
            exit;
        }

        try {
            $order = $this->admin->getOrderById($id);
            if (!$order) {
                $_SESSION['error'] = 'Order tidak ditemukan';
                header('Location: admin.php?action=manage_orders');
                exit;
            }

            $orderItems = $this->admin->getOrderItems($id);
            $this->includeView('views/admin/view_order.php', compact('order', 'orderItems'));
        } catch (Exception $e) {
            error_log("View order error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading order data';
            header('Location: admin.php?action=manage_orders');
            exit;
        }
    }

    public function updateOrderStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_orders');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $status = SecurityHelper::sanitizeInput($_POST['status'] ?? '');

        if (!$id || empty($status)) {
            $_SESSION['error'] = 'Data tidak valid';
            header('Location: admin.php?action=manage_orders');
            exit;
        }

        try {
            $result = $this->admin->updateOrderStatus($id, $status);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'update_order_status',
                    "Mengubah status order ID $id menjadi $status"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Update order status error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating order status';
        }

        header('Location: admin.php?action=manage_orders');
        exit;
    }

    // ==================== REVIEW MANAGEMENT ====================

    public function manageReviews()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');

            $reviews = $this->admin->getReviews($page, 20, $search, $status);
            $totalReviews = $this->admin->countReviews($search, $status);
            $totalPages = ceil($totalReviews / 20);

            $this->includeView('views/admin/manage_reviews.php', compact('reviews', 'totalReviews', 'totalPages', 'page', 'search', 'status'));
        } catch (Exception $e) {
            error_log("Manage reviews error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading reviews data';
            $reviews = [];
            $totalReviews = 0;
            $totalPages = 1;
            $page = 1;
            $search = '';
            $status = '';
            $this->includeView('views/admin/manage_reviews.php', compact('reviews', 'totalReviews', 'totalPages', 'page', 'search', 'status'));
        }
    }

    public function approveReview()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID review tidak valid';
            header('Location: admin.php?action=manage_reviews');
            exit;
        }

        try {
            $result = $this->admin->updateReviewStatus($id, 'published');

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'approve_review',
                    "Menyetujui review ID: $id"
                );
                $_SESSION['success'] = 'Review berhasil disetujui';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Approve review error: " . $e->getMessage());
            $_SESSION['error'] = 'Error approving review';
        }

        header('Location: admin.php?action=manage_reviews');
        exit;
    }

    public function rejectReview()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID review tidak valid';
            header('Location: admin.php?action=manage_reviews');
            exit;
        }

        try {
            $result = $this->admin->updateReviewStatus($id, 'rejected');

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'reject_review',
                    "Menolak review ID: $id"
                );
                $_SESSION['success'] = 'Review berhasil ditolak';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Reject review error: " . $e->getMessage());
            $_SESSION['error'] = 'Error rejecting review';
        }

        header('Location: admin.php?action=manage_reviews');
        exit;
    }

    public function deleteReview()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID review tidak valid';
            header('Location: admin.php?action=manage_reviews');
            exit;
        }

        try {
            $result = $this->admin->deleteReview($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_review',
                    "Menghapus review ID: $id"
                );
                $_SESSION['success'] = 'Review berhasil dihapus';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete review error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting review';
        }

        header('Location: admin.php?action=manage_reviews');
        exit;
    }

    public function bulkApproveReviews()
    {
        try {
            $result = $this->admin->bulkApproveReviews();

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'bulk_approve_reviews',
                    "Bulk approve " . $result['count'] . " reviews"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Bulk approve reviews error: " . $e->getMessage());
            $_SESSION['error'] = 'Error bulk approving reviews';
        }

        header('Location: admin.php?action=manage_reviews');
        exit;
    }

    // ==================== FORUM MANAGEMENT ====================

    public function manageForum()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $status = SecurityHelper::sanitizeInput($_GET['status'] ?? '');

            $posts = $this->admin->getForumPosts($page, 20, $search, $status);
            $totalPosts = $this->admin->countForumPosts($search, $status);
            $totalPages = ceil($totalPosts / 20);

            $this->includeView('views/admin/manage_forum.php', compact('posts', 'totalPosts', 'totalPages', 'page', 'search', 'status'));
        } catch (Exception $e) {
            error_log("Manage forum error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading forum data';
            $posts = [];
            $totalPosts = 0;
            $totalPages = 1;
            $page = 1;
            $search = '';
            $status = '';
            $this->includeView('views/admin/manage_forum.php', compact('posts', 'totalPosts', 'totalPages', 'page', 'search', 'status'));
        }
    }

    public function moderatePost()
    {
        $id = intval($_GET['id'] ?? 0);
        $action = SecurityHelper::sanitizeInput($_GET['moderate'] ?? '');

        if (!$id || empty($action)) {
            $_SESSION['error'] = 'Data tidak valid';
            header('Location: admin.php?action=manage_forum');
            exit;
        }

        try {
            if ($action === 'pin') {
                $result = $this->admin->pinForumPost($id, true);
                $message = 'Post berhasil di-pin';
            } elseif ($action === 'unpin') {
                $result = $this->admin->pinForumPost($id, false);
                $message = 'Post berhasil di-unpin';
            } elseif ($action === 'lock') {
                $result = $this->admin->lockForumPost($id, true);
                $message = 'Post berhasil dikunci';
            } elseif ($action === 'unlock') {
                $result = $this->admin->lockForumPost($id, false);
                $message = 'Post berhasil dibuka';
            } else {
                $_SESSION['error'] = 'Aksi tidak valid';
                header('Location: admin.php?action=manage_forum');
                exit;
            }

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'moderate_forum_post',
                    "Moderasi post ID $id: $action"
                );
                $_SESSION['success'] = $message;
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Moderate post error: " . $e->getMessage());
            $_SESSION['error'] = 'Error moderating post';
        }

        header('Location: admin.php?action=manage_forum');
        exit;
    }

    public function deleteForumPost()
    {
        $id = intval($_GET['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID post tidak valid';
            header('Location: admin.php?action=manage_forum');
            exit;
        }

        try {
            $result = $this->admin->deleteForumPost($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_forum_post',
                    "Menghapus forum post ID: $id"
                );
                $_SESSION['success'] = 'Post berhasil dihapus';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete forum post error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting forum post';
        }

        header('Location: admin.php?action=manage_forum');
        exit;
    }

    // ==================== SETTINGS MANAGEMENT ====================

    public function manageSettings()
    {
        try {
            $settings = $this->admin->getSettings();
            $this->includeView('views/admin/manage_settings.php', compact('settings'));
        } catch (Exception $e) {
            error_log("Manage settings error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading settings';
            $settings = [];
            $this->includeView('views/admin/manage_settings.php', compact('settings'));
        }
    }

    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_settings');
            exit;
        }

        try {
            $updatedCount = 0;
            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token') {
                    $sanitizedValue = SecurityHelper::sanitizeInput($value);
                    $result = $this->admin->updateSetting($key, $sanitizedValue);
                    if ($result['success']) {
                        $updatedCount++;
                    }
                }
            }

            $this->admin->logActivity(
                $_SESSION['admin_id'],
                'update_settings',
                "Mengupdate $updatedCount pengaturan sistem"
            );

            $_SESSION['success'] = "Berhasil mengupdate $updatedCount pengaturan";
        } catch (Exception $e) {
            error_log("Update settings error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating settings';
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    // ==================== SYSTEM TOOLS ====================

    public function backupDatabase()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = 'backups/' . $filename;

            // Create backups directory if not exists
            if (!is_dir('backups')) {
                mkdir('backups', 0755, true);
            }

            $result = $this->admin->createDatabaseBackup($backupPath);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'backup_database',
                    "Database backup created: $filename"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Backup database error: " . $e->getMessage());
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    public function cleanLogs()
    {
        try {
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
        } catch (Exception $e) {
            error_log("Clean logs error: " . $e->getMessage());
            $_SESSION['error'] = 'Error cleaning logs';
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    public function exportData()
    {
        $type = SecurityHelper::sanitizeInput($_GET['type'] ?? '');

        if (empty($type)) {
            $_SESSION['error'] = 'Tipe export tidak valid';
            header('Location: admin.php?action=manage_settings');
            exit;
        }

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
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
            error_log("Export data error: " . $e->getMessage());
            $_SESSION['error'] = 'Error export: ' . $e->getMessage();
            header('Location: admin.php?action=manage_settings');
            exit;
        }
    }

    public function optimizeDatabase()
    {
        try {
            $result = $this->admin->optimizeDatabase();

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'optimize_database',
                    "Database optimization completed"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Optimize database error: " . $e->getMessage());
            $_SESSION['error'] = 'Error optimizing database';
        }

        header('Location: admin.php?action=manage_settings');
        exit;
    }

    public function checkSystemHealth()
    {
        try {
            $healthCheck = $this->admin->checkSystemHealth();

            $this->admin->logActivity(
                $_SESSION['admin_id'],
                'system_health_check',
                "System health check performed"
            );

            header('Content-Type: application/json');
            echo json_encode($healthCheck);
        } catch (Exception $e) {
            error_log("System health check error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error checking system health']);
        }
        exit;
    }

    // ==================== ADMIN PROFILE ====================

    public function showProfile()
    {
        try {
            $admin = $this->admin->getAdminById($_SESSION['admin_id']);
            $this->includeView('views/admin/profile.php', compact('admin'));
        } catch (Exception $e) {
            error_log("Show profile error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading profile';
            $admin = [];
            $this->includeView('views/admin/profile.php', compact('admin'));
        }
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=profile');
            exit;
        }

        $id = $_SESSION['admin_id'];
        $data = [
            'name' => SecurityHelper::sanitizeInput($_POST['name'] ?? ''),
            'email' => SecurityHelper::sanitizeInput($_POST['email'] ?? ''),
            'phone' => SecurityHelper::sanitizeInput($_POST['phone'] ?? ''),
            'department' => SecurityHelper::sanitizeInput($_POST['department'] ?? ''),
            'timezone' => SecurityHelper::sanitizeInput($_POST['timezone'] ?? ''),
            'language' => SecurityHelper::sanitizeInput($_POST['language'] ?? '')
        ];

        try {
            $result = $this->admin->updateAdminProfile($id, $data);

            if ($result['success']) {
                $_SESSION['admin_name'] = $data['name'];
                $_SESSION['admin_email'] = $data['email'];

                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'update_profile',
                    "Update admin profile"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            $_SESSION['error'] = 'Error updating profile';
        }

        header('Location: admin.php?action=profile');
        exit;
    }

    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=profile');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok';
            header('Location: admin.php?action=profile');
            exit;
        }

        try {
            $result = $this->admin->changeAdminPassword($_SESSION['admin_id'], $currentPassword, $newPassword);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'change_password',
                    "Admin password changed"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            $_SESSION['error'] = 'Error changing password';
        }

        header('Location: admin.php?action=profile');
        exit;
    }

    // ==================== ACTIVITY LOG ====================

    public function activityLog()
    {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $search = SecurityHelper::sanitizeInput($_GET['search'] ?? '');
            $activity_type = SecurityHelper::sanitizeInput($_GET['activity_type'] ?? '');
            $admin_id = intval($_GET['admin_id'] ?? 0);
            $date_from = SecurityHelper::sanitizeInput($_GET['date_from'] ?? '');
            $date_to = SecurityHelper::sanitizeInput($_GET['date_to'] ?? '');

            $activities = $this->admin->getActivityLog($page, 50, $search, $activity_type, $admin_id, $date_from, $date_to);
            $totalActivities = $this->admin->countActivityLog($search, $activity_type, $admin_id, $date_from, $date_to);
            $totalPages = ceil($totalActivities / 50);

            $this->includeView('views/admin/activity_logs.php', compact('activities', 'totalActivities', 'totalPages', 'page', 'search', 'activity_type', 'admin_id', 'date_from', 'date_to'));
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading activity log';
            $activities = [];
            $totalActivities = 0;
            $totalPages = 1;
            $page = 1;
            $search = '';
            $activity_type = '';
            $admin_id = 0;
            $date_from = '';
            $date_to = '';
            $this->includeView('views/admin/activity_logs.php', compact('activities', 'totalActivities', 'totalPages', 'page', 'search', 'activity_type', 'admin_id', 'date_from', 'date_to'));
        }
    }

    // ==================== AJAX ENDPOINTS ====================

    public function ajaxGetUser()
    {
        $id = intval($_GET['id'] ?? 0);

        try {
            $user = $this->admin->getUserById($id);
            header('Content-Type: application/json');
            echo json_encode($user ?: null);
        } catch (Exception $e) {
            error_log("Ajax get user error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(null);
        }
        exit;
    }

    public function ajaxGetBootcamp()
    {
        $id = intval($_GET['id'] ?? 0);

        try {
            $bootcamp = $this->admin->getBootcampById($id);
            header('Content-Type: application/json');
            echo json_encode($bootcamp ?: null);
        } catch (Exception $e) {
            error_log("Ajax get bootcamp error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(null);
        }
        exit;
    }

    public function ajaxDashboardStats()
    {
        try {
            $stats = $this->admin->getDashboardStats();
            header('Content-Type: application/json');
            echo json_encode($stats);
        } catch (Exception $e) {
            error_log("Ajax dashboard stats error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([]);
        }
        exit;
    }



    /**
     * Handle AJAX request for chat room details
     */
    public function ajaxGetChatRoom()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $roomId = intval($_GET['room_id'] ?? 0);

        if (!$roomId) {
            echo json_encode(['success' => false, 'message' => 'Room ID required']);
            exit;
        }

        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            $room = $chat->getRoomById($roomId);
            $messages = $chat->getRoomMessages($roomId, 50);

            if ($room) {
                echo json_encode([
                    'success' => true,
                    'room' => $room,
                    'messages' => $messages
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Room not found']);
            }
        } catch (Exception $e) {
            error_log("AJAX get chat room error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal error']);
        }
    }

    /**
     * Handle AJAX request for chat statistics
     */
    public function ajaxChatStats()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $stats = $this->getChatStats();
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            error_log("AJAX chat stats error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal error']);
        }
    }

    /**
     * Export chat data
     */
    public function exportChatData()
    {
        AdminMiddleware::checkPermission('export_data');

        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            // Get all rooms with messages
            $rooms = $chat->getAllActiveRooms();
            $exportData = [];

            foreach ($rooms as $room) {
                $messages = $chat->getRoomMessages($room['id'], 1000); // Last 1000 messages

                $exportData[] = [
                    'room_id' => $room['id'],
                    'user_name' => $room['user_name'],
                    'user_email' => $room['user_email'],
                    'admin_name' => $room['admin_name'],
                    'created_at' => $room['created_at'],
                    'status' => $room['status'],
                    'messages' => $messages
                ];
            }

            // Set headers for download
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="chat_export_' . date('Y-m-d_H-i-s') . '.json"');

            echo json_encode($exportData, JSON_PRETTY_PRINT);

            // Log activity
            $this->admin->logActivity(
                $_SESSION['admin_id'],
                'export_data',
                'Admin mengexport data chat',
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        } catch (Exception $e) {
            error_log("Export chat data error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal mengexport data chat';
            header('Location: admin.php?action=manage_chat');
        }
    }


    /**
     * Clean old chat messages
     */


    /**
     * View chat room details
     */
    public function viewChatRoom()
    {
        AdminMiddleware::checkPermission('manage_chat');

        $roomId = intval($_GET['id'] ?? 0);

        if (!$roomId) {
            $_SESSION['error'] = 'Room ID tidak valid';
            header('Location: admin.php?action=manage_chat');
            exit;
        }

        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            $room = $chat->getRoomById($roomId);
            if (!$room) {
                $_SESSION['error'] = 'Room tidak ditemukan';
                header('Location: admin.php?action=manage_chat');
                exit;
            }

            $messages = $chat->getRoomMessages($roomId, 100);

            // Mark messages as read
            $chat->markMessagesAsRead($roomId, 'admin', $_SESSION['admin_id']);

            // Log activity
            $this->admin->logActivity(
                $_SESSION['admin_id'],
                'view_chat_room',
                'Admin melihat detail room chat ID: ' . $roomId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );

            include 'views/admin/chat_detail.php';
        } catch (Exception $e) {
            error_log("View chat room error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal memuat detail room chat';
            header('Location: admin.php?action=manage_chat');
        }
    }

    /**
     * Search chat messages
     */
    public function searchChatMessages()
    {
        AdminMiddleware::checkPermission('manage_chat');

        $query = SecurityHelper::sanitizeInput($_GET['q'] ?? '');
        $roomId = intval($_GET['room_id'] ?? 0);

        if (empty($query)) {
            header('Location: admin.php?action=manage_chat');
            exit;
        }

        try {
            $database = new Database();
            $conn = $database->getConnection();

            $sql = "
            SELECT m.*, r.user_id, u.name as user_name, u.alamat_email,
                   CASE 
                       WHEN m.sender_type = 'user' THEN u.name 
                       WHEN m.sender_type = 'admin' THEN a.name 
                   END as sender_name
            FROM chat_messages m
            JOIN chat_rooms r ON m.room_id = r.id
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN admins a ON m.sender_type = 'admin' AND m.sender_id = a.id
            WHERE m.message LIKE ?
        ";

            $params = ["%$query%"];

            if ($roomId) {
                $sql .= " AND m.room_id = ?";
                $params[] = $roomId;
            }

            $sql .= " ORDER BY m.created_at DESC LIMIT 100";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log activity
            $this->admin->logActivity(
                $_SESSION['admin_id'],
                'search_chat',
                'Admin mencari pesan chat: ' . $query,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );

            include 'views/admin/chat_search.php';
        } catch (Exception $e) {
            error_log("Search chat messages error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal mencari pesan';
            header('Location: admin.php?action=manage_chat');
        }
    }

    /**
     * Get chat metrics for analytics
     */
    public function getChatMetrics()
    {
        try {
            $database = new Database();
            $conn = $database->getConnection();

            // Messages per day (last 7 days)
            $stmt = $conn->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM chat_messages 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
            $stmt->execute();
            $messagesPerDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Response times (average time between user message and admin response)
            $stmt = $conn->prepare("
            SELECT AVG(
                TIMESTAMPDIFF(MINUTE, 
                    (SELECT created_at FROM chat_messages m2 
                     WHERE m2.room_id = m1.room_id 
                     AND m2.sender_type = 'user' 
                     AND m2.created_at < m1.created_at 
                     ORDER BY m2.created_at DESC LIMIT 1),
                    m1.created_at
                )
            ) as avg_response_time_minutes
            FROM chat_messages m1 
            WHERE m1.sender_type = 'admin' 
            AND m1.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
            $stmt->execute();
            $avgResponseTime = $stmt->fetchColumn() ?? 0;

            // Most active users
            $stmt = $conn->prepare("
            SELECT u.name, u.alamat_email, COUNT(m.id) as message_count
            FROM users u
            JOIN chat_rooms r ON u.id = r.user_id
            JOIN chat_messages m ON r.id = m.room_id AND m.sender_type = 'user'
            WHERE m.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY u.id
            ORDER BY message_count DESC
            LIMIT 10
        ");
            $stmt->execute();
            $activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'messages_per_day' => $messagesPerDay,
                'avg_response_time' => round($avgResponseTime, 2),
                'active_users' => $activeUsers
            ];
        } catch (Exception $e) {
            error_log("Get chat metrics error: " . $e->getMessage());
            return [
                'messages_per_day' => [],
                'avg_response_time' => 0,
                'active_users' => []
            ];
        }
    }
    // ==================== CHAT MANAGEMENT METHODS ====================



    /**
     * Show chat management interface
     */
    public function manageChat()
    {
        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            // Get all active chat rooms
            $rooms = $chat->getAllActiveRooms();

            // Log activity
            if (method_exists($this->admin, 'logActivity')) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'view_chat',
                    'Admin melihat halaman chat management',
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
            }

            // Include the chat view
            include 'views/admin/chat.php';
        } catch (Exception $e) {
            error_log("Manage chat error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal memuat halaman chat';
            header('Location: admin.php?action=dashboard');
            exit;
        }
    }

    /**
     * Get chat statistics for dashboard
     */
    public function getChatStats()
    {
        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            return $chat->getChatStats();
        } catch (Exception $e) {
            error_log("Get chat stats error: " . $e->getMessage());
            return [
                'active_rooms' => 0,
                'messages_today' => 0,
                'unread_messages' => 0
            ];
        }
    }

    /**
     * Bulk close chat rooms
     */
    public function bulkCloseChatRooms()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_chat');
            exit;
        }

        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            // Close all active rooms
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("UPDATE chat_rooms SET status = 'closed', updated_at = NOW() WHERE status = 'active'");
            $success = $stmt->execute();
            $count = $stmt->rowCount();

            if ($success) {
                $_SESSION['success'] = "$count room chat berhasil ditutup";

                // Log activity
                if (method_exists($this->admin, 'logActivity')) {
                    $this->admin->logActivity(
                        $_SESSION['admin_id'],
                        'bulk_close_chat',
                        "Admin menutup $count room chat secara bulk",
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        $_SERVER['HTTP_USER_AGENT'] ?? null
                    );
                }
            } else {
                $_SESSION['error'] = 'Gagal menutup room chat';
            }
        } catch (Exception $e) {
            error_log("Bulk close chat rooms error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal menutup room chat';
        }

        header('Location: admin.php?action=manage_chat');
    }

    /**
     * Clean old chat messages
     */
    public function cleanOldChatMessages()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_chat');
            exit;
        }

        $days = intval($_POST['days'] ?? 30);

        try {
            $database = new Database();
            $conn = $database->getConnection();

            // Delete messages older than specified days
            $stmt = $conn->prepare("
            DELETE FROM chat_messages 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
            $stmt->execute([$days]);

            $deletedMessages = $stmt->rowCount();

            // Delete empty rooms
            $stmt = $conn->prepare("
            DELETE r FROM chat_rooms r 
            LEFT JOIN chat_messages m ON r.id = m.room_id 
            WHERE m.id IS NULL AND r.status = 'closed'
        ");
            $stmt->execute();

            $deletedRooms = $stmt->rowCount();

            $_SESSION['success'] = "Berhasil menghapus $deletedMessages pesan lama dan $deletedRooms room kosong";

            // Log activity
            if (method_exists($this->admin, 'logActivity')) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'clean_chat',
                    "Admin membersihkan $deletedMessages pesan chat lama (>$days hari)",
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
            }
        } catch (Exception $e) {
            error_log("Clean old chat messages error: " . $e->getMessage());
            $_SESSION['error'] = 'Gagal membersihkan pesan lama';
        }

        header('Location: admin.php?action=manage_chat');
    }

    /**
     * AJAX - Get chat unread count
     */
    public function ajaxChatUnreadCount()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            require_once 'models/Chat.php';
            $chat = new Chat();

            $count = $chat->getTotalUnreadForAdmin();
            echo json_encode(['success' => true, 'count' => $count]);
        } catch (Exception $e) {
            error_log("AJAX chat unread count error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Internal error']);
        }
    }

    /**
     * Handle invalid access attempts
     */
    public function logInvalidAccess($action)
    {
        try {
            if (method_exists($this->admin, 'logActivity')) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'] ?? 0,
                    'invalid_access',
                    "Attempt to access invalid action: $action",
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
            }
        } catch (Exception $e) {
            error_log("Log invalid access error: " . $e->getMessage());
        }
    }

    // ==================== FEATURES MANAGEMENT ====================
    
    public function manageFeatures()
    {
        try {
            // Get statistics for all features
            $wishlistStats = $this->admin->getWishlistStats();
            $cvStats = $this->admin->getCVStats();
            $todoStats = $this->admin->getTodoStats();

            // Get recent activities
            $recentWishlists = $this->admin->getRecentWishlists(10);
            $recentCVs = $this->admin->getRecentCVs(10);
            $recentTodos = $this->admin->getRecentTodos(10);

            $this->includeView('views/admin/manage_features.php', compact(
                'wishlistStats', 'cvStats', 'todoStats',
                'recentWishlists', 'recentCVs', 'recentTodos'
            ));
        } catch (Exception $e) {
            error_log("Manage features error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading features data';
            
            // Set empty defaults
            $wishlistStats = ['total' => 0, 'today' => 0];
            $cvStats = ['total' => 0, 'today' => 0];
            $todoStats = ['total' => 0, 'completed' => 0, 'pending' => 0];
            $recentWishlists = [];
            $recentCVs = [];
            $recentTodos = [];
            
            $this->includeView('views/admin/manage_features.php', compact(
                'wishlistStats', 'cvStats', 'todoStats',
                'recentWishlists', 'recentCVs', 'recentTodos'
            ));
        }
    }

    // ==================== WISHLIST MANAGEMENT ====================
    
    public function exportFeatures()
    {
        $type = SecurityHelper::sanitizeInput($_GET['type'] ?? '');
        
        if (!in_array($type, ['wishlist', 'cv', 'todo'])) {
            $_SESSION['error'] = 'Invalid export type';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            switch ($type) {
                case 'wishlist':
                    $data = $this->admin->getWishlistExportData();
                    $filename = 'wishlist_export_' . date('Y-m-d_H-i-s') . '.csv';
                    break;
                case 'cv':
                    $data = $this->admin->getCVExportData();
                    $filename = 'cv_export_' . date('Y-m-d_H-i-s') . '.csv';
                    break;
                case 'todo':
                    $data = $this->admin->getTodoExportData();
                    $filename = 'todo_export_' . date('Y-m-d_H-i-s') . '.csv';
                    break;
            }

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Create file pointer connected to output stream
            $output = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
                'export_features',
                "Export $type features data"
            );

            exit;
        } catch (Exception $e) {
            error_log("Export features error: " . $e->getMessage());
            $_SESSION['error'] = 'Error exporting data: ' . $e->getMessage();
            header('Location: admin.php?action=manage_features');
            exit;
        }
    }

    public function clearOldWishlists()
    {
        try {
            $result = $this->admin->clearOldWishlists(30); // Clear wishlists older than 30 days

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'clear_old_wishlists',
                    "Cleared " . $result['count'] . " old wishlist items"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Clear old wishlists error: " . $e->getMessage());
            $_SESSION['error'] = 'Error clearing old wishlists';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    public function removeWishlist()
    {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Invalid wishlist ID';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            $result = $this->admin->removeWishlistItem($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'remove_wishlist',
                    "Removed wishlist item ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Remove wishlist error: " . $e->getMessage());
            $_SESSION['error'] = 'Error removing wishlist item';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    // ==================== CV MANAGEMENT ====================
    
    public function backupCVData()
    {
        try {
            $result = $this->admin->backupCVData();

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'backup_cv_data',
                    "CV data backup created: " . $result['filename']
                );
                
                // Force download the backup file
                $filepath = $result['filepath'];
                if (file_exists($filepath)) {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    header('Content-Length: ' . filesize($filepath));
                    readfile($filepath);
                    unlink($filepath); // Remove file after download
                    exit;
                }
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Backup CV data error: " . $e->getMessage());
            $_SESSION['error'] = 'Error creating CV backup';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    public function viewCV()
    {
        $userId = intval($_GET['user_id'] ?? 0);
        if (!$userId) {
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            $cvData = $this->admin->getCVByUserId($userId);
            if (!$cvData) {
                $_SESSION['error'] = 'CV not found';
                header('Location: admin.php?action=manage_features');
                exit;
            }

            $this->includeView('views/admin/view_cv.php', compact('cvData'));
        } catch (Exception $e) {
            error_log("View CV error: " . $e->getMessage());
            $_SESSION['error'] = 'Error loading CV data';
            header('Location: admin.php?action=manage_features');
            exit;
        }
    }

    public function deleteCV()
    {
        $userId = intval($_GET['user_id'] ?? 0);
        if (!$userId) {
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            $result = $this->admin->deleteCVByUserId($userId);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_cv',
                    "Deleted CV for user ID: $userId"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete CV error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting CV';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    // ==================== TODO MANAGEMENT ====================
    
    public function clearCompletedTodos()
    {
        try {
            $result = $this->admin->clearCompletedTodos();

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'clear_completed_todos',
                    "Cleared " . $result['count'] . " completed todos"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Clear completed todos error: " . $e->getMessage());
            $_SESSION['error'] = 'Error clearing completed todos';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    public function deleteTodo()
    {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Invalid todo ID';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            $result = $this->admin->deleteTodoItem($id);

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    'delete_todo',
                    "Deleted todo item ID: $id"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Delete todo error: " . $e->getMessage());
            $_SESSION['error'] = 'Error deleting todo item';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    // ==================== BULK ACTIONS ====================
    
    public function bulkActionFeatures()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=manage_features');
            exit;
        }

        $action = SecurityHelper::sanitizeInput($_POST['bulk_action'] ?? '');
        $type = SecurityHelper::sanitizeInput($_POST['type'] ?? '');
        $ids = $_POST['selected_ids'] ?? [];

        if (empty($action) || empty($type) || empty($ids)) {
            $_SESSION['error'] = 'Invalid bulk action parameters';
            header('Location: admin.php?action=manage_features');
            exit;
        }

        try {
            switch ($type) {
                case 'wishlist':
                    $result = $this->bulkWishlistAction($action, $ids);
                    break;
                case 'cv':
                    $result = $this->bulkCVAction($action, $ids);
                    break;
                case 'todo':
                    $result = $this->bulkTodoAction($action, $ids);
                    break;
                default:
                    throw new Exception('Invalid feature type');
            }

            if ($result['success']) {
                $this->admin->logActivity(
                    $_SESSION['admin_id'],
                    "bulk_action_$type",
                    "Bulk $action on " . count($ids) . " $type items"
                );
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Bulk action features error: " . $e->getMessage());
            $_SESSION['error'] = 'Error performing bulk action';
        }

        header('Location: admin.php?action=manage_features');
        exit;
    }

    private function bulkWishlistAction($action, $ids)
    {
        switch ($action) {
            case 'delete':
                return $this->admin->bulkDeleteWishlists($ids);
            case 'export':
                return $this->admin->bulkExportWishlists($ids);
            default:
                return ['success' => false, 'message' => 'Invalid wishlist action'];
        }
    }

    private function bulkCVAction($action, $ids)
    {
        switch ($action) {
            case 'delete':
                return $this->admin->bulkDeleteCVs($ids);
            case 'backup':
                return $this->admin->bulkBackupCVs($ids);
            case 'export':
                return $this->admin->bulkExportCVs($ids);
            default:
                return ['success' => false, 'message' => 'Invalid CV action'];
        }
    }

    private function bulkTodoAction($action, $ids)
    {
        switch ($action) {
            case 'delete':
                return $this->admin->bulkDeleteTodos($ids);
            case 'complete':
                return $this->admin->bulkCompleteTodos($ids);
            case 'export':
                return $this->admin->bulkExportTodos($ids);
            default:
                return ['success' => false, 'message' => 'Invalid todo action'];
        }
    }
}
