<?php
// Test API functions directly
header('Content-Type: application/json');

echo "Testing API Functions Directly\n";
echo "==============================\n\n";

// Mock user session
session_start();
$_SESSION['user_id'] = 1;

try {
    require_once 'config/database.php';

    // Test department report function
    echo "Testing getDepartmentReport:\n";

    function getDepartmentReport($pdo, $user_id, $from_date, $to_date, $format) {
        try {
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

            echo json_encode([
                'success' => true,
                'message' => 'Department report generated successfully',
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error generating department report: ' . $e->getMessage()
            ]);
        }
    }

    getDepartmentReport($pdo, 1, '', '', 'json');

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>