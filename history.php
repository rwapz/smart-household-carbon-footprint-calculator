<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=unauthorized&tab=login');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get calculator results
$calcResults = [];
$stmt = $CONN->prepare("SELECT * FROM calculator_results WHERE USER_ID = :uid ORDER BY CREATED_AT DESC");
$stmt->execute([':uid' => $user_id]);
$calcResults = $stmt->fetchAll();

// Handle delete single entry
if (isset($_GET['delete'])) {
    $stmt = $CONN->prepare("DELETE FROM activity_log WHERE LOG_ID = :id AND USER_ID = :uid");
    $stmt->execute([':id' => $_GET['delete'], ':uid' => $user_id]);
    header('Location: history.php');
    exit;
}

// Handle clear all
if (isset($_GET['clear'])) {
    $stmt = $CONN->prepare("DELETE FROM activity_log WHERE USER_ID = :uid");
    $stmt->execute([':uid' => $user_id]);
    header('Location: history.php');
    exit;
}

// Get all activities with category info
$activities = [];
$stmt = $CONN->prepare("
    SELECT al.*, ef.ACTIVITY_NAME, ef.CO2_PER_UNIT, ef.UNIT, c.CATAGORY_NAME 
    FROM activity_log al 
    JOIN emission_factors ef ON al.FACTOR_ID = ef.FACTOR_ID 
    LEFT JOIN catagories c ON ef.CATAGORY_ID = c.CATAGORY_ID
    WHERE al.USER_ID = :uid 
    ORDER BY al.DATE_RECORDED DESC, al.LOG_ID DESC
");
$stmt->execute([':uid' => $user_id]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by category
$byCategory = [];
$byDate = [];
$totalCO2 = 0;
$totalEntries = count($activities);

foreach ($activities as $a) {
    $cat = $a['CATAGORY_NAME'] ?? 'Other';
    $co2 = $a['AMOUNT'] * $a['CO2_PER_UNIT'];
    $totalCO2 += $co2;
    
    if (!isset($byCategory[$cat])) {
        $byCategory[$cat] = ['co2' => 0, 'count' => 0, 'activities' => []];
    }
    $byCategory[$cat]['co2'] += $co2;
    $byCategory[$cat]['count']++;
    $byCategory[$cat]['activities'][] = $a;
    
    $date = $a['DATE_RECORDED'];
    if (!isset($byDate[$date])) {
        $byDate[$date] = 0;
    }
    $byDate[$date] += $co2;
}

// Calculate stats
$avgPerDay = $totalEntries > 0 ? $totalCO2 / count($byDate) : 0;
$bestDay = count($byDate) > 0 ? min($byDate) : 0;
$worstDay = count($byDate) > 0 ? max($byDate) : 0;

// Get recent entries (last 10)
$recentActivities = array_slice($activities, 0, 10);

// Chart data
$chartLabels = json_encode(array_keys($byCategory));
$chartData = json_encode(array_map(fn($c) => round($c['co2'], 1), $byCategory));
$chartColors = json_encode(['#2b8ad9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4']);
?>
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
    <style>
        :root {
            --white: #ffffff;
            --slate: #f1f5f9;
            --border: #e2e8f0;
            --primary: #2b8ad9;
            --subtext: #64748b;
            --text: #0f172a;
        }
        [data-theme="dark"] {
            --white: #1e293b;
            --slate: #0f172a;
            --border: #334155;
            --subtext: #94a3b8;
            --text: #f1f5f9;
        }
        .history-container { max-width: 1100px; margin: 0 auto; padding: 24px 16px 60px; }
        .history-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 24px rgba(0,0,0,0.06); padding: 28px; }
        .history-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .history-header h1 { font-size: 1.6rem; font-weight: 900; margin: 0; color: var(--text); }
        .history-header p { color: var(--subtext); margin: 4px 0 0; font-size: 0.9rem; }
        .header-actions { display: flex; gap: 10px; align-items: center; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 28px; }
        .stat-card { background: var(--slate); border-radius: 12px; padding: 18px; text-align: center; border: 1px solid var(--border); transition: border-color 0.2s; }
        .stat-card:hover { border-color: var(--primary); }
        .stat-card .stat-value { font-size: 1.5rem; font-weight: 900; color: var(--primary); }
        .stat-card .stat-label { font-size: 0.7rem; font-weight: 600; color: var(--subtext); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
        
        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .chart-card { background: var(--slate); border-radius: 12px; padding: 20px; border: 1px solid var(--border); }
        .chart-card h3 { font-size: 0.8rem; font-weight: 700; color: var(--subtext); text-transform: uppercase; margin: 0 0 16px; }
        .chart-card canvas { max-height: 200px; }
        
        .section-title { font-size: 1rem; font-weight: 700; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; color: var(--text); }
        
        .category-list { display: grid; gap: 10px; }
        .category-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: var(--slate); border-radius: 10px; border: 1px solid var(--border); }
        .category-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .category-info { flex: 1; }
        .category-name { font-weight: 700; font-size: 0.95rem; color: var(--text); }
        .category-count { font-size: 0.8rem; color: var(--subtext); }
        .category-co2 { font-weight: 900; color: var(--primary); font-size: 1.1rem; }
        
        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th { text-align: left; padding: 10px 14px; font-size: 0.7rem; font-weight: 700; color: var(--subtext); text-transform: uppercase; border-bottom: 2px solid var(--border); }
        .activity-table td { padding: 12px 14px; font-size: 0.85rem; border-bottom: 1px solid var(--border); color: var(--text); }
        .activity-table tr:hover td { background: var(--slate); }
        .activity-table .activity-name { font-weight: 600; color: var(--text); }
        .activity-table .activity-meta { font-size: 0.75rem; color: var(--subtext); }
        
        .empty-state { text-align: center; padding: 50px 20px; color: var(--subtext); }
        .empty-state a { color: var(--primary); font-weight: 600; }
        
        .action-bar { display: flex; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); }
        .action-bar button { padding: 10px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: 1px solid var(--border); }
        .btn-print { background: var(--slate); color: var(--text); }
        .btn-clear { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
        [data-theme="dark"] .btn-clear { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
        [data-theme="dark"] .activity-table th { border-color: #334155; color: #94a3b8; }
        [data-theme="dark"] .activity-table td { border-color: #334155; color: #f1f5f9; }
        [data-theme="dark"] .grade-pill { color: white; }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-row { grid-template-columns: 1fr; }
            .history-header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
        }
    </style>
        
        .footer-link { display: inline-block; margin-top: 20px; color: var(--subtext); text-decoration: none; font-size: 0.85rem; }
        .footer-link:hover { color: var(--primary); }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-row { grid-template-columns: 1fr; }
            .history-header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>

<div class="history-container">
    <div class="history-card">

        <div class="history-header">
            <div>
                <h1>Activity History</h1>
                <p>Your carbon footprint over time</p>
            </div>
            <div class="header-actions">
                <a href="activity-log.php" class="btn-create">+ Add Activity</a>
                <a href="graph.php" class="btn-create" style="background: #10b981;">📊 Graph</a>
                <a href="dashboard.php" class="btn-create" style="background: #64748b;">Dashboard</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalEntries; ?></div>
                <div class="stat-label">Total Entries</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo round($totalCO2, 1); ?></div>
                <div class="stat-label">Total CO2 (kg)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($byDate) > 0 ? round($bestDay, 1) : '--'; ?></div>
                <div class="stat-label">Best Day</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($byDate) > 0 ? round($worstDay, 1) : '--'; ?></div>
                <div class="stat-label">Highest Day</div>
            </div>
        </div>

        <?php if ($byCategory): ?>
        <div class="charts-row">
            <div class="chart-card">
                <h3>CO2 by Category</h3>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>Category Breakdown</h3>
                <div class="category-list">
                    <?php 
                    $colors = ['#2b8ad9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'];
                    $icons = ['Transport' => '🚗', 'Energy' => '⚡', 'Food' => '🍔', 'Waste' => '🗑️', 'Water' => '💧'];
                    $i = 0;
                    foreach ($byCategory as $cat => $data): 
                        $color = $colors[$i % count($colors)];
                        $icon = $icons[$cat] ?? '📊';
                    ?>
                    <div class="category-item">
                        <div class="category-icon" style="background: <?php echo $color; ?>20; color: <?php echo $color; ?>;"><?php echo $icon; ?></div>
                        <div class="category-info">
                            <div class="category-name"><?php echo htmlspecialchars($cat); ?></div>
                            <div class="category-count"><?php echo $data['count']; ?> entries</div>
                        </div>
                        <div class="category-co2"><?php echo round($data['co2'], 1); ?> kg</div>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>

        <h3 class="section-title">📋 Recent Activities</h3>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>CO2</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivities as $a): 
                    $co2 = $a['AMOUNT'] * $a['CO2_PER_UNIT'];
                    $unit = $a['UNIT'] ?? 'kg';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($a['DATE_RECORDED']); ?></td>
                    <td class="activity-name"><?php echo htmlspecialchars($a['ACTIVITY_NAME']); ?></td>
                    <td class="activity-meta"><?php echo htmlspecialchars($a['CATAGORY_NAME'] ?? 'Other'); ?></td>
                    <td><?php echo $a['AMOUNT'] . ' ' . $unit; ?></td>
                    <td><strong><?php echo round($co2, 1); ?> kg</strong></td>
                    <td><a href="?delete=<?php echo $a['LOG_ID']; ?>" onclick="return confirm('Delete?')" style="color:#ef4444;text-decoration:none;">🗑️</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($calcResults): ?>
        <h3 class="section-title" style="margin-top:32px;">📊 Calculator Results</h3>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total CO2</th>
                    <th>Grade</th>
                    <th>Period</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calcResults as $r): 
                    $gradeColor = match($r['GRADE']) {
                        'A' => '#10b981',
                        'B' => '#2b8ad9',
                        'C' => '#f59e0b',
                        'D' => '#f97316',
                        default => '#ef4444'
                    };
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['CREATED_AT']); ?></td>
                    <td><strong><?php echo round($r['TOTAL_CO2'], 1); ?> kg</strong></td>
                    <td><span class="grade-pill" style="background:<?php echo $gradeColor; ?>"><?php echo $r['GRADE']; ?></span></td>
                    <td><?php echo htmlspecialchars($r['PERIOD']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="action-bar">
            <button class="btn-print" onclick="window.print()">🖨️ Print</button>
            <button class="btn-clear" onclick="if(confirm('Clear all history?')){window.location.href='?clear=1'}">🗑️ Clear All</button>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p style="font-size:1.1rem;margin-bottom:10px;">No activities logged yet.</p>
            <p>Start tracking your carbon footprint!</p>
            <a href="activity-log.php" class="btn-create" style="display:inline-block;margin-top:16px;">+ Add First Activity</a>
        </div>
        <?php endif; ?>

        <div style="margin-top:20px;display:flex;gap:12px;">
            <a href="dashboard.php" class="btn-create" style="background:#64748b;">← Dashboard</a>
            <a href="graph.php" class="btn-create" style="background:#10b981;">📊 View Graph</a>
        </div>

    </div>
</div>

<script src="scripts/accessibility.js"></script>
<?php if ($byCategory): ?>
<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                data: <?php echo $chartData; ?>,
                backgroundColor: <?php echo $chartColors; ?>,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right', labels: { font: { size: 11 }, padding: 12 } }
            }
        }
    });
</script>
<?php endif; ?>
</body>
</html>