<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php?error=unauthorized&tab=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'update_username':
            handleUpdateUsername();
            break;
        case 'update_password':
            handleUpdatePassword();
            break;
        case 'update_household':
            handleUpdateHousehold();
            break;
        case 'delete_account':
            handleDeleteAccount();
            break;
        case 'clear_history':
            handleClearHistory();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
    exit;
}

// ============ AJAX HANDLERS ============

function handleUpdateUsername() {
    global $CONN, $user_id;
    
    $newUsername = trim($_POST['new_username'] ?? '');
    
    if (strlen($newUsername) < 3) {
        echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
        return;
    }
    
    try {
        // Check if username already exists
        $checkStmt = $CONN->prepare("SELECT USER_ID FROM USERS WHERE USERNAME = :username AND USER_ID != :user_id");
        $checkStmt->execute([':username' => $newUsername, ':user_id' => $user_id]);
        
        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }
        
        // Update username
        $updateStmt = $CONN->prepare("UPDATE USERS SET USERNAME = :username WHERE USER_ID = :user_id");
        $updateStmt->execute([':username' => $newUsername, ':user_id' => $user_id]);
        
        $_SESSION['username'] = $newUsername;
        echo json_encode(['success' => true, 'message' => 'Username updated successfully']);
    } catch (PDOException $e) {
        error_log("Username update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleUpdatePassword() {
    global $CONN, $user_id;
    
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    if (empty($currentPassword)) {
        echo json_encode(['success' => false, 'message' => 'Current password is required']);
        return;
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
        return;
    }
    
    try {
        // Get current password hash
        $stmt = $CONN->prepare("SELECT PASSWORD_HASH FROM USERS WHERE USER_ID = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['PASSWORD_HASH'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            return;
        }
        
        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $CONN->prepare("UPDATE USERS SET PASSWORD_HASH = :password WHERE USER_ID = :user_id");
        $updateStmt->execute([':password' => $newHash, ':user_id' => $user_id]);
        
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } catch (PDOException $e) {
        error_log("Password update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleUpdateHousehold() {
    global $CONN, $user_id;
    
    $householdId = $_POST['household_id'] ?? '';
    
    if (empty($householdId)) {
        echo json_encode(['success' => false, 'message' => 'Please select a household']);
        return;
    }
    
    try {
        // Verify household exists
        $checkStmt = $CONN->prepare("SELECT HOUSEHOLD_ID FROM household WHERE HOUSEHOLD_ID = :id");
        $checkStmt->execute([':id' => $householdId]);
        
        if ($checkStmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'Household not found']);
            return;
        }
        
        // Update household
        $updateStmt = $CONN->prepare("UPDATE USERS SET HOUSEHOLD_ID = :household_id WHERE USER_ID = :user_id");
        $updateStmt->execute([':household_id' => $householdId, ':user_id' => $user_id]);
        
        echo json_encode(['success' => true, 'message' => 'Household updated successfully']);
    } catch (PDOException $e) {
        error_log("Household update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleDeleteAccount() {
    global $CONN, $user_id;
    
    $usernameConfirmation = $_POST['username_confirmation'] ?? '';
    
    try {
        // Get current username
        $stmt = $CONN->prepare("SELECT USERNAME FROM USERS WHERE USER_ID = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || $user['USERNAME'] !== $usernameConfirmation) {
            echo json_encode(['success' => false, 'message' => 'Username confirmation does not match']);
            return;
        }
        
        // Delete user - cascade should handle related records
        $deleteStmt = $CONN->prepare("DELETE FROM USERS WHERE USER_ID = :user_id");
        $deleteStmt->execute([':user_id' => $user_id]);
        
        // Clear session
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Account deleted']);
    } catch (PDOException $e) {
        error_log("Account deletion error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleClearHistory() {
    global $CONN, $user_id;
    
    try {
        // Delete all activity log entries for this user
        $deleteStmt = $CONN->prepare("DELETE FROM ACTIVITY_LOG WHERE USER_ID = :user_id");
        $deleteStmt->execute([':user_id' => $user_id]);
        
        echo json_encode(['success' => true, 'message' => 'History cleared']);
    } catch (PDOException $e) {
        error_log("History clear error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

// Fetch user's current household and all available households
$userHouseholdId = null;
$userHouseholdName = null;
$households = [];

try {
    // Get user's current household
    $userStmt = $CONN->prepare("SELECT HOUSEHOLD_ID FROM USERS WHERE USER_ID = :user_id");
    $userStmt->execute([':user_id' => $user_id]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    $userHouseholdId = $userData['HOUSEHOLD_ID'] ?? null;

    // Fetch all households
    $householdStmt = $CONN->prepare("SELECT HOUSEHOLD_ID, HOUSEHOLD_NAME FROM household ORDER BY HOUSEHOLD_NAME ASC");
    $householdStmt->execute();
    $households = $householdStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get current household name
    foreach ($households as $h) {
        if ($h['HOUSEHOLD_ID'] == $userHouseholdId) {
            $userHouseholdName = $h['HOUSEHOLD_NAME'];
            break;
        }
    }
} catch (PDOException $e) {
    error_log("Failed to fetch households: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Smart Household</title>
    <link rel="stylesheet" href="stylesheets/settings.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">

    <!-- Apply saved theme BEFORE paint to prevent flash -->
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>

<div class="settings-container">
    <!-- Header -->
    <header class="settings-header">
        <div class="header-content">
            <h1>Account Settings</h1>
            <p>Manage your account and preferences</p>
        </div>
        <div class="header-actions">
            <button class="action-btn dark-mode-btn" id="dark-btn" onclick="toggleDarkMode()">🌙 Dark</button>
            <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="settings-content">
        <!-- Account Info Card -->
        <section class="settings-section">
            <div class="section-header">
                <h2>👤 Account Information</h2>
                <p>Current account details</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Current Username</label>
                    <div class="info-value"><?php echo $username; ?></div>
                </div>
                <div class="info-item">
                    <label>User ID</label>
                    <div class="info-value">#<?php echo $user_id; ?></div>
                </div>
                <div class="info-item">
                    <label>Current Household</label>
                    <div class="info-value"><?php echo htmlspecialchars($userHouseholdName); ?></div>
                </div>
                <div class="info-item">
                    <label>Account Created</label>
                    <div class="info-value">April 15, 2026</div>
                </div>
            </div>
        </section>

        <!-- Change Username -->
        <section class="settings-section">
            <div class="section-header">
                <h2>✏️ Change Username</h2>
                <p>Update your login username</p>
            </div>

            <form id="change-username-form" class="settings-form">
                <div class="form-group">
                    <label for="new_username">New Username</label>
                    <input type="text" id="new_username" placeholder="Enter new username" required />
                    <small class="help-text">Username must be unique and at least 3 characters</small>
                </div>
                <button type="submit" class="btn btn-primary">Update Username</button>
            </form>
        </section>

        <!-- Change Password -->
        <section class="settings-section">
            <div class="section-header">
                <h2>🔐 Change Password</h2>
                <p>Update your account password</p>
            </div>

            <form id="change-password-form" class="settings-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="current_password" placeholder="Enter current password" required />
                        <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" placeholder="Enter new password" required />
                        <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                    </div>
                    <small class="help-text">Password must be at least 6 characters</small>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Strength: <strong>Weak</strong></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_new_password" placeholder="Confirm new password" required />
                        <button type="button" class="toggle-password" aria-label="Show password">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </section>

        <!-- Change Household -->
        <section class="settings-section">
            <div class="section-header">
                <h2>🏠 Change Household</h2>
                <p>Switch to a different household</p>
            </div>

            <form id="change-household-form" class="settings-form">
                <div class="form-group">
                    <label for="household_id">Select Household</label>
                    <select id="household_id" required>
                        <option value="">-- Choose a household --</option>
                        <?php foreach ($households as $h): ?>
                            <option value="<?php echo htmlspecialchars($h['HOUSEHOLD_ID']); ?>" 
                                    <?php echo ($h['HOUSEHOLD_ID'] == $userHouseholdId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($h['HOUSEHOLD_NAME']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-text">Select a different household to manage its carbon footprint</small>
                </div>

                <button type="submit" class="btn btn-primary">Switch Household</button>
            </form>
        </section>

        <!-- Danger Zone -->
        <section class="settings-section danger-zone">
            <div class="section-header">
                <h2>⚠️ Danger Zone</h2>
                <p>Irreversible actions</p>
            </div>

            <div class="danger-actions">
                <div class="danger-action">
                    <div>
                        <h3>Delete Account</h3>
                        <p>Permanently delete your account and all associated data</p>
                    </div>
                    <button type="button" id="delete-account-btn" class="btn btn-danger">Delete Account</button>
                </div>

                <div class="danger-action">
                    <div>
                        <h3>Clear All History</h3>
                        <p>Delete all your carbon footprint entries and activity history</p>
                    </div>
                    <button type="button" id="clear-history-btn" class="btn btn-danger">Clear History</button>
                </div>

                <div class="danger-action">
                    <div>
                        <h3>Logout</h3>
                        <p>Sign out of your account on this device</p>
                    </div>
                    <form method="POST" action="logout.php" style="margin: 0;">
                        <button type="submit" class="btn btn-warning">Logout</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal for Household Change Confirmation -->
    <div id="householdModal" class="modal">
        <div class="modal-content">
            <h2>Switch Household?</h2>
            <p>Are you sure you want to switch to a different household? Your carbon footprint tracking will be associated with the new household.</p>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeAllModals()">Cancel</button>
                <button class="btn btn-primary" id="confirm-household">Yes, Switch</button>
            </div>
        </div>
    </div>

    <!-- Modal for Account Deletion -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Delete Account?</h2>
            <p>This action cannot be undone. All your data will be permanently deleted.</p>
            <div class="form-group" style="margin-top: 20px;">
                <label for="delete-username">Type your username to confirm:</label>
                <input type="text" id="delete-username" placeholder="<?php echo $username; ?>" />
            </div>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeAllModals()">Cancel</button>
                <button class="btn btn-danger" id="confirm-delete">Delete Account</button>
            </div>
        </div>
    </div>

    <!-- Modal for History Clear -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <h2>Clear All History?</h2>
            <p>Are you sure you want to delete all your carbon footprint entries? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeAllModals()">Cancel</button>
                <button class="btn btn-danger" id="confirm-history">Clear All</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/accessibility.js"></script>
<script src="scripts/settings.js"></script>
</body>
</html>
