<?php
// Very simple test endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'success' => true,
    'message' => 'Simple endpoint working',
    'timestamp' => date('Y-m-d H:i:s'),
    'data' => [
        'test' => 'This is a test response'
    ]
]);
?>