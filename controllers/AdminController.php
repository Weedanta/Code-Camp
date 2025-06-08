<?php
// Perbaikan method processLogin() di AdminController.php

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
?>