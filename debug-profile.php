<?php
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h2>Debug Profile Page</h2>";

// Check session
echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>No user session found!</p>";
    exit();
}

$user_id = $_SESSION['user_id'];
echo "<p>Current User ID: " . $user_id . "</p>";

// Test the exact query from profile.php
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.email, u.is_active, u.last_login,
               r.role_name, r.permissions,
               e.employee_id, e.first_name, e.middle_name, e.last_name,
               e.email as employee_email, e.phone, e.date_of_birth,
               e.address, e.city, e.state, e.zip_code, e.country,
               e.hire_date, e.employment_status, e.employee_type,
               e.emergency_contact_name, e.emergency_contact_phone, e.emergency_contact_relationship,
               d.dept_name as department_name,
               p.position_title,
               ec.basic_salary
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN employees e ON u.id = e.user_id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN positions p ON e.position_id = p.id
        LEFT JOIN employee_compensation ec ON e.id = ec.employee_id AND ec.is_active = 1
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h3>Query Result:</h3>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";

    if (!$user) {
        echo "<p style='color: red;'>No user data found for ID: " . $user_id . "</p>";
    }

} catch(PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Check individual tables
echo "<h3>User Table Data:</h3>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($userData);
echo "</pre>";

echo "<h3>Employee Table Data:</h3>";
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$user_id]);
$empData = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($empData);
echo "</pre>";

echo "<h3>Roles Table Data:</h3>";
if ($userData && $userData['role_id']) {
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->execute([$userData['role_id']]);
    $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($roleData);
    echo "</pre>";
}
?>