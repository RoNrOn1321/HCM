<?php
session_start();

// Simulate login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
}

echo "<h2>Direct API Testing</h2>";
echo "<p>Session User ID: " . $_SESSION['user_id'] . "</p>";

// Test Dashboard Stats API
echo "<h3>Dashboard Stats API</h3>";
ob_start();
include 'api/dashboard.php';
$output = ob_get_clean();

// Reset for next test
$_GET = ['type' => 'stats'];
ob_start();
include 'api/dashboard.php';
$statsOutput = ob_get_clean();
echo "<pre>Stats Output: " . htmlspecialchars($statsOutput) . "</pre>";

// Test Profile API
echo "<h3>Profile API</h3>";
unset($_GET);
ob_start();
include 'api/profile.php';
$profileOutput = ob_get_clean();
echo "<pre>Profile Output: " . htmlspecialchars($profileOutput) . "</pre>";
?>