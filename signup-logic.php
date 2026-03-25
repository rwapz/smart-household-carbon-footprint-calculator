<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['USERNAME'] ?? '');
$password = trim($_POST['PASSWORD'] ?? '');

if (empty($username) || empty($password)) {
    header('Location: index.php?error=empty&tab=signup');
    exit;
}

if (strlen($password) < 6) {
    header('Location: index.php?error=shortpass&tab=signup');
    exit;
}

try {
    // Check if username is taken
    $check = $CONN->prepare("SELECT USER_ID FROM USERS WHERE USERNAME = :username LIMIT 1");
    $check->execute([':username' => $username]);

    if ($check->fetch()) {
        header('Location: index.php?error=taken&tab=signup');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Note: Inserting into HOUSEHOLD_ID 1 as a default for testing
    $stmt = $CONN->prepare("INSERT INTO USERS (HOUSEHOLD_ID, USERNAME, PASSWORD_HASH) VALUES (1, :username, :password)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashed
    ]);

    // Auto login
    $_SESSION['user_id'] = $CONN->lastInsertId();
    $_SESSION['username'] = $username;

    header('Location: dashboard.php');
    exit;

} catch (PDOException $e) {
    header('Location: index.php?error=db&tab=signup');
    exit;
}