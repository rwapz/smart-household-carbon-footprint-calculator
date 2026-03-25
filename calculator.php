<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker Pro | Sheffield Hallam</title>
    <link rel="stylesheet" href="stylesheets/calculator-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /*
         * INLINE CRITICAL STYLES
         * These are here intentionally so they cannot be overridden
         * by any external stylesheet, browser extension, or dark-mode injection.
         */

        /* 1. APP SHELL */
        html, body { height: 100%; overflow: hidden; }
        .app-wrapper { display: flex; flex-direction: column; height: 100vh; }

        /* 2. TWO-COLUMN LAYOUT — the one rule that kept getting broken */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr;   /* left narrower, right wider = matches prototype */
            height: calc(100vh - 60px);
            overflow: hidden;
        }

        /* 3. LEFT PANEL */
        .content-grid > section:first-child {
            overflow-y: auto;
            height: 100%;
            padding: 24px 28px;
            border-right: 1px solid var(--border, #e2e8f0);
            background: var(--bg, #f8fafc);
        }

        /* 4. RIGHT PANEL */
        #output-side {
            overflow-y: auto;
            height: 100%;
            padding: 40px 32px;
            background: var(--bg, #f8fafc);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        #calc-display { width: 100%; text-align: center; }

        /* 5. SVG RING — fill:none hardcoded here, permanently */
        .progress-ring circle { fill: none; }
        .ring-bg   { fill: none; stroke: var(--ring-track, #e2e8f0); stroke-width: 8; }
        .ring-fill { fill: none; stroke: #10b981; stroke-width: 8; stroke-linecap: round;
                     transition: stroke-dashoffset 0.6s ease, stroke 0.3s; }

        /* 6. DARK MODE — only recolour, never change display/grid */
        [data-theme="dark"] .content-grid > section:first-child {
            background: var(--bg, #0f172a);
            border-right-color: var(--border, #334155);
        }
        [data-theme="dark"] #output-side { background: var(--right-bg, #0c1628); }
        [data-theme="dark"] .main-header { background: var(--card, #1e293b); border-bottom-color: var(--border, #334155); }
    </style>
</head>
<body>

<div class="app-wrapper">

    <header class="main-header">
        <div class="main-logo">ECO<span>TRACKER</span></div>
        <div class="header-right">
            <button class="action-btn" id="toggle-view-btn"  onclick="goToRankings()">🏆 Leaderboard</button>
            <button class="action-btn hidden" id="return-calc-btn" onclick="goToCalc()">↩ Calculator</button>
            <a href="dashboard.php" class="action-btn">🏠 Dashboard</a>
            <button class="action-btn" onclick="shareResult()">🔗 Share</button>
            <button class="action-btn reset-btn" onclick="resetAll()">↺ Reset</button>
            <button class="action-btn" id="dark-btn" onclick="toggleDarkMode()">🌙 Dark</button>
            <button class="sync-btn" onclick="sendToDatabaseTable()">Sync Data</button>
        </div>
    </header>

    <main class="content-grid" id="main-content">

        <!-- ════ LEFT: INPUTS ════ -->
        <section>

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

            <div class="input-section" style="margin-top:18px;">
                <span class="section-tag">Input Period</span>
                <div class="period-toggle">
                    <button class="period-btn active" id="btn-weekly"  onclick="setPeriod('weekly')">📅 Weekly</button>
                    <button class="period-btn"        id="btn-monthly" onclick="setPeriod('monthly')">🗓️ Monthly</button>
                </div>
                <p class="period-hint" id="period-hint">Enter your weekly usage figures below</p>
            </div>

            <div class="input-section" style="margin-top:18px;">
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
                    <div id="private-vehicle-fields" class="hidden" style="margin-top:12px;">
                        <div class="car-entry">
                            <div>
                                <label>Vehicle 1 Type</label>
                                <select class="car-type" onchange="calculateTotal()">
                                    <option value="petrol">Petrol</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="ev">Electric (EV)</option>
                                </select>
                            </div>
                            <div>
                                <label>Miles/week</label>
                                <input type="number" min="0" class="car-miles" placeholder="e.g. 80" oninput="calculateTotal()">
                            </div>
                        </div>
                        <div id="additional-cars"></div>
                        <div style="margin-top:8px;">
                            <label>More than one vehicle?</label>
                            <button type="button" class="mini-btn" onclick="addCarField()">+ Add Vehicle</button>
                        </div>
                    </div>
                    <div id="public-fields" class="hidden" style="margin-top:12px;">
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
                        <option value="0">Select diet...</option>
                        <option value="vegan">Vegan / Plant-based</option>
                        <option value="veggie">Vegetarian</option>
                        <option value="average">Average (some meat)</option>
                        <option value="meatheavy">Meat-heavy</option>
                    </select>
                    <label style="margin-top:10px;">Shopping Habits</label>
                    <select id="input-shopping" onchange="calculateTotal()">
                        <option value="0">Select shopping...</option>
                        <option value="minimal">Minimal (second-hand, rarely buy new)</option>
                        <option value="average">Average</option>
                        <option value="heavy">Heavy (frequent new clothes, tech, etc.)</option>
                    </select>
                    <label style="margin-top:10px;">Flights per Year</label>
                    <select id="input-flights" onchange="calculateTotal()">
                        <option value="none">None (0 flights)</option>
                        <option value="occasional">1–2 flights</option>
                        <option value="frequent">3+ flights</option>
                    </select>
                </div>
            </div>

            <div class="input-section">
                <span class="section-tag">Carbon Budget</span>
                <div class="card">
                    <label>Set Weekly Goal (kg CO2e)</label>
                    <input type="number" min="0" id="input-budget" placeholder="e.g. 120" oninput="updateBudget()">
                    <div id="budget-status" class="budget-status hidden"></div>
                </div>
            </div>

        </section>

        <!-- ════ RIGHT: OUTPUT ════ -->
        <section id="output-side">

            <div id="calc-display">

                <div id="mood-emoji" class="mood-emoji">🌍</div>
                <span class="meta-text" id="period-label">WEEKLY CO2 ESTIMATE</span>

                <div class="result-box">
                    <h1 id="total-output">0</h1>
                    <span class="unit-label">kg CO2e</span>
                </div>

                <div class="ring-wrapper">
                    <svg class="progress-ring" viewBox="0 0 120 120" width="120" height="120" xmlns="http://www.w3.org/2000/svg">
                        <circle class="ring-bg"   cx="60" cy="60" r="50"/>
                        <circle class="ring-fill" cx="60" cy="60" r="50"
                                id="ring-fill"
                                stroke-dasharray="314"
                                stroke-dashoffset="314"/>
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
                <div class="chart-toggle-row" id="chart-toggle-row">
                    <button class="period-btn active" id="btn-bar-chart" onclick="setChartType('bar')">📊 Bar</button>
                    <button class="period-btn"        id="btn-pie-chart" onclick="setChartType('pie')">🥧 Pie</button>
                </div>

                <!-- Bar breakdown — matches prototype dot legend -->
                <div class="visual-data" id="bar-breakdown">
                    <div class="bar-legend">
                        <span class="legend-dot e"></span><span>Electricity</span>
                        <span class="legend-dot g"></span><span>Gas</span>
                        <span class="legend-dot w"></span><span>Waste</span>
                        <span class="legend-dot t"></span><span>Transport</span>
                    </div>
                    <div class="composite-bar">
                        <div id="bar-elec"      class="bar-seg e" style="width:0%"></div>
                        <div id="bar-gas"       class="bar-seg g" style="width:0%"></div>
                        <div id="bar-water"     class="bar-seg w" style="width:0%"></div>
                        <div id="bar-transport" class="bar-seg t" style="width:0%"></div>
                    </div>
                </div>

                <!-- Pie chart -->
                <div id="pie-breakdown" class="hidden" style="width:200px;margin:8px auto;">
                    <canvas id="pie-chart"></canvas>
                </div>

                <div id="tips-panel" class="tips-panel hidden">
                    <span class="section-tag" style="margin-bottom:6px;">💡 Personalised Tips</span>
                    <div id="tips-list"></div>
                </div>

                <div id="history-row" class="hidden">
                    <button class="mini-btn" onclick="saveToHistory()" style="padding:7px 14px;">� Save Entry</button>
                    <span id="history-saved-msg" class="hidden" style="font-size:0.74rem;color:#10b981;margin-left:6px;">✅ Saved!</span>
                </div>

                <button id="download-report-btn" class="download-btn hidden" onclick="downloadData()">📥 Export Report</button>

            </div>

            <!-- Leaderboard -->
            <div id="rankings-display" class="hidden full-height">
                <h2 id="area-title">Rankings</h2>
                <div id="leaderboard-list"></div>
                <button class="action-btn" onclick="goToCalc()" style="margin-top:14px;">↩ Return to Calculator</button>
            </div>

        </section>

    </main>
</div>

<script src="scripts/accessibility.js" defer></script>
<script src="scripts/calculator-logic.js" defer></script>
</body>
</html>