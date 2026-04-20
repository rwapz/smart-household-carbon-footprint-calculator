<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User | Admin</title>
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
            <h1>Create User</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php
        if (isset($_POST['createuser'])) {
            $H_id = $_POST['HOUSEHOLD_ID'];
            $Uname = $_POST['USERNAME'];
            $Phash = password_hash($_POST['PASSWORD_HASH'], PASSWORD_DEFAULT);
            try {
                $stmt = $CONN->prepare("INSERT INTO USERS (HOUSEHOLD_ID, USERNAME, PASSWORD_HASH) VALUES (:H_id, :Uname, :Phash)");
                $stmt->execute([':H_id' => $H_id, ':Uname' => $Uname, ':Phash' => $Phash]);
                echo "<div class='alert alert-success'>User created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="HOUSEHOLD_ID">Household ID</label>
                    <input type="number" name="HOUSEHOLD_ID" placeholder="Enter household ID" required>
                </div>
                <div class="form-group">
                    <label for="USERNAME">Username</label>
                    <input type="text" name="USERNAME" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="PASSWORD_HASH">Password</label>
                    <input type="password" name="PASSWORD_HASH" placeholder="Enter password" required>
                </div>
                <button type="submit" name="createuser" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </main>
</body>
</html>
