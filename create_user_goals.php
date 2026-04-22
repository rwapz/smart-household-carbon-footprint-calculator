<?php
include 'connect.php';

$sql = "
CREATE TABLE IF NOT EXISTS user_goals (
    USER_GOAL_ID INT AUTO_INCREMENT PRIMARY KEY,
    USER_ID INT NOT NULL,
    TARGET_CO2 DECIMAL(10,2) DEFAULT 30,
    UPDATED_AT DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID)
);
";

$CONN->exec($sql);
echo "Table created\n";

$sql2 = "
INSERT INTO user_goals (USER_ID, TARGET_CO2)
SELECT USER_ID, 30 FROM USERS;
";

$CONN->exec($sql2);
echo "Default goals inserted\n";

$sql3 = "SELECT * FROM user_goals";
$stmt = $CONN->query($sql3);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($results);
?>