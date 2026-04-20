<?php
// -------------------- HEALTH CHECK SIMULATOR API --------------------
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get parameters
$status_code = isset($_GET['status']) ? intval($_GET['status']) : 200;
$delay = isset($_GET['delay']) ? intval($_GET['delay']) : 0;
$body = isset($_GET['body']) ? $_GET['body'] : '';

// Validate status code
if ($status_code < 100 || $status_code > 599) {
    $status_code = 200;
}

// Apply delay if requested (simulate slow health check)
if ($delay > 0) {
    usleep($delay * 1000); // Convert ms to microseconds
}

// Set response status
http_response_code($status_code);

// Prepare response
$response = [
    'status' => $status_code,
    'message' => getStatusMessage($status_code),
    'timestamp' => date('Y-m-d H:i:s.u'),
    'server' => gethostname(),
    'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
    'delay_ms' => $delay,
    'uptime' => getServerUptime(),
    'memory' => [
        'used_mb' => round(memory_get_usage() / 1024 / 1024, 2),
        'limit' => ini_get('memory_limit')
    ],
    'load_average' => sys_getloadavg()
];

// Add custom body if provided
if ($body) {
    $response['custom_body'] = $body;
}

// Add HAProxy info if available
if (isset($_SERVER['HTTP_X_HAPROXY_SERVER'])) {
    $response['haproxy_server'] = $_SERVER['HTTP_X_HAPROXY_SERVER'];
}
if (isset($_SERVER['HTTP_X_HAPROXY_BACKEND'])) {
    $response['haproxy_backend'] = $_SERVER['HTTP_X_HAPROXY_BACKEND'];
}

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

// Helper functions
function getStatusMessage($code) {
    $messages = [
        200 => 'OK - Service Healthy',
        204 => 'No Content - Service Healthy',
        301 => 'Moved Permanently',
        302 => 'Found - Temporary Redirect',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error - Service Unhealthy',
        503 => 'Service Unavailable - Service Down'
    ];
    
    return $messages[$code] ?? 'Unknown Status';
}

function getServerUptime() {
    if (PHP_OS_FAMILY === 'Linux') {
        $uptime = @file_get_contents('/proc/uptime');
        if ($uptime) {
            $uptime_seconds = intval(explode(' ', $uptime)[0]);
            return formatUptime($uptime_seconds);
        }
    }
    
    // Fallback to uptime command
    $uptime = @exec('uptime -p 2>/dev/null');
    return $uptime ?: 'unknown';
}

function formatUptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = "{$days}d";
    if ($hours > 0) $parts[] = "{$hours}h";
    if ($minutes > 0) $parts[] = "{$minutes}m";
    
    return implode(' ', $parts) ?: '0m';
}

exit;