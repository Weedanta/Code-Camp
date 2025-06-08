<?php
// helper/SecurityHelper.php - Enhanced Security Helper for Admin Panel

class SecurityHelper {
    
    // ==================== XSS PREVENTION ====================
    
    /**
     * Sanitize input to prevent XSS attacks
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove null bytes
            $input = str_replace(chr(0), '', $input);
            
            // Trim whitespace
            $input = trim($input);
            
            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            return $input;
        }
        
        return $input;
    }
    
    /**
     * Sanitize output for safe display
     */
    public static function sanitizeOutput($output, $allowedTags = '') {
        if (is_array($output)) {
            return array_map(function($item) use ($allowedTags) {
                return self::sanitizeOutput($item, $allowedTags);
            }, $output);
        }
        
        if (is_string($output)) {
            // Strip tags except allowed ones
            $output = strip_tags($output, $allowedTags);
            
            // Convert special characters
            $output = htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            return $output;
        }
        
        return $output;
    }
    
    /**
     * Clean HTML content while preserving safe tags
     */
    public static function cleanHTML($html) {
        $allowedTags = '<p><br><strong><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6>';
        
        // Remove script tags and their content
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Remove on* event attributes
        $html = preg_replace('/\son\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Remove javascript: links
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html);
        
        // Strip unwanted tags
        $html = strip_tags($html, $allowedTags);
        
        return $html;
    }
    
    // ==================== CSRF PROTECTION ====================
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if token exists
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        // Check token age (expire after 1 hour)
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        // Compare tokens using hash_equals to prevent timing attacks
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // ==================== INPUT VALIDATION ====================
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    public static function validatePhone($phone) {
        // Remove all non-digit characters except + and -
        $cleaned = preg_replace('/[^\d+\-]/', '', $phone);
        
        // Check if it's a valid format
        return preg_match('/^[\+]?[0-9\-\s\(\)]{8,20}$/', $phone);
    }
    
    /**
     * Validate strong password
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
     * Validate URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Validate integer within range
     */
    public static function validateInteger($value, $min = null, $max = null) {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        
        if ($value === false) {
            return false;
        }
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return $value;
    }
    
    // ==================== RATE LIMITING ====================
    
    /**
     * Implement rate limiting for brute force prevention
     */
    public static function preventBruteForce($identifier, $maxAttempts = 5, $timeWindow = 900) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . hash('sha256', $identifier);
        
