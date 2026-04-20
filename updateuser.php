<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updateuser"])) {
    $U_id = $_POST["USER_ID"];
    $H_id = $_POST["HOUSEHOLD_ID"];
    $Uname = $_POST["USERNAME"];
    $Phash = password_hash($_POST["PASSWORD_HASH"], PASSWORD_DEFAULT);
    try {
        $stmt = $CONN->prepare("UPDATE USERS SET HOUSEHOLD_ID = :H_id, USERNAME = :Uname, PASSWORD_HASH = :Phash WHERE USER_ID = :id");
        $stmt->execute([':H_id' => $H_id, ':Uname' => $Uname, ':Phash' => $Phash, ':id' => $U_id]);
        header("Location: viewuser.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["U_id"]) || $_GET["U_id"] === "") {
    die("No user ID given");
}
$U_id = (int)$_GET["U_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM USERS WHERE USER_ID = :id");
    $stmt->execute([':id' => $U_id]);
    $us = $stmt->fetch();
    if (!$us) {
        die("User not found");
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
    <title>Update User | Admin</title>
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
            <h1>Update User</h1>
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
                <input type="hidden" name="USER_ID" value="<?php echo htmlspecialchars($us['USER_ID']); ?>">
                <div class="form-group">
                    <label for="HOUSEHOLD_ID">Household ID</label>
                    <input type="number" name="HOUSEHOLD_ID" value="<?php echo htmlspecialchars($us['HOUSEHOLD_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="USERNAME">Username</label>
                    <input type="text" name="USERNAME" value="<?php echo htmlspecialchars($us['USERNAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="PASSWORD_HASH">New Password (leave blank to keep current)</label>
                    <input type="password" name="PASSWORD_HASH" placeholder="Enter new password">
                </div>
                <button type="submit" name="updateuser" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </main>
</body>
</html>
