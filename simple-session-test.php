<?php
// Simple session test without auth functions
session_start();

echo "<h1>Simple Session Test</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>Session Status:</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

echo "<h2>Session Data:</h2>";
if (empty($_SESSION)) {
    echo "❌ Session is EMPTY<br>";
    echo "This means you're not logged in or session was lost.<br>";
} else {
    echo "✅ Session has data:<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}

echo "<h2>Raw Session Variables:</h2>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "username: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";
echo "role: " . ($_SESSION['role'] ?? 'NOT SET') . "<br>";

echo "<h2>Login Status:</h2>";
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    echo "✅ You appear to be logged in<br>";
} else {
    echo "❌ You are NOT logged in<br>";
    echo "Go to <a href='views/login.php'>login page</a> first<br>";
}
?>