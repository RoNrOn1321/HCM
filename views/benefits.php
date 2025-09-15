<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();

// Mock benefits data - in real implementation, fetch from database
$benefitPlans = [
    [
        'id' => 1,
        'plan_name' => 'PhilHealth',
        'plan_type' => 'Government Health',
        'provider' => 'Philippine Health Insurance Corporation',
        'monthly_premium' => '₱500.00',
        'coverage_limit' => '₱1,500,000',
        'status' => 'Active',
        'enrolled_employees' => 248,
        'description' => 'Mandatory government health insurance for all employees'
    ],
    [
        'id' => 2,
        'plan_name' => 'SSS',
        'plan_type' => 'Social Security',
        'provider' => 'Social Security System',
        'monthly_premium' => '₱1,125.00',
        'coverage_limit' => 'Retirement Benefits',
        'status' => 'Active',
        'enrolled_employees' => 248,
        'description' => 'Mandatory social security system for retirement, disability, and death benefits'
    ],
    [
        'id' => 3,
        'plan_name' => 'Pag-IBIG',
        'plan_type' => 'Housing Fund',
        'provider' => 'Home Development Mutual Fund',
        'monthly_premium' => '₱200.00',
        'coverage_limit' => 'Housing Loan',
        'status' => 'Active',
        'enrolled_employees' => 248,
        'description' => 'Government housing fund for housing loans and savings'
    ],
    [
        'id' => 4,
        'plan_name' => 'Maxicare Prime',
        'plan_type' => 'HMO',
        'provider' => 'Maxicare Healthcare Corporation',
        'monthly_premium' => '₱2,500.00',
        'coverage_limit' => '₱150,000',
        'status' => 'Active',
        'enrolled_employees' => 185,
        'description' => 'Comprehensive healthcare coverage with nationwide network'
    ],
    [
        'id' => 5,
        'plan_name' => 'Group Life Insurance',
        'plan_type' => 'Life Insurance',
        'provider' => 'BPI-Philam Life',
        'monthly_premium' => '₱800.00',
        'coverage_limit' => '₱500,000',
        'status' => 'Active',
        'enrolled_employees' => 220,
        'description' => 'Group term life insurance with accidental death benefit'
    ]
];

