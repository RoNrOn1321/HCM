<?php
// Destroy any existing session and start fresh
session_start();
session_destroy();
session_start();

echo "🔧 Authentication Fix\n";
echo "=====================\n";

// Set up proper admin session with all required fields
$_SESSION['authenticated'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['email'] = 'robert.johnson@company.com';
$_SESSION['employee_email'] = 'robert.johnson@company.com';
$_SESSION['role'] = 'Super Admin';
$_SESSION['first_name'] = 'Robert';
$_SESSION['last_name'] = 'Sentry';
$_SESSION['access_token'] = 'manual_admin_token_' . time();
$_SESSION['refresh_token'] = 'refresh_' . time();
$_SESSION['employee_id'] = 1;

echo "✅ Session cleared and recreated\n";
echo "✅ Admin authentication data set\n";

// Test the auth helper
require_once 'includes/auth_helper.php';

echo "\n🔍 Testing Authentication:\n";
echo "- isAuthenticated(): " . (isAuthenticated() ? "✅ TRUE" : "❌ FALSE") . "\n";

$currentUser = getCurrentUser();
if ($currentUser) {
    echo "- getCurrentUser(): ✅ SUCCESS\n";
    echo "  User ID: " . $currentUser['id'] . "\n";
    echo "  Username: " . $currentUser['username'] . "\n";
    echo "  Role: " . $currentUser['role'] . "\n";
} else {
    echo "- getCurrentUser(): ❌ FAILED\n";
}

// Test requireAuth function
if (function_exists('requireAuth')) {
    echo "- requireAuth(): " . (requireAuth() ? "✅ PASSED" : "❌ FAILED") . "\n";
} else {
    echo "- requireAuth(): ℹ️ Function not loaded yet\n";
}

echo "\n🔗 Now try accessing:\n";
echo "- Settings: http://localhost/HCM/views/settings.php\n";
echo "- Dashboard: http://localhost/HCM/views/index.php\n";
echo "- Debugger: http://localhost/HCM/settings-debugger.php\n";

echo "\n💡 If this works, the issue was an incomplete session.\n";
echo "   You should now be able to access the settings page!\n";
?>