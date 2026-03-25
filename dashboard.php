<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($username, 0, 1));
?>
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

    <!-- TOP DASHBOARD HEADER -->
    <div class="dashboard-header">
        <div class="dashboard-welcome">
            <h1>Welcome back, <span><?php echo $username; ?></span>! 👋</h1>
            <p>Select a calculator or view your activity history</p>
        </div>

        <div class="dashboard-user-section">
            <div class="user-avatar"><?php echo $initial; ?></div>
            <div class="user-info">
                <p>Logged in as</p>
                <h3><?php echo $username; ?></h3>
            </div>
            <form method="POST" action="logout.php" style="margin: 0;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

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

        </div>
    </div>

    <!-- EmailJS -->
    <span id="eco-user"
        data-name="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>"
        data-email="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>"
        style="display:none;">
    </span>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script src="scripts/emailjs-handler.js"></script>
    <script src="scripts/accessibility.js"></script>
</body>
</html>