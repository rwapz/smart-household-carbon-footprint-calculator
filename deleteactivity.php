<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deleteact"])) {
    $L_id = $_POST["LOG_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM ACTIVITY_LOG WHERE LOG_ID = :id");
        $stmt->execute([':id' => $L_id]);
        header("Location: viewactivity.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$L_id = $_GET["L_id"] ?? null;
if ($L_id === null) {
    die("Error: No log ID provided");
}

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
    <title>Delete Activity Log | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
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
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Delete Activity Log</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php if (!empty($error)) { echo "<div class='alert alert-error'>Error: " . htmlspecialchars($error) . "</div>"; } ?>
        <div class="form-card">
            <form method="post">
                <p class="confirm-text">Are you sure you want to delete this activity log?</p>
                <input type="hidden" name="LOG_ID" value="<?php echo htmlspecialchars($log['LOG_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>User ID:</strong> <?php echo htmlspecialchars($log['USER_ID']); ?></p>
                    <p><strong>Factor ID:</strong> <?php echo htmlspecialchars($log['FACTOR_ID']); ?></p>
                    <p><strong>Amount:</strong> <?php echo htmlspecialchars($log['AMOUNT']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($log['DATE_RECORDED']); ?></p>
                </div>
                <button type="submit" name="deleteact" class="btn btn-danger">Delete Activity Log</button>
            </form>
        </div>
    </main>
</body>
</html>
