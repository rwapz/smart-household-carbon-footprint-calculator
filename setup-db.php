<?php
/**
 * Database Setup - Run this ONCE to initialize the database
 * Access via: http://localhost/task/smart-household-carbon-footprint-calculator/setup-db.php
 */

$HOST = "localhost";
$DB_NAME = "smarthousehold"; 
$USERNAME = "root";
$PASSWORD = "";

try {
    // Connect to MySQL server (without database)
    $conn = new PDO("mysql:host=$HOST;charset=utf8", $USERNAME, $PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS $DB_NAME;");
    echo "✅ Database created/verified<br>";
    
    // Now connect to the database
    $conn = new PDO("mysql:host=$HOST;dbname=$DB_NAME;charset=utf8", $USERNAME, $PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create HOUSEHOLDS table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS HOUSEHOLDS (
            HOUSEHOLD_ID INT AUTO_INCREMENT PRIMARY KEY,
            HOUSEHOLD_NAME VARCHAR(200) NOT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ HOUSEHOLDS table created/verified<br>";
    
    // Insert default household
    $check = $conn->query("SELECT * FROM HOUSEHOLDS WHERE HOUSEHOLD_ID = 1");
    if ($check->rowCount() == 0) {
        $conn->exec("INSERT INTO HOUSEHOLDS (HOUSEHOLD_ID, HOUSEHOLD_NAME) VALUES (1, 'Default Household')");
        echo "✅ Default household inserted<br>";
    }
    
    // Create USERS table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS USERS (
            USER_ID INT AUTO_INCREMENT PRIMARY KEY,
            HOUSEHOLD_ID INT NOT NULL DEFAULT 1,
            USERNAME VARCHAR(100) NOT NULL UNIQUE,
            PASSWORD_HASH VARCHAR(255) NOT NULL,
            EMAIL VARCHAR(100),
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (HOUSEHOLD_ID) REFERENCES HOUSEHOLDS(HOUSEHOLD_ID) ON DELETE CASCADE
        )
    ");
    echo "✅ USERS table created/verified<br>";
    
    // Create CARBON_ENTRIES table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS CARBON_ENTRIES (
            ENTRY_ID INT AUTO_INCREMENT PRIMARY KEY,
            USER_ID INT NOT NULL,
            PERIOD VARCHAR(20) DEFAULT 'weekly',
            ELECTRICITY DECIMAL(8,2),
            GAS DECIMAL(8,2),
            WATER DECIMAL(8,2),
            WASTE INT,
            DIET VARCHAR(50),
            SHOPPING VARCHAR(50),
            FLIGHTS VARCHAR(50),
            TRANSPORT_TYPE VARCHAR(50),
            TRANSPORT_MILES DECIMAL(8,2),
            TOTAL_CO2 DECIMAL(10,2),
            GRADE VARCHAR(2),
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID) ON DELETE CASCADE
        )
    ");
    echo "✅ CARBON_ENTRIES table created/verified<br>";
    
    // Create LEADERBOARD table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS LEADERBOARD (
            LEADERBOARD_ID INT AUTO_INCREMENT PRIMARY KEY,
            USER_ID INT NOT NULL,
            AREA VARCHAR(100),
            PERIOD VARCHAR(20) DEFAULT 'weekly',
            SCORE DECIMAL(10,2),
            RANK INT,
            VERIFIED TINYINT DEFAULT 0,
            SUBMITTED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID) ON DELETE CASCADE
        )
    ");
    echo "✅ LEADERBOARD table created/verified<br>";
    
    echo "<br><strong>✅ Database setup complete!</strong><br>";
    echo "<a href='index.php'>← Go to Login Page</a>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
