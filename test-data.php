<?php
// Include your connection file
require_once 'connect.php';

try {
    // 1. Fetch everything from the USERS table
    $STMT = $CONN->prepare("SELECT * FROM USERS");
    $STMT->execute();
    $USERS = $STMT->fetchAll();

    echo "<h1>Database Account Audit</h1>";
    
    if (count($USERS) > 0) {
        // Start a basic HTML table
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%; text-align: left;'>";
        echo "<tr style='background-color: #2ecc71; color: white;'>
                <th>USER_ID</th>
                <th>HOUSEHOLD_ID</th>
                <th>USERNAME</th>
                <th>PASSWORD_HASH (Encrypted)</th>
              </tr>";

        foreach ($USERS as $row) {
            echo "<tr>";
            echo "<td>" . $row['USER_ID'] . "</td>";
            echo "<td>" . $row['HOUSEHOLD_ID'] . "</td>";
            echo "<td>" . $row['USERNAME'] . "</td>";
            // We truncate the hash so it doesn't take up the whole screen
            echo "<td style='font-family: monospace; font-size: 0.8rem;'>" . substr($row['PASSWORD_HASH'], 0, 30) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No accounts found in the database.</p>";
    }

} catch(PDOException $e) {
    echo "<h3 style='color:red;'>Query Error: " . $e->getMessage() . "</h3>";
}
?>