<?php
// Test the department API via HTTP request
session_start();

// Mock a user session for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['authenticated'] = true;

echo "Testing Department API via HTTP Request\n";
echo "======================================\n\n";

// Make a request to the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/reports.php?type=department");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";
echo "Response:\n";
echo $response;
?>