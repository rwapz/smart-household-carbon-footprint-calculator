<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update Your Goals</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updategoals"])) {
        $G_id = $_POST["GOAL_ID"];
        $H_id = $_POST["HOUSEHOLD_ID"];
        $co2 = $_POST["TARGET_CO2_LIMIT"];
        $Tmon = $_POST["TARGET_MONTH"];
        updategoal($G_id, $H_id, $co2, $Tmon);
        header("location: viewhouseg.php");
        exit;
    }

if (!isset($_GET["G_id"]) || $_GET["G_id"] ==="") {
    die("no goals id given");
}
$G_id = (int)$_GET["G_id"];

$gl = viewgoal($G_id);

if (!$gl || count($gl) === 0) {
    die("user type not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $gl[0][0] ?> Goals</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="GOAL_ID" value="<?php echo $gl[0][0] ?>">
        <input type="integer" name="HOUSEHOLD_ID" value="<?php echo $gl[0][1] ?>">
        <input type="text" name="TARGET_CO2_LIMIT" value="<?php echo $gl[0][2] ?>">
        <input type="text" name="TARGET_MONTH" value="<?php echo $gl[0][3] ?>">
        <input type="submit" name="updategoals" value="update Goals">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    