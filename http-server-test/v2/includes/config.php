<?php
// -------------------- CONFIGURATION FILE --------------------

// Application Settings
define('APP_NAME', 'Advanced PHP/HAProxy Test Suite');
define('APP_VERSION', '2.0.0');

// File Size Limits (in MB)
define('MAX_DOWNLOAD_SIZE', 2048); // 2GB max download
define('MAX_UPLOAD_SIZE', 500);    // 500MB max upload

// Stress Test Limits
define('MAX_STRESS_REQUESTS', 10000);
define('MAX_STRESS_CONCURRENT', 100);

// Security Settings
define('ENABLE_AUTH', false); // Set to true to enable authentication
define('ALLOWED_IPS', []); // Empty = allow all, or ['192.168.1.0/24', '10.0.0.1']

// Rate Limiting (requests per IP per minute)
define('ENABLE_RATE_LIMIT', false);
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 60); // seconds

// Session Settings
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);

// Error Reporting (disable in production)
if (gethostname() === 'production-server') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Timezone
date_default_timezone_set('UTC');

// CORS Settings
define('CORS_ENABLED', true);
define('CORS_ORIGINS', '*'); // '*' or specific domains like 'https://example.com'

// Paths
define('BASE_PATH', __DIR__ . '/..');
define('API_PATH', BASE_PATH . '/api');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('SECTIONS_PATH', BASE_PATH . '/sections');

// Database (if needed in future)
define('DB_ENABLED', false);
define('DB_HOST', 'localhost');
define('DB_NAME', 'server_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Logging
define('ENABLE_LOGGING', false);
define('LOG_PATH', BASE_PATH . '/logs');
define('LOG_FILE', LOG_PATH . '/app.log');

// Feature Flags
define('FEATURE_DOWNLOAD', true);
define('FEATURE_UPLOAD', true);
define('FEATURE_STRESS_TEST', true);
define('FEATURE_COOKIE_MANAGER', true);
define('FEATURE_USER_AGENT', true);
define('FEATURE_PROXY_PROTOCOL', false); // Coming soon

// Cache Settings
define('ENABLE_CACHE', true);
define('CACHE_TIME', 300); // 5 minutes

// Performance
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

// Function to check if IP is allowed
function isIpAllowed($ip) {
    if (empty(ALLOWED_IPS)) {
        return true;
    }
    
    foreach (ALLOWED_IPS as $allowed) {
        if (strpos($allowed, '/') !== false) {
            // CIDR notation
            list($subnet, $mask) = explode('/', $allowed);
            $ip_long = ip2long($ip);
            $subnet_long = ip2long($subnet);
            $mask_long = -1 << (32 - $mask);
            
            if (($ip_long & $mask_long) == ($subnet_long & $mask_long)) {
                return true;
            }
        } else {
            // Direct IP match
            if ($ip === $allowed) {
                return true;
            }
        }
    }
    
    return false;
}

// Function to log messages
function logMessage($message, $level = 'INFO') {
    if (!ENABLE_LOGGING) {
        return;
    }
    
    if (!file_exists(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND);
}

// Initialize
if (ENABLE_LOGGING) {
    logMessage('Application loaded', 'INFO');
}