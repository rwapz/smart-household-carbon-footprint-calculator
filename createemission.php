<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Emission Factor | Admin</title>
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
            <h1>Create Emission Factor</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php
        if (isset($_POST['createemission'])) {
            $C_id = $_POST['CATAGORY_ID'];
            $Aname = $_POST['ACTIVITY_NAME'];
            $co2 = $_POST['CO2_PER_UNIT'];
            $unit = $_POST['UNIT'];
            try {
                $stmt = $CONN->prepare("INSERT INTO EMISSION_FACTORS (CATAGORY_ID, ACTIVITY_NAME, CO2_PER_UNIT, UNIT) VALUES (:C_id, :Aname, :co2, :unit)");
                $stmt->execute([':C_id' => $C_id, ':Aname' => $Aname, ':co2' => $co2, ':unit' => $unit]);
                echo "<div class='alert alert-success'>Emission factor created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="CATAGORY_ID">Category ID</label>
                    <input type="number" name="CATAGORY_ID" placeholder="Enter category ID" required>
                </div>
                <div class="form-group">
                    <label for="ACTIVITY_NAME">Activity Name</label>
                    <input type="text" name="ACTIVITY_NAME" placeholder="e.g., Driving Car" required>
                </div>
                <div class="form-group">
                    <label for="CO2_PER_UNIT">CO2 Per Unit (kg)</label>
                    <input type="number" step="0.01" name="CO2_PER_UNIT" placeholder="e.g., 0.21" required>
                </div>
                <div class="form-group">
                    <label for="UNIT">Unit</label>
                    <select name="UNIT" required>
                        <option value="kg">kg</option>
                        <option value="L">L (litres)</option>
                        <option value="kWh">kWh</option>
                        <option value="km">km</option>
                        <option value="m³">m³</option>
                        <option value="kg waste">kg waste</option>
                    </select>
                </div>
                <button type="submit" name="createemission" class="btn btn-primary">Create Emission Factor</button>
            </form>
        </div>
    </main>
</body>
</html>
