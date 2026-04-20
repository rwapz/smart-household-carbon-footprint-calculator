<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Type | Admin</title>
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
            <h1>Create User Type</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php
        if (isset($_POST['createusert'])) {
            $U_id = $_POST['USER_ID'];
            $UTname = $_POST['USER_TYPE_NAME'];
            $des = $_POST['DESCRIPTION'];
            try {
                $stmt = $CONN->prepare("INSERT INTO USER_TYPES (USER_ID, USER_TYPE_NAME, DESCRIPTION) VALUES (:U_id, :UTname, :des)");
                $stmt->execute([':U_id' => $U_id, ':UTname' => $UTname, ':des' => $des]);
                echo "<div class='alert alert-success'>User type created successfully!</div>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="USER_ID">User ID</label>
                    <input type="number" name="USER_ID" placeholder="Enter user ID" required>
                </div>
                <div class="form-group">
                    <label for="USER_TYPE_NAME">User Type Name</label>
                    <input type="text" name="USER_TYPE_NAME" placeholder="e.g., Admin, Member" required>
                </div>
                <div class="form-group">
                    <label for="DESCRIPTION">Description</label>
                    <input type="text" name="DESCRIPTION" placeholder="Enter description">
                </div>
                <button type="submit" name="createusert" class="btn btn-primary">Create User Type</button>
            </form>
        </div>
    </main>
</body>
</html>
