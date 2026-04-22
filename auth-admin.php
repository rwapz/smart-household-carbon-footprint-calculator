<?php


session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php?error=admin&tab=login');
    exit;
}

require_once 'connect.php';

try {
    $stmt = $CONN->prepare("SELECT * FROM USER_TYPES WHERE USER_ID = :uid AND USER_TYPE_NAME = 'Admin' LIMIT 1");
    $stmt->execute([':uid' => $_SESSION['user_id']]);
    $isAdmin = $stmt->fetch() !== false;
} catch (PDOException $e) {
    $isAdmin = false;
}

if (!$isAdmin) {
    header('Location: dashboard.php?error=notadmin');
    exit;
}
?>
