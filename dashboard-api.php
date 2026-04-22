<?php
session_start();
require_once 'connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
header('Content-Type: application/json');

if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get user's household
$userStmt = $CONN->prepare("SELECT * FROM users WHERE USER_ID = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();
$household_id = $user['HOUSEHOLD_ID'] ?? 1;

// Handle action
$action = $_GET['action'] ?? '';

// Set household goal
if ($action === 'setGoal' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $target = intval($input['target'] ?? 30);
    
    $stmt = $CONN->prepare("UPDATE household_goals SET TARGET_CO2_LIMIT = ? WHERE HOUSEHOLD_ID = ?");
    $stmt->execute([$target, $household_id]);
    
    echo json_encode(['success' => true, 'goalTarget' => $target, 'household_id' => $household_id]);
    exit;
}

// Get activities for ALL users in the household - SHARED data
$activityStmt = $CONN->prepare("
    SELECT al.* FROM activity_log al 
    JOIN users u ON al.USER_ID = u.USER_ID 
    WHERE u.HOUSEHOLD_ID = ? 
    ORDER BY al.DATE_RECORDED DESC
");
$activityStmt->execute([$household_id]);
$activities = $activityStmt->fetchAll();

// Calculate household TOTAL
$totalCO2 = 0;
foreach ($activities as $a) {
    $totalCO2 += floatval($a['TOTAL_CO2']);
}
$activityCount = count($activities);
$monthlyAvg = $activityCount > 0 ? round($totalCO2 / $activityCount, 1) : 0;

// Get household goal - default 30
$goalStmt = $CONN->prepare("SELECT TARGET_CO2_LIMIT FROM household_goals WHERE HOUSEHOLD_ID = ?");
$goalStmt->execute([$household_id]);
$goalRow = $goalStmt->fetch();
$goalTarget = $goalRow ? intval($goalRow['TARGET_CO2_LIMIT']) : 30; // Default 30

// Calculate rank among users
$rankData = $CONN->query("SELECT USER_ID, SUM(TOTAL_CO2) as total FROM activity_log GROUP BY USER_ID ORDER BY total ASC");
$allUsers = $rankData->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;
foreach ($allUsers as $u) {
    if ($u['USER_ID'] == $user_id) break;
    $rank++;
}

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'household_id' => $household_id,
    'monthlyTotal' => round($totalCO2, 1),
    'monthlyAvg' => $monthlyAvg,
    'rank' => $rank,
    'goalTarget' => $goalTarget,
    'activityCount' => $activityCount
]);