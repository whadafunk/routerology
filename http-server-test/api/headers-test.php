<?php
// -------------------- CUSTOM HEADERS TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Expose-Headers: *');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get all request headers
$request_headers = getallheaders();

// Categorize headers
$custom_headers = [];
$standard_headers = [];
$proxy_headers = [];

foreach ($request_headers as $name => $value) {
    $lower_name = strtolower($name);
    
    if (strpos($lower_name, 'x-custom-') === 0) {
        $custom_headers[$name] = $value;
    } elseif (in_array($lower_name, ['x-forwarded-for', 'x-forwarded-proto', 'x-forwarded-port', 'x-real-ip', 'forwarded'])) {
        $proxy_headers[$name] = $value;
    } else {
        $standard_headers[$name] = $value;
    }
}

// Check if headers were modified/added by proxy
$headers_analysis = [
    'total_headers' => count($request_headers),
    'custom_headers_count' => count($custom_headers),
    'proxy_headers_detected' => !empty($proxy_headers),
    'behind_proxy' => !empty($proxy_headers) || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
];

$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => [
        'hostname' => gethostname(),
        'ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
        'port' => $_SERVER['SERVER_PORT'] ?? 'unknown'
    ],
    'client' => [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'real_ip' => $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ],
    'analysis' => $headers_analysis,
    'headers' => [
        'custom' => $custom_headers,
        'proxy' => $proxy_headers,
        'standard' => $standard_headers
    ],
    'all_headers' => $request_headers
];

// Add information about specific custom headers
if (!empty($custom_headers)) {
    $response['custom_headers_info'] = [];
    foreach ($custom_headers as $name => $value) {
        $response['custom_headers_info'][] = [
            'name' => $name,
            'value' => $value,
            'length' => strlen($value),
            'type' => detectHeaderType($value)
        ];
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);

function detectHeaderType($value) {
    if (is_numeric($value)) {
        return 'numeric';
    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
        return 'url';
    } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return 'email';
    } elseif (preg_match('/^[a-f0-9]{32,}$/i', $value)) {
        return 'hash/token';
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
        return 'datetime';
    } else {
        return 'string';
    }
}

exit;