<?php
// Test without session_start()
echo "<h1>No Session Test</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";
echo "<p>This page loaded successfully without session_start()</p>";

echo "<h2>Check if session files are writable:</h2>";
$sessionPath = session_save_path();
if (empty($sessionPath)) {
    $sessionPath = sys_get_temp_dir();
}
echo "Session save path: " . $sessionPath . "<br>";
echo "Is writable: " . (is_writable($sessionPath) ? "YES" : "NO") . "<br>";

echo "<h2>Memory and execution limits:</h2>";
echo "Memory limit: " . ini_get('memory_limit') . "<br>";
echo "Max execution time: " . ini_get('max_execution_time') . "<br>";
?>