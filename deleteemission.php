<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deleteemiss"])) {
    $F_id = $_POST["FACTOR_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM EMISSION_FACTORS WHERE FACTOR_ID = :id");
        $stmt->execute([':id' => $F_id]);
        header("Location: viewemission.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$F_id = $_GET["F_id"] ?? null;
if ($F_id === null) {
    die("Error: No factor ID provided");
}

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
    <title>Delete Emission Factor | Admin</title>
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
            <h1>Delete Emission Factor</h1>
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
                <p class="confirm-text">Are you sure you want to delete this emission factor?</p>
                <input type="hidden" name="FACTOR_ID" value="<?php echo htmlspecialchars($ef['FACTOR_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>Activity:</strong> <?php echo htmlspecialchars($ef['ACTIVITY_NAME']); ?></p>
                    <p><strong>Category ID:</strong> <?php echo htmlspecialchars($ef['CATAGORY_ID']); ?></p>
                    <p><strong>CO2 Per Unit:</strong> <?php echo htmlspecialchars($ef['CO2_PER_UNIT']); ?> kg</p>
                </div>
                <button type="submit" name="deleteemiss" class="btn btn-danger">Delete Emission Factor</button>
            </form>
        </div>
    </main>
</body>
</html>
