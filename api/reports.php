<?php
// reports.php - API endpoint for reports and analytics

require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../includes/ApiResponse.php';
require_once '../includes/auth_helper.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set content type
header('Content-Type: application/json');

// Require authentication
$auth = requireApiAuth();
if (!$auth['success']) {
    echo ApiResponse::error($auth['message'], 401);
    exit;
}

$user = $auth['user'];
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim($path, '/'));

try {
    $db = new Database();
    $conn = $db->getConnection();

    switch ($method) {
        case 'GET':
            handleGet($conn, $user);
            break;
        case 'POST':
            handlePost($conn, $user);
            break;
        default:
            echo ApiResponse::error('Method not allowed', 405);
    }
} catch (Exception $e) {
    echo ApiResponse::error('Internal server error: ' . $e->getMessage(), 500);
}

function handleGet($conn, $user) {
    // Get query parameters
    $type = $_GET['type'] ?? '';
    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';
    $department_id = $_GET['department_id'] ?? '';
    $format = $_GET['format'] ?? 'json';

    switch ($type) {
        case 'dashboard_metrics':
            getDashboardMetrics($conn, $user);
            break;
        case 'employee':
            getEmployeeReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'attendance':
            getAttendanceReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'payroll':
            getPayrollReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'leave':
            getLeaveReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'department':
            getDepartmentReport($conn, $user, $from_date, $to_date, $format);
            break;
        case 'performance':
            getPerformanceReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'benefits':
            getBenefitsReport($conn, $user, $from_date, $to_date, $department_id, $format);
            break;
        case 'charts':
            getChartsData($conn, $user);
            break;
        default:
            echo ApiResponse::error('Invalid report type', 400);
    }
}

function handlePost($conn, $user) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo ApiResponse::error('Invalid JSON data', 400);
        return;
    }

    $action = $input['action'] ?? '';

    switch ($action) {
        case 'generate_custom_report':
            generateCustomReport($conn, $user, $input);
            break;
        case 'export_report':
            exportReport($conn, $user, $input);
            break;
        default:
            echo ApiResponse::error('Invalid action', 400);
    }
}

function getDashboardMetrics($conn, $user) {
    try {
        // Get total employees
        $stmt = $conn->prepare("SELECT COUNT(*) as total_employees FROM employees WHERE employment_status = 'Active'");
        $stmt->execute();
        $total_employees = $stmt->fetch(PDO::FETCH_ASSOC)['total_employees'];

        // Get total departments
        $stmt = $conn->prepare("SELECT COUNT(*) as total_departments FROM departments");
        $stmt->execute();
        $total_departments = $stmt->fetch(PDO::FETCH_ASSOC)['total_departments'];

        // Get average attendance (mock data for now - would need attendance_records table)
        $avg_attendance = 94.2;

        // Get total payroll
        $stmt = $conn->prepare("
            SELECT SUM(ec.basic_salary) as total_salary
            FROM employee_compensation ec
            JOIN employees e ON ec.employee_id = e.id
            WHERE ec.is_active = 1 AND e.employment_status = 'Active'
        ");
        $stmt->execute();
        $total_salary = $stmt->fetch(PDO::FETCH_ASSOC)['total_salary'] ?? 0;
        $total_payroll = '₱' . number_format($total_salary / 1000000, 1) . 'M';

        $data = [
            'total_employees' => (int)$total_employees,
            'total_departments' => (int)$total_departments,
            'avg_attendance' => $avg_attendance,
            'total_payroll' => $total_payroll
        ];

        echo ApiResponse::success('Dashboard metrics retrieved successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error retrieving dashboard metrics: ' . $e->getMessage(), 500);
    }
}

function getEmployeeReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        $where_clause = "WHERE e.employment_status = 'Active'";
        $params = [];

        if ($department_id) {
            $where_clause .= " AND e.department_id = :department_id";
            $params[':department_id'] = $department_id;
        }

        if ($from_date) {
            $where_clause .= " AND e.hire_date >= :from_date";
            $params[':from_date'] = $from_date;
        }

        if ($to_date) {
            $where_clause .= " AND e.hire_date <= :to_date";
            $params[':to_date'] = $to_date;
        }

        $stmt = $conn->prepare("
            SELECT
                e.id,
                e.employee_id,
                CONCAT(e.first_name, ' ', IFNULL(e.middle_name, ''), ' ', e.last_name) as full_name,
                e.email,
                e.phone,
                e.hire_date,
                e.employment_status,
                e.employee_type,
                d.dept_name as department,
                p.position_title as position,
                ec.basic_salary
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN positions p ON e.position_id = p.id
            LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
            $where_clause
            ORDER BY e.first_name, e.last_name
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'report_type' => 'employee',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'department_id' => $department_id
            ],
            'total_records' => count($employees),
            'employees' => $employees
        ];

        echo ApiResponse::success('Employee report generated successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating employee report: ' . $e->getMessage(), 500);
    }
}

function getAttendanceReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        // Mock attendance data - in real implementation, this would query attendance_records table
        $attendance_data = [
            'report_type' => 'attendance',
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_employees' => 248,
                'avg_attendance_rate' => 94.2,
                'total_working_days' => 22,
                'total_present_days' => 5135,
                'total_absent_days' => 321
            ],
            'monthly_trends' => [
                'Jan' => 95.2, 'Feb' => 94.8, 'Mar' => 96.1, 'Apr' => 93.7,
                'May' => 94.5, 'Jun' => 95.8, 'Jul' => 92.3, 'Aug' => 94.1,
                'Sep' => 96.4, 'Oct' => 95.7, 'Nov' => 94.9, 'Dec' => 95.3
            ],
            'department_breakdown' => [
                'IT Department' => 96.5,
                'Finance' => 98.1,
                'HR' => 95.3,
                'Marketing' => 92.8,
                'Operations' => 93.7,
                'Sales' => 91.4,
                'Legal' => 97.2,
                'Admin' => 94.8
            ]
        ];

        echo ApiResponse::success('Attendance report generated successfully', $attendance_data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating attendance report: ' . $e->getMessage(), 500);
    }
}

function getPayrollReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        $where_clause = "WHERE e.employment_status = 'Active'";
        $params = [];

        if ($department_id) {
            $where_clause .= " AND e.department_id = :department_id";
            $params[':department_id'] = $department_id;
        }

        // Get payroll summary
        $stmt = $conn->prepare("
            SELECT
                COUNT(e.id) as total_employees,
                SUM(ec.basic_salary) as total_basic_salary,
                AVG(ec.basic_salary) as avg_salary,
                MIN(ec.basic_salary) as min_salary,
                MAX(ec.basic_salary) as max_salary
            FROM employees e
            LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
            $where_clause
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get department breakdown
        $stmt = $conn->prepare("
            SELECT
                d.dept_name as department,
                COUNT(e.id) as employee_count,
                SUM(ec.basic_salary) as total_salary,
                AVG(ec.basic_salary) as avg_salary
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
            $where_clause
            GROUP BY d.id, d.dept_name
            ORDER BY total_salary DESC
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $department_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'report_type' => 'payroll',
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'department_breakdown' => $department_breakdown,
            'breakdown_categories' => [
                'Basic Salary' => floatval($summary['total_basic_salary'] ?? 0),
                'Allowances' => floatval($summary['total_basic_salary'] ?? 0) * 0.15, // Mock 15%
                'Overtime' => floatval($summary['total_basic_salary'] ?? 0) * 0.08, // Mock 8%
                'Bonuses' => floatval($summary['total_basic_salary'] ?? 0) * 0.05  // Mock 5%
            ]
        ];

        echo ApiResponse::success('Payroll report generated successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating payroll report: ' . $e->getMessage(), 500);
    }
}

function getLeaveReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        $where_clause = "";
        $params = [];

        if ($from_date && $to_date) {
            $where_clause = "WHERE el.start_date >= :from_date AND el.end_date <= :to_date";
            $params[':from_date'] = $from_date;
            $params[':to_date'] = $to_date;
        }

        if ($department_id) {
            $where_clause .= $where_clause ? " AND" : "WHERE";
            $where_clause .= " e.department_id = :department_id";
            $params[':department_id'] = $department_id;
        }

        // Get leave statistics
        $stmt = $conn->prepare("
            SELECT
                lt.type_name as leave_type,
                COUNT(el.id) as total_requests,
                SUM(el.days_requested) as total_days,
                AVG(el.days_requested) as avg_days_per_request
            FROM employee_leaves el
            LEFT JOIN leave_types lt ON el.leave_type_id = lt.id
            LEFT JOIN employees e ON el.employee_id = e.id
            $where_clause
            GROUP BY el.leave_type_id, lt.type_name
            ORDER BY total_requests DESC
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $leave_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get status breakdown
        $stmt = $conn->prepare("
            SELECT
                el.status,
                COUNT(el.id) as count
            FROM employee_leaves el
            LEFT JOIN employees e ON el.employee_id = e.id
            $where_clause
            GROUP BY el.status
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $status_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'report_type' => 'leave',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'department_id' => $department_id
            ],
            'leave_statistics' => $leave_stats,
            'status_breakdown' => $status_breakdown
        ];

        echo ApiResponse::success('Leave report generated successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating leave report: ' . $e->getMessage(), 500);
    }
}

function getDepartmentReport($conn, $user, $from_date, $to_date, $format) {
    try {
        $stmt = $conn->prepare("
            SELECT
                d.id,
                d.dept_name as department,
                d.description,
                COUNT(e.id) as employee_count,
                AVG(ec.basic_salary) as avg_salary,
                SUM(ec.basic_salary) as total_salary,
                MIN(ec.basic_salary) as min_salary,
                MAX(ec.basic_salary) as max_salary
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.employment_status = 'Active'
            LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
            GROUP BY d.id, d.dept_name, d.description
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

        echo ApiResponse::success('Department report generated successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating department report: ' . $e->getMessage(), 500);
    }
}

function getPerformanceReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        // Mock performance data - in real implementation, this would query performance evaluation tables
        $performance_data = [
            'report_type' => 'performance',
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => [
                'total_evaluations' => 245,
                'avg_rating' => 4.2,
                'completed_evaluations' => 198,
                'pending_evaluations' => 47
            ],
            'rating_distribution' => [
                'Excellent (5.0)' => 42,
                'Very Good (4.0-4.9)' => 89,
                'Good (3.0-3.9)' => 67,
                'Needs Improvement (2.0-2.9)' => 38,
                'Poor (1.0-1.9)' => 9
            ],
            'department_performance' => [
                'IT Department' => 4.5,
                'Finance' => 4.3,
                'HR' => 4.1,
                'Marketing' => 3.9,
                'Operations' => 4.0,
                'Sales' => 3.8,
                'Legal' => 4.6,
                'Admin' => 4.2
            ]
        ];

        echo ApiResponse::success('Performance report generated successfully', $performance_data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating performance report: ' . $e->getMessage(), 500);
    }
}

