<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete User Types</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deleteusert"])) {
            $UT_id = $_POST["USER_TYPE_ID"];
             $U_id = $_POST["USER_ID"];
            $UTname = $_POST["USER_TYPE_NAME"];
            $des = $_POST["DESCRIPTION"];
            deleteusert($UT_id);
            header("Location: viewusert.php");
            
        }
        $UT_id = $_GET["UT_id"] ?? null;

        if ($UT_id === null) {
            die("error");
        }

       


         $ut = viewUT($UT_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $ut[0][1] ?> User Types</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="hidden" name="USER_TYPE_ID" value="<?php echo $ut[0][0]; ?>">
            <input type="text" name="USER_ID" value="<?php echo $ut[0][1]; ?>">
            <input type="text" name="USER_TYPE_NAME" value="<?php echo $ut[0][2]; ?>">
            <input type="text" name="DESCRIPTION" value="<?php echo $ut[0][3]; ?>">
            <input type="submit" name="deleteusert" value="delete user type">
            <form>
    </div>
    <footer>
            <p> </p>
    
        </footer>
    </body>
</html>