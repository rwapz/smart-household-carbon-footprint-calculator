<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Household | Carbon Footprint Calculator</title>
    <link rel="stylesheet" href="stylesheets/login-style.css">
</head>
<body>

    <div class="main-container">
        <div class="auth-card">
            
            <div class="auth-visual">
                <div class="visual-content">
                    <div class="logo-container">
                        </div>
                    <h1>Smart<br>Household</h1>
                    <p>Small steps for you, big leaps for the planet.</p>
                    <div class="project-tag">SHU Computer Science Project</div>
                </div>
            </div>

            <div class="auth-form-container">
                
                <div class="form-toggle">
                    <button id="loginTab" class="active">Login</button>
                    <button id="signupTab">Sign Up</button>
                </div>

                <form id="loginForm" action="login_logic.php" method="POST" class="auth-form">
                    <h2>Welcome Back</h2>
                    <div class="input-group">
                        <label for="login-user">Username</label>
                        <input type="text" id="login-user" name="USERNAME" placeholder="Enter your username" required>
                    </div>
                    <div class="input-group">
                        <label for="login-pass">Password</label>
                        <input type="password" id="login-pass" name="PASSWORD" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-primary">Login to Dashboard</button>
                </form>

                <form id="signupForm" action="signup_logic.php" method="POST" class="auth-form" style="display: none;">
                    <h2>Create Account</h2>
                    <div class="input-group">
                        <label for="signup-user">Username</label>
                        <input type="text" id="signup-user" name="USERNAME" placeholder="Choose a username" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-email">Email</label>
                        <input type="email" id="signup-email" name="EMAIL" placeholder="student@shu.ac.uk" required>
                    </div>
                    <div class="input-group">
                        <label for="signup-pass">Password</label>
                        <input type="password" id="signup-pass" name="PASSWORD" placeholder="Create a password" required>
                    </div>
                    <button type="submit" class="btn-primary">Start Tracking</button>
                </form>

            </div> 
        </div> 
    </div> 

    <script src="scripts/login-script.js" defer></script>
</body>
</html>