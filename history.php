<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity History | Smart Household</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylesheets/history-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

<div class="main-container">
    <div class="history-card">

        <div class="history-header">
            <div>
                <h1>Activity History</h1>
                <p>Your saved carbon footprint entries</p>
            </div>
            <div class="header-controls">
                <input type="date" id="date-filter" class="date-input" onchange="renderTable()">
                <button class="page-btn" id="btn-prev" onclick="changePage(-1)">◀</button>
                <button class="page-btn" id="btn-next" onclick="changePage(1)">▶</button>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-val" id="stat-entries">0</div>
                <div class="stat-label">Entries</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" id="stat-avg">--</div>
                <div class="stat-label">Avg / Week</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" id="stat-best">--</div>
                <div class="stat-label">Best Week 🏆</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" id="stat-worst">--</div>
                <div class="stat-label">Worst Week ⚠️</div>
            </div>
        </div>

        <div id="badges-section" style="display:none;">
            <h3 class="chart-title">🏅 Your Badges</h3>
            <div id="badges-container" class="badges-container"></div>
        </div>

        <div id="table-container">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>CO2 (kg)</th>
                        <th>Grade</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>
        <p class="showing-text" id="showing-text"></p>

        <div id="trend-section" style="display:none; margin-top:24px;">
            <h3 class="chart-title">📈 CO2 Trend Over Time</h3>
            <canvas id="trend-chart"></canvas>
        </div>

        <div class="action-row">
            <button class="export-btn" onclick="exportCSV()">📥 Export CSV</button>
            <button class="clear-btn" onclick="clearAllHistory()">🗑️ Clear All</button>
        </div>

        <div class="footer-nav">
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>

    </div>
</div>

<script src="scripts/accessibility.js"></script>
<script src="scripts/history-logic.js"></script>

</body>
</html>