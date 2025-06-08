<?php
// middleware/AdminMiddleware.php - Enhanced Admin Middleware

class AdminMiddleware {
    
    /**
     * Check admin access and security
     */
    public static function checkAdminAccess() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in as admin
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: admin.php?action=login');
            exit;
        }
        
        // Check if admin_id exists
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error'] = 'Session tidak valid. Silakan login kembali.';
            self::destroySession();
            header('Location: admin.php?action=login');
            exit;
        }
        
        // Check session timeout (2 hours)
        if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > 7200)) {
            self::logSecurityEvent('session_timeout', 'Admin session timeout for ID: ' . $_SESSION['admin_id']);
            self::destroySession();
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
        
        // Check for suspicious activity
        self::checkSuspiciousActivity();
        
        // IP validation (optional - enable if needed)
        // self::validateAdminIP();
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        // Check token age (expire after 1 hour)
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Check for suspicious activity
     */
    private static function checkSuspiciousActivity() {
        $currentTime = time();
        $suspiciousThreshold = 100; // requests per minute
        
        // Initialize or get current request count
        if (!isset($_SESSION['admin_request_count'])) {
            $_SESSION['admin_request_count'] = ['count' => 0, 'start_time' => $currentTime];
        }
        
        $requestData = $_SESSION['admin_request_count'];
        
        // Reset counter if more than 1 minute has passed
        if (($currentTime - $requestData['start_time']) > 60) {
            $_SESSION['admin_request_count'] = ['count' => 1, 'start_time' => $currentTime];
        } else {
            // Increment request count
            $_SESSION['admin_request_count']['count']++;
            
            // Check if threshold exceeded
            if ($_SESSION['admin_request_count']['count'] > $suspiciousThreshold) {
                self::logSecurityEvent('suspicious_activity', 
                    'Suspicious activity detected for admin ID: ' . $_SESSION['admin_id'] . 
                    ' - ' . $_SESSION['admin_request_count']['count'] . ' requests in 1 minute', 
                    'high'
                );
                
                // Optional: Temporarily suspend session
                // self::suspendSession();
            }
        }
    }
    
    /**
     * Validate admin IP (optional security layer)
     */
    private static function validateAdminIP() {
        $allowedIPs = [
            '127.0.0.1',
            '::1',
            // Add specific admin IPs here
        ];
        
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Skip validation if no IPs configured or in development
        if (empty($allowedIPs) || in_array($clientIP, ['127.0.0.1', '::1'])) {
            return;
        }
        
        if (!in_array($clientIP, $allowedIPs)) {
            self::logSecurityEvent('unauthorized_ip', 
                'Admin access attempt from unauthorized IP: ' . $clientIP . 
                ' by admin ID: ' . $_SESSION['admin_id'], 
                'high'
            );
            
            $_SESSION['error'] = 'Akses ditolak dari IP ini.';
            self::destroySession();
            header('Location: admin.php?action=login');
            exit;
        }
    }
    
    /**
     * Check specific permission for action
     */
    public static function checkPermission($action, $requiredRole = 'admin') {
        if (!isset($_SESSION['admin_role'])) {
            $_SESSION['error'] = 'Role tidak ditemukan. Silakan login kembali.';
            header('Location: admin.php?action=login');
            exit;
        }
        
        $adminRole = $_SESSION['admin_role'];
        
        // Super admin has access to everything
        if ($adminRole === 'super_admin') {
            return true;
        }
        
        // Define role permissions
        $permissions = [
            'admin' => [
                'dashboard', 'manage_users', 'edit_user', 'update_user',
                'manage_bootcamps', 'create_bootcamp', 'edit_bootcamp', 'update_bootcamp',
                'manage_categories', 'create_category', 'update_category',
                'manage_orders', 'view_order', 'update_order_status',
                'manage_reviews', 'approve_review', 'reject_review',
                'manage_forum', 'moderate_post',
                'profile', 'update_profile', 'change_password',
                'activity_log'
            ],
            'moderator' => [
                'dashboard', 'manage_reviews', 'approve_review', 'reject_review',
                'manage_forum', 'moderate_post', 'activity_log',
                'profile', 'update_profile', 'change_password'
            ],
            'editor' => [
                'dashboard', 'manage_bootcamps', 'create_bootcamp', 'edit_bootcamp', 'update_bootcamp',
                'manage_categories', 'create_category', 'update_category',
                'profile', 'update_profile', 'change_password'
            ]
        ];
        
        // Check if admin role has permission for this action
        if (!isset($permissions[$adminRole]) || !in_array($action, $permissions[$adminRole])) {
            self::logSecurityEvent('unauthorized_access', 
                'Unauthorized access attempt to action: ' . $action . 
                ' by admin ID: ' . $_SESSION['admin_id'] . 
                ' with role: ' . $adminRole, 
                'medium'
            );
            
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
            header('Location: admin.php?action=dashboard');
            exit;
        }
        
        return true;
    }
    
    /**
     * Rate limiting for admin actions
     */
    public static function checkRateLimit($action, $maxAttempts = 10, $timeWindow = 60) {
        $key = 'admin_rate_limit_' . $action . '_' . $_SESSION['admin_id'];
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'start_time' => time()];
        }
        
        $rateLimitData = $_SESSION[$key];
        
        // Reset if time window has passed
        if ((time() - $rateLimitData['start_time']) > $timeWindow) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => time()];
            return true;
        }
        
        // Increment count
        $_SESSION[$key]['count']++;
        
        // Check if limit exceeded
        if ($_SESSION[$key]['count'] > $maxAttempts) {
            self::logSecurityEvent('rate_limit_exceeded', 
                'Rate limit exceeded for action: ' . $action . 
                ' by admin ID: ' . $_SESSION['admin_id'], 
                'medium'
            );
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate request method
     */
    public static function validateRequestMethod($allowedMethods = ['GET', 'POST']) {
        $currentMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (!in_array($currentMethod, $allowedMethods)) {
            self::logSecurityEvent('invalid_request_method', 
                'Invalid request method: ' . $currentMethod . 
                ' by admin ID: ' . ($_SESSION['admin_id'] ?? 'unknown'), 
                'medium'
            );
            
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        return true;
    }
    
    /**
     * Sanitize and validate admin input
     */
    public static function validateInput($data, $rules = []) {
        $errors = [];
        $sanitized = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Check required fields
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required';
                continue;
            }
            
            // Skip validation if field is empty and not required
            if (empty($value) && (!isset($rule['required']) || !$rule['required'])) {
                $sanitized[$field] = '';
                continue;
            }
            
            // Sanitize input
            $sanitized[$field] = self::sanitizeInput($value);
            
            // Apply specific validations
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Invalid email format';
                        }
                        break;
                        
                    case 'integer':
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field] = 'Must be a valid integer';
                        }
                        break;
                        
                    case 'float':
                        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                            $errors[$field] = 'Must be a valid number';
                        }
                        break;
                        
                    case 'url':
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $errors[$field] = 'Must be a valid URL';
                        }
                        break;
                }
            }
            
            // Check min/max length
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = ucfirst($field) . ' must be at least ' . $rule['min_length'] . ' characters';
            }
            
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = ucfirst($field) . ' must not exceed ' . $rule['max_length'] . ' characters';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Sanitize input (reuse from SecurityHelper)
     */
    private static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            $input = trim($input);
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return $input;
        }
        
        return $input;
    }
    
    /**
     * Destroy session securely
     */
    private static function destroySession() {
        $_SESSION = array();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        session_destroy();
    }
    
    /**
     * Suspend session temporarily
     */
    private static function suspendSession() {
        $_SESSION['admin_suspended'] = true;
        $_SESSION['admin_suspend_time'] = time();
        
        $_SESSION['error'] = 'Aktivitas mencurigakan terdeteksi. Session ditangguhkan sementara.';
        header('Location: admin.php?action=login');
        exit;
    }
    
    /**
     * Log security events
     */
    private static function logSecurityEvent($eventType, $description, $severity = 'medium') {
        $logEntry = sprintf(
            "[%s] [%s] [%s] %s - IP: %s - User Agent: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($severity),
            $eventType,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        // Create logs directory if not exists
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        
        // Log to security file
        $logFile = 'logs/admin_security_' . date('Y-m-d') . '.log';
        error_log($logEntry, 3, $logFile);
        
        // For critical events, also log to system log
        if ($severity === 'high') {
            error_log("ADMIN SECURITY ALERT: $description", 0);
        }
    }
    
    /**
     * Get current admin info
     */
    public static function getCurrentAdmin() {
        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'name' => $_SESSION['admin_name'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null,
            'role' => $_SESSION['admin_role'] ?? null,
            'last_activity' => $_SESSION['admin_last_activity'] ?? null
        ];
    }
    
    /**
     * Check if admin has specific role
     */
    public static function hasRole($role) {
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === $role;
    }
    
    /**
     * Check if admin can perform action
     */
    public static function canPerform($action) {
        if (!isset($_SESSION['admin_role'])) {
            return false;
        }
        
        // Super admin can do everything
        if ($_SESSION['admin_role'] === 'super_admin') {
            return true;
        }
        
        // Define what each role can do
        $rolePermissions = [
            'admin' => ['*'], // All actions
            'moderator' => ['manage_reviews', 'manage_forum', 'moderate_post'],
            'editor' => ['manage_bootcamps', 'manage_categories']
        ];
        
        $userRole = $_SESSION['admin_role'];
        $permissions = $rolePermissions[$userRole] ?? [];
        
        return in_array('*', $permissions) || in_array($action, $permissions);
    }
    
    /**
     * Get session timeout remaining
     */
    public static function getTimeoutRemaining() {
        if (!isset($_SESSION['admin_last_activity'])) {
            return 0;
        }
        
        $sessionTimeout = 7200; // 2 hours
        $elapsed = time() - $_SESSION['admin_last_activity'];
        $remaining = $sessionTimeout - $elapsed;
        
        return max(0, $remaining);
    }
    
    /**
     * Extend session
     */
    public static function extendSession() {
        $_SESSION['admin_last_activity'] = time();
        
        // Also regenerate session ID for security
        session_regenerate_id(true);
        $_SESSION['admin_session_regenerated'] = time();
        
        return true;
    }
}
?>