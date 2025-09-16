<?php
// Start with a completely fresh session
session_start();

echo "<html><head><title>Ultimate Settings Debug</title>";
echo "<style>body{font-family:monospace;background:#000;color:#0f0;padding:20px;} .error{color:#f00;} .warning{color:#ff0;} .info{color:#0af;} .section{border:1px solid #333;margin:10px 0;padding:10px;background:#111;} pre{background:#222;padding:10px;}</style>";
echo "</head><body>";

echo "<h1>🚀 ULTIMATE SETTINGS ACCESS DEBUG</h1>";

// STEP 1: FORCE LOGIN AND SESSION SETUP
echo "<div class='section'>";
echo "<h2>STEP 1: FORCE AUTHENTICATION</h2>";

// Clear any existing session
session_destroy();
session_start();

echo "Session cleared and restarted...<br>";

// Make API call to get valid authentication
$loginData = ['username' => 'admin', 'password' => 'password123'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/api/auth.php/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData && $responseData['success']) {
        // Set session exactly like login.php does
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

        echo "<div class='info'>✅ API login successful, session set</div>";
        echo "User: " . $_SESSION['username'] . " | Role: " . $_SESSION['role'] . "<br>";
    } else {
        echo "<div class='error'>❌ API login failed</div>";
    }
} else {
    echo "<div class='error'>❌ API call failed: HTTP $httpCode</div>";
}
echo "</div>";

// STEP 2: TEST AUTH HELPER
echo "<div class='section'>";
echo "<h2>STEP 2: TEST AUTH HELPER FUNCTIONS</h2>";

require_once 'includes/auth_helper.php';

echo "isAuthenticated(): " . (isAuthenticated() ? "<span class='info'>✅ TRUE</span>" : "<span class='error'>❌ FALSE</span>") . "<br>";

$currentUser = getCurrentUser();
echo "getCurrentUser(): " . ($currentUser ? "<span class='info'>✅ SUCCESS</span>" : "<span class='error'>❌ NULL</span>") . "<br>";

if ($currentUser) {
    echo "<pre>" . print_r($currentUser, true) . "</pre>";
}

// Test validateAndRefreshToken
echo "Testing validateAndRefreshToken()...<br>";
$tokenValid = validateAndRefreshToken();
echo "validateAndRefreshToken(): " . ($tokenValid ? "<span class='info'>✅ VALID</span>" : "<span class='error'>❌ INVALID</span>") . "<br>";

echo "</div>";

// STEP 3: MANUALLY TEST SETTINGS.PHP LOGIC
echo "<div class='section'>";
echo "<h2>STEP 3: SIMULATE SETTINGS.PHP LOGIC</h2>";

echo "Testing each step of settings.php...<br>";

