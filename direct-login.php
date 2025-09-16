<?php
session_start();

echo "Direct Login Process\n";
echo "===================\n";

// Make API call to login
$data = [
    'username' => 'admin',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/auth.php/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "API Response Code: $httpCode\n";

if ($response === false) {
    echo "❌ Unable to connect to authentication service\n";
    exit();
}

// Extract JSON from response
$jsonStart = strpos($response, '{');
if ($jsonStart !== false) {
    $jsonResponse = substr($response, $jsonStart);
    $responseData = json_decode($jsonResponse, true);
} else {
    $responseData = json_decode($response, true);
}

echo "Response Data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";

if ($httpCode === 200 && $responseData && isset($responseData['success']) && $responseData['success']) {
    // Store authentication data in session
    $_SESSION['user_id'] = $responseData['data']['user']['id'] ?? null;
    $_SESSION['username'] = $responseData['data']['user']['username'] ?? null;
    $_SESSION['email'] = $responseData['data']['user']['email'] ?? null;
    $_SESSION['employee_email'] = $responseData['data']['user']['employee_email'] ?? null;
    $_SESSION['role'] = $responseData['data']['user']['role'] ?? null;
    $_SESSION['first_name'] = $responseData['data']['user']['first_name'] ?? null;
    $_SESSION['last_name'] = $responseData['data']['user']['last_name'] ?? null;
    $_SESSION['access_token'] = $responseData['data']['access_token'] ?? null;
    $_SESSION['refresh_token'] = $responseData['data']['refresh_token'] ?? null;
    $_SESSION['authenticated'] = true;

    echo "✅ Login successful! Session created.\n";
    echo "Session Data:\n";
    echo "- User ID: " . $_SESSION['user_id'] . "\n";
    echo "- Username: " . $_SESSION['username'] . "\n";
    echo "- Role: " . $_SESSION['role'] . "\n";
    echo "- Authenticated: " . ($_SESSION['authenticated'] ? 'Yes' : 'No') . "\n";

    echo "\n🔗 You can now access:\n";
    echo "- Dashboard: http://localhost/HCM/views/index.php\n";
    echo "- Settings: http://localhost/HCM/views/settings.php\n";
} else {
    echo "❌ Login failed\n";
    if ($responseData && isset($responseData['message'])) {
        echo "Error: " . $responseData['message'] . "\n";
    }
}
?>