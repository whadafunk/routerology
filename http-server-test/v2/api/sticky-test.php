<?php
// -------------------- SESSION STICKINESS TEST API --------------------
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Initialize session tracking
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['first_seen'] = date('Y-m-d H:i:s');
}

$_SESSION['request_count']++;
$_SESSION['last_seen'] = date('Y-m-d H:i:s');

// Get server identification
$server_id = gethostname();
$server_ip = $_SERVER['SERVER_ADDR'] ?? 'unknown';
$server_port = $_SERVER['SERVER_PORT'] ?? 'unknown';

// Get HAProxy/load balancer info if available
$backend_server = $_SERVER['HTTP_X_HAPROXY_SERVER'] ?? null;
$backend_name = $_SERVER['HTTP_X_HAPROXY_BACKEND'] ?? null;

// Create unique server identifier
$server_identifier = $backend_server ?? "{$server_id}:{$server_ip}:{$server_port}";

$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s.u'),
    'session_id' => session_id(),
    'session_data' => [
        'request_count' => $_SESSION['request_count'],
        'first_seen' => $_SESSION['first_seen'],
        'last_seen' => $_SESSION['last_seen']
    ],
    'server' => [
        'identifier' => $server_identifier,
        'hostname' => $server_id,
        'ip' => $server_ip,
        'port' => $server_port,
        'haproxy_server' => $backend_server,
        'haproxy_backend' => $backend_name
    ],
    'client' => [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ],
    'cookies_sent' => $_COOKIE
];

echo json_encode($response, JSON_PRETTY_PRINT);
exit;