<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();

// Mock leave types
$leaveTypes = [
    ['id' => 1, 'name' => 'Annual Leave', 'days_allowed' => 21, 'color' => 'blue'],
    ['id' => 2, 'name' => 'Sick Leave', 'days_allowed' => 10, 'color' => 'red'],
    ['id' => 3, 'name' => 'Personal Leave', 'days_allowed' => 5, 'color' => 'purple'],
    ['id' => 4, 'name' => 'Maternity Leave', 'days_allowed' => 90, 'color' => 'pink'],
    ['id' => 5, 'name' => 'Paternity Leave', 'days_allowed' => 7, 'color' => 'green'],
    ['id' => 6, 'name' => 'Emergency Leave', 'days_allowed' => 3, 'color' => 'orange']
];

// Mock leave balance for current user
$leaveBalance = [
    ['type' => 'Annual Leave', 'total' => 21, 'used' => 8, 'remaining' => 13, 'color' => 'blue'],
    ['type' => 'Sick Leave', 'total' => 10, 'used' => 2, 'remaining' => 8, 'color' => 'red'],
    ['type' => 'Personal Leave', 'total' => 5, 'used' => 1, 'remaining' => 4, 'color' => 'purple'],
    ['type' => 'Emergency Leave', 'total' => 3, 'used' => 0, 'remaining' => 3, 'color' => 'orange']
];

