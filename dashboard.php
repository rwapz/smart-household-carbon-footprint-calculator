<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php?error=unauthorized&tab=login');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($username, 0, 1));

require_once 'connect.php';
$stmt = $CONN->prepare("SELECT * FROM USER_TYPES WHERE USER_ID = :uid AND USER_TYPE_NAME = 'Admin' LIMIT 1");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$isAdmin = $stmt->fetch() !== false;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/dashboard-style.css">
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
    <header class="dashboard-header">
        <div class="header-left">
            <h1>Welcome back, <span><?php echo $username; ?></span></h1>
            <p>Track your environmental impact</p>
        </div>
        <div class="header-right">
            <?php if ($isAdmin): ?>
            <a href="admin-dashboard.php" class="admin-btn">Admin Dashboard</a>
            <?php endif; ?>
            <div class="user-info">
                <span class="user-avatar"><?php echo $initial; ?></span>
                <span class="user-name"><?php echo $username; ?></span>
            </div>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" aria-label="Toggle dark mode">🌙</button>
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <main class="main-container">
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">📊</span>
                    <div class="stat-content">
                        <span class="stat-label">This Month</span>
                        <span class="stat-value" id="monthly-co2">--</span>
                        <span class="stat-unit">kg CO₂</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📈</span>
                    <div class="stat-content">
                        <span class="stat-label">Monthly Avg</span>
                        <span class="stat-value" id="avg-co2">--</span>
                        <span class="stat-unit">kg CO₂</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🏆</span>
                    <div class="stat-content">
                        <span class="stat-label">Your Rank</span>
                        <span class="stat-value" id="your-rank">--</span>
                        <span class="stat-unit">of 150</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🎯</span>
                    <div class="stat-content">
                        <span class="stat-label">Goal Progress</span>
                        <span class="stat-value" id="goal-progress">--</span>
                        <span class="stat-unit">Target: 30kg</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid">
            <div class="dashboard-column">
                <section class="dashboard-section">
                    <h2>🌱 Environmental Impact</h2>
                    <div class="impact-grid">
                        <div class="impact-item">
                            <span class="impact-value" id="trees-equiv">--</span>
                            <span class="impact-label">Trees Equivalent</span>
                        </div>
                        <div class="impact-item">
                            <span class="impact-value" id="water-saved">--</span>
                            <span class="impact-label">Water Saved (L)</span>
                        </div>
                        <div class="impact-item">
                            <span class="impact-value" id="clean-energy">--</span>
                            <span class="impact-label">Clean Energy (kWh)</span>
                        </div>
                    </div>
                    <p class="impact-note" id="impact-note">Start tracking to see your impact!</p>
                </section>

                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>🎯 Monthly Goal</h2>
                        <button class="edit-btn" onclick="openGoalModal()">Edit</button>
                    </div>
                    <div class="goal-card">
                        <div class="goal-header">
                            <span id="goal-target-display">Target: -- kg CO₂</span>
                            <span class="goal-pct" id="goal-pct-text">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="goal-bar"></div>
                        </div>
                        <p class="goal-message"><strong id="goal-status">Set your goal!</strong></p>
                    </div>
                </section>
            </div>

            <div class="dashboard-column">
                <section class="dashboard-section">
                    <h2>💡 Daily Tip</h2>
                    <div class="tip-card">
                        <p id="daily-tip">Loading tip...</p>
                    </div>
                </section>

                <section class="dashboard-section">
                    <h2>⚡ Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="calculator.php" class="action-card">
                            <span class="action-icon">🧮</span>
                            <span class="action-text">Carbon Calculator</span>
                        </a>
                        <a href="history.php" class="action-card">
                            <span class="action-icon">📊</span>
                            <span class="action-text">Activity History</span>
                        </a>
                        <a href="accessibility-settings.php" class="action-card">
                            <span class="action-icon">♿</span>
                            <span class="action-text">Accessibility</span>
                        </a>
                        <a href="settings.php" class="action-card">
                            <span class="action-icon">⚙️</span>
                            <span class="action-text">Settings</span>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <div id="goalModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h2>Set Your Monthly Goal</h2>
            <p>Choose a target for your carbon footprint.</p>
            
            <div class="goal-options">
                <button class="goal-preset" onclick="setGoal(25)">
                    <span class="preset-icon">🌱</span>
                    <span class="preset-name">Eco Warrior</span>
                    <span class="preset-value">25 kg</span>
                </button>
                <button class="goal-preset active" onclick="setGoal(30)">
                    <span class="preset-icon">🌍</span>
                    <span class="preset-name">Balanced</span>
                    <span class="preset-value">30 kg</span>
                </button>
                <button class="goal-preset" onclick="setGoal(40)">
                    <span class="preset-icon">🏠</span>
                    <span class="preset-name">Comfortable</span>
                    <span class="preset-value">40 kg</span>
                </button>
            </div>

            <div class="custom-goal">
                <label for="customGoalInput">Custom target:</label>
                <div class="input-row">
                    <input type="number" id="customGoalInput" min="5" max="200" placeholder="35">
                    <span class="input-unit">kg CO₂</span>
                </div>
                <button class="btn-primary" onclick="setCustomGoal()">Set Goal</button>
            </div>

            <button class="modal-close" onclick="closeGoalModal()" aria-label="Close">✕</button>
        </div>
    </div>

    <script src="scripts/accessibility.js"></script>
    <script src="scripts/dashboard-enhanced.js"></script>
    <script>
        if (window.location.search.includes('loggedin=1')) {
            console.log('Login successful! Welcome to Smart Household Carbon Footprint Tracker.');
        }
    </script>
</body>
</html>
