<?php
// middleware/AdminMiddleware.php
// Middleware untuk mengecek akses admin

class AdminMiddleware {
    
    /**
     * Check if current user has admin access
     */
    public static function checkAccess() {
        session_start();
        
        // Check if user is logged in as admin
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            self::redirectToLogin();
        }
        
        // Additional security checks
        self::validateSession();
        
        return true;
    }
    
    /**
     * Redirect to login page
     */
    private static function redirectToLogin() {
        $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
        header('Location: /views/auth/login.php');
        exit;
    }
    
    /**
     * Validate admin session
     */
    private static function validateSession() {
        // Check session timeout (2 hours)
        if (isset($_SESSION['admin_last_activity'])) {
            if (time() - $_SESSION['admin_last_activity'] > 7200) {
                session_destroy();
                $_SESSION['error'] = 'Sesi admin telah berakhir. Silakan login kembali.';
                header('Location: /views/auth/login.php');
                exit;
            }
        }
        
        // Update last activity
        $_SESSION['admin_last_activity'] = time();
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['admin_session_regenerated'])) {
            session_regenerate_id(true);
            $_SESSION['admin_session_regenerated'] = time();
        } elseif (time() - $_SESSION['admin_session_regenerated'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['admin_session_regenerated'] = time();
        }
    }
    
    /**
     * Check if current admin has specific role
     */
    public static function requireRole($requiredRole) {
        self::checkAccess();
        
        if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== $requiredRole) {
            $_SESSION['error'] = 'Anda tidak memiliki akses untuk halaman ini.';
            header('Location: /views/admin/dashboard.php');
            exit;
        }
        
        return true;
    }
    
    /**
     * Log admin activity
     */
    public static function logActivity($activity, $description = '') {
        if (!isset($_SESSION['admin_id'])) {
            return;
        }
        
        try {
            require_once '../config/database.php';
            require_once '../models/Admin.php';
            
            $database = new Database();
            $db = $database->getConnection();
            $admin = new Admin($db);
            
            $admin->logActivity(
                $_SESSION['admin_id'],
                $activity,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        } catch (Exception $e) {
            // Log error silently
            error_log("Failed to log admin activity: " . $e->getMessage());
        }
    }
}

// Helper function to include in admin pages
function requireAdmin() {
    AdminMiddleware::checkAccess();
}

function requireSuperAdmin() {
    AdminMiddleware::requireRole('super_admin');
}

function logAdminActivity($activity, $description = '') {
    AdminMiddleware::logActivity($activity, $description);
}
?>