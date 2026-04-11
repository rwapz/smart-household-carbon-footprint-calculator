<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Household | Carbon Footprint Calculator</title>
    <link rel="stylesheet" href="stylesheets/login-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
</head>
<body>

    <!-- Main App Container -->
    <main class="main-container">
        <div class="auth-card">
            
            <!-- Left Side: Branding & Visuals -->
            <div class="auth-visual">
                <div class="visual-content">
                    <h1>Smart<br>Household</h1>
                    <p>Small steps for you, big leaps for the planet.</p>
                </div>
            </div>

            <!-- Right Side: Authentication Forms -->
            <div class="auth-form-container">
                
                <!-- Form Tabs -->
                <div class="form-toggle">
                    <button id="loginTab" class="active">Login</button>
                    <button id="signupTab">Sign Up</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" action="login-logic.php" method="POST" class="auth-form">
                    <h2>Welcome Back</h2>
                    <div class="input-group">
                        <label for="login-user">Username</label>
                        <input type="text" id="login-user" name="USERNAME" placeholder="Enter your username" required />
                    </div>
                    <div class="input-group">
                        <label for="login-pass">Password</label>
                        <input type="password" id="login-pass" name="PASSWORD" placeholder="Enter your password" required />
                    </div>
                    <button type="submit" class="btn-primary">Login to Dashboard</button>
                </form>

                <!-- Sign Up Form (Hidden by default) -->
                <form id="signupForm" action="signup-logic.php" method="POST" class="auth-form" style="display: none;">
                    <h2>Create Account</h2>
                    <div class="input-group">
                        <label for="signup-user">Username</label>
                        <input type="text" id="signup-user" name="USERNAME" placeholder="Choose a username" required />
                    </div>
                    <div class="input-group">
                        <label for="signup-pass">Password</label>
                        <input type="password" id="signup-pass" name="PASSWORD" placeholder="Create a password" required />
                    </div>
                    <button type="submit" class="btn-primary">Start Tracking</button>
                </form>

                <!-- Quick Navigation for Testing -->
                <div class="test-navigation" style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
                    <a href="dashboard.php" style="color: #2ecc71; font-size: 0.85rem; text-decoration: none; font-weight: 600;">
                        &larr; Skip to Dashboard Menu
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="scripts/accessibility.js"></script>
    <script src="scripts/login-script.js" defer></script>
</body>
</html>