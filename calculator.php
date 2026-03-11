<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Calculator | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/calculator-style.css">
</head>
<body>

    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>Smart<br><span>Household</span></h2>
            </div>
            <nav class="sidebar-nav">
                <button onclick="window.location.href='dashboard.php'">Dashboard</button>
                <button class="active">Calculator</button>
                <button onclick="toggleView()" id="toggle-view-btn">🏆 Leaderboard</button>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php">Log Out</a>
            </div>
        </aside>

        <main class="content-area">
            <header class="content-header">
                <div class="header-info">
                    <h1>Carbon Footprint Tracker</h1>
                    <p id="area-status">Monitoring: <span id="current-area-display">Not Set</span></p>
                </div>
                <div class="header-actions">
                    <select id="user-area" onchange="updateAreaDisplay()">
                        <option value="">Select Sheffield Area...</option>
                        <option value="Sheffield">Sheffield</option>
                        <option value="Rotherham">Rotherham</option>
                        <option value="Barnsley">Barnsley</option>
                        <option value="Doncaster">Doncaster</option>
                        <option value="Chesterfield">Chesterfield</option>
                    </select>
                    <button class="sync-btn" onclick="sendToDatabaseTable()">Save & Sync ☁️</button>
                </div>
            </header>

            <section class="calculator-grid">
                <div id="calculator-view" class="card">
                    <h3>Usage Data</h3>
                    <div class="input-row">
                        <label>Electricity (kWh)</label>
                        <input type="number" id="input-elec" placeholder="0" oninput="calculateTotal()">
                    </div>
                    <div class="input-row">
                        <label>Gas (kWh)</label>
                        <input type="number" id="input-gas" placeholder="0" oninput="calculateTotal()">
                    </div>
                    <div class="input-row">
                        <label>Car Miles (Petrol)</label>
                        <input type="number" id="input-petrol" placeholder="0" oninput="calculateTotal()">
                    </div>
                </div>

                <div class="card result-card">
                    <h3>Impact Summary</h3>
                    <div class="total-display">
                        <span id="total-output">0</span>
                        <small>kg/CO2</small>
                    </div>
                    <div id="grade-badge" class="badge">Grade: --</div>
                    
                    <div class="breakdown-bar">
                        <div id="bar-elec" class="b-part" style="background:#10b981; width: 0%;"></div>
                        <div id="bar-gas" class="b-part" style="background:#f59e0b; width: 0%;"></div>
                        <div id="bar-petrol" class="b-part" style="background:#3b82f6; width: 0%;"></div>
                    </div>
                </div>

                <div id="leaderboard-view" class="card hidden">
                    <h3>Area Rankings</h3>
                    <div id="leaderboard-list">
                        </div>
                </div>
            </section>
        </main>
    </div>

    <script src="scripts/calculator-logic.js" defer></script>
</body>
</html>