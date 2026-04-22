<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updategoals"])) {
    $G_id = $_POST["GOAL_ID"];
    $H_id = $_POST["HOUSEHOLD_ID"];
    $co2 = $_POST["TARGET_CO2_LIMIT"];
    $Tmon = $_POST["TARGET_MONTH"];
    try {
        $stmt = $CONN->prepare("UPDATE HOUSEHOLD_GOALS SET HOUSEHOLD_ID = :H_id, TARGET_CO2_LIMIT = :co2, TARGET_MONTH = :Tmon WHERE GOAL_ID = :id");
        $stmt->execute([':H_id' => $H_id, ':co2' => $co2, ':Tmon' => $Tmon, ':id' => $G_id]);
        header("Location: viewhouseg.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["G_id"]) || $_GET["G_id"] === "") {
    die("No goal ID given");
}
$G_id = (int)$_GET["G_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :id");
    $stmt->execute([':id' => $G_id]);
    $goal = $stmt->fetch();
    if (!$goal) {
        die("Goal not found");
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Household Goal | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update Household Goal</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php if (!empty($error)) { echo "<div class='alert alert-error'>Error: " . htmlspecialchars($error) . "</div>"; } ?>
        <div class="form-card">
            <form method="post">
                <input type="hidden" name="GOAL_ID" value="<?php echo htmlspecialchars($goal['GOAL_ID']); ?>">
                <div class="form-group">
                    <label for="HOUSEHOLD_ID">Household ID</label>
                    <input type="number" name="HOUSEHOLD_ID" value="<?php echo htmlspecialchars($goal['HOUSEHOLD_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="TARGET_CO2_LIMIT">Target CO2 Limit (kg)</label>
                    <input type="number" step="0.01" name="TARGET_CO2_LIMIT" value="<?php echo htmlspecialchars($goal['TARGET_CO2_LIMIT']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="TARGET_MONTH">Target Month</label>
                    <input type="text" name="TARGET_MONTH" value="<?php echo htmlspecialchars($goal['TARGET_MONTH']); ?>" required>
                </div>
                <button type="submit" name="updategoals" class="btn btn-primary">Update Goal</button>
            </form>
        </div>
    </main>
</body>
</html>
