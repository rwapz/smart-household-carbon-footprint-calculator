<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deletehousehold"])) {
    $H_id = $_POST["HOUSEHOLD_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM HOUSEHOLD WHERE HOUSEHOLD_ID = :id");
        $stmt->execute([':id' => $H_id]);
        header("Location: viewhousehold.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$H_id = $_GET["H_id"] ?? null;
if ($H_id === null) {
    die("Error: No household ID provided");
}

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
    <title>Delete Household | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Delete Household</h1>
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
                <p class="confirm-text">Are you sure you want to delete this household?</p>
                <input type="hidden" name="HOUSEHOLD_ID" value="<?php echo htmlspecialchars($hh['HOUSEHOLD_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($hh['HOUSEHOLD_NAME']); ?></p>
                    <p><strong>Postcode:</strong> <?php echo htmlspecialchars($hh['POSTCODE']); ?></p>
                </div>
                <button type="submit" name="deletehousehold" class="btn btn-danger">Delete Household</button>
            </form>
        </div>
    </main>
</body>
</html>
