<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Household | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Create Household</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php
        if (isset($_POST['createhousehold'])) {
            $Hname = $_POST['HOUSEHOLD_NAME'];
            $post = $_POST['POSTCODE'];
            try {
                $stmt = $CONN->prepare("INSERT INTO HOUSEHOLD (HOUSEHOLD_NAME, POSTCODE) VALUES (:Hname, :post)");
                $stmt->execute([':Hname' => $Hname, ':post' => $post]);
                echo "<div class='alert alert-success'>Household created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="HOUSEHOLD_NAME">Household Name</label>
                    <input type="text" name="HOUSEHOLD_NAME" placeholder="Enter household name" required>
                </div>
                <div class="form-group">
                    <label for="POSTCODE">Postcode</label>
                    <input type="text" name="POSTCODE" placeholder="Enter postcode">
                </div>
                <button type="submit" name="createhousehold" class="btn btn-primary">Create Household</button>
            </form>
        </div>
    </main>
</body>
</html>
