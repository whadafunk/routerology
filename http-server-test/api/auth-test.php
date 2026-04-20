<?php
// -------------------- AUTHENTICATION TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Test credentials
$valid_users = [
    'admin' => [
        'password' => 'Admin123!',
        'role' => 'Administrator',
        'permissions' => ['read', 'write', 'delete', 'admin']
    ],
    'testuser' => [
        'password' => 'Test123!',
        'role' => 'Regular User',
        'permissions' => ['read', 'write']
    ],
    'readonly' => [
        'password' => 'Read123!',
        'role' => 'Read-Only',
        'permissions' => ['read']
    ]
];

// Get credentials from request
$username = $_POST['username'] ?? $_GET['username'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';

$start_time = microtime(true);

// Validate credentials
$authenticated = false;
$user_info = null;

if (isset($valid_users[$username])) {
    if ($valid_users[$username]['password'] === $password) {
        $authenticated = true;
        $user_info = [
            'username' => $username,
            'role' => $valid_users[$username]['role'],
            'permissions' => $valid_users[$username]['permissions']
        ];
    }
}

$response_time = (microtime(true) - $start_time) * 1000;

// Prepare response
if ($authenticated) {
    // Successful authentication
    http_response_code(200);
    $response = [
        'success' => true,
        'authenticated' => true,
        'message' => 'Authentication successful',
        'user' => $user_info,
        'timestamp' => date('Y-m-d H:i:s'),
        'response_time_ms' => round($response_time, 2),
        'server' => gethostname(),
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
} else {
    // Failed authentication
    http_response_code(401);
    $response = [
        'success' => false,
        'authenticated' => false,
        'message' => 'Authentication failed',
        'error' => 'Invalid username or password',
        'timestamp' => date('Y-m-d H:i:s'),
        'response_time_ms' => round($response_time, 2),
        'server' => gethostname(),
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'attempted_username' => $username
    ];
}

// Add HAProxy info if available
if (isset($_SERVER['HTTP_X_HAPROXY_SERVER'])) {
    $response['haproxy_server'] = $_SERVER['HTTP_X_HAPROXY_SERVER'];
}

echo json_encode($response, JSON_PRETTY_PRINT);
exit;