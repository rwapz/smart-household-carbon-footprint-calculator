<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updatehousehold"])) {
    $H_id = $_POST["HOUSEHOLD_ID"];
    $Hname = $_POST["HOUSEHOLD_NAME"];
    $post = $_POST["POSTCODE"];
    try {
        $stmt = $CONN->prepare("UPDATE HOUSEHOLD SET HOUSEHOLD_NAME = :Hname, POSTCODE = :post WHERE HOUSEHOLD_ID = :id");
        $stmt->execute([':Hname' => $Hname, ':post' => $post, ':id' => $H_id]);
        header("Location: viewhousehold.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["H_id"]) || $_GET["H_id"] === "") {
    die("No household ID given");
}
$H_id = (int)$_GET["H_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM HOUSEHOLD WHERE HOUSEHOLD_ID = :id");
    $stmt->execute([':id' => $H_id]);
    $hh = $stmt->fetch();
    if (!$hh) {
        die("Household not found");
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
    <title>Update Household | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update Household</h1>
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
                <input type="hidden" name="HOUSEHOLD_ID" value="<?php echo htmlspecialchars($hh['HOUSEHOLD_ID']); ?>">
                <div class="form-group">
                    <label for="HOUSEHOLD_NAME">Household Name</label>
                    <input type="text" name="HOUSEHOLD_NAME" value="<?php echo htmlspecialchars($hh['HOUSEHOLD_NAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="POSTCODE">Postcode</label>
                    <input type="text" name="POSTCODE" value="<?php echo htmlspecialchars($hh['POSTCODE']); ?>">
                </div>
                <button type="submit" name="updatehousehold" class="btn btn-primary">Update Household</button>
            </form>
        </div>
    </main>
</body>
</html>
