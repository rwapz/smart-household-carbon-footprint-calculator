<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deleteusert"])) {
    $UT_id = $_POST["USER_TYPE_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM USER_TYPES WHERE USER_TYPE_ID = :id");
        $stmt->execute([':id' => $UT_id]);
        header("Location: viewusert.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$UT_id = $_GET["UT_id"] ?? null;
if ($UT_id === null) {
    die("Error: No user type ID provided");
}

try {
    $stmt = $CONN->prepare("SELECT * FROM USER_TYPES WHERE USER_TYPE_ID = :id");
    $stmt->execute([':id' => $UT_id]);
    $ut = $stmt->fetch();
    if (!$ut) {
        die("User type not found");
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
    <title>Delete User Type | Admin</title>
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
            <h1>Delete User Type</h1>
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
                <p class="confirm-text">Are you sure you want to delete this user type?</p>
                <input type="hidden" name="USER_TYPE_ID" value="<?php echo htmlspecialchars($ut['USER_TYPE_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>User Type:</strong> <?php echo htmlspecialchars($ut['USER_TYPE_NAME']); ?></p>
                    <p><strong>User ID:</strong> <?php echo htmlspecialchars($ut['USER_ID']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($ut['DESCRIPTION']); ?></p>
                </div>
                <button type="submit" name="deleteusert" class="btn btn-danger">Delete User Type</button>
            </form>
        </div>
    </main>
</body>
</html>
