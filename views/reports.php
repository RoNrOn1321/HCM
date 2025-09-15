<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();

// Mock report data
$reportSummary = [
    'total_employees' => 248,
    'total_departments' => 8,
    'avg_attendance' => 94.2,
    'total_payroll' => '₱9.7M'
];

// Mock department data
$departmentData = [
    ['name' => 'IT Department', 'employees' => 45, 'avg_salary' => 65000, 'attendance' => 96.5],
    ['name' => 'Finance', 'employees' => 32, 'avg_salary' => 58000, 'attendance' => 98.1],
    ['name' => 'HR', 'employees' => 28, 'avg_salary' => 52000, 'attendance' => 95.3],
    ['name' => 'Marketing', 'employees' => 35, 'avg_salary' => 48000, 'attendance' => 92.8],
    ['name' => 'Operations', 'employees' => 42, 'avg_salary' => 55000, 'attendance' => 93.7],
    ['name' => 'Sales', 'employees' => 38, 'avg_salary' => 51000, 'attendance' => 91.4],
    ['name' => 'Legal', 'employees' => 15, 'avg_salary' => 75000, 'attendance' => 97.2],
    ['name' => 'Admin', 'employees' => 13, 'avg_salary' => 42000, 'attendance' => 94.8]
];

// Mock attendance trends (last 12 months)
$attendanceTrends = [
    'Jan' => 95.2, 'Feb' => 94.8, 'Mar' => 96.1, 'Apr' => 93.7,
    'May' => 94.5, 'Jun' => 95.8, 'Jul' => 92.3, 'Aug' => 94.1,
    'Sep' => 96.4, 'Oct' => 95.7, 'Nov' => 94.9, 'Dec' => 95.3
];

// Mock leave statistics
$leaveStats = [
    'Annual Leave' => 45,
    'Sick Leave' => 28,
    'Personal Leave' => 15,
    'Maternity Leave' => 8,
    'Emergency Leave' => 12
];

// Mock payroll breakdown
$payrollBreakdown = [
    'Basic Salary' => 6500000,
    'Allowances' => 1800000,
    'Overtime' => 950000,
    'Bonuses' => 720000
];

