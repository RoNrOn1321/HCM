<?php
// Debug version of reports API
session_start();

// Set JSON response header
header('Content-Type: application/json');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Log to file for debugging
error_log("DEBUG: Reports API called with type: " . ($_GET['type'] ?? 'none'));

// Check authentication
if (!isset($_SESSION['user_id'])) {
    error_log("DEBUG: No user session found");
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized - Please login first',
        'code' => 'AUTH_REQUIRED'
    ]);
    exit();
}

error_log("DEBUG: User session found: " . $_SESSION['user_id']);

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? '';

try {
    require_once 'config/database.php';
    error_log("DEBUG: Database connected successfully");

    if ($type === 'department') {
        error_log("DEBUG: Processing department request");

        $stmt = $pdo->prepare("
            SELECT
                d.id,
                d.dept_name as department,
                d.dept_code,
                COUNT(e.id) as employee_count
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.employment_status = 'Active'
            GROUP BY d.id, d.dept_name, d.dept_code
            ORDER BY employee_count DESC
            LIMIT 3
        ");

        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("DEBUG: Found " . count($departments) . " departments");

        // Add mock attendance rates
        foreach ($departments as &$dept) {
            $dept['attendance_rate'] = rand(90, 98) + (rand(0, 9) / 10);
            $dept['performance_rating'] = $dept['attendance_rate'] >= 95 ? 'Excellent' : 'Good';
        }

        $data = [
            'report_type' => 'department',
            'generated_at' => date('Y-m-d H:i:s'),
            'total_departments' => count($departments),
            'departments' => $departments
        ];

        error_log("DEBUG: Sending successful response");
        echo json_encode([
            'success' => true,
            'message' => 'Department report generated successfully',
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        error_log("DEBUG: Invalid type requested: " . $type);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid report type: ' . $type
        ]);
    }
} catch (Exception $e) {
    error_log("DEBUG: Exception occurred: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>