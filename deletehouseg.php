<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deletegoal"])) {
    $G_id = $_POST["GOAL_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :id");
        $stmt->execute([':id' => $G_id]);
        header("Location: viewhouseg.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$G_id = $_GET["G_id"] ?? null;
if ($G_id === null) {
    die("Error: No goal ID provided");
}

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
    <title>Delete Household Goal | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Delete Household Goal</h1>
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
                <p class="confirm-text">Are you sure you want to delete this goal?</p>
                <input type="hidden" name="GOAL_ID" value="<?php echo htmlspecialchars($goal['GOAL_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>Household ID:</strong> <?php echo htmlspecialchars($goal['HOUSEHOLD_ID']); ?></p>
                    <p><strong>Target CO2:</strong> <?php echo htmlspecialchars($goal['TARGET_CO2_LIMIT']); ?> kg</p>
                    <p><strong>Target Month:</strong> <?php echo htmlspecialchars($goal['TARGET_MONTH']); ?></p>
                </div>
                <button type="submit" name="deletegoal" class="btn btn-danger">Delete Goal</button>
            </form>
        </div>
    </main>
</body>
</html>
