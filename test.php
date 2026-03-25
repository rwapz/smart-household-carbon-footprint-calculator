<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity History | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/test.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <!-- Stats -->
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

        <!-- Badges -->
        <div id="badges-section" style="display:none;">
            <h3 class="chart-title">🏅 Your Badges</h3>
            <div id="badges-container" class="badges-container"></div>
        </div>

        <!-- Table -->
        <div id="table-container"></div>
        <p class="showing-text" id="showing-text"></p>

        <!-- Trend chart -->
        <div id="trend-section" style="display:none; margin-top:24px;">
            <h3 class="chart-title">📈 CO2 Trend Over Time</h3>
            <canvas id="trend-chart"></canvas>
        </div>

        <!-- Actions -->
        <div class="action-row">
            <button class="export-btn" onclick="exportCSV()">📥 Export PDF</button>
            <button class="clear-btn" onclick="clearHistory()">🗑️ Clear All</button>
        </div>

        <div class="footer-nav">
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>

    </div>
</div>

<script src="scripts/history-test.js"></script>
</body>
</html>
