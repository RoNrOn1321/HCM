<nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <!-- Sidebar Toggle -->
                <button id="toggleSidebar" class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-bars w-6 h-6"></i>
                </button>
                <!-- Logo -->
                <a href="index.php" class="flex ml-2 md:mr-24">
                    <div class="h-8 w-8 bg-primary rounded flex items-center justify-center mr-3">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-gray-900">HCM System</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="hidden lg:flex items-center lg:ml-6">
                <div class="relative">
                    <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary focus:border-primary block w-80 pl-10 p-2.5" placeholder="Search employees, payroll...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-500"></i>
                    </div>
                </div>
            </div>

            <!-- Right Navigation -->
            <div class="flex items-center">
                <!-- Notifications -->
                <button class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 mr-2">
                    <i class="fas fa-bell w-5 h-5"></i>
                    <span class="absolute -mt-5 ml-2.5 px-1.5 py-0.5 text-xs font-medium text-white bg-red-500 rounded-full">3</span>
                </button>

                <!-- User Menu -->
                <div class="flex items-center ml-3 relative">
                    <button class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300" id="user-menu-button">
                        <img class="w-8 h-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="user photo">
                    </button>
                    <div class="z-50 hidden absolute right-0 top-full mt-2 w-48 bg-white divide-y divide-gray-100 rounded-md shadow-lg border border-gray-200" id="dropdown">
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-900"><?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . (isset($_SESSION['first_name'], $_SESSION['last_name']) ? ' ' : '') . ($_SESSION['last_name'] ?? $_SESSION['username'] ?? 'User')); ?></p>
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['employee_email'] ?? $_SESSION['email'] ?? $_SESSION['username'] . '@company.com'); ?></p>
                        </div>
                        <ul class="py-1">
                            <li><a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a></li>
                            <li><a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a></li>
                            <li><a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>