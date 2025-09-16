<?php
// Get current page to highlight active menu
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="index.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'index') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-tachometer-alt w-5 h-5 <?php echo ($currentPage == 'index') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            <!-- Employees -->
            <li>
                <a href="employees.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'employees') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-users w-5 h-5 <?php echo ($currentPage == 'employees') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Employees</span>
                </a>
            </li>

            <!-- Payroll -->
            <li>
                <a href="payroll.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'payroll') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-money-bill-wave w-5 h-5 <?php echo ($currentPage == 'payroll') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Payroll</span>
                </a>
            </li>

            <!-- Benefits -->
            <li>
                <a href="benefits.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'benefits') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-shield-alt w-5 h-5 <?php echo ($currentPage == 'benefits') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Benefits</span>
                </a>
            </li>

            <!-- Attendance -->
            <li>
                <a href="attendance.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'attendance') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-clock w-5 h-5 <?php echo ($currentPage == 'attendance') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Attendance</span>
                </a>
            </li>

            <!-- Leaves -->
            <li>
                <a href="leaves.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'leaves') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-calendar-times w-5 h-5 <?php echo ($currentPage == 'leaves') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Leave Management</span>
                </a>
            </li>

            <!-- Reports -->
            <li>
                <a href="reports.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'reports') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-chart-bar w-5 h-5 <?php echo ($currentPage == 'reports') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Reports</span>
                </a>
            </li>

            <!-- Manage Dependents -->
            <li>
                <a href="dependents.php" class="flex items-center p-2 rounded-lg group <?php echo ($currentPage == 'dependents') ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-users-cog w-5 h-5 <?php echo ($currentPage == 'dependents') ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Manage Dependents</span>
                </a>
            </li>

            <!-- Settings -->
            <?php
            // Check if user has admin or HR permissions for settings
            $currentUser = getCurrentUser();
            $showSettings = false;

            if ($currentUser && isset($currentUser['id'])) {
                try {
                    require_once __DIR__ . '/../../config/database.php';
                    global $conn;

                    if ($conn) {
                        $stmt = $conn->prepare("
                            SELECT r.role_name
                            FROM users u
                            LEFT JOIN roles r ON u.role_id = r.id
                            WHERE u.id = ?
                        ");
                        $stmt->execute([$currentUser['id']]);
                        $result = $stmt->fetch();
                        $userRole = $result['role_name'] ?? 'employee';

                        $showSettings = in_array(strtolower($userRole), ['admin', 'hr']);
                    }
                } catch (Exception $e) {
                    error_log("Settings menu error: " . $e->getMessage());
                    // Default to not showing settings on error
                    $showSettings = false;
                }
            }

            if ($showSettings): ?>
            <li>
                <a href="settings-new.php" class="flex items-center p-2 rounded-lg group <?php echo (in_array($currentPage, ['settings', 'settings-new'])) ? 'text-white bg-primary' : 'text-gray-900 hover:bg-gray-100'; ?>">
                    <i class="fas fa-cog w-5 h-5 <?php echo (in_array($currentPage, ['settings', 'settings-new'])) ? 'text-white' : 'text-gray-500 group-hover:text-gray-900'; ?>"></i>
                    <span class="ml-3">Settings</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>