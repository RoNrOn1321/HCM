<?php
session_start();

// Simulate login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'Super Admin';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Debug Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        button { background: #007cba; color: white; padding: 10px 15px; border: none; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>API Debug Test</h1>
    <p>Session User ID: <?php echo $_SESSION['user_id']; ?></p>
    <p>Session Username: <?php echo $_SESSION['username'] ?? 'N/A'; ?></p>

    <div class="test-section">
        <h2>Profile API Test</h2>
        <button onclick="testProfileAPI()">Test Profile API</button>
        <div id="profileResult"></div>
    </div>

    <div class="test-section">
        <h2>Dashboard Stats API Test</h2>
        <button onclick="testDashboardStats()">Test Dashboard Stats</button>
        <div id="dashboardStatsResult"></div>
    </div>

    <div class="test-section">
        <h2>Dashboard Activities API Test</h2>
        <button onclick="testDashboardActivities()">Test Dashboard Activities</button>
        <div id="dashboardActivitiesResult"></div>
    </div>

    <div class="test-section">
        <h2>Dashboard Chart API Test</h2>
        <button onclick="testDashboardChart()">Test Dashboard Chart</button>
        <div id="dashboardChartResult"></div>
    </div>

    <script>
        async function testAPI(url, resultElementId, description) {
            const resultElement = document.getElementById(resultElementId);
            resultElement.innerHTML = '<p>Testing ' + description + '...</p>';

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin'
                });

                const responseText = await response.text();

                let result;
                try {
                    result = JSON.parse(responseText);
                    resultElement.innerHTML = `
                        <p class="success">✅ ${description} - Status: ${response.status}</p>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    `;
                } catch (jsonError) {
                    resultElement.innerHTML = `
                        <p class="error">❌ ${description} - JSON Parse Error</p>
                        <p>Status: ${response.status}</p>
                        <p>Raw Response:</p>
                        <pre>${responseText}</pre>
                    `;
                }
            } catch (error) {
                resultElement.innerHTML = `
                    <p class="error">❌ ${description} - Network Error</p>
                    <pre>${error.message}</pre>
                `;
            }
        }

        function testProfileAPI() {
            testAPI('/HCM/api/profile.php', 'profileResult', 'Profile API');
        }

        function testDashboardStats() {
            testAPI('/HCM/api/dashboard.php?type=stats', 'dashboardStatsResult', 'Dashboard Stats API');
        }

        function testDashboardActivities() {
            testAPI('/HCM/api/dashboard.php?type=activities', 'dashboardActivitiesResult', 'Dashboard Activities API');
        }

        function testDashboardChart() {
            testAPI('/HCM/api/dashboard.php?type=chart', 'dashboardChartResult', 'Dashboard Chart API');
        }
    </script>
</body>
</html>