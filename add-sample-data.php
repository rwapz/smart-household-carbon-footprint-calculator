<?php
require_once 'connect.php';

// Get all users
$users = [];
$stmt = $CONN->query("SELECT USER_ID, USERNAME FROM USERS");
$users = $stmt->fetchAll();

// Get emission factors
$factors = [];
$stmt = $CONN->query("SELECT FACTOR_ID, ACTIVITY_NAME, CO2_PER_UNIT, UNIT FROM emission_factors");
$factors = $stmt->fetchAll();

if (empty($factors)) {
    die("No emission factors found. Create some first.");
}

echo "Adding sample data for " . count($users) . " users...\n";

foreach ($users as $user) {
    $uid = $user['USER_ID'];
    $username = $user['USERNAME'];
    
    // Skip admin
    if (strpos($username, 'admin') !== false) {
        continue;
    }
    
    // Delete existing for this user
    $CONN->prepare("DELETE FROM activity_log WHERE USER_ID = ?")->execute([$uid]);
    
    // 6 entries per user: 2 good, 2 bad, 1 awful
    $entries = [
        // GOOD (low CO2)
        ['factor' => 'Walking', 'amount' => 5, 'days' => -2],
        ['factor' => 'Cycling', 'amount' => 3, 'days' => -3],
        // BAD (high CO2)
        ['factor' => 'Car Travel', 'amount' => 50, 'days' => -1],
        ['factor' => 'Electricity', 'amount' => 20, 'days' => -4],
        // AWFUL (very high CO2)
        ['factor' => 'Long Flight', 'amount' => 10, 'days' => -7],
        // Extra to make it look real
        ['factor' => 'Meat Diet', 'amount' => 1, 'days' => -5],
    ];
    
    foreach ($entries as $e) {
        // Find matching factor
        $factor = null;
        foreach ($factors as $f) {
            if (stripos($f['ACTIVITY_NAME'], $e['factor']) !== false) {
                $factor = $f;
                break;
            }
        }
        
        if (!$factor) {
            // Use random factor
            $factor = $factors[array_rand($factors)];
        }
        
        $date = date('Y-m-d', strtotime($e['days'] . ' days'));
        $co2 = $e['amount'] * $factor['CO2_PER_UNIT'];
        
        try {
            $CONN->prepare("INSERT INTO activity_log (USER_ID, FACTOR_ID, AMOUNT, DATE_RECORDED, TOTAL_CO2) VALUES (?, ?, ?, ?, ?)")
                ->execute([$uid, $factor['FACTOR_ID'], $e['amount'], $date, $co2]);
            echo "$username added: $e[factor] ({$e['amount']} $factor[UNIT]) = " . round($co2, 1) . "kg\n";
        } catch (Exception $ex) {
            echo "Error: " . $ex->getMessage() . "\n";
        }
    }
}

echo "\nDone! Login as memberrhys (password: rhys2005) and check history.php";