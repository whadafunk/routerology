<?php
// -------------------- HELPER FUNCTIONS --------------------

/**
 * Format bytes to human-readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Get client IP address (handles proxies)
 */
function getClientIP() {
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER)) {
            $ip_list = explode(',', $_SERVER[$key]);
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Generate random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Send JSON response
 */
function jsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function errorResponse($message, $status_code = 400) {
    jsonResponse([
        'success' => false,
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $status_code);
}

/**
 * Send success response
 */
function successResponse($data = [], $message = 'Success') {
    jsonResponse(array_merge([
        'success' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $data), 200);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check rate limit
 */
function checkRateLimit($identifier, $limit = 100, $window = 60) {
    if (!ENABLE_RATE_LIMIT) {
        return true;
    }
    
    session_start();
    $key = 'rate_limit_' . md5($identifier);
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    $data = $_SESSION[$key];
    
    // Reset if window expired
    if ($now - $data['start'] > $window) {
        $_SESSION[$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    
    // Check limit
    if ($data['count'] >= $limit) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Get server load average
 */
function getServerLoad() {
    $load = sys_getloadavg();
    return [
        '1min' => round($load[0], 2),
        '5min' => round($load[1], 2),
        '15min' => round($load[2], 2)
    ];
}

/**
 * Get memory usage info
 */
function getMemoryInfo() {
    return [
        'current' => formatBytes(memory_get_usage()),
        'peak' => formatBytes(memory_get_peak_usage()),
        'limit' => ini_get('memory_limit')
    ];
}

/**
 * Validate file size
 */
function isValidFileSize($size, $max_size_mb) {
    $max_bytes = $max_size_mb * 1024 * 1024;
    return $size > 0 && $size <= $max_bytes;
}

/**
 * Get proxy headers
 */
function getProxyHeaders() {
    return [
        'X-Forwarded-For' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
        'X-Forwarded-Proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '',
        'X-Forwarded-Port' => $_SERVER['HTTP_X_FORWARDED_PORT'] ?? '',
        'X-Real-IP' => $_SERVER['HTTP_X_REAL_IP'] ?? '',
        'Forwarded' => $_SERVER['HTTP_FORWARDED'] ?? ''
    ];
}

/**
 * Check if connection is HTTPS
 */
function isHttps() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
           $_SERVER['SERVER_PORT'] == 443 ||
           ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

/**
 * Get request ID for tracking
 */
function getRequestId() {
    static $request_id = null;
    if ($request_id === null) {
        $request_id = uniqid('req_', true);
    }
    return $request_id;
}

/**
 * Timer functions for performance measurement
 */
class Timer {
    private static $timers = [];
    
    public static function start($name) {
        self::$timers[$name] = microtime(true);
    }
    
    public static function stop($name) {
        if (!isset(self::$timers[$name])) {
            return 0;
        }
        $elapsed = microtime(true) - self::$timers[$name];
        unset(self::$timers[$name]);
        return round($elapsed * 1000, 2); // Return in milliseconds
    }
    
    public static function measure($name, callable $callback) {
        self::start($name);
        $result = $callback();
        $time = self::stop($name);
        return ['result' => $result, 'time_ms' => $time];
    }
}

/**
 * Simple cache implementation
 */
class SimpleCache {
    private static $cache = [];
    
    public static function get($key) {
        if (!ENABLE_CACHE) {
            return null;
        }
        
        if (!isset(self::$cache[$key])) {
            return null;
        }
        
        $data = self::$cache[$key];
        if (time() > $data['expires']) {
            unset(self::$cache[$key]);
            return null;
        }
        
        return $data['value'];
    }
    
    public static function set($key, $value, $ttl = null) {
        if (!ENABLE_CACHE) {
            return false;
        }
        
        $ttl = $ttl ?? CACHE_TIME;
        self::$cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        return true;
    }
    
    public static function delete($key) {
        unset(self::$cache[$key]);
    }
    
    public static function clear() {
        self::$cache = [];
    }
}