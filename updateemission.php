<?php
require_once 'auth-admin.php';
require_once 'connect.php';

if (!isset($_GET['F_id'])) {
    header('Location: viewemission.php');
    exit;
}

$factor_id = $_GET['F_id'];
$factor = null;

try {
    $stmt = $CONN->prepare("SELECT * FROM EMISSION_FACTORS WHERE FACTOR_ID = :id");
    $stmt->execute([':id' => $factor_id]);
    $factor = $stmt->fetch();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!$factor) {
    die("Emission factor not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = $_POST['CATAGORY_ID'];
    $name = $_POST['ACTIVITY_NAME'];
    $co2 = $_POST['CO2_PER_UNIT'];
    $unit = $_POST['UNIT'];
    
    try {
        $stmt = $CONN->prepare("UPDATE EMISSION_FACTORS SET CATAGORY_ID = :cat, ACTIVITY_NAME = :name, CO2_PER_UNIT = :co2, UNIT = :unit WHERE FACTOR_ID = :id");
        $stmt->execute([':cat' => $cat_id, ':name' => $name, ':co2' => $co2, ':unit' => $unit, ':id' => $factor_id]);
        header('Location: viewemission.php');
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
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
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update Emission Factor</h1>
        </div>
        <div class="header-right">
            <a href="viewemission.php" class="header-btn">← Back to Emission Factors</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="CATAGORY_ID">Category ID</label>
                    <input type="number" name="CATAGORY_ID" value="<?php echo htmlspecialchars($factor['CATAGORY_ID']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ACTIVITY_NAME">Activity Name</label>
                    <input type="text" name="ACTIVITY_NAME" value="<?php echo htmlspecialchars($factor['ACTIVITY_NAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="CO2_PER_UNIT">CO2 Per Unit (kg)</label>
                    <input type="number" step="0.01" name="CO2_PER_UNIT" value="<?php echo htmlspecialchars($factor['CO2_PER_UNIT']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="UNIT">Unit</label>
                    <select name="UNIT" required>
                        <option value="kg" <?php echo ($factor['UNIT'] ?? '') === 'kg' ? 'selected' : ''; ?>>kg</option>
                        <option value="L" <?php echo ($factor['UNIT'] ?? '') === 'L' ? 'selected' : ''; ?>>L</option>
                        <option value="kWh" <?php echo ($factor['UNIT'] ?? '') === 'kWh' ? 'selected' : ''; ?>>kWh</option>
                        <option value="km" <?php echo ($factor['UNIT'] ?? '') === 'km' ? 'selected' : ''; ?>>km</option>
                        <option value="m³" <?php echo ($factor['UNIT'] ?? '') === 'm³' ? 'selected' : ''; ?>>m³</option>
                        <option value="kg waste" <?php echo ($factor['UNIT'] ?? '') === 'kg waste' ? 'selected' : ''; ?>>kg waste</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Emission Factor</button>
            </form>
        </div>
    </main>
</body>
</html>