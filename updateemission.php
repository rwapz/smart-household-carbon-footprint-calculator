<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updateemiss"])) {
    $F_id = $_POST["FACTOR_ID"];
    $C_id = $_POST["CATAGORY_ID"];
    $Aname = $_POST["ACTIVITY_NAME"];
    $co2 = $_POST["CO2_PER_UNIT"];
    try {
        $stmt = $CONN->prepare("UPDATE EMISSION_FACTORS SET CATAGORY_ID = :C_id, ACTIVITY_NAME = :Aname, CO2_PER_UNIT = :co2 WHERE FACTOR_ID = :id");
        $stmt->execute([':C_id' => $C_id, ':Aname' => $Aname, ':co2' => $co2, ':id' => $F_id]);
        header("Location: viewemission.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["F_id"]) || $_GET["F_id"] === "") {
    die("No factor ID given");
}
$F_id = (int)$_GET["F_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM EMISSION_FACTORS WHERE FACTOR_ID = :id");
    $stmt->execute([':id' => $F_id]);
    $ef = $stmt->fetch();
    if (!$ef) {
        die("Emission factor not found");
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
    <title>Update Emission Factor | Admin</title>
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
            <h1>Update Emission Factor</h1>
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
                <input type="hidden" name="FACTOR_ID" value="<?php echo htmlspecialchars($ef['FACTOR_ID']); ?>">
                <div class="form-group">
                    <label for="CATAGORY_ID">Category ID</label>
                    <input type="number" name="CATAGORY_ID" value="<?php echo htmlspecialchars($ef['CATAGORY_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ACTIVITY_NAME">Activity Name</label>
                    <input type="text" name="ACTIVITY_NAME" value="<?php echo htmlspecialchars($ef['ACTIVITY_NAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="CO2_PER_UNIT">CO2 Per Unit (kg)</label>
                    <input type="number" step="0.01" name="CO2_PER_UNIT" value="<?php echo htmlspecialchars($ef['CO2_PER_UNIT']); ?>" required>
                </div>
                <button type="submit" name="updateemiss" class="btn btn-primary">Update Emission Factor</button>
            </form>
        </div>
    </main>
</body>
</html>
