<?php
require_once 'connect.php';

try {
    $CONN->exec("DROP TABLE IF EXISTS calculator_results");
$CONN->exec("CREATE TABLE calculator_results (
        RESULT_ID INT AUTO_INCREMENT PRIMARY KEY,
        USER_ID INT NOT NULL,
        TOTAL_CO2 DECIMAL(10,2) NOT NULL,
        GRADE VARCHAR(1),
        PERIOD VARCHAR(20),
        CREATED_AT DATE NOT NULL
    )");
    echo "Table created or exists\n";
} catch (PDOException $e) {
    echo "Table error: " . $e->getMessage() . "\n";
}

// Get users
$users = $CONN->query("SELECT USER_ID, USERNAME FROM USERS WHERE USERNAME LIKE 'memberrhys%' OR USERNAME LIKE 'viewerrhys%'")->fetchAll();

foreach ($users as $user) {
    $uid = $user['USER_ID'];
    $CONN->prepare("DELETE FROM calculator_results WHERE USER_ID = ?")->execute([$uid]);
    
    $entries = [
        ['co2' => rand(35, 55), 'grade' => 'A', 'days' => rand(2, 8)],
        ['co2' => rand(40, 60), 'grade' => 'A', 'days' => rand(15, 22)],
        ['co2' => rand(85, 105), 'grade' => 'C', 'days' => rand(9, 15)],
        ['co2' => rand(110, 140), 'grade' => 'D', 'days' => rand(23, 35)],
        ['co2' => rand(180, 240), 'grade' => 'F', 'days' => rand(40, 75)],
    ];
    
    foreach ($entries as $e) {
        $date = date('Y-m-d', strtotime('-' . $e['days'] . ' days'));
        $CONN->prepare("INSERT INTO calculator_results (USER_ID, TOTAL_CO2, GRADE, PERIOD, CREATED_AT) VALUES (?, ?, ?, 'weekly', ?)")
            ->execute([$uid, $e['co2'], $e['grade'], $date]);
    }
    echo "Added 5 results for {$user['USERNAME']}\n";
}

echo "Done!";