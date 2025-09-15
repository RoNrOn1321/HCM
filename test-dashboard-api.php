<?php
session_start();

// Simulate login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'Super Admin';
}

echo "<h2>Dashboard API Test</h2>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>Username: " . ($_SESSION['username'] ?? 'N/A') . "</p>";

// Test stats API
echo "<h3>Testing Stats API:</h3>";
$statsUrl = "http://localhost/HCM/api/dashboard.php?type=stats";
echo "<p>URL: $statsUrl</p>";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Cookie: " . $_SERVER['HTTP_COOKIE'] . "\r\n"
    ]
]);

$statsResponse = file_get_contents($statsUrl, false, $context);
echo "<pre>" . htmlspecialchars($statsResponse) . "</pre>";

// Test activities API
echo "<h3>Testing Activities API:</h3>";
$activitiesUrl = "http://localhost/HCM/api/dashboard.php?type=activities";
echo "<p>URL: $activitiesUrl</p>";

$activitiesResponse = file_get_contents($activitiesUrl, false, $context);
echo "<pre>" . htmlspecialchars($activitiesResponse) . "</pre>";

// Test chart API
echo "<h3>Testing Chart API:</h3>";
$chartUrl = "http://localhost/HCM/api/dashboard.php?type=chart";
echo "<p>URL: $chartUrl</p>";

$chartResponse = file_get_contents($chartUrl, false, $context);
echo "<pre>" . htmlspecialchars($chartResponse) . "</pre>";
?>