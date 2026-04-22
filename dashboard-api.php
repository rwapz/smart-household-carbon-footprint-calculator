<?php
session_start();
require_once 'connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
header('Content-Type: application/json');

if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get action
$action = $_GET['action'] ?? '';

// Set goal action
if ($action === 'setGoal' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $target = intval($input['target'] ?? 30);
    
    $stmt = $CONN->prepare("UPDATE user_goals SET TARGET_CO2 = ?, UPDATED_AT = CURDATE() WHERE USER_ID = ?");
    $stmt->execute([$target, $user_id]);
    
    echo json_encode(['success' => true, 'goalTarget' => $target]);
    exit;
}

// Get dashboard data
$activityStmt = $CONN->prepare("SELECT * FROM activity_log WHERE USER_ID = ? ORDER BY DATE_RECORDED DESC LIMIT 20");
$activityStmt->execute([$user_id]);
$activities = $activityStmt->fetchAll();

$totalCO2 = 0;
foreach ($activities as $a) {
    $totalCO2 += floatval($a['TOTAL_CO2']);
}
$activityCount = count($activities);

// Average per activity (not per month)
$monthlyAvg = $activityCount > 0 ? round($totalCO2 / $activityCount, 1) : 0;

// Get goal
$goalStmt = $CONN->prepare("SELECT TARGET_CO2 FROM user_goals WHERE USER_ID = ?");
$goalStmt->execute([$user_id]);
$goalRow = $goalStmt->fetch();
$goalTarget = $goalRow ? intval($goalRow['TARGET_CO2']) : 30;

// Calculate rank

// Calculate rank
$rankData = $CONN->query("SELECT USER_ID, SUM(TOTAL_CO2) as total FROM activity_log GROUP BY USER_ID ORDER BY total DESC");
$allUsers = $rankData->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;
foreach ($allUsers as $u) {
    if ($u['USER_ID'] == $user_id) break;
    $rank++;
}

echo json_encode([
    'success' => true,
    'monthlyTotal' => round($totalCO2, 1),
    'monthlyAvg' => count($activities) > 0 ? round($totalCO2 / count($activities), 1) : 0,
    'rank' => $rank,
    'goalTarget' => $goalTarget,
    'activityCount' => count($activities)
]);