$recentEnrollments = [
    [
        'employee' => 'Maria Santos',
        'plan' => 'Maxicare Prime',
        'enrollment_date' => '2024-09-10',
        'status' => 'Approved',
        'effective_date' => '2024-10-01'
    ],
    [
        'employee' => 'John Dela Cruz',
        'plan' => 'Group Life Insurance',
        'enrollment_date' => '2024-09-08',
        'status' => 'Pending',
        'effective_date' => '2024-10-01'
    ],
    [
        'employee' => 'Ana Reyes',
        'plan' => 'Maxicare Prime',
        'enrollment_date' => '2024-09-05',
        'status' => 'Approved',
        'effective_date' => '2024-09-15'
    ]
];

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_plan':
                // Add new benefit plan logic
                $message = 'Benefit plan added successfully!';
                $messageType = 'success';
                break;
            case 'enroll_employee':
                // Enroll employee in benefit plan
                $message = 'Employee enrolled successfully!';
                $messageType = 'success';
                break;
            case 'update_plan':
                // Update benefit plan
                $message = 'Benefit plan updated successfully!';
                $messageType = 'success';
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
    <title>Benefits Management - HCM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <!-- Page Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Benefits Management</h1>
                    <p class="text-gray-600">Manage employee benefits, insurance plans, and enrollment</p>
                </div>
                <button onclick="openModal('addPlanModal')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Benefit Plan
                </button>
            </div>

            <!-- Alert Messages -->
            <?php if ($message): ?>
            <div id="alert" class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                <div class="flex justify-between items-center">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button onclick="document.getElementById('alert').remove()" class="text-lg font-bold">&times;</button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-shield-alt text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Plans</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo count($benefitPlans); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-users text-success text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                            <p class="text-2xl font-bold text-gray-900">1,101</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-warning text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Enrollments</p>
                            <p class="text-2xl font-bold text-gray-900">5</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-peso-sign text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Monthly Premium</p>
                            <p class="text-2xl font-bold text-gray-900">₱1.2M</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Benefit Plans -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Benefit Plans</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php foreach ($benefitPlans as $plan): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($plan['plan_name']); ?></h4>
                                                <span class="ml-3 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    <?php echo htmlspecialchars($plan['status']); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($plan['description']); ?></p>
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-500">Type:</span>
                                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($plan['plan_type']); ?></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Provider:</span>
                                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($plan['provider']); ?></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Monthly Premium:</span>
                                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($plan['monthly_premium']); ?></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Coverage:</span>
                                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($plan['coverage_limit']); ?></span>
                                                </div>
                                            </div>
                                            <div class="mt-2 flex items-center">
                                                <i class="fas fa-users text-gray-400 text-sm mr-1"></i>
                                                <span class="text-sm text-gray-600"><?php echo $plan['enrolled_employees']; ?> employees enrolled</span>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex space-x-2">
                                            <button onclick="editPlan(<?php echo $plan['id']; ?>)" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="viewPlanDetails(<?php echo $plan['id']; ?>)" class="p-2 text-gray-400 hover:text-info transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <button onclick="openModal('enrollEmployeeModal')" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <i class="fas fa-user-plus text-primary text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Enroll Employee</p>
                                        <p class="text-sm text-gray-600">Add employee to benefit plan</p>
                                    </div>
                                </button>

                                <button onclick="generateReport()" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                                        <i class="fas fa-file-alt text-success text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Generate Report</p>
                                        <p class="text-sm text-gray-600">Benefits enrollment report</p>
                                    </div>
                                </button>

                                <button onclick="manageDependents()" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                        <i class="fas fa-users text-warning text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Manage Dependents</p>
                                        <p class="text-sm text-gray-600">Add/remove dependents</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enrollments -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Enrollments</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php foreach ($recentEnrollments as $enrollment): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($enrollment['employee']); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($enrollment['plan']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $enrollment['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo htmlspecialchars($enrollment['status']); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Benefit Plan Modal -->
    <div id="addPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Add Benefit Plan</h3>
                    <button onclick="closeModal('addPlanModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form method="POST" class="p-6">
                <input type="hidden" name="action" value="add_plan">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name</label>
                        <input type="text" name="plan_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Type</label>
                        <select name="plan_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="">Select Type</option>
                            <option value="HMO">HMO</option>
                            <option value="Life Insurance">Life Insurance</option>
                            <option value="Government Health">Government Health</option>
                            <option value="Social Security">Social Security</option>
                            <option value="Housing Fund">Housing Fund</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                        <input type="text" name="provider" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Premium</label>
                        <input type="text" name="monthly_premium" placeholder="₱0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coverage Limit</label>
                        <input type="text" name="coverage_limit" placeholder="₱0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addPlanModal')" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">Add Plan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Enroll Employee Modal -->
    <div id="enrollEmployeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Enroll Employee</h3>
                    <button onclick="closeModal('enrollEmployeeModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form method="POST" class="p-6">
                <input type="hidden" name="action" value="enroll_employee">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                        <select name="employee_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="">Select Employee</option>
                            <option value="1">Sarah Johnson - IT Department</option>
                            <option value="2">Michael Chen - Finance</option>
                            <option value="3">Emily Rodriguez - HR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Benefit Plan</label>
                        <select name="plan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="">Select Plan</option>
                            <?php foreach ($benefitPlans as $plan): ?>
                            <option value="<?php echo $plan['id']; ?>"><?php echo htmlspecialchars($plan['plan_name']); ?> - <?php echo htmlspecialchars($plan['monthly_premium']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                        <input type="date" name="effective_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('enrollEmployeeModal')" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">Enroll</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.getElementById(modalId).classList.add('flex');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
        }

        function editPlan(planId) {
            alert('Edit plan functionality - Plan ID: ' + planId);
        }

        function viewPlanDetails(planId) {
            alert('View plan details - Plan ID: ' + planId);
        }

        function generateReport() {
            alert('Generate benefits report functionality');
        }

        function manageDependents() {
            alert('Manage dependents functionality');
        }

        // Close modal when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
    </script>

    <!-- JavaScript for Interactivity -->
    <?php include 'includes/scripts.php'; ?>
</body>
</html>