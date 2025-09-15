<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();

// Mock attendance data - in real implementation, fetch from database
$attendanceRecords = [
    [
        'id' => 1,
        'employee_id' => 'EMP001',
        'name' => 'Sarah Johnson',
        'date' => '2024-03-14',
        'time_in' => '08:00 AM',
        'time_out' => '05:30 PM',
        'break_time' => '1h 00m',
        'total_hours' => '8h 30m',
        'status' => 'Present',
        'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b890?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ],
    [
        'id' => 2,
        'employee_id' => 'EMP002',
        'name' => 'Michael Chen',
        'date' => '2024-03-14',
        'time_in' => '08:15 AM',
        'time_out' => '05:00 PM',
        'break_time' => '45m',
        'total_hours' => '8h 00m',
        'status' => 'Present',
        'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ],
    [
        'id' => 3,
        'employee_id' => 'EMP003',
        'name' => 'Emily Rodriguez',
        'date' => '2024-03-14',
        'time_in' => '-',
        'time_out' => '-',
        'break_time' => '-',
        'total_hours' => '0h 00m',
        'status' => 'Absent',
        'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    ]
];

// Mock current user attendance status
$currentUser = [
    'status' => 'clocked_out', // clocked_in, clocked_out, on_break
    'last_action_time' => '05:30 PM',
    'total_hours_today' => '8h 30m'
];

// Attendance summary
$attendanceSummary = [
    'total_employees' => 248,
    'present_today' => 195,
    'absent_today' => 15,
    'late_arrivals' => 8,
    'early_departures' => 3
];

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'clock_in':
                $currentUser['status'] = 'clocked_in';
                $success = "Successfully clocked in!";
                break;
            case 'clock_out':
                $currentUser['status'] = 'clocked_out';
                $success = "Successfully clocked out!";
                break;
            case 'start_break':
                $currentUser['status'] = 'on_break';
                $success = "Break started!";
                break;
            case 'end_break':
                $currentUser['status'] = 'clocked_in';
                $success = "Break ended!";
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
    <title>Attendance Management - HCM System</title>
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
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Attendance Management</h1>
                <p class="text-gray-600">Track employee attendance and working hours</p>
            </div>

            <!-- Quick Clock Actions -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    <div class="text-sm text-gray-500" id="current-time"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Current Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center <?php echo $currentUser['status'] == 'clocked_in' ? 'bg-green-100' : 'bg-gray-100'; ?>">
                                <i class="fas <?php echo $currentUser['status'] == 'clocked_in' ? 'fa-clock text-green-600' : 'fa-clock text-gray-500'; ?> text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Current Status</p>
                            <p class="text-xs text-gray-500 capitalize"><?php echo str_replace('_', ' ', $currentUser['status']); ?></p>
                        </div>
                    </div>

                    <!-- Clock In/Out -->
                    <div>
                        <?php if ($currentUser['status'] == 'clocked_out'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="clock_in">
                            <button type="submit" class="w-full bg-success text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-play mr-2"></i>
                                Clock In
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="clock_out">
                            <button type="submit" class="w-full bg-danger text-white px-4 py-3 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-stop mr-2"></i>
                                Clock Out
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Break Actions -->
                    <div>
                        <?php if ($currentUser['status'] == 'clocked_in'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="start_break">
                            <button type="submit" class="w-full bg-warning text-white px-4 py-3 rounded-lg hover:bg-yellow-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-coffee mr-2"></i>
                                Start Break
                            </button>
                        </form>
                        <?php elseif ($currentUser['status'] == 'on_break'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="end_break">
                            <button type="submit" class="w-full bg-primary text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-play mr-2"></i>
                                End Break
                            </button>
                        </form>
                        <?php else: ?>
                        <button disabled class="w-full bg-gray-300 text-gray-500 px-4 py-3 rounded-lg cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-coffee mr-2"></i>
                            Start Break
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Today's Hours -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Today's Hours</p>
                            <p class="text-xs text-gray-500"><?php echo $currentUser['total_hours_today']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Employees</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $attendanceSummary['total_employees']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-success text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Present Today</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $attendanceSummary['present_today']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-times-circle text-danger text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Absent Today</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $attendanceSummary['absent_today']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-warning text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Late Arrivals</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $attendanceSummary['late_arrivals']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-sign-out-alt text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Early Departures</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $attendanceSummary['early_departures']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search Bar -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="search" id="attendance-search" class="w-full bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary focus:border-primary block pl-10 p-2.5" placeholder="Search by employee name or ID...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <input type="date" id="date-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5" value="2024-03-14">

                        <select id="status-filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-40 p-2.5">
                            <option value="">All Status</option>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                            <option value="Half Day">Half Day</option>
                        </select>

                        <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Today's Attendance - March 14, 2024</h3>
                    <p class="text-sm text-gray-600"><?php echo count($attendanceRecords); ?> records found</p>
                </div>

                <div class="overflow-x-auto">
                    <table id="attendance-table" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 cursor-pointer" data-sort="employee">Employee</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="date">Date</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="time_in">Time In</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="time_out">Time Out</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="break_time">Break Time</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="total_hours">Total Hours</th>
                                <th class="px-6 py-3 cursor-pointer" data-sort="status">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceRecords as $record): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4" data-sort="employee">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3" src="<?php echo htmlspecialchars($record['avatar']); ?>" alt="employee">
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($record['name']); ?></div>
                                            <div class="text-gray-500"><?php echo htmlspecialchars($record['employee_id']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" data-sort="date"><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                                <td class="px-6 py-4" data-sort="time_in">
                                    <span class="<?php echo $record['time_in'] != '-' ? 'text-green-600' : 'text-gray-400'; ?>">
                                        <?php echo htmlspecialchars($record['time_in']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4" data-sort="time_out">
                                    <span class="<?php echo $record['time_out'] != '-' ? 'text-red-600' : 'text-gray-400'; ?>">
                                        <?php echo htmlspecialchars($record['time_out']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4" data-sort="break_time"><?php echo htmlspecialchars($record['break_time']); ?></td>
                                <td class="px-6 py-4 font-medium" data-sort="total_hours"><?php echo htmlspecialchars($record['total_hours']); ?></td>
                                <td class="px-6 py-4" data-sort="status">
                                    <span class="<?php
                                        echo $record['status'] == 'Present' ? 'bg-green-100 text-green-800' :
                                            ($record['status'] == 'Absent' ? 'bg-red-100 text-red-800' :
                                            ($record['status'] == 'Late' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
                                    ?> text-xs font-medium mr-2 px-2.5 py-0.5 rounded">
                                        <?php echo htmlspecialchars($record['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800" title="View Details" onclick="viewAttendanceDetails(<?php echo $record['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-800" title="Edit" onclick="editAttendance(<?php echo $record['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-purple-600 hover:text-purple-800" title="Add Note" onclick="addNote(<?php echo $record['id']; ?>)">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
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
                            Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($attendanceRecords); ?></span> of <span class="font-medium"><?php echo count($attendanceRecords); ?></span> results
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

    <!-- JavaScript for Interactivity -->
    <?php include 'includes/scripts.php'; ?>

    <script>
        // Initialize search functionality
        initializeSearch('attendance-table', 'attendance-search');

        // Update current time
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('current-time').textContent = `${dateString} - ${timeString}`;
        }

        // Update time every second
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);

        // Attendance management functions
        function viewAttendanceDetails(id) {
            // Implementation for viewing attendance details
            console.log('View attendance details for ID:', id);
        }

        function editAttendance(id) {
            // Implementation for editing attendance
            console.log('Edit attendance for ID:', id);
        }

        function addNote(id) {
            // Implementation for adding note
            const note = prompt('Add a note for this attendance record:');
            if (note) {
                console.log('Add note for ID:', id, 'Note:', note);
                alert('Note added successfully!');
            }
        }

        // Filter functionality
        document.getElementById('date-filter').addEventListener('change', filterTable);
        document.getElementById('status-filter').addEventListener('change', filterTable);

        function filterTable() {
            const dateFilter = document.getElementById('date-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const rows = document.querySelectorAll('#attendance-table tbody tr');

            rows.forEach(row => {
                const dateCell = row.querySelector('[data-sort="date"]').textContent;
                const statusCell = row.querySelector('[data-sort="status"]').textContent.trim();

                // Convert date for comparison
                const rowDate = new Date(dateCell).toISOString().split('T')[0];
                const dateMatch = !dateFilter || rowDate === dateFilter;
                const statusMatch = !statusFilter || statusCell === statusFilter;

                if (dateMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>