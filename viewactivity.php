<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log | Admin</title>
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
            <h1>Activity Log</h1>
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

            $countSql = "SELECT COUNT(*) as cnt FROM ACTIVITY_LOG";
            $countStmt = $CONN->prepare($countSql);
            $countStmt->execute();
            $countResult = $countStmt->fetch();
            echo "<p class='count'>Total Records " . $countResult['cnt'] . "</p>";

            if (!empty($search)) {
                $sql = "SELECT * FROM ACTIVITY_LOG WHERE LOG_ID LIKE :search ORDER BY LOG_ID ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute([':search' => "%$search%"]);
            } else {
                $sql = "SELECT * FROM ACTIVITY_LOG ORDER BY LOG_ID ASC";
                $stmt = $CONN->prepare($sql);
                $stmt->execute();
            }

            echo "<table>";
            echo "<thead><tr>";
            echo "<td>LOG_ID</td>";
            echo "<td>USER_ID</td>";
            echo "<td>FACTOR_ID</td>";
            echo "<td>AMOUNT</td>";
            echo "<td>DATE_RECORDED</td>";
            echo "<td style='text-align: center' colspan='2'>Action</td>";
            echo "</tr></thead>";

            while ($row = $stmt->fetch()) {
                $L_id = htmlspecialchars($row['LOG_ID']);
                $U_id = htmlspecialchars($row['USER_ID']);
                $F_id = htmlspecialchars($row['FACTOR_ID']);
                $Amot = htmlspecialchars($row['AMOUNT']);
                $Drec = htmlspecialchars($row['DATE_RECORDED']);
                echo "<tbody><tr>";
                echo "<td>$L_id</td>";
                echo "<td>$U_id</td>";
                echo "<td>$F_id</td>";
                echo "<td>$Amot</td>";
                echo "<td>$Drec</td>";
                echo "<td><a href='updateactivity.php?L_id=$L_id'>update</a></td>";
                echo "<td><a href='deleteactivity.php?L_id=$L_id'>delete</a></td>";
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
