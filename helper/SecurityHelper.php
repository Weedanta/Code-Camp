<?php
// helpers/SecurityHelper.php
// Kelas untuk membantu keamanan aplikasi

class SecurityHelper {
    
    /**
     * Sanitize input untuk mencegah XSS
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        // Trim whitespace
        $data = trim($data);
        
        // Remove tags and encode special characters
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }
    
    /**
     * Check if user is logged in (regular user)
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Redirect if not admin
     */
    public static function requireAdmin($redirectUrl = '/views/auth/login.php') {
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'Akses ditolak. Anda harus login sebagai admin.';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Redirect if not logged in
     */
    public static function requireLogin($redirectUrl = '/views/auth/login.php') {
        if (!self::isLoggedIn() && !self::isAdmin()) {
            $_SESSION['error'] = 'Anda harus login terlebih dahulu.';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password harus minimal 8 karakter';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf besar';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kecil';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 angka';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Rate limiting sederhana
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 300) {
        $cacheKey = 'rate_limit_' . md5($key);
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }
        
        $data = $_SESSION[$cacheKey];
        
        // Reset jika sudah melewati time window
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$cacheKey] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }
        
        // Check apakah sudah melebihi batas
        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }
        
        // Increment attempt
        $_SESSION[$cacheKey]['attempts']++;
        return true;
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $description, $severity = 'info') {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'description' => $description,
            'severity' => $severity,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? 'anonymous'
        ];
        
        // Log to file (pastikan direktori logs ada dan writable)
        $logFile = '../logs/security_' . date('Y-m-d') . '.log';
        $logEntry = json_encode($logData) . "\n";
        
        // Create logs directory if not exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Detect SQL injection attempts
     */
    public static function detectSQLInjection($input) {
        $sqlKeywords = [
            'union', 'select', 'insert', 'update', 'delete', 'drop', 'create', 'alter',
            'exec', 'execute', 'sp_', 'xp_', 'script', 'javascript', 'vbscript',
            '--', '/*', '*/', ';', '\'', '"', '=', 'or', 'and', '1=1', '1=0'
        ];
        
        $input = strtolower($input);
        
        foreach ($sqlKeywords as $keyword) {
            if (strpos($input, $keyword) !== false) {
                self::logSecurityEvent(
                    'sql_injection_attempt',
                    "Possible SQL injection detected: " . $input,
                    'critical'
                );
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate secure random password
     */
    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Prevent brute force attacks
     */
    public static function preventBruteForce($identifier, $maxAttempts = 5) {
        $key = 'login_attempts_' . md5($identifier);
        
        if (!self::checkRateLimit($key, $maxAttempts, 900)) { // 15 minutes
            self::logSecurityEvent(
                'brute_force_attempt',
                "Too many login attempts from: " . $identifier,
                'high'
            );
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Clean and validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 2097152) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = 'File type not allowed';
            }
        }
        
        // Check for dangerous file extensions
        $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'com', 'scr', 'vbs', 'js'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'Dangerous file extension detected';
            self::logSecurityEvent(
                'dangerous_file_upload',
                "Attempt to upload dangerous file: " . $file['name'],
                'high'
            );
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}