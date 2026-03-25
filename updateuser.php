<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update your user information</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updateuser"])) {
        $U_id = $_POST["USER_ID"];
        $H_id = $_POST["HOUSEHOLD_ID"];
        $Uname = $_POST["USERNAME"];
        $Phash = $_POST["PASSWORD_HASH"];
        updateUser($U_id, $H_id, $Uname, $Phash);
        header("location: viewuser.php");
        exit;
    }

if (!isset($_GET["U_id"]) || $_GET["U_id"] ==="") {
    die("no user id given");
}
$U_id = (int)$_GET["U_id"];

$us = viewUser($U_id);

if (!$us || count($us) === 0) {
    die("user not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $us[0][1] ?> User details</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="USER_ID" value="<?php echo $us[0][0] ?>">
        <input type="integer" name="HOUSEHOLD_ID" value="<?php echo $us[0][1] ?>">
        <input type="text" name="USERNAME" value="<?php echo $us[0][2] ?>">
        <input type="text" name="PASSWORD_HASH" value="<?php echo $us[0][3] ?>">
        <input type="submit" name="updateuser" value="update user">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>

    