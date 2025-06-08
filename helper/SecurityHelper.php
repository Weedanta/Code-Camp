<?php
// helper/SecurityHelper.php - Security utilities for Code Camp

class SecurityHelper {
    
    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
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
     * Generate secure slug from text
     */
    public static function generateSlug($text) {
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        $result = [
            'valid' => false,
            'errors' => []
        ];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $result['errors'][] = 'File not uploaded properly';
            return $result;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $result['errors'][] = 'File size exceeds maximum limit (' . number_format($maxSize / 1024 / 1024, 1) . 'MB)';
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $result['errors'][] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
            }
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $result['errors'][] = 'File size too large';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $result['errors'][] = 'File was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $result['errors'][] = 'No file was uploaded';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $result['errors'][] = 'Missing temporary folder';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $result['errors'][] = 'Failed to write file to disk';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $result['errors'][] = 'File upload stopped by extension';
                    break;
                default:
                    $result['errors'][] = 'Unknown upload error';
            }
        }
        
        $result['valid'] = empty($result['errors']);
        return $result;
    }
    
    /**
     * Prevent brute force attacks
     */
    public static function preventBruteForce($identifier, $maxAttempts = 5, $timeWindow = 900) {
        $cacheKey = 'bf_' . md5($identifier);
        
        // Get current attempts
        $attempts = $_SESSION[$cacheKey] ?? [
            'count' => 0,
            'first_attempt' => time()
        ];
        
        // Reset if time window passed
        if (time() - $attempts['first_attempt'] > $timeWindow) {
            $attempts = [
                'count' => 0,
                'first_attempt' => time()
            ];
        }
        
        // Check if max attempts exceeded
        if ($attempts['count'] >= $maxAttempts) {
            return false;
        }
        
        // Increment attempts
        $attempts['count']++;
        $_SESSION[$cacheKey] = $attempts;
        
        return true;
    }
    
    /**
     * Reset brute force counter
     */
    public static function resetBruteForce($identifier) {
        $cacheKey = 'bf_' . md5($identifier);
        unset($_SESSION[$cacheKey]);
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($type, $description, $severity = 'medium') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'description' => $description,
            'severity' => $severity,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        // Write to security log file
        $logFile = __DIR__ . '/../logs/security.log';
        
        // Create logs directory if not exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logLine = json_encode($logEntry) . PHP_EOL;
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Also log to error log for critical events
        if ($severity === 'high') {
            error_log("Security Event [$type]: $description");
        }
    }
    
    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password, $minLength = 8) {
        $errors = [];
        
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least $minLength characters long";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => self::calculatePasswordStrength($password)
        ];
    }
    
    /**
     * Calculate password strength score
     */
    private static function calculatePasswordStrength($password) {
        $score = 0;
        
        // Length bonus
        $score += min(strlen($password) / 8, 2);
        
        // Character variety bonus
        if (preg_match('/[a-z]/', $password)) $score += 1;
        if (preg_match('/[A-Z]/', $password)) $score += 1;
        if (preg_match('/[0-9]/', $password)) $score += 1;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 1;
        
        // Penalty for common patterns
        if (preg_match('/(.)\1{2,}/', $password)) $score -= 1; // Repeated characters
        if (preg_match('/012|123|234|345|456|567|678|789|890/', $password)) $score -= 1; // Sequential numbers
        if (preg_match('/abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz/i', $password)) $score -= 1; // Sequential letters
        
        // Normalize score to 0-100
        $strength = max(0, min(100, ($score / 6) * 100));
        
        if ($strength < 30) return 'weak';
        if ($strength < 60) return 'medium';
        if ($strength < 80) return 'strong';
        return 'very_strong';
    }
    
    /**
     * Generate secure random password
     */
    public static function generateSecurePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if IP is in whitelist
     */
    public static function isIPWhitelisted($ip, $whitelist = []) {
        if (empty($whitelist)) {
            return true; // No whitelist means all IPs allowed
        }
        
        foreach ($whitelist as $whitelistedIP) {
            if ($ip === $whitelistedIP) {
                return true;
            }
            
            // Check for CIDR notation
            if (strpos($whitelistedIP, '/') !== false) {
                if (self::ipInRange($ip, $whitelistedIP)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if IP is in range (CIDR)
     */
    private static function ipInRange($ip, $range) {
        list($subnet, $bits) = explode('/', $range);
        
        if ($bits === null) {
            $bits = 32;
        }
        
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) == $subnet;
    }
    
    /**
     * Clean and validate file name
     */
    public static function sanitizeFileName($filename) {
        // Remove path separators
        $filename = basename($filename);
        
        // Remove special characters except dots, hyphens, and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Remove multiple dots
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // Limit length
        if (strlen($filename) > 255) {
            $pathinfo = pathinfo($filename);
            $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
            $basename = substr($pathinfo['filename'], 0, 255 - strlen($extension));
            $filename = $basename . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Check if request is AJAX
     */
    public static function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                   'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 
                   'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Validate URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Escape output for different contexts
     */
    public static function escapeOutput($data, $context = 'html') {
        switch ($context) {
            case 'html':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            case 'html_attr':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            case 'js':
                return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            case 'css':
                return preg_replace('/[^a-zA-Z0-9\-_]/', '', $data);
            case 'url':
                return urlencode($data);
            default:
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Rate limiting
     */
    public static function rateLimit($identifier, $maxRequests = 60, $timeWindow = 3600) {
        $cacheKey = 'rl_' . md5($identifier);
        
        $requests = $_SESSION[$cacheKey] ?? [
            'count' => 0,
            'window_start' => time()
        ];
        
        // Reset if time window passed
        if (time() - $requests['window_start'] > $timeWindow) {
            $requests = [
                'count' => 1,
                'window_start' => time()
            ];
        } else {
            $requests['count']++;
        }
        
        $_SESSION[$cacheKey] = $requests;
        
        return $requests['count'] <= $maxRequests;
    }
}
?>