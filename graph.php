<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=unauthorized&tab=login');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get calculator results
$results = [];
$stmt = $CONN->prepare("SELECT * FROM calculator_results WHERE USER_ID = :uid ORDER BY CREATED_AT DESC");
$stmt->execute([':uid' => $user_id]);
$results = $stmt->fetchAll();

// Group by grade
$byGrade = [];
foreach ($results as $r) {
    $g = $r['GRADE'];
    if (!isset($byGrade[$g])) $byGrade[$g] = 0;
    $byGrade[$g]++;
}

// Group by date
$byDate = [];
foreach ($results as $r) {
    $d = $r['CREATED_AT'];
    $byDate[$d] = (float)$r['TOTAL_CO2'];
}
krsort($byDate);

// Chart data
$gradeLabels = json_encode(array_keys($byGrade));
$gradeData = json_encode(array_values($byGrade));
$gradeColors = json_encode(['A' => '#10b981', 'B' => '#2b8ad9', 'C' => '#f59e0b', 'D' => '#f97316', 'F' => '#ef4444']);

$dateLabels = json_encode(array_keys($byDate));
$dateData = json_encode(array_values($byDate));
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Graph | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/graph-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="history-header">
                <div>
                    <h1>📊 Your Results</h1>
                    <p>Calculator results over time</p>
                </div>
                <div class="header-actions">
                <button id="dark-btn" class="btn-create" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
                <a href="history.php" class="btn-create">← History</a>
                <a href="dashboard.php" class="btn-create" style="background: #64748b;">Dashboard</a>
            </div>
            </div>
            
            <div class="summary">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($results); ?></div>
                    <div class="stat-label">Total Tests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#10b981"><?php echo $byGrade['A'] ?? 0; ?></div>
                    <div class="stat-label">Grade A</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#ef4444"><?php echo ($byGrade['F'] ?? 0) + ($byGrade['D'] ?? 0); ?></div>
                    <div class="stat-label">Needs Work</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($results) > 0 ? round(array_sum(array_column($results, 'TOTAL_CO2')) / count($results), 1) : 0; ?></div>
                    <div class="stat-label">Avg CO2</div>
                </div>
            </div>
            
            <div class="chart-toggle">
                <button class="toggle-btn active" id="btn-bar" onclick="switchChart('bar')">📊 Bar Chart</button>
                <button class="toggle-btn" id="btn-pie" onclick="switchChart('pie')">🥧 Pie Chart</button>
            </div>
            
            <div class="chart-box">
                <h3 id="chart-title">CO2 Over Time</h3>
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

<script>
let chartType = 'bar';
let myChart = null;

function switchChart(type) {
    chartType = type;
    document.getElementById('btn-bar').classList.toggle('active', type === 'bar');
    document.getElementById('btn-pie').classList.toggle('active', type === 'pie');
    document.getElementById('chart-title').textContent = type === 'bar' ? 'CO2 Over Time' : 'Grades Distribution';
    renderChart();
}

function renderChart() {
    const dates = <?php echo $dateLabels; ?>;
    const co2s = <?php echo $dateData; ?>;
    const grades = <?php echo $gradeLabels; ?>;
    const gradeCounts = <?php echo $gradeData; ?>;
    const gradeColors = <?php echo $gradeColors; ?>;
    
    if (myChart) myChart.destroy();
    
    const ctx = document.getElementById('mainChart').getContext('2d');
    
    if (chartType === 'bar') {
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'CO2 (kg)',
                    data: co2s,
                    backgroundColor: '#2b8ad9',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    } else {
        myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: grades,
                datasets: [{
                    data: gradeCounts,
                    backgroundColor: grades.map(g => gradeColors[g] || '#64748b')
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

renderChart();
</script>
</body>
</html>