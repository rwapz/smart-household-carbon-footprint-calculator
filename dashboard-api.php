<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
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

    // Get this month's CO2 total
    $monthStart = date('Y-m-01');
    $monthStmt = $CONN->prepare("
        SELECT SUM(CO2_VALUE) as total FROM ACTIVITY_LOG 
        WHERE USER_ID = :user_id AND DATE(CREATED_AT) >= :month_start
    ");
    $monthStmt->execute([':user_id' => $user_id, ':month_start' => $monthStart]);
    $monthData = $monthStmt->fetch();
    $monthlyTotal = round($monthData['total'] ?? 0, 1);

    // Get average monthly CO2
    $avgStmt = $CONN->prepare("
        SELECT AVG(CASE WHEN cnt > 0 THEN monthly_total ELSE 0 END) as avg_total FROM (
            SELECT SUM(CO2_VALUE) as monthly_total, COUNT(*) as cnt FROM ACTIVITY_LOG 
            WHERE USER_ID = :user_id 
            GROUP BY YEAR(CREATED_AT), MONTH(CREATED_AT)
        ) sub
    ");
    $avgStmt->execute([':user_id' => $user_id]);
    $avgData = $avgStmt->fetch();
    $monthlyAvg = round($avgData['avg_total'] ?? 0, 1);

    // Get recent activities (last 5)
    $activitiesStmt = $CONN->prepare("
        SELECT ACTIVITY_TYPE, CO2_VALUE, CREATED_AT FROM ACTIVITY_LOG 
        WHERE USER_ID = :user_id 
        ORDER BY CREATED_AT DESC 
        LIMIT 5
    ");
    $activitiesStmt->execute([':user_id' => $user_id]);
    $activities = $activitiesStmt->fetchAll();

    // Format recent activities for display
    $recentActivities = [];
    foreach ($activities as $activity) {
        $time = strtotime($activity['CREATED_AT']);
        $diff = time() - $time;
        
        if ($diff < 3600) {
            $timeStr = round($diff / 60) . ' minutes ago';
        } else if ($diff < 86400) {
            $timeStr = round($diff / 3600) . ' hours ago';
        } else if ($diff < 604800) {
            $timeStr = round($diff / 86400) . ' days ago';
        } else {
            $timeStr = date('M d', $time);
        }
        
        $recentActivities[] = [
            'time' => $timeStr,
            'activity' => ucfirst(str_replace('_', ' ', $activity['ACTIVITY_TYPE'])),
            'value' => round($activity['CO2_VALUE'], 1),
            'unit' => 'kg CO₂'
        ];
    }

    // Get energy breakdown (estimate based on activity types)
    $breakdownStmt = $CONN->prepare("
        SELECT ACTIVITY_TYPE, SUM(CO2_VALUE) as total FROM ACTIVITY_LOG 
        WHERE USER_ID = :user_id AND DATE(CREATED_AT) >= :month_start
        GROUP BY ACTIVITY_TYPE
    ");
    $breakdownStmt->execute([':user_id' => $user_id, ':month_start' => $monthStart]);
    $breakdownData = $breakdownStmt->fetchAll();

    $energyBreakdown = [
        'electricity' => ['value' => 0, 'percentage' => 0],
        'transport' => ['value' => 0, 'percentage' => 0],
        'heating' => ['value' => 0, 'percentage' => 0],
        'water' => ['value' => 0, 'percentage' => 0]
    ];

    // Map activities to categories
    foreach ($breakdownData as $item) {
        $type = strtolower($item['ACTIVITY_TYPE']);
        $value = round($item['total'], 1);
        
        if (strpos($type, 'electricity') !== false || strpos($type, 'elec') !== false) {
            $energyBreakdown['electricity']['value'] = $value;
        } else if (strpos($type, 'transport') !== false || strpos($type, 'car') !== false) {
            $energyBreakdown['transport']['value'] = $value;
        } else if (strpos($type, 'heating') !== false || strpos($type, 'gas') !== false) {
            $energyBreakdown['heating']['value'] = $value;
        } else if (strpos($type, 'water') !== false) {
            $energyBreakdown['water']['value'] = $value;
        }
    }

    // Calculate percentages
    $total = array_sum(array_column($energyBreakdown, 'value', 0));
    if ($total > 0) {
        foreach ($energyBreakdown as &$item) {
            $item['percentage'] = round(($item['value'] / $total) * 100);
        }
    } else {
        // Default distribution if no data
        $energyBreakdown = [
            'electricity' => ['value' => 25.1, 'percentage' => 55],
            'transport' => ['value' => 11.4, 'percentage' => 25],
            'heating' => ['value' => 6.8, 'percentage' => 15],
            'water' => ['value' => 2.3, 'percentage' => 5]
        ];
        $monthlyTotal = 45.2;
        $monthlyAvg = 38.1;
    }

    // Get household average (all households in the system)
    $householdAvgStmt = $CONN->prepare("
        SELECT AVG(avg_co2) as avg FROM (
            SELECT AVG(CO2_VALUE) as avg_co2 FROM ACTIVITY_LOG 
            GROUP BY HOUSEHOLD_ID
        ) sub
    ");
    $householdAvgStmt->execute();
    $householdAvg = $householdAvgStmt->fetch();
    $comparisonAvg = round($householdAvg['avg'] ?? 52.1, 1);

    // Calculate rank (simple ranking)
    $rankStmt = $CONN->prepare("
        SELECT COUNT(DISTINCT h.HOUSEHOLD_ID) as total,
               RANK() OVER (ORDER BY SUM(al.CO2_VALUE) ASC) as rank
        FROM household h
        LEFT JOIN ACTIVITY_LOG al ON h.HOUSEHOLD_ID = (
            SELECT HOUSEHOLD_ID FROM USERS WHERE USER_ID = al.USER_ID
        )
        GROUP BY h.HOUSEHOLD_ID
        HAVING h.HOUSEHOLD_ID = :household_id
    ");
    $rankStmt->execute([':household_id' => $household_id]);
    $rankData = $rankStmt->fetch();
    $rank = $rankData['rank'] ?? 12;

    // Goal calculation
    $goalTarget = 30;
    $goalCurrent = $monthlyTotal;
    $goalPercent = min(round(($goalCurrent / $goalTarget) * 100), 100);
    $savings = max(round(($goalTarget - $goalCurrent) * 0.5), 0); // Rough savings estimate

    // Get most recent single activity for environmental impact
    $recentActivityStmt = $CONN->prepare("
        SELECT CO2_VALUE, ACTIVITY_TYPE, CREATED_AT FROM ACTIVITY_LOG 
        WHERE USER_ID = :user_id 
        ORDER BY CREATED_AT DESC 
        LIMIT 1
    ");
    $recentActivityStmt->execute([':user_id' => $user_id]);
    $recentActivity = $recentActivityStmt->fetch();
    
    $latestCO2 = $recentActivity ? round($recentActivity['CO2_VALUE'], 1) : 0;
    $latestTime = $recentActivity ? $recentActivity['CREATED_AT'] : null;
    $latestActivityType = $recentActivity ? $recentActivity['ACTIVITY_TYPE'] : null;

    echo json_encode([
        'success' => true,
        'monthlyTotal' => $monthlyTotal,
        'monthlyAvg' => $monthlyAvg,
        'rank' => $rank,
        'latestCO2' => $latestCO2,
        'latestTime' => $latestTime,
        'latestActivityType' => $latestActivityType,
        'recentActivities' => $recentActivities,
        'comparisonAvg' => $comparisonAvg,
        'goalTarget' => $goalTarget,
        'goalCurrent' => $goalCurrent,
        'savings' => $savings
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
