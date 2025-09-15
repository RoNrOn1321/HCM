<!DOCTYPE html>
<html>
<head>
    <title>Test Reports API</title>
    <script>
        // API configuration
        const API_BASE_URL = '/HCM/api';

        // API helper function
        async function apiCall(endpoint, options = {}) {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        }

        async function testAPIs() {
            console.log('Testing APIs...');

            try {
                // Test department API
                console.log('Testing department API...');
                const deptResponse = await apiCall('/reports_no_auth.php?type=department');
                console.log('Department API success:', deptResponse.success);
                console.log('Department count:', deptResponse.data.departments.length);

                // Test charts API
                console.log('Testing charts API...');
                const chartsResponse = await apiCall('/reports_no_auth.php?type=charts');
                console.log('Charts API success:', chartsResponse.success);

                // Test benefits API
                console.log('Testing benefits API...');
                const benefitsResponse = await apiCall('/reports_no_auth.php?type=benefits');
                console.log('Benefits API success:', benefitsResponse.success);

                document.getElementById('results').innerHTML = `
                    <h2>API Tests Results:</h2>
                    <p>✅ Department API: ${deptResponse.success ? 'PASS' : 'FAIL'}</p>
                    <p>✅ Charts API: ${chartsResponse.success ? 'PASS' : 'FAIL'}</p>
                    <p>✅ Benefits API: ${benefitsResponse.success ? 'PASS' : 'FAIL'}</p>
                `;

            } catch (error) {
                console.error('API test failed:', error);
                document.getElementById('results').innerHTML = `
                    <h2>API Tests Results:</h2>
                    <p>❌ Error: ${error.message}</p>
                `;
            }
        }

        window.onload = function() {
            testAPIs();
        };
    </script>
</head>
<body>
    <h1>Testing Reports API</h1>
    <div id="results">Loading...</div>
</body>
</html>