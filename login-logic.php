<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['USERNAME'] ?? '');
$password = trim($_POST['PASSWORD'] ?? '');
$remember_me = isset($_POST['REMEMBER_ME']) ? $_POST['REMEMBER_ME'] : '';

if (empty($username) || empty($password)) {
    header('Location: index.php?error=empty&tab=login');
    exit;
}

try {
    // Select the user by username
    $stmt = $CONN->prepare("SELECT * FROM USERS WHERE USERNAME = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password against the PASSWORD_HASH column
    if ($user && password_verify($password, $user['PASSWORD_HASH'])) {
        $_SESSION['user_id']  = $user['USER_ID']; 
        $_SESSION['username'] = $user['USERNAME'];
        
        // Handle "Remember Me" checkbox
        if (!empty($remember_me)) {
            // Set cookie to remember the username for 30 days
            setcookie('remembered_username', $username, time() + (30 * 24 * 60 * 60), '/');
        } else {
            // Clear the cookie if unchecked
            setcookie('remembered_username', '', time() - 3600, '/');
        }
        
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: index.php?error=invalid&tab=login');
        exit;
    }

} catch (PDOException $e) {
    header('Location: index.php?error=db&tab=login');
    exit;
}