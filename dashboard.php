<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php?error=unauthorized&tab=login');
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

    <!-- Apply saved theme BEFORE paint to prevent flash -->
    <script>
        (function() {
            const theme    = localStorage.getItem('eco-theme')    || 'light';
            const contrast = localStorage.getItem('eco-contrast') === 'true' ? 'high' : 'normal';
            const font     = localStorage.getItem('eco-fontsize') || 'normal';
            const fontMap  = { small: '14px', normal: '16px', large: '19px' };
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-contrast', contrast);
            document.documentElement.style.fontSize = fontMap[font] || '16px';
        })();
    </script>
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
        <!-- QUICK STATS SECTION -->
        <section class="dashboard-section stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <p class="stat-label">This Month</p>
                        <h3 class="stat-value" id="monthly-co2">--</h3>
                        <p class="stat-unit">kg CO₂</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-content">
                        <p class="stat-label">Monthly Avg</p>
                        <h3 class="stat-value" id="avg-co2">--</h3>
                        <p class="stat-unit">kg CO₂</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-content">
                        <p class="stat-label">Your Rank</p>
                        <h3 class="stat-value" id="your-rank">--</h3>
                        <p class="stat-unit">of 150</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-content">
                        <p class="stat-label">Goal Progress</p>
                        <h3 class="stat-value" id="goal-progress">--</h3>
                        <p class="stat-unit">Target: 30kg</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN GRID -->
        <div class="dashboard-grid">
            
            <!-- LEFT COLUMN -->
            <div class="dashboard-column-left">

                <!-- ENVIRONMENTAL IMPACT -->
                <section class="dashboard-section impact-section">
                    <h2>🌱 Latest Environmental Impact</h2>
                    <div class="impact-grid">
                        <div class="impact-item">
                            <p class="impact-value" id="trees-equiv">--</p>
                            <p class="impact-label">🌳 Trees Equivalent</p>
                        </div>
                        <div class="impact-item">
                            <p class="impact-value" id="water-saved">--</p>
                            <p class="impact-label">💧 Water Saved (L)</p>
                        </div>
                        <div class="impact-item">
                            <p class="impact-value" id="clean-energy">--</p>
                            <p class="impact-label">🔋 Clean Energy (kWh)</p>
                        </div>
                    </div>
                    <p class="impact-note" id="impact-note">No recent data yet. Start logging activities!</p>
                </section>

                <!-- CARBON GOAL -->
                <section class="dashboard-section goal-section">
                    <div class="goal-header-top">
                        <h2>🎯 Monthly Goal</h2>
                        <button class="edit-goal-btn" onclick="openGoalModal()">⚙️ Edit</button>
                    </div>
                    <div class="goal-card">
                        <div class="goal-header">
                            <span id="goal-target-display">Target: -- kg CO₂</span>
                            <span class="goal-pct" id="goal-pct-text">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="goal-bar" style="width: 0%;"></div>
                        </div>
                        <p class="goal-message"><strong id="goal-status">Set your goal!</strong> You can edit it anytime.</p>
                    </div>
                </section>

                <!-- TIPS & RECOMMENDATIONS -->
                <section class="dashboard-section tips-section">
                    <h2>💡 Daily Tip</h2>
                    <div class="tip-card">
                        <p id="daily-tip">Loading tip...</p>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="dashboard-column-right">

                <!-- QUICK STATS (TOP RIGHT) -->
                <section class="dashboard-section stats-summary">
                    <h2>📊 Overview</h2>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <p class="summary-label">This Month</p>
                            <h3 class="summary-value" id="monthly-co2">--</h3>
                            <p class="summary-unit">kg CO₂</p>
                        </div>
                        <div class="summary-item">
                            <p class="summary-label">Monthly Avg</p>
                            <h3 class="summary-value" id="avg-co2">--</h3>
                            <p class="summary-unit">kg CO₂</p>
                        </div>
                    </div>
                </section>

            </div>

        </div>

        <!-- NAVIGATION CARD -->
        <div class="menu-card menu-card-compact">
            <div class="menu-header menu-header-compact">
                <h2>Smart Hub</h2>
                <p>Quick access to all tools</p>
            </div>

            <div class="menu-grid">
                <button class="menu-btn btn-green" onclick="window.location.href='calculator.php'">
                    🧮 Carbon Calculator
                </button>

                <button class="menu-btn btn-green" onclick="window.location.href='history.php'">
                    📊 Activity History
                </button>

                <button class="menu-btn btn-blue" onclick="window.location.href='accessibility-settings.php'">
                    ♿ Accessibility
                </button>

                <button class="menu-btn btn-blue" onclick="window.location.href='settings.php'">
                    ⚙️ Account Settings
                </button>
            </div>

        </div>

    </div>

    <!-- GOAL SETTING MODAL -->
    <div id="goalModal" class="modal-overlay" style="display: none;">
        <div class="modal-content-goal">
            <h2>Set Your Monthly CO₂ Goal</h2>
            <p class="modal-description">Choose a target for your household's monthly carbon footprint.</p>
            
            <div class="goal-preset-options">
                <button class="preset-btn" onclick="setGoal(25)">
                    <span class="preset-icon">🌱</span>
                    <span class="preset-name">Eco Warrior</span>
                    <span class="preset-value">25 kg</span>
                </button>
                <button class="preset-btn" onclick="setGoal(30)">
                    <span class="preset-icon">🌍</span>
                    <span class="preset-name">Balanced</span>
                    <span class="preset-value">30 kg</span>
                </button>
                <button class="preset-btn" onclick="setGoal(40)">
                    <span class="preset-icon">🏠</span>
                    <span class="preset-name">Comfortable</span>
                    <span class="preset-value">40 kg</span>
                </button>
            </div>

            <div class="custom-goal-section">
                <label for="customGoalInput">Or enter a custom target:</label>
                <div class="custom-goal-input">
                    <input type="number" id="customGoalInput" min="5" max="200" placeholder="e.g., 35" />
                    <span class="input-unit">kg CO₂</span>
                </div>
                <button class="btn-set-custom" onclick="setCustomGoal()">Set Custom Goal</button>
            </div>

            <button class="modal-close" onclick="closeGoalModal()">✕</button>
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
    <script src="scripts/dashboard-enhanced.js"></script>
</body>
</html>