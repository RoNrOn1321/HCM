<?php
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'hcm_system';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test basic employee query
    $stmt = $pdo->prepare("
        SELECT
            e.id,
            e.employee_id,
            CONCAT(e.first_name, ' ', e.last_name) as name,
            COALESCE(ec.basic_salary, 0) as basic_salary,
            'Pending' as status
        FROM employees e
        LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
        WHERE e.employment_status = 'Active'
        LIMIT 3
    ");

    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Test payroll_status table
    $statusStmt = $pdo->query("SELECT COUNT(*) as count FROM payroll_status");
    $statusCount = $statusStmt->fetch(PDO::FETCH_ASSOC)['count'];

    echo json_encode([
        'success' => true,
        'database_connection' => 'OK',
        'employee_records' => count($records),
        'sample_employees' => $records,
        'payroll_status_table' => 'EXISTS',
        'payroll_status_count' => $statusCount,
        'message' => 'Database test successful'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Database test failed'
    ]);
}
?>