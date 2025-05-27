<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

class AdminController {
    private $admin;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->admin = new Admin($db);
    }

    // Check if user is admin
    private function isAdmin() {
        return isset($_SESSION['admin_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    // Sanitize input to prevent XSS
    private function sanitizeInput($data) {
        return SecurityHelper::sanitizeInput($data);
    }

    // Validate email
    private function validateEmail($email) {
        return SecurityHelper::validateEmail($email);
    }

    // Admin login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Check if email contains 'admin'
            if (strpos(strtolower($email), 'admin') === false) {
                $_SESSION['error'] = 'Akses ditolak. Email harus mengandung kata "admin".';
                include __DIR__ . '/../views/admin/login.php';
                return;
            }

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Email dan password harus diisi';
                include __DIR__ . '/../views/admin/login.php';
                return;
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Format email tidak valid';
                include __DIR__ . '/../views/admin/login.php';
                return;
            }

            // Rate limiting
            if (!SecurityHelper::preventBruteForce($email)) {
                $_SESSION['error'] = 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.';
                include __DIR__ . '/../views/admin/login.php';
                return;
            }

            $admin = $this->admin->login($email, $password);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_last_activity'] = time();

                // Log activity
                $this->admin->logActivity(
                    $admin['id'], 
                    'login', 
                    'Admin berhasil login', 
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );

                header('Location: ?action=dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Email atau password salah';
                include __DIR__ . '/../views/admin/login.php';
                return;
            }
        } else {
            include __DIR__ . '/../views/admin/login.php';
        }
    }

    // Admin logout
    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'logout', 
                'Admin logout', 
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        }

        // Clear admin session
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['is_admin']);
        unset($_SESSION['admin_last_activity']);

        header('Location: ?action=login');
        exit;
    }

    // Show manage users page
    public function manageUsers() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ?action=login');
            exit;
        }

        // Get search parameter
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Get all users
        $allUsers = $this->admin->getAllUsers();
        $userCount = $this->admin->getUserCount();

        // Apply search filter
        if (!empty($search)) {
            $allUsers = array_filter($allUsers, function ($user) use ($search) {
                return stripos($user['name'], $search) !== false ||
                       stripos($user['alamat_email'], $search) !== false ||
                       stripos($user['no_telepon'], $search) !== false;
            });
        }

        // Apply pagination
        $totalUsers = count($allUsers);
        $totalPages = ceil($totalUsers / $perPage);
        $users = array_slice($allUsers, $offset, $perPage);
        
        include __DIR__ . '/../views/admin/manage_users.php';
    }

    // Show edit user form
    public function editUser($id = null) {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ?action=login');
            exit;
        }

        $id = $id ?? ($_GET['id'] ?? null);
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: ?action=manage_users');
            exit;
        }

        $user = $this->admin->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: ?action=manage_users');
            exit;
        }

        include __DIR__ . '/../views/admin/edit_user.php';
    }

    // Update user
    public function updateUser() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->sanitizeInput($_POST['id'] ?? '');
            $name = $this->sanitizeInput($_POST['name'] ?? '');
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $phone = $this->sanitizeInput($_POST['phone'] ?? '');

            if (empty($id) || empty($name) || empty($email)) {
                $_SESSION['error'] = 'Semua field wajib diisi kecuali nomor telepon';
                header('Location: ?action=edit_user&id=' . $id);
                exit;
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Format email tidak valid';
                header('Location: ?action=edit_user&id=' . $id);
                exit;
            }

            if (strlen($name) < 2) {
                $_SESSION['error'] = 'Nama harus minimal 2 karakter';
                header('Location: ?action=edit_user&id=' . $id);
                exit;
            }

            $result = $this->admin->updateUser($id, $name, $email, $phone);
            
            if ($result['success']) {
                // Log activity
                $this->admin->logActivity(
                    $_SESSION['admin_id'], 
                    'update_user', 
                    "Mengupdate data user ID: $id", 
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );
                
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: ?action=manage_users');
            exit;
        }
    }

    // Delete user
    public function deleteUser() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ?action=login');
            exit;
        }

        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: ?action=manage_users');
            exit;
        }

        $result = $this->admin->deleteUser($id);
        
        if ($result['success']) {
            // Log activity
            $this->admin->logActivity(
                $_SESSION['admin_id'], 
                'delete_user', 
                "Menghapus user ID: $id", 
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
            
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ?action=manage_users');
        exit;
    }

    // Admin dashboard
    public function dashboard() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ?action=login');
            exit;
        }

        $userCount = $this->admin->getUserCount();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    // Get admin statistics
    public function getStats() {
        if (!$this->isAdmin()) {
            return ['error' => 'Unauthorized'];
        }

        return [
            'user_count' => $this->admin->getUserCount(),
            'admin_count' => 1, // Placeholder
            'system_status' => 'online'
        ];
    }
}