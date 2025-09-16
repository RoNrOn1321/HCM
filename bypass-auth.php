<?php
session_start();

echo "🚀 Authentication Bypass for Settings Access\n";
echo "=============================================\n";

// First, let's make a real API call to get valid tokens
$loginData = [
    'username' => 'admin',
    'password' => 'password123'
];

echo "📡 Making API login call...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/auth.php/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "API Response Code: $httpCode\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);

    if ($responseData && $responseData['success']) {
        echo "✅ API Login Successful!\n";

        // Store authentication data exactly like the login page does
        $_SESSION['user_id'] = $responseData['data']['user']['id'];
        $_SESSION['username'] = $responseData['data']['user']['username'];
        $_SESSION['email'] = $responseData['data']['user']['email'];
        $_SESSION['employee_email'] = $responseData['data']['user']['employee_email'];
        $_SESSION['role'] = $responseData['data']['user']['role'];
        $_SESSION['first_name'] = $responseData['data']['user']['first_name'];
        $_SESSION['last_name'] = $responseData['data']['user']['last_name'];
        $_SESSION['access_token'] = $responseData['data']['access_token'];
        $_SESSION['refresh_token'] = $responseData['data']['refresh_token'];
        $_SESSION['authenticated'] = true;

        echo "✅ Session data stored\n";
        echo "✅ Valid access token obtained\n";

        // Test authentication
        require_once 'includes/auth_helper.php';

        echo "\n🔍 Testing Authentication:\n";
        echo "- isAuthenticated(): " . (isAuthenticated() ? "✅ TRUE" : "❌ FALSE") . "\n";

        $currentUser = getCurrentUser();
        if ($currentUser) {
            echo "- getCurrentUser(): ✅ SUCCESS\n";
            echo "  Role: " . $currentUser['role'] . "\n";
        }

        echo "\n🎉 SUCCESS! You should now be able to access:\n";
        echo "🔗 Settings: http://localhost/HCM/views/settings.php\n";
        echo "🔗 Dashboard: http://localhost/HCM/views/index.php\n";

    } else {
        echo "❌ API login failed: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ API call failed with HTTP code: $httpCode\n";
    echo "Response: $response\n";
}
?>