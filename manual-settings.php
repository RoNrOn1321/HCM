<!DOCTYPE html>
<html>
<head>
    <title>Manual Settings Page</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a8b; }
        .tab { display: inline-block; padding: 10px 20px; margin-right: 5px; background: #e0e0e0; cursor: pointer; border-radius: 4px 4px 0 0; }
        .tab.active { background: #007cba; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Manual Settings Page</h1>
        <p>This is a direct settings interface with no authentication required.</p>

        <div class="tabs">
            <div class="tab active" onclick="switchTab('company')">Company</div>
            <div class="tab" onclick="switchTab('system')">System</div>
            <div class="tab" onclick="switchTab('payroll')">Payroll</div>
        </div>

        <div id="company" class="tab-content active">
            <h2>Company Settings</h2>
            <form onsubmit="saveSettings(event, 'company')">
                <div class="form-group">
                    <label>Company Name:</label>
                    <input type="text" name="company_name" value="TechCorp Solutions">
                </div>
                <div class="form-group">
                    <label>Company Email:</label>
                    <input type="email" name="company_email" value="info@techcorp.com">
                </div>
                <div class="form-group">
                    <label>Company Phone:</label>
                    <input type="text" name="company_phone" value="+1 (555) 123-4567">
                </div>
                <div class="form-group">
                    <label>Company Address:</label>
                    <textarea name="company_address" rows="3">123 Business Street, Tech City, TC 12345</textarea>
                </div>
                <button type="submit">Save Company Settings</button>
            </form>
        </div>

        <div id="system" class="tab-content">
            <h2>System Settings</h2>
            <form onsubmit="saveSettings(event, 'system')">
                <div class="form-group">
                    <label>Session Timeout (minutes):</label>
                    <input type="number" name="session_timeout" value="30" min="5" max="120">
                </div>
                <div class="form-group">
                    <label>Max Login Attempts:</label>
                    <input type="number" name="max_login_attempts" value="5" min="3" max="10">
                </div>
                <div class="form-group">
                    <label>Backup Frequency:</label>
                    <select name="backup_frequency">
                        <option value="daily" selected>Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <button type="submit">Save System Settings</button>
            </form>
        </div>

        <div id="payroll" class="tab-content">
            <h2>Payroll Settings</h2>
            <form onsubmit="saveSettings(event, 'payroll')">
                <div class="form-group">
                    <label>Pay Frequency:</label>
                    <select name="pay_frequency">
                        <option value="monthly" selected>Monthly</option>
                        <option value="bi-weekly">Bi-weekly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Overtime Rate (multiplier):</label>
                    <input type="number" name="overtime_rate" value="1.5" step="0.1" min="1" max="3">
                </div>
                <div class="form-group">
                    <label>Tax Rate (%):</label>
                    <input type="number" name="tax_rate" value="12.0" step="0.01" min="0" max="50">
                </div>
                <button type="submit">Save Payroll Settings</button>
            </form>
        </div>

        <div id="result" style="margin-top: 20px; padding: 10px; background: #e8f5e8; border-radius: 4px; display: none;">
            <strong>Settings saved successfully!</strong>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function saveSettings(event, category) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const settings = {};

            for (const [key, value] of formData.entries()) {
                settings[key] = { value: value, category: category };
            }

            console.log('Saving settings:', settings);

            // Show success message
            const result = document.getElementById('result');
            result.style.display = 'block';
            result.innerHTML = `<strong>✅ ${category.charAt(0).toUpperCase() + category.slice(1)} settings saved successfully!</strong>`;

            setTimeout(() => {
                result.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>