<?php
session_start();

// Mock a user session for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['authenticated'] = true;

// Test the department API directly
echo "Testing Department API Endpoint\n";
echo "===============================\n\n";

try {
    // Include the database config
    require_once 'config/database.php';

    // Test the getDepartmentReport function directly
    $user_id = 1;

    $stmt = $pdo->prepare("
        SELECT
            d.id,
            d.dept_name as department,
            d.dept_code,
            COUNT(e.id) as employee_count,
            AVG(ec.basic_salary) as avg_salary,
            SUM(ec.basic_salary) as total_salary,
            MIN(ec.basic_salary) as min_salary,
            MAX(ec.basic_salary) as max_salary
        FROM departments d
        LEFT JOIN employees e ON d.id = e.department_id AND e.employment_status = 'Active'
        LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
        GROUP BY d.id, d.dept_name, d.dept_code
        ORDER BY employee_count DESC
    ");

    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Raw query result:\n";
    print_r($departments);
    echo "\n";

    // Add mock attendance rates
    foreach ($departments as &$dept) {
        $dept['attendance_rate'] = rand(90, 98) + (rand(0, 9) / 10); // Mock between 90-98%
        $dept['performance_rating'] = $dept['attendance_rate'] >= 95 ? 'Excellent' :
                                    ($dept['attendance_rate'] >= 90 ? 'Good' : 'Needs Improvement');
    }

    $data = [
        'report_type' => 'department',
        'generated_at' => date('Y-m-d H:i:s'),
        'total_departments' => count($departments),
        'departments' => $departments
    ];

    echo "Final data structure:\n";
    print_r($data);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>