<?php
// -------------------- USER-AGENT TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Custom-User-Agent');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Detect browser/client info
function detectClient($user_agent) {
    $client_info = [
        'type' => 'unknown',
        'browser' => 'unknown',
        'version' => 'unknown',
        'os' => 'unknown',
        'is_mobile' => false,
        'is_bot' => false
    ];
    
    // Detect bots
    if (preg_match('/(bot|crawler|spider|scraper)/i', $user_agent)) {
        $client_info['type'] = 'bot';
        $client_info['is_bot'] = true;
        
        if (stripos($user_agent, 'googlebot') !== false) {
            $client_info['browser'] = 'Googlebot';
        } elseif (stripos($user_agent, 'bingbot') !== false) {
            $client_info['browser'] = 'Bingbot';
        }
    }
    // Detect mobile
    elseif (preg_match('/(mobile|android|iphone|ipad|ipod)/i', $user_agent)) {
        $client_info['type'] = 'mobile';
        $client_info['is_mobile'] = true;
    }
    // Desktop/other
    else {
        $client_info['type'] = 'desktop';
    }
    
    // Detect browser
    if (stripos($user_agent, 'Edge') !== false || stripos($user_agent, 'Edg/') !== false) {
        $client_info['browser'] = 'Edge';
        preg_match('/Edg[e]?\/([0-9.]+)/', $user_agent, $matches);
        $client_info['version'] = $matches[1] ?? 'unknown';
    } elseif (stripos($user_agent, 'Chrome') !== false) {
        $client_info['browser'] = 'Chrome';
        preg_match('/Chrome\/([0-9.]+)/', $user_agent, $matches);
        $client_info['version'] = $matches[1] ?? 'unknown';
    } elseif (stripos($user_agent, 'Safari') !== false) {
        $client_info['browser'] = 'Safari';
        preg_match('/Version\/([0-9.]+)/', $user_agent, $matches);
        $client_info['version'] = $matches[1] ?? 'unknown';
    } elseif (stripos($user_agent, 'Firefox') !== false) {
        $client_info['browser'] = 'Firefox';
        preg_match('/Firefox\/([0-9.]+)/', $user_agent, $matches);
        $client_info['version'] = $matches[1] ?? 'unknown';
    } elseif (stripos($user_agent, 'curl') !== false) {
        $client_info['browser'] = 'curl';
        preg_match('/curl\/([0-9.]+)/', $user_agent, $matches);
        $client_info['version'] = $matches[1] ?? 'unknown';
    }
    
    // Detect OS
    if (stripos($user_agent, 'Windows NT 10.0') !== false) {
        $client_info['os'] = 'Windows 10/11';
    } elseif (stripos($user_agent, 'Windows') !== false) {
        $client_info['os'] = 'Windows';
    } elseif (stripos($user_agent, 'Mac OS X') !== false) {
        preg_match('/Mac OS X ([0-9_]+)/', $user_agent, $matches);
        $version = isset($matches[1]) ? str_replace('_', '.', $matches[1]) : '';
        $client_info['os'] = 'macOS ' . $version;
    } elseif (stripos($user_agent, 'Linux') !== false) {
        $client_info['os'] = 'Linux';
    } elseif (stripos($user_agent, 'Android') !== false) {
        preg_match('/Android ([0-9.]+)/', $user_agent, $matches);
        $client_info['os'] = 'Android ' . ($matches[1] ?? '');
    } elseif (stripos($user_agent, 'iPhone') !== false || stripos($user_agent, 'iPad') !== false) {
        preg_match('/OS ([0-9_]+)/', $user_agent, $matches);
        $version = isset($matches[1]) ? str_replace('_', '.', $matches[1]) : '';
        $client_info['os'] = 'iOS ' . $version;
    }
    
    return $client_info;
}

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$custom_ua = $_SERVER['HTTP_X_CUSTOM_USER_AGENT'] ?? null;

$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'user_agent' => $user_agent,
    'custom_ua_header' => $custom_ua,
    'client_info' => detectClient($user_agent),
    'all_headers' => getallheaders(),
    'server_info' => [
        'hostname' => gethostname(),
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
exit;