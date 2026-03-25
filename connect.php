<?php
$HOST = "localhost";
$DB_NAME = "smarthousehold";
$USERNAME = "root";
$PASSWORD = "";

try {
    $CONN = new PDO("mysql:host=$HOST;dbname=$DB_NAME;charset=utf8", $USERNAME, $PASSWORD);
    $CONN->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $CONN->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("CONNECTION FAILED: " . $e->getMessage());
}
?>