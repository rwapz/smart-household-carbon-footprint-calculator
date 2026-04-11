<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update Acivity</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updateemiss"])) {
        $F_id = $_POST["FACTOR_ID"];
        $C_id = $_POST["CATAGORY_ID"];
        $Aname = $_POST["ACTIVITY_NAME"];
        $co2 = $_POST["CO2_PER_UNIT"];
        updateemiss($F_id, $C_id, $Aname, $co2);
        header("location: viewemission.php");
        exit;
    }

if (!isset($_GET["F_id"]) || $_GET["F_id"] ==="") {
    die("no Factor id given");
}
$F_id = (int)$_GET["F_id"];

$ft = viewemiss($F_id);

if (!$ft || count($ft) === 0) {
    die("record not found not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $ft[0][0] ?> Activity</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="FACTOR_ID" value="<?php echo $ft[0][0] ?>">
        <input type="integer" name="CATAGORY_ID" value="<?php echo $ft[0][1] ?>">
        <input type="text" name="ACTIVITY_NAME" value="<?php echo $ft[0][2] ?>">
        <input type="text" name="CO2_PER_UNIT" value="<?php echo $ft[0][3] ?>">
        <input type="submit" name="updateemiss" value="update Activity">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    