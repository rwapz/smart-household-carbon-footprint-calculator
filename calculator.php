<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>EcoTracker Pro | Carbon Analytics</title>
    <link rel="stylesheet" href="stylesheets/calculator-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <div class="app-wrapper">
        <nav class="main-header">
            <div class="header-left">
                <button class="action-btn" onclick="location.reload()">🔄 Reset</button>
            </div>
            <div class="branding">
                <h2 class="main-logo">Eco<span>Tracker</span></h2>
            </div>
            <div class="header-right">
                <button class="action-btn" id="toggle-view-btn" onclick="toggleView()">🏆 Rankings</button>
                <button class="sync-btn" onclick="sendToDatabaseTable()">Sync Data ☁️</button>
            </div>
        </nav>

        <main class="content-grid">
            <section class="ui-panel">
                <div class="panel-container">
                    
                    <div class="input-section">
                        <label class="section-tag">Profile & Region</label>
                        <div class="card highlight">
                            <label for="user-area">Active Location</label>
                            <select id="user-area" onchange="handleAreaChange()">
                                <option value="">Select Region...</option>
                                <option value="Sheffield">Sheffield</option>
                                <option value="Chesterfield">Chesterfield</option>
                                <option value="Rotherham">Rotherham</option>
                                <option value="Barnsley">Barnsley</option>
                                <option value="Doncaster">Doncaster</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-section">
                        <div class="card">
                            <label>Household Type & Diet</label>
                            <div class="flex-row">
                                <select id="house-type" onchange="calculateTotal()">
                                    <option value="1.0">Flat</option>
                                    <option value="1.5">Semi-Detached</option>
                                    <option value="2.0">Detached</option>
                                </select>
                                <select id="diet-type" onchange="calculateTotal()">
                                    <option value="1.0">Omnivore</option>
                                    <option value="0.7">Flexitarian</option>
                                    <option value="0.3">Vegan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="input-section">
                        <label class="section-tag">Transport Configuration</label>
                        <div class="card">
                            <label>Vehicle Fleet</label>
                            <select id="vehicle-setup" onchange="toggleVehicleFields()">
                                <option value="none">No Private Vehicle</option>
                                <option value="petrol">Petrol / Diesel Only</option>
                                <option value="ev">Electric Only (EV)</option>
                                <option value="petrol-ev">Petrol + EV Hybrid Fleet</option>
                                <option value="petrol-diesel">Petrol + Diesel Fleet</option>
                            </select>
                            
                            <div id="dynamic-fields">
                                <div id="petrol-box" class="field-wrap hidden">
                                    <input type="number" id="input-petrol" placeholder="Petrol Weekly Miles" oninput="calculateTotal()">
                                </div>
                                <div id="diesel-box" class="field-wrap hidden">
                                    <input type="number" id="input-diesel" placeholder="Diesel Weekly Miles" oninput="calculateTotal()">
                                </div>
                                <div id="ev-box" class="field-wrap hidden">
                                    <input type="number" id="input-ev" placeholder="EV Weekly Miles" oninput="calculateTotal()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="input-section">
                        <label class="section-tag">Energy, Water & Waste</label>
                        <div class="grid-2">
                            <input type="number" id="input-elec" placeholder="Elec kWh" oninput="calculateTotal()">
                            <input type="number" id="input-gas" placeholder="Gas kWh" oninput="calculateTotal()">
                        </div>
                        <div class="grid-2" style="margin-top:8px;">
                            <input type="number" id="input-water" placeholder="Water m³" oninput="calculateTotal()">
                            <div class="range-inline">
                                <label>Waste: <span id="waste-display">1</span></label>
                                <input type="range" id="input-waste" min="0" max="15" value="1" oninput="calculateTotal()">
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <section class="ui-panel analytics-panel">
                <div id="calc-display" class="center-content">
                    <div class="output-container">
                        <p class="meta-text">Weekly CO2 Estimate</p>
                        <h1 id="total-output">0</h1>
                        <div id="grade-badge" class="status-badge">Grade: --</div>
                        <p id="cooked-msg" class="warning-alert"></p>
                    </div>

                    <div class="visual-data">
                        <div class="composite-bar">
                            <div id="bar-elec" class="bar-seg e"></div>
                            <div id="bar-gas" class="bar-seg g"></div>
                            <div id="bar-transport" class="bar-seg t"></div>
                            <div id="bar-water" class="bar-seg w"></div>
                        </div>
                        <div class="bar-legend">
                            <span><i class="dot e"></i> Elec</span>
                            <span><i class="dot g"></i> Gas</span>
                            <span><i class="dot t"></i> Transport</span>
                            <span><i class="dot w"></i> Water</span>
                        </div>
                    </div>
                </div>

                <div id="rankings-display" class="hidden full-height">
                    <h2 id="area-title">Regional Rankings</h2>
                    <div id="leaderboard-list" class="scroll-list"></div>
                </div>
            </section>
        </main>
    </div>

    <script src="scripts/calculator-logic.js"></script>
</body>
</html>