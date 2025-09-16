<?php
echo "<h1>Simple Test Page</h1>";
echo "<p>If you can see this, PHP is working.</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Test basic navigation
echo "<h2>Navigation Test</h2>";
echo "<a href='views/settings.php'>Try Settings Page</a><br>";
echo "<a href='views/login.php'>Try Login Page</a><br>";
echo "<a href='views/index.php'>Try Dashboard</a><br>";

// Test session
session_start();
echo "<h2>Session Test</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session data: " . (empty($_SESSION) ? "Empty" : "Has data") . "<br>";

// Show current directory
echo "<h2>Directory Info</h2>";
echo "Current directory: " . getcwd() . "<br>";
echo "Script location: " . __FILE__ . "<br>";
?>