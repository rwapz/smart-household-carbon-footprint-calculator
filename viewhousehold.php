<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Records | Admin</title>
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <link rel="stylesheet" href="style.css">
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
            <h1>Household Records</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <div class="main">
        <form method="get" style="margin-bottom:20px;">
            <input type="text" name="search" placeholder="search by name or ID">
            <input type="submit" value="search">
        </form>
        <?php
        try {
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            $countSql = "SELECT COUNT(*) as cnt FROM HOUSEHOLD";
            $countStmt = $CONN->prepare($countSql);
            $countStmt->execute();
            $countResult = $countStmt->fetch();
            echo "<p class='count'>Total Records " . $countResult['cnt'] . "</p>";

            if (!empty($search)) {
                $sql = "SELECT * FROM HOUSEHOLD WHERE HOUSEHOLD_NAME LIKE :search OR HOUSEHOLD_ID LIKE :search2 ORDER BY HOUSEHOLD_NAME ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute([':search' => "%$search%", ':search2' => "%$search%"]);
            } else {
                $sql = "SELECT * FROM HOUSEHOLD ORDER BY HOUSEHOLD_NAME ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute();
            }

            echo "<table>";
            echo "<thead><tr>";
            echo "<td>HOUSEHOLD_ID</td>";
            echo "<td>HOUSEHOLD_NAME</td>";
            echo "<td>POSTCODE</td>";
            echo "<td style='text-align: center' colspan='2'>Action</td>";
            echo "</tr></thead>";

            while ($row = $stmt->fetch()) {
                $H_id = htmlspecialchars($row['HOUSEHOLD_ID']);
                $Hname = htmlspecialchars($row['HOUSEHOLD_NAME']);
                $post = htmlspecialchars($row['POSTCODE']);
                echo "<tbody><tr>";
                echo "<td>$H_id</td>";
                echo "<td>$Hname</td>";
                echo "<td>$post</td>";
                echo "<td><a href='updatehousehold.php?H_id=$H_id'>update</a></td>";
                echo "<td><a href='deletehousehold.php?H_id=$H_id'>delete</a></td>";
                echo "</tr></tbody>";
            }
            echo "</table>";
        } catch(PDOException $e) {
            echo "<p class='count'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
</body>
</html>
