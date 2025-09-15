<?php
// Simple test script to check reports API without authentication
session_start();

// Mock a user session for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['authenticated'] = true;

// Test the reports API
echo "<h1>Testing Reports API</h1>\n";

// Test benefits report
echo "<h2>Testing Benefits Report:</h2>\n";
$benefits_url = "http://localhost/HCM/api/reports.php?type=benefits";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $benefits_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>" . htmlspecialchars($response) . "</pre>\n";

// Test charts data
echo "<h2>Testing Charts Data:</h2>\n";
$charts_url = "http://localhost/HCM/api/reports.php?type=charts";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $charts_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>" . htmlspecialchars($response) . "</pre>\n";

// Test dashboard metrics
echo "<h2>Testing Dashboard Metrics:</h2>\n";
$dashboard_url = "http://localhost/HCM/api/reports.php?type=dashboard_metrics";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboard_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
?>