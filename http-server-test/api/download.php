<?php
// -------------------- DOWNLOAD SPEED TEST API --------------------
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

$size = isset($_GET['size']) ? intval($_GET['size']) : 1; // MB
$type = isset($_GET['type']) ? $_GET['type'] : 'random';

// Limit maximum size to prevent server overload
$max_size = 2048; // 2GB max
if ($size > $max_size) {
    http_response_code(400);
    die(json_encode(['error' => "Size exceeds maximum of {$max_size}MB"]));
}

$bytes = $size * 1024 * 1024;

// Set headers
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="test_' . $size . 'MB_' . $type . '.bin"');
header('Content-Length: ' . $bytes);
header('X-Download-Type: ' . $type);
header('X-Download-Size-MB: ' . $size);

// Disable output buffering
if (ob_get_level()) {
    ob_end_clean();
}

// Generate and send data
$chunk_size = 1024 * 1024; // 1MB chunks
$remaining = $bytes;

while ($remaining > 0 && connection_status() == 0) {
    $current_chunk = min($chunk_size, $remaining);
    
    switch ($type) {
        case 'zeros':
            // Compressible data (all zeros)
            echo str_repeat("\0", $current_chunk);
            break;
            
        case 'text':
            // Text data
            $text = str_repeat("The quick brown fox jumps over the lazy dog. ", intval($current_chunk / 46));
            echo substr($text, 0, $current_chunk);
            break;
            
        case 'random':
        default:
            // Random binary data (incompressible)
            echo random_bytes($current_chunk);
            break;
    }
    
    flush();
    $remaining -= $current_chunk;
}

exit;