<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update Activity Log</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updateact"])) {
        $L_id = $_POST["LOG_ID"];
        $U_id = $_POST["USER_ID"];
        $F_id = $_POST["FACTOR_ID"];
        $Amot = $_POST["AMOUNT"];
         $Drec = $_POST["DATE_RECORDED"];
        updateact($L_id, $U_id, $F_id, $Amot, $Drec);
        header("location: viewactivity.php");
        exit;
    }

if (!isset($_GET["L_id"]) || $_GET["L_id"] ==="") {
    die("no Log id given");
}
$L_id = (int)$_GET["L_id"];

$lg = viewact($L_id);

if (!$lg || count($lg) === 0) {
    die("Logs not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $lg[0][0] ?> the Activity Log </h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="LOG_ID" value="<?php echo $lg[0][0] ?>">
        <input type="integer" name="USER_ID" value="<?php echo $lg[0][1] ?>">
        <input type="integer" name="FACTOR_ID" value="<?php echo $lg[0][2] ?>">
        <input type="text" name="AMOUNT" value="<?php echo $lg[0][3] ?>">
        <input type="text" name="DATE_RECORDED" value="<?php echo $lg[0][4] ?>">
        <input type="submit" name="updateact" value="update the Activity Log">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    