        // Get current attempts
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => time()];
        
        // Reset if time window has passed
        if ((time() - $attempts['first_attempt']) > $timeWindow) {
            $attempts = ['count' => 0, 'first_attempt' => time()];
        }
        
        // Check if max attempts exceeded
        if ($attempts['count'] >= $maxAttempts) {
            self::logSecurityEvent(
                'brute_force_blocked',
                "Brute force attempt blocked for: $identifier",
                'high'
            );
            return false;
        }
        
        return true;
    }
    
    /**
     * Record failed login attempt
     */
    public static function recordFailedAttempt($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . hash('sha256', $identifier);
        
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => time()];
        $attempts['count']++;
        
        $_SESSION[$key] = $attempts;
    }
    
    /**
     * Clear failed attempts after successful login
     */
    public static function clearFailedAttempts($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_attempts_' . hash('sha256', $identifier);
        unset($_SESSION[$key]);
    }
    
    // ==================== PASSWORD SECURITY ====================
    
    /**
     * Generate secure password hash
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }
    
    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate secure random password
     */
    public static function generateRandomPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    // ==================== FILE UPLOAD SECURITY ====================
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error: ' . $file['error'];
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check for malicious content
        if (self::containsMaliciousContent($file['tmp_name'])) {
            $errors[] = 'File contains malicious content';
        }
        
        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'File extension not allowed';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime_type' => $mimeType,
            'extension' => $extension
        ];
    }
    
    /**
     * Check if file contains malicious content
     */
    private static function containsMaliciousContent($filePath) {
        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB
        
        // Check for common malicious patterns
        $maliciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    // ==================== SECURITY LOGGING ====================
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($eventType, $description, $severity = 'medium') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $eventType,
            'description' => $description,
            'severity' => $severity,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'session_id' => session_id(),
            'admin_id' => $_SESSION['admin_id'] ?? null
        ];
        
        $logString = sprintf(
            "[%s] [%s] [%s] %s - IP: %s\n",
            $logEntry['timestamp'],
            strtoupper($severity),
            $eventType,
            $description,
            $logEntry['ip_address']
        );
        
        // Log to file
        $logFile = 'logs/security_' . date('Y-m-d') . '.log';
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        
        error_log($logString, 3, $logFile);
        
        // For high severity events, also log to system log
        if ($severity === 'high') {
            error_log("SECURITY ALERT: $description", 0);
        }
    }
    
    // ==================== UTILITY FUNCTIONS ====================
    
    /**
     * Generate secure slug from string
     */
    public static function generateSlug($string) {
        // Convert to lowercase
        $slug = strtolower($string);
        
        // Remove special characters
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Replace spaces and multiple hyphens with single hyphen
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Trim hyphens from ends
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Mask sensitive data for logging
     */
    public static function maskSensitiveData($data, $fields = ['password', 'email', 'phone']) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array(strtolower($key), $fields)) {
                    if (is_string($value) && strlen($value) > 4) {
                        $data[$key] = substr($value, 0, 2) . str_repeat('*', strlen($value) - 4) . substr($value, -2);
                    } else {
                        $data[$key] = '***';
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Check if request is from admin IP (if configured)
     */
    public static function isAdminIP() {
        $adminIPs = [
            '127.0.0.1',
            '::1',
            // Add your admin IPs here
        ];
        
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
        
        return in_array($clientIP, $adminIPs);
    }
    
    /**
     * Generate secure random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Time-safe string comparison
     */
    public static function timeSafeEquals($a, $b) {
        return hash_equals((string)$a, (string)$b);
    }
    
    /**
     * Format currency for Indonesian Rupiah
     */
    public static function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Format date to Indonesian format
     */
    public static function formatDate($date, $format = 'd/m/Y H:i') {
        if (empty($date) || $date === '0000-00-00 00:00:00') {
            return '-';
        }
        return date($format, strtotime($date));
    }
    
    /**
     * Get file extension from filename
     */
    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
    
    /**
     * Check if file is image
     */
    public static function isImageFile($filename) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array(self::getFileExtension($filename), $imageExtensions);
    }
    
    /**
     * Generate random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Sanitize filename for upload
     */
    public static function sanitizeFilename($filename) {
        // Get file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Sanitize basename
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
        
        // Limit length
        if (strlen($basename) > 50) {
            $basename = substr($basename, 0, 50);
        }
        
        return $basename . '.' . $extension;
    }
    
    /**
     * Create directory if not exists
     */
    public static function createDirectoryIfNotExists($path) {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
    
    /**
     * Calculate file size in readable format
     */
    public static function formatFileSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
    
    /**
     * Validate date format
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Generate breadcrumb for admin pages
     */
    public static function generateBreadcrumb($action) {
        $breadcrumbs = [
            'dashboard' => ['Dashboard'],
            'manage_users' => ['Dashboard', 'Kelola Users'],
            'edit_user' => ['Dashboard', 'Kelola Users', 'Edit User'],
            'manage_bootcamps' => ['Dashboard', 'Kelola Bootcamps'],
            'create_bootcamp' => ['Dashboard', 'Kelola Bootcamps', 'Tambah Bootcamp'],
            'edit_bootcamp' => ['Dashboard', 'Kelola Bootcamps', 'Edit Bootcamp'],
            'manage_categories' => ['Dashboard', 'Kelola Kategori'],
            'manage_orders' => ['Dashboard', 'Kelola Orders'],
            'view_order' => ['Dashboard', 'Kelola Orders', 'Detail Order'],
            'manage_reviews' => ['Dashboard', 'Kelola Reviews'],
            'manage_forum' => ['Dashboard', 'Kelola Forum'],
            'manage_settings' => ['Dashboard', 'Pengaturan'],
            'profile' => ['Dashboard', 'Profile Admin'],
            'activity_log' => ['Dashboard', 'Log Aktivitas']
        ];
        
        return $breadcrumbs[$action] ?? ['Dashboard'];
    }
}
?>