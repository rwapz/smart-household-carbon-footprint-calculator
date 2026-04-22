<?php
session_start();
require_once 'connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
header('Content-Type: application/json');

if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

// Get settings
if ($action === 'get') {
    $stmt = $CONN->prepare("SELECT THEME, FONT_SIZE, CONTRAST FROM user_settings WHERE USER_ID = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    echo json_encode([
        'theme' => $row ? $row['THEME'] : 'light',
        'font' => $row ? $row['FONT_SIZE'] : 'normal',
        'contrast' => $row ? $row['CONTRAST'] : 'normal'
    ]);
    exit;
}

// Save settings
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $theme = $data['theme'] ?? 'light';
    $font = $data['font'] ?? 'normal';
    $contrast = $data['contrast'] ?? 'normal';
    
    $stmt = $CONN->prepare("UPDATE user_settings SET THEME = ?, FONT_SIZE = ?, CONTRAST = ? WHERE USER_ID = ?");
    $stmt->execute([$theme, $font, $contrast, $user_id]);
    
    echo json_encode(['success' => true]);
    exit;
}