function getBenefitsReport($conn, $user, $from_date, $to_date, $department_id, $format) {
    try {
        $where_clause = "";
        $params = [];

        if ($department_id) {
            $where_clause = "WHERE e.department_id = :department_id";
            $params[':department_id'] = $department_id;
        }

        // Get insurance summary
        $stmt = $conn->prepare("
            SELECT
                ip.plan_name,
                COUNT(ei.id) as enrolled_count,
                SUM(ei.employee_contribution) as total_employee_contributions,
                SUM(ei.employer_contribution) as total_employer_contributions,
                AVG(ei.employee_contribution) as avg_employee_contribution,
                AVG(ei.employer_contribution) as avg_employer_contribution
            FROM employee_insurance ei
            LEFT JOIN insurance_plans ip ON ei.plan_id = ip.id
            LEFT JOIN employees e ON ei.employee_id = e.id
            $where_clause
            GROUP BY ip.id, ip.plan_name
            ORDER BY enrolled_count DESC
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $insurance_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get dependents covered by HMO
        $stmt = $conn->prepare("
            SELECT
                COUNT(ed.id) as total_dependents,
                SUM(CASE WHEN ed.is_hmo_covered = 1 THEN 1 ELSE 0 END) as hmo_covered_dependents,
                AVG(CASE WHEN ed.is_hmo_covered = 1 THEN 1 ELSE 0 END) * 100 as hmo_coverage_percentage
            FROM employee_dependents ed
            LEFT JOIN employees e ON ed.employee_id = e.id
            $where_clause
        ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $dependents_summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get benefits utilization by department
        $stmt = $conn->prepare("
            SELECT
                d.dept_name as department,
                COUNT(DISTINCT ei.employee_id) as employees_with_benefits,
                COUNT(DISTINCT e.id) as total_employees,
                COUNT(DISTINCT ei.employee_id) / COUNT(DISTINCT e.id) * 100 as utilization_rate,
                SUM(ei.employee_contribution + ei.employer_contribution) as total_benefit_cost
            FROM departments d
            LEFT JOIN employees e ON d.id = e.department_id AND e.employment_status = 'Active'
            LEFT JOIN employee_insurance ei ON e.id = ei.employee_id
            " . ($department_id ? "WHERE d.id = :department_id" : "") . "
            GROUP BY d.id, d.dept_name
            ORDER BY utilization_rate DESC
        ");

        if ($department_id) {
            $stmt->bindParam(':department_id', $department_id);
        }

        $stmt->execute();
        $department_utilization = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get top benefit plans
        $benefit_plans_distribution = [];
        foreach ($insurance_summary as $plan) {
            $benefit_plans_distribution[$plan['plan_name']] = (int)$plan['enrolled_count'];
        }

        // Calculate overall statistics
        $total_enrolled = array_sum($benefit_plans_distribution);
        $total_cost = array_sum(array_column($insurance_summary, 'total_employee_contributions')) +
                     array_sum(array_column($insurance_summary, 'total_employer_contributions'));

        $data = [
            'report_type' => 'benefits',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'department_id' => $department_id
            ],
            'summary' => [
                'total_enrolled_employees' => $total_enrolled,
                'total_benefit_cost' => $total_cost,
                'avg_cost_per_employee' => $total_enrolled > 0 ? $total_cost / $total_enrolled : 0,
                'total_dependents' => (int)$dependents_summary['total_dependents'],
                'hmo_covered_dependents' => (int)$dependents_summary['hmo_covered_dependents'],
                'dependent_hmo_coverage_rate' => round((float)$dependents_summary['hmo_coverage_percentage'], 2)
            ],
            'insurance_plans' => $insurance_summary,
            'benefit_plans_distribution' => $benefit_plans_distribution,
            'department_utilization' => $department_utilization,
            'cost_breakdown' => [
                'employee_contributions' => array_sum(array_column($insurance_summary, 'total_employee_contributions')),
                'employer_contributions' => array_sum(array_column($insurance_summary, 'total_employer_contributions'))
            ]
        ];

        echo ApiResponse::success('Benefits report generated successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating benefits report: ' . $e->getMessage(), 500);
    }
}

function getChartsData($conn, $user) {
    try {
        // Get attendance trends (mock data)
        $attendance_trends = [
            'Jan' => 95.2, 'Feb' => 94.8, 'Mar' => 96.1, 'Apr' => 93.7,
            'May' => 94.5, 'Jun' => 95.8, 'Jul' => 92.3, 'Aug' => 94.1,
            'Sep' => 96.4, 'Oct' => 95.7, 'Nov' => 94.9, 'Dec' => 95.3
        ];

        // Get leave statistics
        $stmt = $conn->prepare("
            SELECT
                lt.type_name as leave_type,
                COUNT(el.id) as count
            FROM employee_leaves el
            LEFT JOIN leave_types lt ON el.leave_type_id = lt.id
            WHERE el.status = 'Approved'
            GROUP BY el.leave_type_id, lt.type_name
            ORDER BY count DESC
            LIMIT 5
        ");
        $stmt->execute();
        $leave_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert to chart format
        $leave_chart_data = [];
        foreach ($leave_stats as $stat) {
            $leave_chart_data[$stat['leave_type']] = intval($stat['count']);
        }

        // Get payroll breakdown
        $stmt = $conn->prepare("
            SELECT SUM(ec.basic_salary) as total_basic_salary
            FROM employee_compensation ec
            JOIN employees e ON ec.employee_id = e.id
            WHERE ec.is_active = 1 AND e.employment_status = 'Active'
        ");
        $stmt->execute();
        $total_salary = $stmt->fetch(PDO::FETCH_ASSOC)['total_basic_salary'] ?? 0;

        $payroll_breakdown = [
            'Basic Salary' => floatval($total_salary),
            'Allowances' => floatval($total_salary) * 0.15,
            'Overtime' => floatval($total_salary) * 0.08,
            'Bonuses' => floatval($total_salary) * 0.05
        ];

        // Get department attendance (mock data)
        $stmt = $conn->prepare("SELECT dept_name FROM departments ORDER BY dept_name");
        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $department_attendance = [];
        foreach ($departments as $dept) {
            $department_attendance[$dept] = rand(90, 98) + (rand(0, 9) / 10);
        }

        $data = [
            'attendance_trends' => $attendance_trends,
            'leave_statistics' => $leave_chart_data,
            'payroll_breakdown' => $payroll_breakdown,
            'department_attendance' => $department_attendance
        ];

        echo ApiResponse::success('Charts data retrieved successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error retrieving charts data: ' . $e->getMessage(), 500);
    }
}

function generateCustomReport($conn, $user, $input) {
    try {
        $report_type = $input['report_type'] ?? '';
        $from_date = $input['from_date'] ?? '';
        $to_date = $input['to_date'] ?? '';
        $department_id = $input['department_id'] ?? '';
        $format = $input['format'] ?? 'json';

        if (!$report_type) {
            echo ApiResponse::error('Report type is required', 400);
            return;
        }

        // Generate the appropriate report based on type
        switch ($report_type) {
            case 'employee':
                getEmployeeReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            case 'attendance':
                getAttendanceReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            case 'payroll':
                getPayrollReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            case 'leave':
                getLeaveReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            case 'department':
                getDepartmentReport($conn, $user, $from_date, $to_date, $format);
                break;
            case 'performance':
                getPerformanceReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            case 'benefits':
                getBenefitsReport($conn, $user, $from_date, $to_date, $department_id, $format);
                break;
            default:
                echo ApiResponse::error('Invalid report type', 400);
        }
    } catch (Exception $e) {
        echo ApiResponse::error('Error generating custom report: ' . $e->getMessage(), 500);
    }
}

function exportReport($conn, $user, $input) {
    try {
        $report_data = $input['report_data'] ?? [];
        $format = $input['format'] ?? 'csv';
        $filename = $input['filename'] ?? 'report_' . date('Y-m-d_H-i-s');

        if (empty($report_data)) {
            echo ApiResponse::error('Report data is required', 400);
            return;
        }

        $data = [
            'export_format' => $format,
            'filename' => $filename . '.' . $format,
            'download_url' => '/api/reports/download/' . $filename . '.' . $format,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ];

        echo ApiResponse::success('Report export prepared successfully', $data);
    } catch (Exception $e) {
        echo ApiResponse::error('Error exporting report: ' . $e->getMessage(), 500);
    }
}
?>