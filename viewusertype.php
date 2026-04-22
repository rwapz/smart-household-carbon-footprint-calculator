<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Types | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>User Types</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>
    <main class="admin-container">
        <div class="form-card" style="padding: 0; overflow: hidden;">
            <?php
            try {
                $countSql = "SELECT COUNT(*) as cnt FROM USER_TYPES";
                $countStmt = $CONN->prepare($countSql);
                $countStmt->execute();
                $countResult = $countStmt->fetch();
                echo "<div style='padding: 16px 24px; border-bottom: 1px solid var(--border); background: var(--surface-2);'>
                    <p style='margin: 0; color: var(--text-secondary);'>Total User Types: <strong>" . $countResult['cnt'] . "</strong></p>
                </div>";

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if (!empty($search)) {
                    $sql = "SELECT ut.*, u.USERNAME FROM USER_TYPES ut 
                            LEFT JOIN USERS u ON ut.USER_ID = u.USER_ID 
                            WHERE ut.USER_TYPE_ID LIKE :search OR u.USERNAME LIKE :search2 
                            ORDER BY ut.USER_TYPE_ID ASC";
                    $stmt = $CONN->prepare($sql);
                    $stmt->execute([':search' => "%$search%", ':search2' => "%$search%"]);
                } else {
                    $sql = "SELECT ut.*, u.USERNAME FROM USER_TYPES ut 
                            LEFT JOIN USERS u ON ut.USER_ID = u.USER_ID 
                            ORDER BY ut.USER_TYPE_ID ASC";
                    $stmt = $CONN->prepare($sql);
                    $stmt->execute();
                }

                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<thead><tr style='border-bottom: 1px solid var(--border);'>
                    <th style='text-align: left; padding: 12px 24px;'>USER_TYPE_ID</th>
                    <th style='text-align: left; padding: 12px 24px;'>USERNAME</th>
                    <th style='text-align: left; padding: 12px 24px;'>USER_TYPE_NAME</th>
                    <th style='text-align: left; padding: 12px 24px;'>DESCRIPTION</th>
                    <th style='text-align: left; padding: 12px 24px;'>Actions</th>
                </tr></thead>";

                while ($row = $stmt->fetch()) {
                    $UT_id = htmlspecialchars($row['USER_TYPE_ID']);
                    $Uname = htmlspecialchars($row['USERNAME'] ?? 'Unknown');
                    $UTname = htmlspecialchars($row['USER_TYPE_NAME']);
                    $des = htmlspecialchars($row['DESCRIPTION']);
                    echo "<tbody><tr style='border-bottom: 1px solid var(--border);'>
                    <td style='padding: 12px 24px;'>$UT_id</td>
                    <td style='padding: 12px 24px;'>$Uname</td>
                    <td style='padding: 12px 24px;'>$UTname</td>
                    <td style='padding: 12px 24px;'>$des</td>
                    <td style='padding: 12px 24px;'>
                        <a href='updateusertype.php?UT_id=$UT_id' class='btn btn-view' style='display: inline-block; padding: 6px 12px; font-size: 0.8rem;'>Edit</a>
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