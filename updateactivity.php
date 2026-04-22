<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updateact"])) {
    $L_id = $_POST["LOG_ID"];
    $U_id = $_POST["USER_ID"];
    $F_id = $_POST["FACTOR_ID"];
    $Amot = $_POST["AMOUNT"];
    $Drec = $_POST["DATE_RECORDED"];
    try {
        $stmt = $CONN->prepare("UPDATE ACTIVITY_LOG SET USER_ID = :U_id, FACTOR_ID = :F_id, AMOUNT = :Amot, DATE_RECORDED = :Drec WHERE LOG_ID = :id");
        $stmt->execute([':U_id' => $U_id, ':F_id' => $F_id, ':Amot' => $Amot, ':Drec' => $Drec, ':id' => $L_id]);
        header("Location: viewactivity.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["L_id"]) || $_GET["L_id"] === "") {
    die("No log ID given");
}
$L_id = (int)$_GET["L_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM ACTIVITY_LOG WHERE LOG_ID = :id");
    $stmt->execute([':id' => $L_id]);
    $log = $stmt->fetch();
    if (!$log) {
        die("Activity log not found");
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
    <title>Update Activity Log | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update Activity Log</h1>
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
                <input type="hidden" name="LOG_ID" value="<?php echo htmlspecialchars($log['LOG_ID']); ?>">
                <div class="form-group">
                    <label for="USER_ID">User ID</label>
                    <input type="number" name="USER_ID" value="<?php echo htmlspecialchars($log['USER_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="FACTOR_ID">Factor ID</label>
                    <input type="number" name="FACTOR_ID" value="<?php echo htmlspecialchars($log['FACTOR_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="AMOUNT">Amount</label>
                    <input type="number" step="0.01" name="AMOUNT" value="<?php echo htmlspecialchars($log['AMOUNT']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="DATE_RECORDED">Date Recorded</label>
                    <input type="date" name="DATE_RECORDED" value="<?php echo htmlspecialchars($log['DATE_RECORDED']); ?>" required>
                </div>
                <button type="submit" name="updateact" class="btn btn-primary">Update Activity Log</button>
            </form>
        </div>
    </main>
</body>
</html>
