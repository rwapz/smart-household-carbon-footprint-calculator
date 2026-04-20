<?php
require_once 'connect.php';

$households = [];
try {
    $stmt = $CONN->prepare("SELECT HOUSEHOLD_ID, HOUSEHOLD_NAME FROM household ORDER BY HOUSEHOLD_NAME ASC");
    $stmt->execute();
    $households = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to fetch households: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Household | Carbon Footprint Calculator</title>
    <link rel="stylesheet" href="stylesheets/login-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script>
        (function() {
            const theme = localStorage.getItem('eco-theme') || 'light';
            const contrast = localStorage.getItem('eco-contrast') === 'true' ? 'high' : 'normal';
            const font = localStorage.getItem('eco-fontsize') || 'normal';
            const fontMap = { small: '14px', normal: '16px', large: '19px' };
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-contrast', contrast);
            document.documentElement.style.fontSize = fontMap[font] || '16px';
        })();
    </script>
</head>
<body>
    <main class="main-container">
        <div class="auth-card">
            <div class="auth-visual">
                <div class="visual-content">
                    <h1>Smart<br>Household</h1>
                    <p>Track your carbon footprint and make a difference.</p>
                </div>
            </div>

            <div class="auth-form-container">
                <div class="form-toggle">
                    <button id="loginTab" class="active" type="button">Login</button>
                    <button id="signupTab" type="button">Sign Up</button>
                </div>

                <form id="loginForm" action="login-logic.php" method="POST" class="auth-form">
                    <h2>Welcome Back</h2>
                    
                    <div class="input-group">
                        <label for="login-user">Username</label>
                        <input type="text" id="login-user" name="USERNAME" placeholder="Enter your username" autocomplete="username" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="login-pass">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="login-pass" name="PASSWORD" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember-me" name="REMEMBER_ME" checked>
                        <label for="remember-me">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn-primary">Login</button>
                </form>

                <form id="signupForm" action="signup-logic.php" method="POST" class="auth-form" style="display: none;">
                    <h2>Create Account</h2>
                    
                    <div class="input-group">
                        <label for="signup-user">Username</label>
                        <input type="text" id="signup-user" name="USERNAME" placeholder="Choose a username" autocomplete="username" required>
                    </div>

                    <div class="input-group">
                        <label for="signup-pass">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="signup-pass" name="PASSWORD" placeholder="Create a password (min 6 characters)" autocomplete="new-password" required>
                            <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill"></div>
                            </div>
                            <span class="strength-text">Password strength: <strong id="strength-label">Weak</strong></span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="signup-pass-confirm">Confirm Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="signup-pass-confirm" name="PASSWORD_CONFIRM" placeholder="Confirm your password" autocomplete="new-password" required>
                            <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="household-label">Select Your Household</label>
                        <div class="household-grid">
                            <?php foreach ($households as $h): ?>
                                <div class="household-card">
                                    <input type="radio" id="household-<?php echo htmlspecialchars($h['HOUSEHOLD_ID']); ?>" 
                                           name="HOUSEHOLD_ID" value="<?php echo htmlspecialchars($h['HOUSEHOLD_ID']); ?>" required>
                                    <label for="household-<?php echo htmlspecialchars($h['HOUSEHOLD_ID']); ?>" class="card-label">
                                        <span class="card-name"><?php echo htmlspecialchars($h['HOUSEHOLD_NAME']); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="terms-checkbox" name="TERMS_AGREED" required>
                        <label for="terms-checkbox">I agree to the <a href="terms.php" target="_blank">Terms & Conditions</a></label>
                    </div>

                    <button type="submit" class="btn-primary">Create Account</button>
                </form>
            </div>
        </div>
    </main>

    <script src="scripts/accessibility.js"></script>
    <script src="scripts/login-script.js" defer></script>
</body>
</html>
