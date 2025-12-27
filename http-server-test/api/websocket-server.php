<?php
// -------------------- WEBSOCKET ECHO SERVER --------------------
// Note: This is a simple WebSocket implementation for testing
// For production, use a proper WebSocket library like Ratchet

// Get connection info
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$headers = getallheaders();

// Check if this is a WebSocket upgrade request
$is_websocket = isset($headers['Upgrade']) && strtolower($headers['Upgrade']) === 'websocket';
$has_key = isset($headers['Sec-WebSocket-Key']);

if (!$is_websocket || !$has_key) {
    // Return info page if accessed via HTTP
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'WebSocket upgrade required',
        'message' => 'This endpoint requires a WebSocket connection',
        'usage' => 'Connect using: ws://your-server/api/websocket-server.php',
        'client_ip' => $client_ip,
        'headers_received' => $headers
    ], JSON_PRETTY_PRINT);
    exit;
}

// WebSocket handshake
$key = $headers['Sec-WebSocket-Key'];
$accept_key = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

// Send handshake response
header('HTTP/1.1 101 Switching Protocols');
header('Upgrade: websocket');
header('Connection: Upgrade');
header("Sec-WebSocket-Accept: $accept_key");
header('Sec-WebSocket-Version: 13');

// Flush headers
flush();

// Simple echo implementation
// In production, use a proper event loop
$socket = fopen('php://input', 'r');

while (!feof($socket)) {
    $data = fread($socket, 8192);
    if ($data) {
        // Echo back the data
        fwrite(STDOUT, $data);
        flush();
    }
}

fclose($socket);