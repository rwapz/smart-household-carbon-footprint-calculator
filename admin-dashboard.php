<?php
require_once 'auth-admin.php';
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="header-right">
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <section class="stats-section">
            <h2>Database Overview</h2>
            <div class="stats-grid">
                <?php
                $tables = ['USERS', 'HOUSEHOLD', 'CATAGORIES', 'EMISSION_FACTORS', 'HOUSEHOLD_GOALS', 'ACTIVITY_LOG', 'USER_TYPES'];
                foreach ($tables as $table) {
                    try {
                        $stmt = $CONN->query("SELECT COUNT(*) as cnt FROM $table");
                        $result = $stmt->fetch();
                        $count = $result['cnt'];
                    } catch(PDOException $e) {
                        $count = 'N/A';
                    }
                    $label = str_replace('_', ' ', strtolower($table));
                    echo "<div class='stat-card'>";
                    echo "<span class='stat-value'>$count</span>";
                    echo "<span class='stat-label'>$label</span>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>

        <section class="admin-grid">
            <div class="admin-column">
                <div class="admin-card">
                    <h3>Users</h3>
                    <p>Manage user accounts</p>
                    <div class="card-actions">
                        <a href="viewuser.php" class="btn btn-view">View All</a>
                        <a href="createuser.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>Households</h3>
                    <p>Manage household records</p>
                    <div class="card-actions">
                        <a href="viewhousehold.php" class="btn btn-view">View All</a>
                        <a href="createhousehold.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>Household Goals</h3>
                    <p>Manage CO2 targets</p>
                    <div class="card-actions">
                        <a href="viewhouseg.php" class="btn btn-view">View All</a>
                        <a href="createhouseg.php" class="btn btn-create">Create</a>
                    </div>
                </div>
            </div>

            <div class="admin-column">
                <div class="admin-card">
                    <h3>Categories</h3>
                    <p>Manage emission categories</p>
                    <div class="card-actions">
                        <a href="viewcatagory.php" class="btn btn-view">View All</a>
                        <a href="createcatagory.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>Emission Factors</h3>
                    <p>Manage CO2 per activity</p>
                    <div class="card-actions">
                        <a href="viewemission.php" class="btn btn-view">View All</a>
                        <a href="createemission.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>Activity Log</h3>
                    <p>View user activities</p>
                    <div class="card-actions">
                        <a href="viewactivity.php" class="btn btn-view">View All</a>
                        <a href="createactivity.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>User Types</h3>
                    <p>Manage user roles</p>
                    <div class="card-actions">
                        <a href="viewusert.php" class="btn btn-view">View All</a>
                        <a href="createusert.php" class="btn btn-create">Create</a>
                    </div>
                </div>

                <div class="admin-card">
                    <h3>Admin Accounts</h3>
                    <p>Manage admin users</p>
                    <div class="card-actions">
                        <a href="admin-accounts.php" class="btn btn-create">Manage</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="scripts/accessibility.js"></script>
</body>
</html>
