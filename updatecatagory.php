<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.8">
        <title>update catagories</title>
        <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
    require_once("functions.php");

    if (isset($_POST["updatecatagory"])) {
        $C_id = $_POST["CATAGORY_ID"];
        $Cname = $_POST["CATAGORY_NAME"];
        updateCatagory($C_id, $Cname);
        header("location: viewcatagory.php");
        exit;
    }

if (!isset($_GET["C_id"]) || $_GET["C_id"] ==="") {
    die("no catagory given");
}
$C_id = (int)$_GET["C_id"];

$cg = viewCatagory($C_id);

if (!$cg || count($cg) === 0) {
    die("catagory not found");
}
?>

<div>
    <h2 class="centered">update <?php echo $cg[0][0] ?> catagory details</h2>
</div>
<div class="main">
    <form method="post">
        <input type="hidden" name="CATAGORY_ID" value="<?php echo $cg[0][0] ?>">
        <input type="text" name="CATAGORY_NAME" value="<?php echo $cg[0][1] ?>">
        <input type="submit" name="updatecatagory" value="update catagory">
</form>
</div>
 <footer>
            <p></p>
    
        </footer>

</body>
</html>