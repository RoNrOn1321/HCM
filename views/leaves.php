<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();
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
            <!-- Alert Messages -->
            <div id="alert-container"></div>

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
            <div id="leave-balance-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Loading placeholder -->
                <div class="col-span-full flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                </div>
            </div>

            <!-- Leave Summary Statistics -->
            <div id="leave-summary-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-calendar-alt text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Requests</p>
                            <p id="total-requests" class="text-2xl font-bold text-gray-900">-</p>
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
                            <p id="pending-requests" class="text-2xl font-bold text-gray-900">-</p>
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
                            <p id="approved-requests" class="text-2xl font-bold text-gray-900">-</p>
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
                            <p id="rejected-requests" class="text-2xl font-bold text-gray-900">-</p>
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
                        </select>

                        <select id="status-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-40 p-2.5">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>

                        <button id="export-btn" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
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
                    <p id="requests-count" class="text-sm text-gray-600">Loading requests...</p>
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
                        <tbody id="leave-table-body">
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center">
                                    <div class="flex justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div id="pagination-info" class="text-sm text-gray-700">
                            Loading...
                        </div>
                        <div id="pagination-buttons" class="flex space-x-1">
                            <!-- Pagination buttons will be populated by JavaScript -->
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
                <form id="apply-leave-form" onsubmit="submitLeaveApplication(event)">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Apply for Leave</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('apply-leave-modal')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <input type="hidden" id="employee-id-input" name="employee_id" value="">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                <select id="leave-type-select" name="leave_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Leave Type</option>
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
        // Global variables
        let currentPage = 1;
        let currentFilters = {};
        let leaveTypes = [];
        let currentUserEmployeeId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadLeaveTypes();
            loadLeaveBalance();
            loadLeaveRequests();
            getCurrentUserEmployeeId();
            setupEventListeners();
        });

        // Get current user's employee ID
        async function getCurrentUserEmployeeId() {
            try {
                const response = await fetch('../api/employees.php');
                const result = await response.json();

                if (result.success && result.data.employees.length > 0) {
                    // Find current user's employee record
                    const currentUser = result.data.employees[0]; // Simplified - in real app, filter by current user
                    currentUserEmployeeId = currentUser.id;
                    document.getElementById('employee-id-input').value = currentUserEmployeeId;
                }
            } catch (error) {
                console.error('Error getting current user employee ID:', error);
            }
        }

        // Load leave types
        async function loadLeaveTypes() {
            try {
                const response = await fetch('../api/leaves.php?action=types');
                const result = await response.json();

                if (result.success) {
                    leaveTypes = result.data;
                    populateLeaveTypeSelects();
                }
            } catch (error) {
                console.error('Error loading leave types:', error);
            }
        }

        // Populate leave type select elements
        function populateLeaveTypeSelects() {
            const modalSelect = document.getElementById('leave-type-select');
            const filterSelect = document.getElementById('leave-type-filter');

            // Clear existing options (except first)
            modalSelect.innerHTML = '<option value="">Select Leave Type</option>';
            filterSelect.innerHTML = '<option value="">All Types</option>';

            leaveTypes.forEach(type => {
                modalSelect.innerHTML += `<option value="${type.id}">${type.leave_name} (${type.max_days_per_year} days max)</option>`;
                filterSelect.innerHTML += `<option value="${type.id}">${type.leave_name}</option>`;
            });
        }

        // Load leave balance
        async function loadLeaveBalance() {
            try {
                const response = await fetch('../api/leaves.php?action=balance');
                const result = await response.json();

                if (result.success) {
                    renderLeaveBalanceCards(result.data.balances);
                }
            } catch (error) {
                console.error('Error loading leave balance:', error);
                document.getElementById('leave-balance-cards').innerHTML =
                    '<div class="col-span-full text-center text-red-600">Error loading leave balance</div>';
            }
        }

        // Render leave balance cards
        function renderLeaveBalanceCards(balances) {
            const container = document.getElementById('leave-balance-cards');
            const colors = ['blue', 'green', 'purple', 'red', 'yellow', 'indigo'];

            container.innerHTML = balances.map((balance, index) => {
                const color = colors[index % colors.length];
                const percentage = balance.max_days_per_year > 0 ? (balance.remaining_days / balance.max_days_per_year) * 100 : 0;

                return `
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-600">${balance.leave_name}</h3>
                            <div class="w-3 h-3 bg-${color}-500 rounded-full"></div>
                        </div>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-gray-900">${balance.remaining_days}</p>
                            <p class="text-sm text-gray-500 ml-1">/ ${balance.max_days_per_year} days</p>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-${color}-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">${balance.used_days} days used</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Load leave requests
        async function loadLeaveRequests(page = 1, filters = {}) {
            try {
                const params = new URLSearchParams({
                    page: page,
                    limit: 25,
                    ...filters
                });

                const response = await fetch(`../api/leaves.php?${params}`);
                const result = await response.json();

                if (result.success) {
                    renderLeaveRequestsTable(result.data.leaves);
                    renderPagination(result.data.pagination);
                    updateSummaryStats(result.data.leaves);
                    document.getElementById('requests-count').textContent =
                        `${result.data.pagination.total} requests found`;
                }
            } catch (error) {
                console.error('Error loading leave requests:', error);
                document.getElementById('leave-table-body').innerHTML =
                    '<tr><td colspan="9" class="px-6 py-8 text-center text-red-600">Error loading leave requests</td></tr>';
            }
        }

        // Render leave requests table
        function renderLeaveRequestsTable(leaves) {
            const tbody = document.getElementById('leave-table-body');

            if (leaves.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">No leave requests found</td></tr>';
                return;
            }

            tbody.innerHTML = leaves.map(leave => {
                const statusClass = getStatusClass(leave.status);
                const canApproveReject = leave.status === 'Pending';
                const canCancel = leave.status === 'Approved' && new Date(leave.start_date) > new Date();

                return `
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">${leave.employee_name}</div>
                                    <div class="text-gray-500">${leave.emp_id}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                ${leave.leave_name}
                            </span>
                        </td>
                        <td class="px-6 py-4">${formatDate(leave.start_date)}</td>
                        <td class="px-6 py-4">${formatDate(leave.end_date)}</td>
                        <td class="px-6 py-4 font-medium">${leave.total_days} day${leave.total_days > 1 ? 's' : ''}</td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600" title="${leave.reason}">
                                ${leave.reason.length > 30 ? leave.reason.substring(0, 30) + '...' : leave.reason}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="${statusClass} text-xs font-medium px-2.5 py-0.5 rounded">
                                ${leave.status}
                            </span>
                        </td>
                        <td class="px-6 py-4">${formatDate(leave.applied_date)}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-800" title="View Details" onclick="viewLeaveDetails(${leave.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${canApproveReject ? `
                                    <button class="text-green-600 hover:text-green-800" title="Approve" onclick="approveLeave(${leave.id})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800" title="Reject" onclick="rejectLeave(${leave.id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                                ${canCancel ? `
                                    <button class="text-orange-600 hover:text-orange-800" title="Cancel" onclick="cancelLeave(${leave.id})">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Update summary statistics
        function updateSummaryStats(leaves) {
            const stats = leaves.reduce((acc, leave) => {
                acc.total++;
                acc[leave.status.toLowerCase()] = (acc[leave.status.toLowerCase()] || 0) + 1;
                return acc;
            }, { total: 0, pending: 0, approved: 0, rejected: 0, cancelled: 0 });

            document.getElementById('total-requests').textContent = stats.total;
            document.getElementById('pending-requests').textContent = stats.pending;
            document.getElementById('approved-requests').textContent = stats.approved;
            document.getElementById('rejected-requests').textContent = stats.rejected;
        }

        // Helper functions
        function getStatusClass(status) {
            switch (status) {
                case 'Approved': return 'bg-green-100 text-green-800';
                case 'Rejected': return 'bg-red-100 text-red-800';
                case 'Cancelled': return 'bg-gray-100 text-gray-800';
                default: return 'bg-yellow-100 text-yellow-800';
            }
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Event listeners
        function setupEventListeners() {
            // Filter events
            document.getElementById('leave-type-filter').addEventListener('change', applyFilters);
            document.getElementById('status-filter').addEventListener('change', applyFilters);
            document.getElementById('leave-search').addEventListener('input', debounce(applyFilters, 300));

            // Form date validation
            document.querySelector('input[name="start_date"]').addEventListener('change', calculateLeaveDays);
            document.querySelector('input[name="end_date"]').addEventListener('change', calculateLeaveDays);
        }

        // Apply filters
        function applyFilters() {
            const filters = {
                leave_type: document.getElementById('leave-type-filter').value,
                status: document.getElementById('status-filter').value,
                search: document.getElementById('leave-search').value
            };

            currentFilters = Object.fromEntries(
                Object.entries(filters).filter(([_, value]) => value !== '')
            );

            currentPage = 1;
            loadLeaveRequests(currentPage, currentFilters);
        }

        // Submit leave application
        async function submitLeaveApplication(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            const leaveData = {
                employee_id: formData.get('employee_id'),
                leave_type_id: formData.get('leave_type'),
                start_date: formData.get('start_date'),
                end_date: formData.get('end_date'),
                reason: formData.get('reason')
            };

            try {
                const response = await fetch('../api/leaves.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(leaveData)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Leave application submitted successfully!', 'success');
                    closeModal('apply-leave-modal');
                    form.reset();
                    loadLeaveRequests();
                    loadLeaveBalance();
                } else {
                    showAlert(result.error || 'Failed to submit leave application', 'error');
                }
            } catch (error) {
                console.error('Error submitting leave application:', error);
                showAlert('Error submitting leave application', 'error');
            }
        }

        // Leave action functions
        async function approveLeave(leaveId) {
            if (!confirm('Are you sure you want to approve this leave request?')) return;

            try {
                const response = await fetch(`../api/leaves.php?id=${leaveId}&action=approve`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ notes: '' })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Leave request approved successfully!', 'success');
                    loadLeaveRequests(currentPage, currentFilters);
                } else {
                    showAlert(result.error || 'Failed to approve leave request', 'error');
                }
            } catch (error) {
                console.error('Error approving leave:', error);
                showAlert('Error approving leave request', 'error');
            }
        }

        async function rejectLeave(leaveId) {
            const reason = prompt('Please provide a reason for rejection (optional):');
            if (reason === null) return; // User cancelled

            try {
                const response = await fetch(`../api/leaves.php?id=${leaveId}&action=reject`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ notes: reason })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Leave request rejected successfully!', 'success');
                    loadLeaveRequests(currentPage, currentFilters);
                } else {
                    showAlert(result.error || 'Failed to reject leave request', 'error');
                }
            } catch (error) {
                console.error('Error rejecting leave:', error);
                showAlert('Error rejecting leave request', 'error');
            }
        }

        async function cancelLeave(leaveId) {
            if (!confirm('Are you sure you want to cancel this leave request?')) return;

            try {
                const response = await fetch(`../api/leaves.php?id=${leaveId}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Leave request cancelled successfully!', 'success');
                    loadLeaveRequests(currentPage, currentFilters);
                    loadLeaveBalance();
                } else {
                    showAlert(result.error || 'Failed to cancel leave request', 'error');
                }
            } catch (error) {
                console.error('Error cancelling leave:', error);
                showAlert('Error cancelling leave request', 'error');
            }
        }

        function viewLeaveDetails(leaveId) {
            // TODO: Implement leave details modal
            console.log('View leave details for ID:', leaveId);
        }

        // Calculate leave days
        function calculateLeaveDays() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (end >= start) {
                    const timeDiff = end.getTime() - start.getTime();
                    const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    console.log('Leave days calculated:', dayDiff);
                } else {
                    alert('End date must be after start date');
                    document.querySelector('input[name="end_date"]').value = '';
                }
            }
        }

        // Pagination
        function renderPagination(pagination) {
            const container = document.getElementById('pagination-buttons');
            const info = document.getElementById('pagination-info');

            info.textContent = `Showing ${((pagination.page - 1) * pagination.limit) + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} results`;

            let buttons = '';

            // Previous button
            buttons += `
                <button onclick="changePage(${pagination.page - 1})" ${pagination.page <= 1 ? 'disabled' : ''}
                    class="px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 ${pagination.page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                    Previous
                </button>
            `;

            // Page numbers
            for (let i = Math.max(1, pagination.page - 2); i <= Math.min(pagination.totalPages, pagination.page + 2); i++) {
                buttons += `
                    <button onclick="changePage(${i})"
                        class="px-3 py-2 text-sm leading-tight ${i === pagination.page ? 'text-white bg-primary border-primary' : 'text-gray-500 bg-white border-gray-300'} hover:bg-blue-700">
                        ${i}
                    </button>
                `;
            }

            // Next button
            buttons += `
                <button onclick="changePage(${pagination.page + 1})" ${pagination.page >= pagination.totalPages ? 'disabled' : ''}
                    class="px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 ${pagination.page >= pagination.totalPages ? 'opacity-50 cursor-not-allowed' : ''}">
                    Next
                </button>
            `;

            container.innerHTML = buttons;
        }

        function changePage(page) {
            if (page < 1) return;
            currentPage = page;
            loadLeaveRequests(currentPage, currentFilters);
        }

        // Utility functions
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                              type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
                              'bg-blue-100 border-blue-400 text-blue-700';

            alertContainer.innerHTML = `
                <div class="${alertClass} border px-4 py-3 rounded mb-4">
                    ${message}
                    <button type="button" class="float-right" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>