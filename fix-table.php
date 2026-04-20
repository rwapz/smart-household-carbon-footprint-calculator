<?php
require_once 'connect.php';

$CONN->exec("ALTER TABLE activity_log ADD COLUMN TOTAL_CO2 DECIMAL(10,2) DEFAULT 0");
echo "Added TOTAL_CO2 column";
include 'add-sample-data.php';