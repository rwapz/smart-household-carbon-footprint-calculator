<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update User Type</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updateusert"])) {
        $UT_id = $_POST["USER_TYPE_ID"];
        $U_id = $_POST["USER_ID"];
        $UTname = $_POST["USER_TYPE_NAME"];
        $des = $_POST["DESCRIPTION"];
        updateusert($UT_id, $U_id, $UTname, $des);
        header("location: viewusert.php");
        exit;
    }

if (!isset($_GET["UT_id"]) || $_GET["UT_id"] ==="") {
    die("no user type id given");
}
$UT_id = (int)$_GET["UT_id"];

$ut = viewusert($UT_id);

if (!$ut || count($ut) === 0) {
    die("user type not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $ut[0][0] ?> user type</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="USER_TYPE_ID" value="<?php echo $ut[0][0] ?>">
        <input type="text" name="USER_ID" value="<?php echo $ut[0][1] ?>">
        <input type="text" name="USER_TYPE_NAME" value="<?php echo $ut[0][2] ?>">
        <input type="text" name="DESCRIPTION" value="<?php echo $ut[0][3] ?>">
        <input type="submit" name="updateusert" value="update user type">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    