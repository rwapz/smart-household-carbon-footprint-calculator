<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deleteuser"])) {
    $U_id = $_POST["USER_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM USERS WHERE USER_ID = :id");
        $stmt->execute([':id' => $U_id]);
        header("Location: viewuser.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$U_id = $_GET["U_id"] ?? null;
if ($U_id === null) {
    die("Error: No user ID provided");
}

try {
    $stmt = $CONN->prepare("SELECT * FROM USERS WHERE USER_ID = :id");
    $stmt->execute([':id' => $U_id]);
    $user = $stmt->fetch();
    if (!$user) {
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
    <title>Delete User | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Delete User</h1>
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
                <p class="confirm-text">Are you sure you want to delete this user?</p>
                <input type="hidden" name="USER_ID" value="<?php echo htmlspecialchars($user['USER_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['USERNAME']); ?></p>
                    <p><strong>Household ID:</strong> <?php echo htmlspecialchars($user['HOUSEHOLD_ID']); ?></p>
                </div>
                <button type="submit" name="deleteuser" class="btn btn-danger">Delete User</button>
            </form>
        </div>
    </main>
</body>
</html>
