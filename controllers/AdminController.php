<?php
// controllers/AdminController.php

require_once 'models/Admin.php';

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
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    // Validate email
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Admin login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Check if email contains 'admin'
            if (strpos(strtolower($email), 'admin') === false) {
                $_SESSION['error'] = 'Akses ditolak. Email harus mengandung kata "admin".';
                header('Location: /views/auth/login.php');
                exit;
            }

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Email dan password harus diisi';
                header('Location: /views/auth/login.php');
                exit;
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Format email tidak valid';
                header('Location: /views/auth/login.php');
                exit;
            }

            $admin = $this->admin->login($email, $password);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['is_admin'] = true;

                // Log activity
                $this->admin->logActivity(
                    $admin['id'], 
                    'login', 
                    'Admin berhasil login', 
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                );

                header('Location: /views/admin/dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'Email atau password salah';
                header('Location: /views/auth/login.php');
                exit;
            }
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

        session_destroy();
        header('Location: /views/auth/login.php');
        exit;
    }

    // Show manage users page
    public function manageUsers() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: /views/auth/login.php');
            exit;
        }

        $users = $this->admin->getAllUsers();
        $userCount = $this->admin->getUserCount();
        
        include 'views/admin/manage_users.php';
    }

    // Show edit user form
    public function editUser($id = null) {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: /views/auth/login.php');
            exit;
        }

        $id = $id ?? ($_GET['id'] ?? null);
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: /views/admin/manage_users.php');
            exit;
        }

        $user = $this->admin->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: /views/admin/manage_users.php');
            exit;
        }

        include 'views/admin/edit_user.php';
    }

    // Update user
    public function updateUser() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: /views/auth/login.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->sanitizeInput($_POST['id'] ?? '');
            $name = $this->sanitizeInput($_POST['name'] ?? '');
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $phone = $this->sanitizeInput($_POST['phone'] ?? '');

            if (empty($id) || empty($name) || empty($email)) {
                $_SESSION['error'] = 'Semua field wajib diisi kecuali nomor telepon';
                header('Location: /views/admin/edit_user.php?id=' . $id);
                exit;
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Format email tidak valid';
                header('Location: /views/admin/edit_user.php?id=' . $id);
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

            header('Location: /views/admin/manage_users.php');
            exit;
        }
    }

    // Delete user
    public function deleteUser() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: /views/auth/login.php');
            exit;
        }

        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = 'ID user tidak valid';
            header('Location: /views/admin/manage_users.php');
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

        header('Location: /views/admin/manage_users.php');
        exit;
    }

    // Admin dashboard
    public function dashboard() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: /views/auth/login.php');
            exit;
        }

        $userCount = $this->admin->getUserCount();
        include 'views/admin/dashboard.php';
    }
}