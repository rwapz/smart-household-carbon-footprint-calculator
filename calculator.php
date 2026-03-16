<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>EcoTracker Pro | Sheffield Hallam</title>
    <link rel="stylesheet" href="stylesheets/calculator-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-wrapper">

<header class="main-header">
    <div class="main-logo">ECO<span>TRACKER</span></div>
    <div class="header-right">
        <button class="action-btn" id="toggle-view-btn" onclick="goToRankings()">🏆 Leaderboard</button>
        <button class="action-btn hidden" id="return-calc-btn" onclick="goToCalc()">↩ Calculator</button>
        <a href="dashboard.php" class="action-btn">🏠 Dashboard</a>
        <button class="action-btn" onclick="shareResult()">🔗 Share</button>
        <button class="action-btn reset-btn" onclick="resetAll()">↺ Reset</button>
        <button class="action-btn dark-btn" onclick="toggleDarkMode()" id="dark-btn">🌙 Dark</button>
        <button class="sync-btn" onclick="sendToDatabaseTable()">Sync Data</button>
    </div>
</header>

<main class="content-grid">

<!-- LEFT: INPUTS -->
<section class="ui-panel">

    <div class="input-section">
        <span class="section-tag">Location Settings</span>
        <select id="user-area" onchange="renderLeaderboard()">
            <option value="">Select Area...</option>
            <option value="Sheffield">Sheffield</option>
            <option value="Chesterfield">Chesterfield</option>
            <option value="Rotherham">Rotherham</option>
            <option value="Barnsley">Barnsley</option>
            <option value="Doncaster">Doncaster</option>
        </select>
    </div>

    <!-- Period toggle -->
    <div class="input-section">
        <span class="section-tag">Input Period</span>
        <div class="period-toggle">
            <button class="period-btn active" id="btn-weekly" onclick="setPeriod('weekly')">Weekly</button>
            <button class="period-btn" id="btn-monthly" onclick="setPeriod('monthly')">Monthly</button>
        </div>
        <p class="period-hint" id="period-hint">Enter your weekly usage figures below</p>
    </div>

    <div class="input-section">
        <span class="section-tag">Household Energy</span>
        <div class="card grid-2">
            <div>
                <label id="label-elec">Electricity (kWh/week)</label>
                <input type="number" min="0" id="input-elec" placeholder="e.g. 56" oninput="calculateTotal()">
            </div>
            <div>
                <label id="label-gas">Gas (kWh/week)</label>
                <input type="number" min="0" id="input-gas" placeholder="e.g. 175" oninput="calculateTotal()">
            </div>
        </div>
    </div>

    <div class="input-section">
        <span class="section-tag">Water Usage</span>
        <div class="card">
            <label id="label-water">Weekly Consumption (Litres)</label>
            <input type="number" min="0" id="input-water" placeholder="e.g. 500" oninput="calculateTotal()">
        </div>
    </div>

    <div class="input-section">
        <span class="section-tag">Transport — weekly miles</span>
        <div class="card">
            <label>Vehicle Setup</label>
            <select id="vehicle-setup" onchange="toggleVehicleFields()">
                <option value="none">No Vehicle</option>
                <option value="private">Private Vehicle</option>
                <option value="public">Public Transport</option>
            </select>
            <div id="private-vehicle-fields" class="hidden" style="margin-top:15px;">
                <div class="car-entry">
                    <label>Vehicle 1 Type</label>
                    <select class="car-type" onchange="calculateTotal()">
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="ev">Electric (EV)</option>
                    </select>
                    <input type="number" min="0" class="car-miles" placeholder="Miles per week" oninput="calculateTotal()">
                </div>
                <div id="additional-cars"></div>
                <div style="margin-top:10px;">
                    <label>More than one vehicle?</label>
                    <button type="button" class="mini-btn" onclick="addCarField()">+ Add Vehicle</button>
                </div>
            </div>
            <div id="public-fields" class="hidden" style="margin-top:15px;">
                <label>Bus/Train Miles per Week</label>
                <input type="number" min="0" id="input-public-miles" placeholder="e.g. 50" oninput="calculateTotal()">
            </div>
        </div>
    </div>

    <div class="input-section">
        <span class="section-tag">Waste Management — weekly</span>
        <div class="card">
            <label>Bags of Rubbish (Weekly)</label>
            <select id="input-waste" onchange="calculateTotal()">
                <option value="0">Select bags...</option>
                <option value="1">1 bag</option>
                <option value="2">2 bags</option>
                <option value="3">3 bags</option>
                <option value="4">4 bags</option>
                <option value="5">5 bags</option>
                <option value="7">6–7 bags</option>
                <option value="10">8–10 bags</option>
                <option value="14">10+ bags</option>
            </select>
        </div>
    </div>

    <div class="input-section">
        <span class="section-tag">Lifestyle</span>
        <div class="card">
            <label>Diet</label>
            <select id="input-diet" onchange="calculateTotal()">
                <option value="0" selected>Select diet...</option>
                <option value="vegan">Vegan / Plant-based</option>
                <option value="veggie">Vegetarian</option>
                <option value="average">Average (some meat)</option>
                <option value="meatheavy">Meat-heavy</option>
            </select>
            <label style="margin-top:12px;">Shopping Habits</label>
            <select id="input-shopping" onchange="calculateTotal()">
                <option value="0" selected>Select shopping...</option>
                <option value="minimal">Minimal (second-hand, rarely buy new)</option>
                <option value="average">Average</option>
                <option value="heavy">Heavy (frequent new clothes, tech, etc.)</option>
            </select>
            <label style="margin-top:12px;">Flights per Year</label>
            <select id="input-flights" onchange="calculateTotal()">
                <option value="none" selected>None (0 flights)</option>
                <option value="occasional">1–2 flights</option>
                <option value="frequent">3+ flights (frequent flyer)</option>
            </select>
        </div>
    </div>

    <!-- Carbon Budget -->
    <div class="input-section">
        <span class="section-tag">Carbon Budget</span>
        <div class="card">
            <label>Set Weekly Goal (kg CO2e)</label>
            <input type="number" min="0" id="input-budget" placeholder="e.g. 120" oninput="updateBudget()">
            <div id="budget-status" class="budget-status hidden"></div>
        </div>
    </div>

