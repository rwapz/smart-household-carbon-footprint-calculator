<?php
// Database Configuration - Using UPPERCASE to match your teammate's style
$HOST = "localhost";
$DB_NAME = "carbon_tracker"; // Tell him to check if he named the DB this!
$USERNAME = "root";          // Default XAMPP username
$PASSWORD = "";              // Default XAMPP password (empty)

try {
    /* This creates the connection object. 
       We use the UPPERCASE variables defined above.
    */
    $CONN = new PDO("mysql:host=$HOST;dbname=$DB_NAME", $USERNAME, $PASSWORD);
    
    // Set error mode to exception so we can see if things break
    $CONN->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Connected successfully"; // Uncomment this to test the connection later

} catch(PDOException $e) {
    // If the DB isn't ready yet, this will catch the error
    // die("CONNECTION FAILED: " . $e->getMessage());
}

/* FUTURE SQL REFERENCE (How we will write it to match him):
   
   SELECT * FROM USERS WHERE USERNAME = :username;
   INSERT INTO USERS (USERNAME, EMAIL, PASSWORD) VALUES (:user, :email, :pass);
*/
?>