<?php
// Direct debug for settings access issue
session_start();

echo "<h1>Settings Access Debug</h1>";

// Check session data
echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test requireAuth step by step
echo "<h2>Authentication Steps:</h2>";

// Include auth helper
require_once __DIR__ . '/includes/auth_helper.php';

echo "1. Auth helper included<br>";

// Check if authenticated
$isAuth = isAuthenticated();
echo "2. isAuthenticated(): " . ($isAuth ? "TRUE" : "FALSE") . "<br>";

if (!$isAuth) {
    echo "❌ Not authenticated - this is why settings fails<br>";
    exit();
}

echo "3. ✅ Authentication passed<br>";

// Get current user
$currentUser = getCurrentUser();
echo "4. getCurrentUser(): ";
if ($currentUser) {
    echo "✅ Success<br>";
    echo "<pre>";
    print_r($currentUser);
    echo "</pre>";
} else {
    echo "❌ NULL - this is why settings fails<br>";
    exit();
}

// Get user role using same function as settings
function getUserRole($userId) {
    try {
        require_once __DIR__ . '/config/database.php';
        global $conn;

        if (!$conn) {
            return 'employee'; // Default role if no connection
        }

        $stmt = $conn->prepare("
            SELECT r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['role_name'] ?? 'employee';
    } catch (Exception $e) {
        echo "getUserRole error: " . $e->getMessage() . "<br>";
        return 'employee'; // Default role on error
    }
}

$userRole = getUserRole($currentUser['id']);
echo "5. getUserRole(" . $currentUser['id'] . "): " . $userRole . "<br>";

// Check role permission
$allowedRoles = ['admin', 'hr', 'super admin', 'hr manager', 'hr staff'];
$roleCheck = in_array(strtolower($userRole), $allowedRoles);
echo "6. Role check (allowed: " . implode(', ', $allowedRoles) . "): " . ($roleCheck ? "✅ PASS" : "❌ FAIL") . "<br>";

if (!$roleCheck) {
    echo "❌ Role check failed - this is why settings redirects<br>";
} else {
    echo "✅ All checks passed - settings should work<br>";
}
?>