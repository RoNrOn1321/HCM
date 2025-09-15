<?php
session_start();

// Set user session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';

echo "Testing Debug API\n";
echo "=================\n\n";

// Test the debug API
$url = "http://localhost/HCM/debug_reports_api.php?type=department";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

echo "Making request to: $url\n";
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $http_code\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response:\n";
echo $response;
echo "\n\n";

// Check error log
$log_file = 'C:\xampp\php\logs\php_error.log';
if (file_exists($log_file)) {
    echo "Recent error log entries:\n";
    echo "========================\n";
    $lines = file($log_file);
    $recent_lines = array_slice($lines, -10);
    foreach ($recent_lines as $line) {
        if (strpos($line, 'DEBUG:') !== false) {
            echo $line;
        }
    }
}
?>