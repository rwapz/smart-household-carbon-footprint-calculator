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

// Group by date (last 30 days)
$byDate = [];
foreach ($results as $r) {
    $d = $r['CREATED_AT'];
    if (!isset($byDate[$d])) $byDate[$d] = 0;
    $byDate[$d] = $r['TOTAL_CO2'];
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const theme = localStorage.getItem('eco-theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --sub: #64748b;
            --border: #e2e8f0;
            --primary: #2b8ad9;
        }
        [data-theme="dark"] {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f1f5f9;
            --sub: #94a3b8;
            --border: #334155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); padding: 24px 16px 60px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 16px; border: 1px solid var(--border); padding: 28px; margin-bottom: 24px; }
        h1 { font-size: 1.6rem; font-weight: 900; margin-bottom: 8px; }
        .nav { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .nav a { padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .nav .btn { background: var(--primary); color: white; }
        .nav .btn-sec { background: var(--sub); color: white; }
        
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .charts-grid { grid-template-columns: 1fr; } }
        
        .chart-box { background: var(--bg); border-radius: 12px; padding: 20px; border: 1px solid var(--border); }
        .chart-box h3 { font-size: 0.85rem; font-weight: 700; color: var(--sub); text-transform: uppercase; margin-bottom: 16px; }
        .chart-box canvas { max-height: 280px; }
        
        .toggle-row { display: flex; gap: 10px; margin-bottom: 20px; }
        .toggle-btn { padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border); background: var(--card); color: var(--text); cursor: pointer; font-weight: 600; }
        .toggle-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .summary-card { background: var(--bg); padding: 18px; border-radius: 12px; text-align: center; border: 1px solid var(--border); }
        .summary-card .val { font-size: 1.4rem; font-weight: 900; color: var(--primary); }
        .summary-card .lbl { font-size: 0.7rem; color: var(--sub); text-transform: uppercase; margin-top: 4px; }
        
        @media (max-width: 600px) { .summary { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>📊 Your Results</h1>
            <p style="color:var(--sub);margin-bottom:20px;">Calculator results over time</p>
            
            <div class="nav">
                <a href="history.php" class="btn">← History</a>
                <a href="dashboard.php" class="btn btn-sec">Dashboard</a>
            </div>
            
            <div class="summary">
                <div class="summary-card">
                    <div class="val"><?php echo count($results); ?></div>
                    <div class="lbl">Total Tests</div>
                </div>
                <div class="summary-card">
                    <div class="val"><?php echo $byGrade['A'] ?? 0; ?></div>
                    <div class="lbl">Grade A</div>
                </div>
                <div class="summary-card">
                    <div class="val"><?php echo ($byGrade['F'] ?? 0) + ($byGrade['D'] ?? 0); ?></div>
                    <div class="lbl">Needs Work</div>
                </div>
                <div class="summary-card">
                    <div class="val"><?php echo count($results) > 0 ? round(array_sum(array_column($results, 'TOTAL_CO2')) / count($results), 1) : 0; ?></div>
                    <div class="lbl">Avg CO2</div>
                </div>
            </div>
            
            <div class="toggle-row">
                <button class="toggle-btn active" onclick="showChart('bar')">📊 Bar Chart</button>
                <button class="toggle-btn" onclick="showChart('pie')">🥧 Pie Chart</button>
            </div>
            
            <div class="charts-grid" id="charts">
                <div class="chart-box">
                    <h3>CO2 Over Time</h3>
                    <canvas id="barChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3>Grades Distribution</h3>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

<script>
let currentChart = 'bar';

function showChart(type) {
    currentChart = type;
    document.querySelectorAll('.toggle-btn').forEach((b, i) => {
        b.classList.toggle('active', i === 0 ? type === 'bar' : type === 'pie');
    });
    renderCharts();
}

function renderCharts() {
    const dates = <?php echo $dateLabels; ?>;
    const co2s = <?php echo $dateData; ?>;
    const grades = <?php echo $gradeLabels; ?>;
    const gradeCounts = <?php echo $gradeData; ?>;
    const gradeColors = <?php echo $gradeColors; ?>;
    
    // Bar chart
    new Chart(document.getElementById('barChart'), {
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
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Pie chart
    new Chart(document.getElementById('pieChart'), {
        type: currentChart === 'pie' ? 'pie' : 'doughnut',
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

renderCharts();
</script>
</body>
</html>