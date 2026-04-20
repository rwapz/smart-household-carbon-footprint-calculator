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
            <h1>Household Goals</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <div class="form-card" style="padding: 0; overflow: hidden;">
            <?php
            try {
                $countSql = "SELECT COUNT(*) as cnt FROM HOUSEHOLD_GOALS";
                $countStmt = $CONN->prepare($countSql);
                $countStmt->execute();
                $countResult = $countStmt->fetch();
                echo "<div style='padding: 16px 24px; border-bottom: 1px solid var(--border); background: var(--surface-2);'>
                    <p style='margin: 0; color: var(--text-secondary);'>Total Goals: <strong>" . $countResult['cnt'] . "</strong></p>
                </div>";

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $sql = "SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID LIKE :search ORDER BY GOAL_ID ASC";
                    $stmt = $CONN->prepare($sql);
                    $stmt->execute([':search' => "%$search%"]);
                } else {
                    $sql = "SELECT * FROM HOUSEHOLD_GOALS ORDER BY GOAL_ID ASC";
                    $stmt = $CONN->prepare($sql);
                    $stmt->execute();
                }

                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<thead><tr style='border-bottom: 1px solid var(--border);'>
                    <th style='text-align: left; padding: 12px 24px;'>GOAL_ID</th>
                    <th style='text-align: left; padding: 12px 24px;'>HOUSEHOLD_ID</th>
                    <th style='text-align: left; padding: 12px 24px;'>TARGET_CO2_LIMIT</th>
                    <th style='text-align: left; padding: 12px 24px;'>TARGET_MONTH</th>
                    <th style='text-align: left; padding: 12px 24px;'>Actions</th>
                </tr></thead>";

                while ($row = $stmt->fetch()) {
                    $G_id = htmlspecialchars($row['GOAL_ID']);
                    $H_id = htmlspecialchars($row['HOUSEHOLD_ID']);
                    $co2 = htmlspecialchars($row['TARGET_CO2_LIMIT']);
                    $Tmon = htmlspecialchars($row['TARGET_MONTH']);
                    echo "<tbody><tr style='border-bottom: 1px solid var(--border);'>
                    <td style='padding: 12px 24px;'>$G_id</td>
                    <td style='padding: 12px 24px;'>$H_id</td>
                    <td style='padding: 12px 24px;'>$co2</td>
                    <td style='padding: 12px 24px;'>$Tmon</td>
                    <td style='padding: 12px 24px;'>
                        <a href='updatehouseg.php?G_id=$G_id' class='btn btn-view' style='display: inline-block; padding: 6px 12px; font-size: 0.8rem;'>Edit</a>
                    </td>
                </tr></tbody>";
                }
                echo "</table>";
            } catch(PDOException $e) {
                echo "<div class='alert alert-error' style='margin: 20px;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>