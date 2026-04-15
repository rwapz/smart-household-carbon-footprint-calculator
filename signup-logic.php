<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['USERNAME'] ?? '');
$password = trim($_POST['PASSWORD'] ?? '');
$password_confirm = trim($_POST['PASSWORD_CONFIRM'] ?? '');
$household_id = intval($_POST['HOUSEHOLD_ID'] ?? 0);
$terms_agreed = isset($_POST['TERMS_AGREED']) ? $_POST['TERMS_AGREED'] : '';

if (empty($username) || empty($password) || empty($password_confirm)) {
    header('Location: index.php?error=empty&tab=signup');
    exit;
}

if ($password !== $password_confirm) {
    header('Location: index.php?error=password_mismatch&tab=signup');
    exit;
}

if (strlen($password) < 6) {
    header('Location: index.php?error=shortpass&tab=signup');
    exit;
}

if (empty($terms_agreed)) {
    header('Location: index.php?error=terms_required&tab=signup');
    exit;
}

if ($household_id <= 0) {
    header('Location: index.php?error=household&tab=signup');
    exit;
}

try {
    // Validate that the household exists
    $householdCheck = $CONN->prepare("SELECT HOUSEHOLD_ID FROM household WHERE HOUSEHOLD_ID = :household_id LIMIT 1");
    $householdCheck->execute([':household_id' => $household_id]);

    if (!$householdCheck->fetch()) {
        header('Location: index.php?error=household&tab=signup');
        exit;
    }

    // Check if username is taken
    $check = $CONN->prepare("SELECT USER_ID FROM USERS WHERE USERNAME = :username LIMIT 1");
    $check->execute([':username' => $username]);

    if ($check->fetch()) {
        header('Location: index.php?error=taken&tab=signup');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user with selected household ID
    $stmt = $CONN->prepare("INSERT INTO USERS (HOUSEHOLD_ID, USERNAME, PASSWORD_HASH) VALUES (:household_id, :username, :password)");
    $stmt->execute([
        ':household_id' => $household_id,
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