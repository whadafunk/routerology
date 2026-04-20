<?php
// -------------------- STRESS TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$start_time = microtime(true);

// Simulate some processing
$processing_time = rand(10, 100); // Random delay between 10-100ms
usleep($processing_time * 1000);

$response_time = (microtime(true) - $start_time) * 1000;

$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s.u'),
    'server' => gethostname(),
    'method' => $_SERVER['REQUEST_METHOD'],
    'response_time_ms' => round($response_time, 2),
    'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
    'server_port' => $_SERVER['SERVER_PORT'] ?? 'unknown',
    'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2),
    'request_id' => uniqid('req_', true)
];

// Add HAProxy headers if available
if (isset($_SERVER['HTTP_X_HAPROXY_SERVER'])) {
    $response['haproxy_server'] = $_SERVER['HTTP_X_HAPROXY_SERVER'];
}
if (isset($_SERVER['HTTP_X_HAPROXY_BACKEND'])) {
    $response['haproxy_backend'] = $_SERVER['HTTP_X_HAPROXY_BACKEND'];
}

echo json_encode($response);
exit;