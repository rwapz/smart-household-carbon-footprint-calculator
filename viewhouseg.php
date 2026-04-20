<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Goals | Admin</title>
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
            <h1>Household Goals</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <div class="main">
        <form method="get" style="margin-bottom:20px;">
            <input type="text" name="search" placeholder="search by ID">
            <input type="submit" value="search">
        </form>
        <?php
        try {
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            $countSql = "SELECT COUNT(*) as cnt FROM HOUSEHOLD_GOALS";
            $countStmt = $CONN->prepare($countSql);
            $countStmt->execute();
            $countResult = $countStmt->fetch();
            echo "<p class='count'>Total Records " . $countResult['cnt'] . "</p>";

            if (!empty($search)) {
                $sql = "SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID LIKE :search ORDER BY GOAL_ID ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute([':search' => "%$search%"]);
            } else {
                $sql = "SELECT * FROM HOUSEHOLD_GOALS ORDER BY GOAL_ID ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute();
            }

            echo "<table>";
            echo "<thead><tr>";
            echo "<td>GOAL_ID</td>";
            echo "<td>HOUSEHOLD_ID</td>";
            echo "<td>TARGET_CO2_LIMIT</td>";
            echo "<td>TARGET_MONTH</td>";
            echo "<td style='text-align: center' colspan='2'>Action</td>";
            echo "</tr></thead>";

            while ($row = $stmt->fetch()) {
                $G_id = htmlspecialchars($row['GOAL_ID']);
                $H_id = htmlspecialchars($row['HOUSEHOLD_ID']);
                $co2 = htmlspecialchars($row['TARGET_CO2_LIMIT']);
                $Tmon = htmlspecialchars($row['TARGET_MONTH']);
                echo "<tbody><tr>";
                echo "<td>$G_id</td>";
                echo "<td>$H_id</td>";
                echo "<td>$co2</td>";
                echo "<td>$Tmon</td>";
                echo "<td><a href='updatehouseg.php?G_id=$G_id'>update</a></td>";
                echo "<td><a href='deletehouseg.php?G_id=$G_id'>delete</a></td>";
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
