<?php
// Simple test to verify dependents API works with session auth
session_start();

// Simulate logged in user for testing
$_SESSION['authenticated'] = true;
$_SESSION['user_id'] = 1; // Admin user
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';

echo "<h1>Dependents API Test</h1>";
echo "<p><strong>Session Data:</strong></p>";
echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";

// Test direct include
echo "<h2>Direct API Test</h2>";
ob_start();
include 'api/dependents.php';
$api_response = ob_get_clean();

echo "<p><strong>API Response:</strong></p>";
echo "<pre>" . htmlspecialchars($api_response) . "</pre>";
?>