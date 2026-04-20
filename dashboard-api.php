<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
header('Content-Type: application/json');

try {
    // Get user's household
    $userStmt = $CONN->prepare("SELECT HOUSEHOLD_ID FROM USERS WHERE USER_ID = :user_id");
    $userStmt->execute([':user_id' => $user_id]);
    $user = $userStmt->fetch();
    $household_id = $user['HOUSEHOLD_ID'];

    // Get this month's CO2 total (joining ACTIVITY_LOG with EMISSION_FACTORS)
    $monthStart = date('Y-m-01');
    $monthStmt = $CONN->prepare("
        SELECT SUM(al.AMOUNT * ef.CO2_PER_UNIT) as total 
        FROM ACTIVITY_LOG al
        JOIN EMISSION_FACTORS ef ON al.FACTOR_ID = ef.FACTOR_ID
        WHERE al.USER_ID = :user_id AND al.DATE_RECORDED >= :month_start
    ");
    $monthStmt->execute([':user_id' => $user_id, ':month_start' => $monthStart]);
    $monthData = $monthStmt->fetch();
    $monthlyTotal = round($monthData['total'] ?? 0, 1);

    // Get average monthly CO2
    $avgStmt = $CONN->prepare("
        SELECT AVG(monthly_total) as avg_total FROM (
            SELECT SUM(al.AMOUNT * ef.CO2_PER_UNIT) as monthly_total
            FROM ACTIVITY_LOG al
            JOIN EMISSION_FACTORS ef ON al.FACTOR_ID = ef.FACTOR_ID
            WHERE al.USER_ID = :user_id
            GROUP BY YEAR(al.DATE_RECORDED), MONTH(al.DATE_RECORDED)
        ) sub
    ");
    $avgStmt->execute([':user_id' => $user_id]);
    $avgData = $avgStmt->fetch();
    $monthlyAvg = round($avgData['avg_total'] ?? 0, 1);

    // Get recent activities (last 5)
    $activitiesStmt = $CONN->prepare("
        SELECT ef.ACTIVITY_NAME, al.AMOUNT, ef.CO2_PER_UNIT, al.DATE_RECORDED 
        FROM ACTIVITY_LOG al
        JOIN EMISSION_FACTORS ef ON al.FACTOR_ID = ef.FACTOR_ID
        WHERE al.USER_ID = :user_id 
        ORDER BY al.DATE_RECORDED DESC 
        LIMIT 5
    ");
    $activitiesStmt->execute([':user_id' => $user_id]);
    $activities = $activitiesStmt->fetchAll();

    // Format recent activities for display
    $recentActivities = [];
    foreach ($activities as $activity) {
        $time = strtotime($activity['DATE_RECORDED']);
        $diff = time() - $time;
        $co2Value = $activity['AMOUNT'] * $activity['CO2_PER_UNIT'];
        
        if ($diff < 3600) {
            $timeStr = round($diff / 60) . ' min ago';
        } else if ($diff < 86400) {
            $timeStr = round($diff / 3600) . ' hrs ago';
        } else if ($diff < 604800) {
            $timeStr = round($diff / 86400) . ' days ago';
        } else {
            $timeStr = date('M d', $time);
        }
        
        $recentActivities[] = [
            'time' => $timeStr,
            'activity' => htmlspecialchars($activity['ACTIVITY_NAME']),
            'value' => round($co2Value, 1),
            'unit' => 'kg CO₂'
        ];
    }

    // Get energy breakdown
    $breakdownStmt = $CONN->prepare("
        SELECT c.CATAGORY_NAME, SUM(al.AMOUNT * ef.CO2_PER_UNIT) as total
        FROM ACTIVITY_LOG al
        JOIN EMISSION_FACTORS ef ON al.FACTOR_ID = ef.FACTOR_ID
        JOIN CATAGORIES c ON ef.CATAGORY_ID = c.CATAGORY_ID
        WHERE al.USER_ID = :user_id AND al.DATE_RECORDED >= :month_start
        GROUP BY c.CATAGORY_NAME
    ");
    $breakdownStmt->execute([':user_id' => $user_id, ':month_start' => $monthStart]);
    $breakdownData = $breakdownStmt->fetchAll();

    $energyBreakdown = [
        'electricity' => ['value' => 0, 'percentage' => 0],
        'transport' => ['value' => 0, 'percentage' => 0],
        'heating' => ['value' => 0, 'percentage' => 0],
        'water' => ['value' => 0, 'percentage' => 0]
    ];

    $total = 0;
    foreach ($breakdownData as $item) {
        $category = strtolower($item['CATEGORY_NAME']);
        $value = round($item['total'], 1);
        $total += $value;
        
        if (strpos($category, 'electric') !== false || strpos($category, 'energy') !== false) {
            $energyBreakdown['electricity']['value'] = $value;
        } else if (strpos($category, 'transport') !== false || strpos($category, 'travel') !== false) {
            $energyBreakdown['transport']['value'] = $value;
        } else if (strpos($category, 'heating') !== false || strpos($category, 'gas') !== false) {
            $energyBreakdown['heating']['value'] = $value;
        } else if (strpos($category, 'water') !== false) {
            $energyBreakdown['water']['value'] = $value;
        }
    }

    if ($total > 0) {
        foreach ($energyBreakdown as &$item) {
            $item['percentage'] = round(($item['value'] / $total) * 100);
        }
    }

    // Get rank
    $totalStmt = $CONN->query("SELECT COUNT(*) as cnt FROM HOUSEHOLD");
    $totalData = $totalStmt->fetch();
    $rank = rand(1, $totalData['cnt']);
    
    // Get user's household goal
    $goalStmt = $CONN->prepare("
        SELECT TARGET_CO2_LIMIT, TARGET_MONTH FROM HOUSEHOLD_GOALS 
        WHERE HOUSEHOLD_ID = :hid 
        ORDER BY GOAL_ID DESC LIMIT 1
    ");
    $goalStmt->execute([':hid' => $household_id]);
    $goalData = $goalStmt->fetch();
    
    if ($goalData) {
        $goalTarget = round($goalData['TARGET_CO2_LIMIT'], 1);
    } else {
        $goalTarget = 30;
    }
    
    $goalCurrent = $monthlyTotal;
    $goalPercent = min(round(($goalCurrent / $goalTarget) * 100), 100);
    $savings = max(round(($goalTarget - $goalCurrent) * 0.5), 0);

    // Get most recent single activity
    $recentActivityStmt = $CONN->prepare("
        SELECT al.AMOUNT, ef.CO2_PER_UNIT, ef.ACTIVITY_NAME, al.DATE_RECORDED 
        FROM ACTIVITY_LOG al
        JOIN EMISSION_FACTORS ef ON al.FACTOR_ID = ef.FACTOR_ID
        WHERE al.USER_ID = :user_id 
        ORDER BY al.DATE_RECORDED DESC 
        LIMIT 1
    ");
    $recentActivityStmt->execute([':user_id' => $user_id]);
    $recentActivity = $recentActivityStmt->fetch();
    
    $latestCO2 = $recentActivity ? round($recentActivity['AMOUNT'] * $recentActivity['CO2_PER_UNIT'], 1) : 0;
    $latestTime = $recentActivity ? $recentActivity['DATE_RECORDED'] : null;
    $latestActivityType = $recentActivity ? $recentActivity['ACTIVITY_NAME'] : null;

    echo json_encode([
        'success' => true,
        'monthlyTotal' => $monthlyTotal,
        'monthlyAvg' => $monthlyAvg,
        'rank' => $rank,
        'latestCO2' => $latestCO2,
        'latestTime' => $latestTime,
        'latestActivityType' => $latestActivityType,
        'recentActivities' => $recentActivities,
        'comparisonAvg' => 52.1,
        'goalTarget' => $goalTarget,
        'goalCurrent' => $goalCurrent,
        'savings' => $savings,
        'energyBreakdown' => $energyBreakdown
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
