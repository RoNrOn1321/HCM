<?php
session_start();

// Create test session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';

echo "Session created with ID: " . session_id() . "\n";
echo "Session User ID: " . $_SESSION['user_id'] . "\n";
echo "Session Username: " . $_SESSION['username'] . "\n";

// Save session ID to file for curl usage
file_put_contents('test_session_id.txt', session_id());
?>