// Available report types
$reportTypes = [
    [
        'id' => 'employee',
        'name' => 'Employee Report',
        'description' => 'Comprehensive employee data and statistics',
        'icon' => 'fas fa-users',
        'color' => 'blue'
    ],
    [
        'id' => 'attendance',
        'name' => 'Attendance Report',
        'description' => 'Employee attendance tracking and analysis',
        'icon' => 'fas fa-clock',
        'color' => 'green'
    ],
    [
        'id' => 'payroll',
        'name' => 'Payroll Report',
        'description' => 'Salary and compensation breakdown',
        'icon' => 'fas fa-money-bill-wave',
        'color' => 'yellow'
    ],
    [
        'id' => 'leave',
        'name' => 'Leave Report',
        'description' => 'Leave requests and balance analysis',
        'icon' => 'fas fa-calendar-times',
        'color' => 'purple'
    ],
    [
        'id' => 'department',
        'name' => 'Department Report',
        'description' => 'Department-wise performance metrics',
        'icon' => 'fas fa-building',
        'color' => 'red'
    ],
    [
        'id' => 'performance',
        'name' => 'Performance Report',
        'description' => 'Employee performance evaluation data',
        'icon' => 'fas fa-chart-line',
        'color' => 'indigo'
    ]
];

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'generate_report':
                $success = "Report generated successfully!";
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - HCM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1b68ff',
                        secondary: '#6c757d',
                        success: '#3ad29f',
                        danger: '#dc3545',
                        warning: '#eea303',
                        info: '#17a2b8',
                        light: '#f8f9fa',
                        dark: '#343a40'
                    },
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Top Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="p-4 sm:ml-64">
        <div class="p-4 rounded-lg mt-14">
            <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                    <p class="text-gray-600">Generate comprehensive reports and analyze HR data</p>
                </div>
                <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center" onclick="openModal('custom-report-modal')">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Custom Report
                </button>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Employees</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $reportSummary['total_employees']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-building text-success text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Departments</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $reportSummary['total_departments']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-warning text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg Attendance</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $reportSummary['avg_attendance']; ?>%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Payroll</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $reportSummary['total_payroll']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Types Grid -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Reports</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($reportTypes as $report): ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer" onclick="generateReport('<?php echo $report['id']; ?>')">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-<?php echo $report['color']; ?>-100 rounded-lg">
                                <i class="<?php echo $report['icon']; ?> text-<?php echo $report['color']; ?>-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($report['name']); ?></h3>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mb-3"><?php echo htmlspecialchars($report['description']); ?></p>
                        <button class="text-<?php echo $report['color']; ?>-600 hover:text-<?php echo $report['color']; ?>-800 text-sm font-medium">
                            Generate Report <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Attendance Trends Chart -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Attendance Trends</h3>
                        <div class="flex space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                <!-- Leave Distribution Chart -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Leave Distribution</h3>
                        <div class="flex space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="leaveChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Additional Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Payroll Breakdown Chart -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Payroll Breakdown</h3>
                        <div class="flex space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="payrollChart"></canvas>
                    </div>
                </div>

                <!-- Department Statistics -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Department Statistics</h3>
                        <div class="flex space-x-2">
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Data Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Department Performance</h3>
                        <p class="text-sm text-gray-600">Detailed breakdown by department</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                        <button class="bg-primary text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-print mr-1"></i>
                            Print
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Department</th>
                                <th class="px-6 py-3">Employees</th>
                                <th class="px-6 py-3">Avg Salary</th>
                                <th class="px-6 py-3">Attendance Rate</th>
                                <th class="px-6 py-3">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departmentData as $dept): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($dept['name']); ?></td>
                                <td class="px-6 py-4"><?php echo $dept['employees']; ?></td>
                                <td class="px-6 py-4">₱<?php echo number_format($dept['avg_salary']); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="mr-2"><?php echo $dept['attendance']; ?>%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $dept['attendance']; ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="<?php echo $dept['attendance'] >= 95 ? 'bg-green-100 text-green-800' : ($dept['attendance'] >= 90 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?> text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?php echo $dept['attendance'] >= 95 ? 'Excellent' : ($dept['attendance'] >= 90 ? 'Good' : 'Needs Improvement'); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Report Modal -->
    <div id="custom-report-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('custom-report-modal')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate_report">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Generate Custom Report</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('custom-report-modal')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                                <select name="report_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Report Type</option>
                                    <option value="employee">Employee Report</option>
                                    <option value="attendance">Attendance Report</option>
                                    <option value="payroll">Payroll Report</option>
                                    <option value="leave">Leave Report</option>
                                    <option value="department">Department Report</option>
                                    <option value="performance">Performance Report</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <input type="date" name="from_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <input type="date" name="to_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">All Departments</option>
                                    <option value="IT">IT Department</option>
                                    <option value="Finance">Finance</option>
                                    <option value="HR">HR</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Operations">Operations</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Legal">Legal</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Output Format</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="format" value="pdf" class="mr-2" checked>
                                        <span class="text-sm">PDF</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="format" value="excel" class="mr-2">
                                        <span class="text-sm">Excel</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="format" value="csv" class="mr-2">
                                        <span class="text-sm">CSV</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="button" class="mr-3 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400" onclick="closeModal('custom-report-modal')">Cancel</button>
                                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generate Report</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for Interactivity -->
    <?php include 'includes/scripts.php'; ?>

    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Attendance Trends Chart
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_keys($attendanceTrends)); ?>,
                    datasets: [{
                        label: 'Attendance %',
                        data: <?php echo json_encode(array_values($attendanceTrends)); ?>,
                        borderColor: '#1b68ff',
                        backgroundColor: 'rgba(27, 104, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 90,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Leave Distribution Chart
            const leaveCtx = document.getElementById('leaveChart').getContext('2d');
            new Chart(leaveCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_keys($leaveStats)); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_values($leaveStats)); ?>,
                        backgroundColor: [
                            '#1b68ff',
                            '#dc3545',
                            '#6f42c1',
                            '#e91e63',
                            '#ff9800'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Payroll Breakdown Chart
            const payrollCtx = document.getElementById('payrollChart').getContext('2d');
            new Chart(payrollCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($payrollBreakdown)); ?>,
                    datasets: [{
                        label: 'Amount (₱)',
                        data: <?php echo json_encode(array_values($payrollBreakdown)); ?>,
                        backgroundColor: [
                            '#1b68ff',
                            '#3ad29f',
                            '#eea303',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });

            // Department Chart
            const departmentCtx = document.getElementById('departmentChart').getContext('2d');
            new Chart(departmentCtx, {
                type: 'radar',
                data: {
                    labels: <?php echo json_encode(array_column($departmentData, 'name')); ?>,
                    datasets: [{
                        label: 'Attendance Rate',
                        data: <?php echo json_encode(array_column($departmentData, 'attendance')); ?>,
                        borderColor: '#1b68ff',
                        backgroundColor: 'rgba(27, 104, 255, 0.2)',
                        pointBackgroundColor: '#1b68ff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            min: 85,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Report generation function
        function generateReport(reportType) {
            console.log('Generating report:', reportType);
            alert(`Generating ${reportType} report...`);
            // In real implementation, this would trigger report generation
        }
    </script>
</body>
</html>