<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/login-style.css">
</head>
<body>

    <div class="main-container">
        <div class="auth-card" style="flex-direction: column; padding: 50px; min-height: auto;">
            
            <div class="dashboard-header" style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #2ecc71; margin-bottom: 10px;">Main Menu</h1>
                <p style="color: #666;">Welcome to the EcoTracker Hub</p>
            </div>

            <div class="menu-grid" style="display: flex; flex-direction: column; gap: 20px;">
                
                <button class="btn-primary" onclick="window.location.href='calculator.php'">
                    Carbon Footprint Calculator
                </button>
                
                <button class="btn-primary" style="background: #95a5a6; cursor: not-allowed;" disabled>
                    View Statistics (Coming Soon)
                </button>

                <button class="btn-primary" style="background: #95a5a6; cursor: not-allowed;" disabled>
                    Household Tips
                </button>

                <div class="logout-section" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
                    <a href="index.php" style="text-decoration: none; color: #e74c3c; font-weight: bold; font-size: 0.9rem;">
                        Log Out / Back to Login
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="scripts/login-script.js" defer></script>
</body>
</html>