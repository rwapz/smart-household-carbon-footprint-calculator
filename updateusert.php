<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updateusert"])) {
    $UT_id = $_POST["USER_TYPE_ID"];
    $U_id = $_POST["USER_ID"];
    $UTname = $_POST["USER_TYPE_NAME"];
    $des = $_POST["DESCRIPTION"];
    try {
        $stmt = $CONN->prepare("UPDATE USER_TYPES SET USER_ID = :U_id, USER_TYPE_NAME = :UTname, DESCRIPTION = :des WHERE USER_TYPE_ID = :id");
        $stmt->execute([':U_id' => $U_id, ':UTname' => $UTname, ':des' => $des, ':id' => $UT_id]);
        header("Location: viewusert.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["UT_id"]) || $_GET["UT_id"] === "") {
    die("No user type ID given");
}
$UT_id = (int)$_GET["UT_id"];

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
    <title>Update User Type | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update User Type</h1>
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
                <input type="hidden" name="USER_TYPE_ID" value="<?php echo htmlspecialchars($ut['USER_TYPE_ID']); ?>">
                <div class="form-group">
                    <label for="USER_ID">User ID</label>
                    <input type="number" name="USER_ID" value="<?php echo htmlspecialchars($ut['USER_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="USER_TYPE_NAME">User Type Name</label>
                    <input type="text" name="USER_TYPE_NAME" value="<?php echo htmlspecialchars($ut['USER_TYPE_NAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="DESCRIPTION">Description</label>
                    <input type="text" name="DESCRIPTION" value="<?php echo htmlspecialchars($ut['DESCRIPTION']); ?>">
                </div>
                <button type="submit" name="updateusert" class="btn btn-primary">Update User Type</button>
            </form>
        </div>
    </main>
</body>
</html>
