<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/dashboard-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
</head>
<body>

    <div class="main-container">
        <div class="menu-card">
            
            <div class="menu-header">
                <h1>Smart Hub</h1>
                <p>Welcome back! Select an option below.</p>
            </div>

            <div class="menu-grid">
                <button class="menu-btn primary" onclick="window.location.href='calculator.php'">
                    Carbon Calculator
                </button>

                <button class="menu-btn primary" onclick="window.location.href='history.php'">
                    Activity History
                </button>
                
                <button class="menu-btn secondary" disabled>
                    Household Stats
                </button>

                <button class="menu-btn secondary" disabled>
                    Eco Settings
                </button>

                <button class="menu-btn primary" onclick="window.location.href='accessibility-settings.php'">
                    ♿ Accessibility
                </button>
            </div>

            <div class="footer-nav">
                <a href="index.php" class="back-link">← Back to Login Page</a>
            </div>

        </div>
    </div>

    <script src="scripts/accessibility.js"></script>
</body>
</html>