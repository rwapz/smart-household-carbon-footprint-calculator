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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap">
    <link rel="stylesheet" href="stylesheets/history-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
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
                <button id="dark-btn" class="btn-create" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
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
            <div class="stat-card" title="Best day: <?php echo array_keys($byDate, $bestDay)[0] ?? ''; ?>">
                <div class="stat-value"><?php echo count($byDate) > 0 ? round($bestDay, 1) : '--'; ?></div>
                <div class="stat-label">Best Day ↗️</div>
            </div>
            <div class="stat-card" title="Highest day: <?php echo array_keys($byDate, $worstDay)[0] ?? ''; ?>">
                <div class="stat-value"><?php echo count($byDate) > 0 ? round($worstDay, 1) : '--'; ?></div>
                <div class="stat-label">Highest Day ⚠️</div>
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
        <h3 class="section-title">📊 Calculator Results</h3>
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

        <div style="margin-top:20px;display:flex;gap:12px;">
            <a href="dashboard.php" class="btn-create" style="background:#64748b;">← Dashboard</a>
            <a href="graph.php" class="btn-create" style="background:#10b981;">📊 View Graph</a>
        </div>

    </div>
</div>

<script src="scripts/accessibility.js"></script>
</body>
</html>