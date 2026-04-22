<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Activity Log | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Create Activity Log</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php
        if (isset($_POST['createactivity'])) {
            $U_id = $_POST['USER_ID'];
            $F_id = $_POST['FACTOR_ID'];
            $Amot = $_POST['AMOUNT'];
            $Drec = $_POST['DATE_RECORDED'];
            try {
                $stmt = $CONN->prepare("INSERT INTO ACTIVITY_LOG (USER_ID, FACTOR_ID, AMOUNT, DATE_RECORDED) VALUES (:U_id, :F_id, :Amot, :Drec)");
                $stmt->execute([':U_id' => $U_id, ':F_id' => $F_id, ':Amot' => $Amot, ':Drec' => $Drec]);
                echo "<div class='alert alert-success'>Activity log created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="USER_ID">User ID</label>
                    <input type="number" name="USER_ID" placeholder="Enter user ID" required>
                </div>
                <div class="form-group">
                    <label for="FACTOR_ID">Factor ID</label>
                    <input type="number" name="FACTOR_ID" placeholder="Enter factor ID" required>
                </div>
                <div class="form-group">
                    <label for="AMOUNT">Amount</label>
                    <input type="number" step="0.01" name="AMOUNT" placeholder="Enter amount" required>
                </div>
                <div class="form-group">
                    <label for="DATE_RECORDED">Date Recorded</label>
                    <input type="date" name="DATE_RECORDED" required>
                </div>
                <button type="submit" name="createactivity" class="btn btn-primary">Create Activity Log</button>
            </form>
        </div>
    </main>
</body>
</html>
