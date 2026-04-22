<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Household Goal | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Create Household Goal</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php
        if (isset($_POST['createhouseholdg'])) {
            $H_id = $_POST['HOUSEHOLD_ID'];
            $co2 = $_POST['TARGET_CO2_LIMIT'];
            $Tmon = $_POST['TARGET_MONTH'];
            try {
                $stmt = $CONN->prepare("INSERT INTO HOUSEHOLD_GOALS (HOUSEHOLD_ID, TARGET_CO2_LIMIT, TARGET_MONTH) VALUES (:H_id, :co2, :Tmon)");
                $stmt->execute([':H_id' => $H_id, ':co2' => $co2, ':Tmon' => $Tmon]);
                echo "<div class='alert alert-success'>Goal created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="HOUSEHOLD_ID">Household ID</label>
                    <input type="number" name="HOUSEHOLD_ID" placeholder="Enter household ID" required>
                </div>
                <div class="form-group">
                    <label for="TARGET_CO2_LIMIT">Target CO2 Limit (kg)</label>
                    <input type="number" step="0.01" name="TARGET_CO2_LIMIT" placeholder="e.g., 30" required>
                </div>
                <div class="form-group">
                    <label for="TARGET_MONTH">Target Month</label>
                    <input type="text" name="TARGET_MONTH" placeholder="e.g., 2026-04" required>
                </div>
                <button type="submit" name="createhouseholdg" class="btn btn-primary">Create Goal</button>
            </form>
        </div>
    </main>
</body>
</html>
