<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update your HouseHold</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updatehousehold"])) {
        $H_id = $_POST["HOUSEHOLD_ID"];
        $Hname = $_POST["HOUSEHOLD_NAME"];
        $post = $_POST["POSTCODE"];
        updateHousehold($H_id, $Hname, $post);
        header("location: viewhousehold.php");
        exit;
    }

if (!isset($_GET["H_id"]) || $_GET["H_id"] ==="") {
    die("no HouseHold id given");
}
$H_id = (int)$_GET["H_id"];

$HH = viewHousehold($H_id);

if (!$HH || count($HH) === 0) {
    die("staff not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $HH[0][1] ?> HouseHold details</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="HOUSEHOLD_ID" value="<?php echo $HH[0][0] ?>">
        <input type="text" name="HOUSEHOLD_NAME" value="<?php echo $HH[0][1] ?>">
        <input type="text" name="POSTCODE" value="<?php echo $HH[0][2] ?>">
        <input type="submit" name="updatehousehold" value="update household">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    