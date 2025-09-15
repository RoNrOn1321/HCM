<?php
session_start();

// Set session for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
}

echo "<h2>Dashboard Queries Test</h2>";

try {
    require_once __DIR__ . '/config/database.php';

    echo "<p style='color: green;'>✅ Database connected</p>";

    // Test 1: Get total employees
    echo "<h3>Test 1: Total Employees</h3>";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM employees WHERE employment_status = 'Active'");
        $stmt->execute();
        $totalEmployees = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Total Active Employees: {$totalEmployees}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }

    // Test 2: Get payroll total
    echo "<h3>Test 2: Monthly Payroll</h3>";
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(ec.basic_salary), 0) as monthly_payroll
            FROM employees e
            LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
            WHERE e.employment_status = 'Active'
        ");
        $stmt->execute();
        $monthlyPayroll = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Monthly Payroll: {$monthlyPayroll}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }

    // Test 3: Get pending leaves
    echo "<h3>Test 3: Pending Leaves</h3>";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as pending_leaves FROM employee_leaves WHERE status = 'Pending'");
        $stmt->execute();
        $pendingLeaves = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Pending Leaves: {$pendingLeaves}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<p style='color: orange;'>ℹ️ This might be because employee_leaves table doesn't exist or has different column names</p>";
    }

    // Test 4: Get benefits enrollment
    echo "<h3>Test 4: Benefits Enrollment</h3>";
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT ei.employee_id) as enrolled_count
            FROM employee_insurance ei
            INNER JOIN employees e ON ei.employee_id = e.id
            WHERE e.employment_status = 'Active' AND ei.status = 'Active'
        ");
        $stmt->execute();
        $benefitsEnrolled = $stmt->fetchColumn();
        echo "<p style='color: green;'>✅ Benefits Enrolled: {$benefitsEnrolled}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<p style='color: orange;'>ℹ️ This might be because employee_insurance table doesn't exist or has different column names</p>";
    }

    // Test 5: Check what tables actually exist
    echo "<h3>Test 5: Available Tables</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Available tables:</p><ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . $e->getMessage() . "</p>";
}
?>