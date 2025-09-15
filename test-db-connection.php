<?php
session_start();

echo "<h2>Database Connection Test</h2>";

try {
    require_once __DIR__ . '/config/database.php';

    echo "<p style='color: green;'>✅ Database config loaded successfully</p>";

    // Test basic query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();

    if ($result && $result['test'] == 1) {
        echo "<p style='color: green;'>✅ Database connection working</p>";
    } else {
        echo "<p style='color: red;'>❌ Database query failed</p>";
    }

    // Test employees table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employees");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Employees table accessible - Count: {$count}</p>";

    // Test departments table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM departments");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Departments table accessible - Count: {$count}</p>";

    // Test roles table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Roles table accessible - Count: {$count}</p>";

    // Test employee_compensation table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employee_compensation");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Employee_compensation table accessible - Count: {$count}</p>";

    // Test employee_leaves table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employee_leaves");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Employee_leaves table accessible - Count: {$count}</p>";

    // Test audit_logs table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM audit_logs");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Audit_logs table accessible - Count: {$count}</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<pre>Error details: " . print_r($e, true) . "</pre>";
}

// Test session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    echo "<p style='color: blue;'>ℹ️ Simulated user login for testing</p>";
}

echo "<p>Session User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "</p>";
echo "<p>Session Username: " . ($_SESSION['username'] ?? 'Not set') . "</p>";
?>