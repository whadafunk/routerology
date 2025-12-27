<?php
// -------------------- PARALLEL CONNECTIONS TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$start_time = microtime(true);

// Get hold time from request (how long to keep connection open)
$hold_time = isset($_GET['hold']) ? intval($_GET['hold']) : 0;
$connection_id = isset($_GET['id']) ? $_GET['id'] : uniqid('conn_');

// Simulate work/hold time
if ($hold_time > 0 && $hold_time <= 60) {
    sleep($hold_time);
}

$response_time = (microtime(true) - $start_time) * 1000;

$response = [
    'success' => true,
    'connection_id' => $connection_id,
    'timestamp' => date('Y-m-d H:i:s.u'),
    'hold_time_seconds' => $hold_time,
    'response_time_ms' => round($response_time, 2),
    'server' => [
        'hostname' => gethostname(),
        'ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
        'port' => $_SERVER['SERVER_PORT'] ?? 'unknown',
        'pid' => getmypid()
    ],
    'connection_info' => [
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'client_port' => $_SERVER['REMOTE_PORT'] ?? 'unknown',
        'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'unknown'
    ],
    'concurrency_info' => [
        'current_connections' => getActiveConnections(),
        'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2)
    ]
];

// Add HAProxy info if available
if (isset($_SERVER['HTTP_X_HAPROXY_SERVER'])) {
    $response['haproxy_server'] = $_SERVER['HTTP_X_HAPROXY_SERVER'];
}

echo json_encode($response);

function getActiveConnections() {
    // Try to get active connection count
    if (function_exists('apache_get_modules') && in_array('mod_status', apache_get_modules())) {
        // Apache with mod_status
        $status = @file_get_contents('http://localhost/server-status?auto');
        if ($status && preg_match('/BusyWorkers: (\d+)/', $status, $matches)) {
            return intval($matches[1]);
        }
    }
    
    // Fallback: count PHP processes (rough estimate)
    if (PHP_OS_FAMILY === 'Linux') {
        $count = @exec("ps aux | grep -c '[p]hp-fpm'");
        if ($count) {
            return intval($count);
        }
    }
    
    return 'unknown';
}

exit;