// Mock leave requests data
$leaveRequests = [
    [
        'id' => 1,
        'employee_id' => 'EMP001',
        'employee_name' => 'Sarah Johnson',
        'leave_type' => 'Annual Leave',
        'start_date' => '2024-03-20',
        'end_date' => '2024-03-22',
        'days' => 3,
        'reason' => 'Family vacation',
        'status' => 'Pending',
        'applied_date' => '2024-03-10',
        'approved_by' => null,
        'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b890?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ],
    [
        'id' => 2,
        'employee_id' => 'EMP002',
        'employee_name' => 'Michael Chen',
        'leave_type' => 'Sick Leave',
        'start_date' => '2024-03-15',
        'end_date' => '2024-03-16',
        'days' => 2,
        'reason' => 'Medical appointment',
        'status' => 'Approved',
        'applied_date' => '2024-03-12',
        'approved_by' => 'HR Manager',
        'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ],
    [
        'id' => 3,
        'employee_id' => 'EMP003',
        'employee_name' => 'Emily Rodriguez',
        'leave_type' => 'Personal Leave',
        'start_date' => '2024-03-25',
        'end_date' => '2024-03-25',
        'days' => 1,
        'reason' => 'Personal matters',
        'status' => 'Rejected',
        'applied_date' => '2024-03-08',
        'approved_by' => 'HR Manager',
        'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ]
];

// Leave summary statistics
$leaveSummary = [
    'total_requests' => 45,
    'pending_requests' => 12,
    'approved_requests' => 28,
    'rejected_requests' => 5
];

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'apply_leave':
                $success = "Leave application submitted successfully!";
                break;
            case 'approve_leave':
                $success = "Leave request approved successfully!";
                break;
            case 'reject_leave':
                $success = "Leave request rejected!";
                break;
            case 'cancel_leave':
                $success = "Leave request cancelled successfully!";
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
    <title>Leave Management - HCM System</title>
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
            <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Leave Management</h1>
                    <p class="text-gray-600">Manage employee leave requests and balances</p>
                </div>
                <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center" onclick="openModal('apply-leave-modal')">
                    <i class="fas fa-plus mr-2"></i>
                    Apply for Leave
                </button>
            </div>

            <!-- Leave Balance Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <?php foreach ($leaveBalance as $balance): ?>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-600"><?php echo htmlspecialchars($balance['type']); ?></h3>
                        <div class="w-3 h-3 bg-<?php echo $balance['color']; ?>-500 rounded-full"></div>
                    </div>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900"><?php echo $balance['remaining']; ?></p>
                        <p class="text-sm text-gray-500 ml-1">/ <?php echo $balance['total']; ?> days</p>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-<?php echo $balance['color']; ?>-500 h-2 rounded-full" style="width: <?php echo ($balance['remaining'] / $balance['total']) * 100; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $balance['used']; ?> days used</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Leave Summary Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-calendar-alt text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Requests</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $leaveSummary['total_requests']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-warning text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $leaveSummary['pending_requests']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-success text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Approved</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $leaveSummary['approved_requests']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-times-circle text-danger text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Rejected</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $leaveSummary['rejected_requests']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search Bar -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="search" id="leave-search" class="w-full bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary focus:border-primary block pl-10 p-2.5" placeholder="Search by employee name or leave type...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <select id="leave-type-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-40 p-2.5">
                            <option value="">All Types</option>
                            <option value="Annual Leave">Annual Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Personal Leave">Personal Leave</option>
                            <option value="Maternity Leave">Maternity Leave</option>
                            <option value="Paternity Leave">Paternity Leave</option>
                            <option value="Emergency Leave">Emergency Leave</option>
                        </select>

                        <select id="status-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-40 p-2.5">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Leave Requests Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Leave Requests</h3>
                    <p class="text-sm text-gray-600"><?php echo count($leaveRequests); ?> requests found</p>
                </div>

                <div class="overflow-x-auto">
                    <table id="leave-table" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 cursor-pointer" data-sort="employee">Employee</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="leave_type">Leave Type</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="start_date">Start Date</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="end_date">End Date</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="days">Days</th>
                                <th class="px-6 py-3">Reason</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="status">Status</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="applied_date">Applied Date</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaveRequests as $request): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4" data-sort="employee">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3" src="<?php echo htmlspecialchars($request['avatar']); ?>" alt="employee">
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($request['employee_name']); ?></div>
                                            <div class="text-gray-500"><?php echo htmlspecialchars($request['employee_id']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" data-sort="leave_type">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?php echo htmlspecialchars($request['leave_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4" data-sort="start_date"><?php echo date('M d, Y', strtotime($request['start_date'])); ?></td>
                                <td class="px-6 py-4" data-sort="end_date"><?php echo date('M d, Y', strtotime($request['end_date'])); ?></td>
                                <td class="px-6 py-4 font-medium" data-sort="days"><?php echo $request['days']; ?> day<?php echo $request['days'] > 1 ? 's' : ''; ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-600" title="<?php echo htmlspecialchars($request['reason']); ?>">
                                        <?php echo strlen($request['reason']) > 30 ? substr(htmlspecialchars($request['reason']), 0, 30) . '...' : htmlspecialchars($request['reason']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4" data-sort="status">
                                    <span class="<?php
                                        echo $request['status'] == 'Approved' ? 'bg-green-100 text-green-800' :
                                            ($request['status'] == 'Rejected' ? 'bg-red-100 text-red-800' :
                                            'bg-yellow-100 text-yellow-800');
                                    ?> text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4" data-sort="applied_date"><?php echo date('M d, Y', strtotime($request['applied_date'])); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800" title="View Details" onclick="viewLeaveDetails(<?php echo $request['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($request['status'] == 'Pending'): ?>
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="action" value="approve_leave">
                                            <input type="hidden" name="leave_id" value="<?php echo $request['id']; ?>">
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="action" value="reject_leave">
                                            <input type="hidden" name="leave_id" value="<?php echo $request['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($request['status'] == 'Approved' && strtotime($request['start_date']) > time()): ?>
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="action" value="cancel_leave">
                                            <input type="hidden" name="leave_id" value="<?php echo $request['id']; ?>">
                                            <button type="submit" class="text-orange-600 hover:text-orange-800" title="Cancel">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($leaveRequests); ?></span> of <span class="font-medium"><?php echo count($leaveRequests); ?></span> results
                        </div>
                        <div class="flex space-x-1">
                            <button class="px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100">Previous</button>
                            <button class="px-3 py-2 text-sm leading-tight text-white bg-primary border border-primary hover:bg-blue-700">1</button>
                            <button class="px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div id="apply-leave-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('apply-leave-modal')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="apply_leave">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Apply for Leave</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('apply-leave-modal')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                <select name="leave_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Leave Type</option>
                                    <?php foreach ($leaveTypes as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?> (<?php echo $type['days_allowed']; ?> days allowed)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="date" name="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Please provide a reason for your leave request"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                                <input type="text" name="emergency_contact" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Contact person during leave (optional)">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
                                <input type="file" name="documents" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <p class="text-xs text-gray-500 mt-1">Upload medical certificates, travel documents, etc. (optional)</p>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="button" class="mr-3 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400" onclick="closeModal('apply-leave-modal')">Cancel</button>
                                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700">Submit Application</button>
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
        // Initialize search functionality
        initializeSearch('leave-table', 'leave-search');

        // Leave management functions
        function viewLeaveDetails(id) {
            // Implementation for viewing leave details
            console.log('View leave details for ID:', id);
            // This would typically open a detailed modal or navigate to a detail page
        }

        // Auto-calculate days when dates change
        document.querySelector('input[name="start_date"]').addEventListener('change', calculateLeaveDays);
        document.querySelector('input[name="end_date"]').addEventListener('change', calculateLeaveDays);

        function calculateLeaveDays() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (end >= start) {
                    const timeDiff = end.getTime() - start.getTime();
                    const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;

                    // Display calculated days (you can add a field to show this)
                    console.log('Leave days calculated:', dayDiff);
                } else {
                    alert('End date must be after start date');
                    document.querySelector('input[name="end_date"]').value = '';
                }
            }
        }

        // Filter functionality
        document.getElementById('leave-type-filter').addEventListener('change', filterTable);
        document.getElementById('status-filter').addEventListener('change', filterTable);

        function filterTable() {
            const leaveTypeFilter = document.getElementById('leave-type-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const rows = document.querySelectorAll('#leave-table tbody tr');

            rows.forEach(row => {
                const leaveType = row.querySelector('[data-sort="leave_type"]').textContent.trim();
                const status = row.querySelector('[data-sort="status"]').textContent.trim();

                const leaveTypeMatch = !leaveTypeFilter || leaveType.includes(leaveTypeFilter);
                const statusMatch = !statusFilter || status === statusFilter;

                if (leaveTypeMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Confirmation for approve/reject/cancel actions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = this.querySelector('input[name="action"]').value;
                let confirmMessage = '';

                switch(action) {
                    case 'approve_leave':
                        confirmMessage = 'Are you sure you want to approve this leave request?';
                        break;
                    case 'reject_leave':
                        confirmMessage = 'Are you sure you want to reject this leave request?';
                        break;
                    case 'cancel_leave':
                        confirmMessage = 'Are you sure you want to cancel this leave request?';
                        break;
                }

                if (confirmMessage && !confirm(confirmMessage)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>