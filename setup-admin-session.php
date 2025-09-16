<?php
session_start();

// Create admin session manually
$_SESSION['authenticated'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['email'] = 'robert.johnson@company.com';
$_SESSION['role'] = 'Super Admin';
$_SESSION['first_name'] = 'Robert';
$_SESSION['last_name'] = 'Sentry';
$_SESSION['access_token'] = 'manual_token_' . time();
$_SESSION['employee_id'] = 1;
$_SESSION['employee_email'] = 'robert.johnson@company.com';

echo "✅ Admin session created successfully!\n\n";
echo "Session Details:\n";
echo "- User ID: " . $_SESSION['user_id'] . "\n";
echo "- Username: " . $_SESSION['username'] . "\n";
echo "- Role: " . $_SESSION['role'] . "\n";
echo "- Authenticated: " . ($_SESSION['authenticated'] ? 'Yes' : 'No') . "\n";

echo "\n🔗 Quick Links:\n";
echo "- Dashboard: http://localhost/HCM/views/index.php\n";
echo "- Settings: http://localhost/HCM/views/settings.php\n";

echo "\n💡 You can now access the settings page!\n";
?>