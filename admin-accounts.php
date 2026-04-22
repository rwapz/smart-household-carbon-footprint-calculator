<?php
/**
 * Admin Accounts - Manage admin users
 */

require_once 'auth-admin.php';
require_once 'connect.php';

$message = '';
$error = '';
$editing = null;

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        try {
            $check = $CONN->prepare("SELECT USER_ID FROM USERS WHERE USERNAME = :username");
            $check->execute([':username' => $username]);
            
            if ($check->rowCount() > 0) {
                $error = "Username already exists.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $CONN->prepare("INSERT INTO USERS (HOUSEHOLD_ID, USERNAME, PASSWORD_HASH) VALUES (1, :username, :password)");
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $passwordHash
                ]);
                
                $userId = $CONN->lastInsertId();
                
                $typeStmt = $CONN->prepare("INSERT INTO USER_TYPES (USER_ID, USER_TYPE_NAME, DESCRIPTION) VALUES (:uid, 'Admin', 'Administrator')");
                $typeStmt->execute([':uid' => $userId]);
                
                $message = "Admin user '$username' created successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $userId = (int)$_POST['user_id'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username)) {
        $error = "Username cannot be empty.";
    } else {
        try {
            $check = $CONN->prepare("SELECT USER_ID FROM USERS WHERE USERNAME = :username AND USER_ID != :id");
            $check->execute([':username' => $username, ':id' => $userId]);
            
            if ($check->rowCount() > 0) {
                $error = "Username already exists.";
            } else {
                if (!empty($password)) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $CONN->prepare("UPDATE USERS SET USERNAME = :username, PASSWORD_HASH = :password WHERE USER_ID = :id");
                    $stmt->execute([':username' => $username, ':password' => $passwordHash, ':id' => $userId]);
                } else {
                    $stmt = $CONN->prepare("UPDATE USERS SET USERNAME = :username WHERE USER_ID = :id");
                    $stmt->execute([':username' => $username, ':id' => $userId]);
                }
                $message = "Admin user updated successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    $userId = (int)$_POST['user_id'];
    
    try {
        $stmt = $CONN->prepare("DELETE FROM USER_TYPES WHERE USER_ID = :id");
        $stmt->execute([':id' => $userId]);
        
        $stmt = $CONN->prepare("DELETE FROM USERS WHERE USER_ID = :id");
        $stmt->execute([':id' => $userId]);
        
        $message = "Admin user deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Load user for editing
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $stmt = $CONN->prepare("SELECT * FROM USERS WHERE USER_ID = :id");
        $stmt->execute([':id' => $editId]);
        $editing = $stmt->fetch();
    } catch (PDOException $e) {}
}

try {
    $stmt = $CONN->query("SELECT u.USER_ID, u.USERNAME, ut.USER_TYPE_NAME FROM USERS u LEFT JOIN USER_TYPES ut ON u.USER_ID = ut.USER_ID WHERE ut.USER_TYPE_NAME = 'Admin'");
    $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $adminUsers = [];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <style>
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
            cursor: pointer;
            border: none;
            font-family: 'DM Sans', sans-serif;
        }
        .action-btn.edit {
            background: var(--green-light);
            color: white;
        }
        .action-btn.delete {
            background: #dc2626;
            color: white;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        .table-actions {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Admin Accounts</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>
    <main class="admin-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($editing): ?>
        <div class="form-card" style="margin-bottom: 32px;">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Edit Admin User</h2>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $editing['USER_ID']; ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($editing['USERNAME']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password (min 6 characters)">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" name="update_admin" class="btn btn-primary">Update Admin</button>
                    <a href="admin-accounts.php" class="btn" style="background: var(--surface-2); border: 1px solid var(--border); color: var(--text-primary);">Cancel</a>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="form-card" style="margin-bottom: 32px;">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Create New Admin Account</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password (min 6 characters)" required>
                </div>
                <button type="submit" name="create_admin" class="btn btn-primary">Create Admin</button>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="form-card">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Current Admin Users</h2>
            <?php if (count($adminUsers) > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="text-align: left; padding: 12px 0;">User ID</th>
                            <th style="text-align: left; padding: 12px 0;">Username</th>
                            <th style="text-align: left; padding: 12px 0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminUsers as $admin): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px 0;"><?php echo htmlspecialchars($admin['USER_ID']); ?></td>
                                <td style="padding: 12px 0;"><?php echo htmlspecialchars($admin['USERNAME']); ?></td>
                                <td style="padding: 12px 0;">
                                    <div class="table-actions">
                                        <a href="admin-accounts.php?edit=<?php echo $admin['USER_ID']; ?>" class="action-btn edit">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $admin['USER_ID']; ?>">
                                            <button type="submit" name="delete_admin" class="action-btn delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">No admin users found.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
