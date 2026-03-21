<?php

$HOST = "localhost";
$DB_NAME = "carbon_tracker"; 
$USERNAME = "root";          // Default XAMPP username
$PASSWORD = "";              // Default XAMPP password (empty)

try {
    
    $CONN = new PDO("mysql:host=$HOST;dbname=$DB_NAME", $USERNAME, $PASSWORD);
    
    // Set error mode to exception so we can see if things break
    $CONN->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Connected successfully"; // Uncomment this to test the connection when connected

} catch(PDOException $e) {
    // If the DB isn't ready yet or has errors, this will catch the error
    // die("CONNECTION FAILED: " . $e->getMessage());
}


?>
