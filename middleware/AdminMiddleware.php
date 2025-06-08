<?php
// middleware/AdminMiddleware.php
class AdminMiddleware {
    
    public static function checkAdminAccess() {
        // Check if user is logged in as admin
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: admin.php?action=login');
            exit;
        }
        
        // Check session timeout (2 hours)
        if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > 7200)) {
            session_destroy();
            header('Location: admin.php?action=login&timeout=1');
            exit;
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
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>