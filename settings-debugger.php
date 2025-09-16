<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Settings Access Debugger</title>";
echo "<style>";
echo "body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }";
echo ".section { border: 1px solid #333; margin: 10px 0; padding: 10px; background: #2a2a2a; }";
echo ".error { color: #ff0000; }";
echo ".success { color: #00ff00; }";
echo ".warning { color: #ffff00; }";
echo ".info { color: #00aaff; }";
echo "pre { background: #000; padding: 10px; border: 1px solid #333; }";
echo "</style></head><body>";

echo "<h1>🔍 SETTINGS ACCESS DEBUGGER</h1>";
echo "<p>Comprehensive debugging of settings access permissions</p>";

// 1. SESSION ANALYSIS
echo "<div class='section'>";
echo "<h2>📊 SESSION ANALYSIS</h2>";
if (empty($_SESSION)) {
    echo "<div class='error'>❌ Session is completely empty</div>";
} else {
    echo "<div class='info'>Session contents:</div>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

$requiredKeys = ['authenticated', 'user_id', 'username', 'role', 'access_token'];
foreach ($requiredKeys as $key) {
    $status = isset($_SESSION[$key]) ?
        "<span class='success'>✅ Set: " . json_encode($_SESSION[$key]) . "</span>" :
        "<span class='error'>❌ Missing</span>";
    echo "- $key: $status<br>";
}
echo "</div>";

// 2. AUTH HELPER TEST
echo "<div class='section'>";
echo "<h2>🔐 AUTH HELPER TEST</h2>";
try {
    require_once 'includes/auth_helper.php';

    $isAuth = isAuthenticated();
    echo "isAuthenticated(): " . ($isAuth ?
        "<span class='success'>✅ TRUE</span>" :
        "<span class='error'>❌ FALSE</span>") . "<br>";

    $currentUser = getCurrentUser();
    if ($currentUser) {
        echo "<div class='success'>✅ getCurrentUser() returned data:</div>";
        echo "<pre>" . print_r($currentUser, true) . "</pre>";
    } else {
        echo "<div class='error'>❌ getCurrentUser() returned NULL</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Auth Helper Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 3. DATABASE CONNECTION TEST
echo "<div class='section'>";
echo "<h2>🗄️ DATABASE CONNECTION TEST</h2>";
try {
    require_once 'config/database.php';
    global $conn;

    if ($conn) {
        echo "<div class='success'>✅ Database connection exists</div>";
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "Database query test: " . ($result['test'] == 1 ?
            "<span class='success'>✅ Working</span>" :
            "<span class='error'>❌ Failed</span>") . "<br>";
    } else {
        echo "<div class='error'>❌ No database connection</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 4. USER ROLE VERIFICATION
echo "<div class='section'>";
echo "<h2>👤 USER ROLE VERIFICATION</h2>";
if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
    try {
        $stmt = $conn->prepare("
            SELECT u.id, u.username, r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            echo "<div class='success'>✅ User found in database:</div>";
            echo "<pre>" . print_r($userInfo, true) . "</pre>";

            $userRole = $userInfo['role_name'];
            $allowedRoles = ['admin', 'hr', 'super admin', 'hr manager', 'hr staff'];
            $hasAccess = in_array(strtolower($userRole), $allowedRoles);

            echo "User role: <strong>" . $userRole . "</strong><br>";
            echo "Allowed roles: " . implode(', ', $allowedRoles) . "<br>";
            echo "Has settings access: " . ($hasAccess ?
                "<span class='success'>✅ YES</span>" :
                "<span class='error'>❌ NO</span>") . "<br>";
        } else {
            echo "<div class='error'>❌ User ID {$_SESSION['user_id']} not found in database</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Role Check Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='warning'>⚠️ No user_id in session</div>";
}
echo "</div>";

// 5. SETTINGS PAGE SIMULATION
echo "<div class='section'>";
echo "<h2>⚙️ SETTINGS PAGE SIMULATION</h2>";
echo "Simulating settings.php access logic...<br>";

$shouldRedirect = false;
$redirectReason = "";

// Check requireAuth()
try {
    if (!function_exists('requireAuth')) {
        function requireAuth() {
            if (!isAuthenticated()) {
                return false;
            }
            return true;
        }
    }

    if (!requireAuth()) {
        $shouldRedirect = true;
        $redirectReason = "requireAuth() failed - not authenticated";
    }
} catch (Exception $e) {
    $shouldRedirect = true;
    $redirectReason = "requireAuth() threw exception: " . $e->getMessage();
}

// Check user role
if (!$shouldRedirect && isset($userRole)) {
    if (!in_array(strtolower($userRole), ['admin', 'hr', 'super admin', 'hr manager', 'hr staff'])) {
        $shouldRedirect = true;
        $redirectReason = "User role '$userRole' does not have settings access";
    }
}

if ($shouldRedirect) {
    echo "<div class='error'>❌ WOULD REDIRECT TO INDEX.PHP</div>";
    echo "<div class='error'>Reason: $redirectReason</div>";
} else {
    echo "<div class='success'>✅ SHOULD ALLOW ACCESS TO SETTINGS</div>";
}
echo "</div>";

// 6. API TEST
echo "<div class='section'>";
echo "<h2>🌐 API SETTINGS TEST</h2>";
if (isset($_SESSION['access_token'])) {
    echo "Testing API with current session...<br>";
    // Would need to test this with curl or similar
    echo "<div class='info'>ℹ️ API test requires separate call</div>";
} else {
    echo "<div class='warning'>⚠️ No access token to test API</div>";
}
echo "</div>";

// 7. RECOMMENDED ACTIONS
echo "<div class='section'>";
echo "<h2>🛠️ RECOMMENDED ACTIONS</h2>";

if (empty($_SESSION)) {
    echo "<div class='warning'>1. 🔄 Try direct login: <a href='direct-login.php' style='color: #00aaff;'>direct-login.php</a></div>";
    echo "<div class='warning'>2. 🔄 Try session setup: <a href='setup-admin-session.php' style='color: #00aaff;'>setup-admin-session.php</a></div>";
} elseif (!isAuthenticated()) {
    echo "<div class='warning'>1. 🔄 Session exists but auth failed - try logout and login again</div>";
    echo "<div class='warning'>2. 🔄 Or use: <a href='setup-admin-session.php' style='color: #00aaff;'>setup-admin-session.php</a></div>";
} elseif (isset($userRole) && !in_array(strtolower($userRole), ['admin', 'hr', 'super admin', 'hr manager', 'hr staff'])) {
    echo "<div class='error'>❌ Your role '$userRole' doesn't have access. Contact admin.</div>";
} else {
    echo "<div class='success'>✅ Everything looks good! Try accessing settings again.</div>";
    echo "<div class='info'>Direct link: <a href='views/settings.php' style='color: #00aaff;'>settings.php</a></div>";
}
echo "</div>";

// 8. SESSION QUICK FIXES
echo "<div class='section'>";
echo "<h2>⚡ QUICK FIXES</h2>";
echo "<div class='info'>";
echo "<a href='setup-admin-session.php' style='color: #00aaff; text-decoration: none; padding: 10px; background: #333; border: 1px solid #555; display: inline-block; margin: 5px;'>🔧 Setup Admin Session</a>";
echo "<a href='direct-login.php' style='color: #00aaff; text-decoration: none; padding: 10px; background: #333; border: 1px solid #555; display: inline-block; margin: 5px;'>🔐 Direct Login</a>";
echo "<a href='views/login.php' style='color: #00aaff; text-decoration: none; padding: 10px; background: #333; border: 1px solid #555; display: inline-block; margin: 5px;'>📝 Regular Login</a>";
echo "<a href='views/settings.php' style='color: #00aaff; text-decoration: none; padding: 10px; background: #333; border: 1px solid #555; display: inline-block; margin: 5px;'>⚙️ Try Settings</a>";
echo "</div>";
echo "</div>";

echo "<div style='margin-top: 20px; padding: 10px; background: #333; border: 1px solid #555;'>";
echo "<strong>Debug completed at:</strong> " . date('Y-m-d H:i:s');
echo "</div>";

echo "</body></html>";
?>