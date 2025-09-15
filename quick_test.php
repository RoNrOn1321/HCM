<?php
// Quick test of the API without sessions
$_GET['type'] = 'benefits';

// Mock user ID
$user_id = 1;

// Test the database connection
try {
    require_once 'config/database.php';
    echo "Database connection test: ";
    $pdo->query("SELECT 1");
    echo "✓ Connected\n";
} catch (Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n";
    exit;
}

// Test a simple query
try {
    echo "Testing simple query: ";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Found " . $result['count'] . " active employees\n";
} catch (Exception $e) {
    echo "✗ Query failed: " . $e->getMessage() . "\n";
}

echo "Test complete.\n";
?>