</section>

<!-- RIGHT: OUTPUT -->
<section class="ui-panel center-content" id="output-side">

    <div id="calc-display">

        <div id="mood-emoji" class="mood-emoji">🌍</div>

        <span class="meta-text" id="period-label">Weekly CO2 Estimate</span>

        <div class="result-box">
            <h1 id="total-output">0</h1>
            <span class="unit-label">kg CO2e</span>
        </div>

        <div class="ring-wrapper">
            <svg class="progress-ring" width="120" height="120" viewBox="0 0 120 120">
                <circle class="ring-bg" cx="60" cy="60" r="50"/>
                <circle id="ring-fill" class="ring-fill" cx="60" cy="60" r="50"
                    stroke-dasharray="314" stroke-dashoffset="314"/>
            </svg>
            <div id="grade-badge" class="grade-inside">--</div>
        </div>

        <div id="annual-projection" class="annual-text hidden">
            ≈ <span id="annual-output">0</span> tonnes CO2e per year
        </div>

        <div id="trees-section" class="trees-section hidden">
            <span id="trees-text"></span>
        </div>

        <div id="comparison-section" class="hidden">
            <div class="comparison-labels">
                <span>You</span>
                <span>UK Avg (170 kg)</span>
            </div>
            <div class="comparison-track">
                <div id="comparison-bar-you" class="comparison-bar-you"></div>
                <div class="comparison-marker"></div>
            </div>
            <div id="comparison-text" class="comparison-text"></div>
        </div>

        <!-- Chart toggle -->
        <div class="chart-toggle-row hidden" id="chart-toggle-row">
            <button class="period-btn active" id="btn-bar-chart" onclick="setChartType('bar')">Bar</button>
            <button class="period-btn" id="btn-pie-chart" onclick="setChartType('pie')">Pie</button>
        </div>

        <!-- Bar breakdown -->
        <div class="visual-data" id="bar-breakdown">
            <div class="bar-legend">
                <span class="legend-dot e"></span><span>Electricity</span>
                <span class="legend-dot g"></span><span>Gas</span>
                <span class="legend-dot w"></span><span>Waste</span>
                <span class="legend-dot t"></span><span>Transport</span>
            </div>
            <div class="composite-bar">
                <div id="bar-elec" class="bar-seg e"></div>
                <div id="bar-gas" class="bar-seg g"></div>
                <div id="bar-water" class="bar-seg w"></div>
                <div id="bar-transport" class="bar-seg t"></div>
            </div>
        </div>

        <!-- Pie chart -->
        <div id="pie-breakdown" class="hidden" style="width:220px;margin:10px auto;">
            <canvas id="pie-chart"></canvas>
        </div>

        <div id="tips-panel" class="tips-panel hidden">
            <span class="section-tag">💡 Personalised Tips</span>
            <div id="tips-list"></div>
        </div>

        <!-- Save to History -->
        <div id="history-row" class="hidden" style="margin-top:12px;">
            <button class="mini-btn" style="padding:8px 16px;font-size:0.8rem;" onclick="saveToHistory()">📅 Save to History</button>
            <span id="history-saved-msg" class="hidden" style="font-size:0.75rem;color:#10b981;margin-left:8px;">✅ Saved!</span>
        </div>

        <button id="download-report-btn" class="download-btn hidden" onclick="downloadData()">Download Report</button>

    </div>

    <!-- Rankings view -->
    <div id="rankings-display" class="hidden full-height">
        <h2 id="area-title">Rankings</h2>
        <div id="leaderboard-list"></div>
    </div>

</section>

</main>
</div>
<script src="scripts/calculator-logic.js" defer></script>
</body>
</html>