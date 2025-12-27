<?php
// -------------------- UPLOAD SPEED TEST API --------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$start_time = microtime(true);

// Get uploaded chunk
if (!isset($_FILES['chunk'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No chunk uploaded']);
    exit;
}

$chunk = $_FILES['chunk'];
$chunk_size = $chunk['size'];
$uploaded = isset($_POST['uploaded']) ? intval($_POST['uploaded']) : 0;
$total = isset($_POST['total']) ? intval($_POST['total']) : 0;

// Verify chunk was uploaded successfully
if ($chunk['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Upload error',
        'code' => $chunk['error']
    ]);
    exit;
}

// Calculate progress
$new_uploaded = $uploaded + $chunk_size;
$progress = $total > 0 ? ($new_uploaded / $total) * 100 : 0;

// Process time
$process_time = (microtime(true) - $start_time) * 1000; // ms

// We don't actually save the file, just acknowledge receipt
// In production, you might want to save to a temp location or process the data

$response = [
    'success' => true,
    'chunk_size' => $chunk_size,
    'uploaded' => $new_uploaded,
    'total' => $total,
    'progress' => round($progress, 2),
    'process_time_ms' => round($process_time, 2),
    'server_time' => date('Y-m-d H:i:s')
];

echo json_encode($response);
exit;