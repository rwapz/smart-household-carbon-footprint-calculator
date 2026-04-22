<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=unauthorized&tab=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Get emission factors for dropdown
$factors = [];
try {
    $stmt = $CONN->query("SELECT * FROM emission_factors ORDER BY ACTIVITY_NAME");
    $factors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $factor_id = $_POST['FACTOR_ID'] ?? '';
    $amount = $_POST['AMOUNT'] ?? '';
    $date = $_POST['DATE_RECORDED'] ?? date('Y-m-d');
    
    if (empty($factor_id) || empty($amount)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // Get CO2 per unit
            $stmt = $CONN->prepare("SELECT CO2_PER_UNIT FROM emission_factors WHERE FACTOR_ID = :id");
            $stmt->execute([':id' => $factor_id]);
            $factor = $stmt->fetch();
            $co2_per_unit = $factor['CO2_PER_UNIT'];
            $total_co2 = $amount * $co2_per_unit;
            
            $stmt = $CONN->prepare("INSERT INTO activity_log (USER_ID, FACTOR_ID, AMOUNT, DATE_RECORDED, TOTAL_CO2) VALUES (:uid, :fid, :amount, :date, :total)");
            $stmt->execute([
                ':uid' => $user_id,
                ':fid' => $factor_id,
                ':amount' => $amount,
                ':date' => $date,
                ':total' => $total_co2
            ]);
            
            $message = "Activity logged successfully! Total CO2: " . round($total_co2, 2) . " kg";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

$factor_id = $_POST['FACTOR_ID'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Activity | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
<script>
        const unitData = {};
        <?php foreach ($factors as $f): ?>
            unitData[<?php echo $f['FACTOR_ID']; ?>] = <?php echo json_encode($f['UNIT'] ?? 'kg'); ?>;
        <?php endforeach; ?>
        
        function calculateCO2() {
            const factorId = document.getElementById('FACTOR_ID').value;
            const amount = parseFloat(document.getElementById('AMOUNT').value) || 0;
            const unit = unitData[factorId] || 'kg';
            document.getElementById('unit-label').textContent = '(' + unit + ')';
        }
        
        document.getElementById('FACTOR_ID').addEventListener('change', calculateCO2);
    </script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Log Activity</h1>
        </div>
        <div class="header-right">
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="history.php" class="header-btn">History</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-card">
            <h2 style="margin-bottom: 20px;">Add New Activity</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="FACTOR_ID">Activity Type</label>
                    <select name="FACTOR_ID" id="FACTOR_ID" required>
                        <option value="">Select an activity...</option>
                        <?php foreach ($factors as $f): ?>
                            <option value="<?php echo $f['FACTOR_ID']; ?>">
                                <?php echo htmlspecialchars($f['ACTIVITY_NAME']); ?> (<?php echo $f['CO2_PER_UNIT']; ?> kg per unit)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="AMOUNT">Amount <span id="unit-label" style="font-weight:normal;color:var(--text-secondary);">(units)</span></label>
                    <input type="number" step="0.01" name="AMOUNT" id="AMOUNT" placeholder="e.g., 10" required oninput="calculateCO2()">
                    <small id="unit-hint" style="color:var(--text-secondary);font-size:0.8rem;"></small>
                </div>
                <div class="form-group">
                    <label for="DATE_RECORDED">Date</label>
                    <input type="date" name="DATE_RECORDED" id="DATE_RECORDED" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <p style="color: var(--text-secondary);">CO2 will be calculated automatically based on activity type.</p>
                </div>
                <button type="submit" class="btn btn-primary">Log Activity</button>
            </form>
        </div>
        
        <div class="form-card" style="margin-top: 24px;">
            <h3>Your Recent Activities</h3>
            <?php
            $stmt = $CONN->prepare("
                SELECT al.*, ef.ACTIVITY_NAME, ef.CO2_PER_UNIT 
                FROM activity_log al 
                JOIN emission_factors ef ON al.FACTOR_ID = ef.FACTOR_ID 
                WHERE al.USER_ID = :uid 
                ORDER BY al.DATE_RECORDED DESC LIMIT 10
            ");
            $stmt->execute([':uid' => $user_id]);
            $activities = $stmt->fetchAll();
            
            if (count($activities) > 0) {
                echo "<table style='width:100%; border-collapse:collapse;'>";
                echo "<tr style='border-bottom:1px solid var(--border);'><th style='text-align:left;padding:8px;'>Date</th><th style='text-align:left;padding:8px;'>Activity</th><th style='text-align:left;padding:8px;'>Amount</th><th style='text-align:left;padding:8px;'>CO2</th></tr>";
                foreach ($activities as $a) {
                    $co2 = $a['AMOUNT'] * $a['CO2_PER_UNIT'];
                    echo "<tr style='border-bottom:1px solid var(--border);'>";
                    echo "<td style='padding:8px;'>" . htmlspecialchars($a['DATE_RECORDED']) . "</td>";
                    echo "<td style='padding:8px;'>" . htmlspecialchars($a['ACTIVITY_NAME']) . "</td>";
                    echo "<td style='padding:8px;'>" . htmlspecialchars($a['AMOUNT']) . "</td>";
                    echo "<td style='padding:8px;'>" . round($co2, 2) . " kg</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No activities logged yet.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>