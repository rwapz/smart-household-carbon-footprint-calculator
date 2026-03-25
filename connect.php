<?php
<<<<<<< HEAD
$HOST = "localhost";
$DB_NAME = "smarthousehold"; 
$USERNAME = "root";          
$PASSWORD = "";              

try {
    $CONN = new PDO("mysql:host=$HOST;dbname=$DB_NAME;charset=utf8", $USERNAME, $PASSWORD);
=======

$HOST = "localhost";
$DB_NAME = "carbon_tracker"; 
$USERNAME = "root";          // Default XAMPP username
$PASSWORD = "";              // Default XAMPP password (empty)

try {
    
    $CONN = new PDO("mysql:host=$HOST;dbname=$DB_NAME", $USERNAME, $PASSWORD);
>>>>>>> 8a01b8fae81aab8bea1de5c2d6f70c4d18e869c4
    
    $CONN->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
<<<<<<< HEAD
    $CONN->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);



} catch(PDOException $e) {
    die("CONNECTION FAILED: " . $e->getMessage());
}
?>
=======
    
    // echo "Connected successfully"; // Uncomment this to test the connection when connected

} catch(PDOException $e) {
    // If the DB isn't ready yet or has errors, this will catch the error
    // die("CONNECTION FAILED: " . $e->getMessage());
}


?>
>>>>>>> 8a01b8fae81aab8bea1de5c2d6f70c4d18e869c4