// Test requireAuth equivalent
if (!isAuthenticated()) {
    echo "<div class='error'>❌ FAIL: Not authenticated</div>";
} elseif (!validateAndRefreshToken()) {
    echo "<div class='error'>❌ FAIL: Token validation failed</div>";
} else {
    echo "<div class='info'>✅ PASS: Authentication checks passed</div>";

    // Test getUserRole function from settings.php
    function getUserRole($userId) {
        try {
            require_once __DIR__ . '/config/database.php';
            global $conn;

            if (!$conn) {
                return 'employee';
            }

            $stmt = $conn->prepare("SELECT r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['role_name'] ?? 'employee';
        } catch (Exception $e) {
            return 'employee';
        }
    }

    $userRole = getUserRole($currentUser['id']);
    echo "User role from database: <strong>$userRole</strong><br>";

    $allowedRoles = ['admin', 'hr', 'super admin', 'hr manager', 'hr staff'];
    $hasAccess = in_array(strtolower($userRole), $allowedRoles);

    echo "Has settings access: " . ($hasAccess ? "<span class='info'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "<br>";

    if (!$hasAccess) {
        echo "<div class='error'>❌ FAIL: Role '$userRole' not in allowed roles</div>";
    } else {
        echo "<div class='info'>✅ PASS: All permission checks passed</div>";
    }
}
echo "</div>";

// STEP 4: TEST ACTUAL SETTINGS PAGE
echo "<div class='section'>";
echo "<h2>STEP 4: TEST ACTUAL SETTINGS PAGE ACCESS</h2>";

echo "Making direct HTTP request to settings page...<br>";

// Get current session cookie
$sessionName = session_name();
$sessionId = session_id();
$cookieHeader = "$sessionName=$sessionId";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/HCM/views/settings.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookieHeader);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";

if ($httpCode === 302) {
    echo "<div class='error'>❌ REDIRECT DETECTED</div>";
    echo "Redirect URL: " . ($redirectUrl ?: "Not provided") . "<br>";

    // Check if it's redirecting to index.php
    if (strpos($response, 'Location: index.php') !== false) {
        echo "<div class='error'>❌ Redirecting to index.php - permission denied</div>";
    }
} elseif ($httpCode === 200) {
    echo "<div class='info'>✅ SUCCESS: Settings page loaded</div>";

    // Check if it actually contains settings content
    if (strpos($response, 'System Settings') !== false) {
        echo "<div class='info'>✅ Contains settings content</div>";
    } else {
        echo "<div class='warning'>⚠️ Loaded but doesn't contain expected content</div>";
    }
} else {
    echo "<div class='error'>❌ Unexpected HTTP code: $httpCode</div>";
}

echo "</div>";

// STEP 5: NUCLEAR OPTION - BYPASS ALL CHECKS
echo "<div class='section'>";
echo "<h2>STEP 5: NUCLEAR OPTION - CREATE BYPASS SETTINGS PAGE</h2>";

$bypassContent = '<?php
// BYPASS VERSION - NO AUTHENTICATION CHECKS
?>
<!DOCTYPE html>
<html>
<head><title>Settings (Bypass)</title></head>
<body style="font-family: Arial; padding: 20px; background: #f5f5f5;">
<h1>🔓 Settings Page (Authentication Bypassed)</h1>
<p>This is a test version with no authentication checks.</p>
<p>If you can see this, the issue is definitely with authentication, not the settings page itself.</p>
<div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
<h2>Company Settings Test</h2>
<form>
<label>Company Name:</label><br>
<input type="text" value="Test Company" style="width: 300px; padding: 8px; margin: 5px 0;"><br><br>
<label>Company Email:</label><br>
<input type="email" value="test@company.com" style="width: 300px; padding: 8px; margin: 5px 0;"><br><br>
<button type="button" style="background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px;">Save (Test)</button>
</form>
</div>
<p><a href="settings.php">← Back to Real Settings</a></p>
</body>
</html>';

file_put_contents('views/settings-bypass.php', $bypassContent);

echo "<div class='info'>✅ Created bypass settings page</div>";
echo "<div class='info'>🔗 Test it: <a href='views/settings-bypass.php' style='color: #0af;'>settings-bypass.php</a></div>";

echo "</div>";

// FINAL RECOMMENDATIONS
echo "<div class='section'>";
echo "<h2>🎯 FINAL DIAGNOSIS & SOLUTIONS</h2>";

echo "<div class='info'>Try these in order:</div>";
echo "1. <a href='views/settings-bypass.php' style='color: #0af;'>Test bypass version</a> - If this works, it's auth issue<br>";
echo "2. <a href='views/settings.php' style='color: #0af;'>Try real settings again</a> - With current session<br>";
echo "3. Check browser console for JavaScript errors<br>";
echo "4. Try incognito/private browsing mode<br>";

echo "</div>";

echo "<div style='margin-top: 20px; padding: 10px; background: #333; color: #0f0;'>";
echo "Debug completed: " . date('Y-m-d H:i:s');
echo "</div>";

echo "</body></html>";
?>