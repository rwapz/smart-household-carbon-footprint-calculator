<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete user records</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deleteuser"])) {
            $U_id = $_POST["USER_ID"];
            $H_id = $_POST["HOUSEHOLD_ID"];
            $Uname = $_POST["USERNAME"];
            $Phash = $_POST["PASSWORD_HASH"];
            deleteUser($U_id);
            header("Location: viewusere.php");
            
        }
        $U_id = $_GET["U_id"] ?? null;

        if ($U_id === null) {
            die("error");
        }

       


         $user = viewU($U_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $user[0][1] ?> User Record</h2>
    </div>
    <div class="main">
        <form method="post">
             <input type="hidden" name="USER_ID" value="<?php echo $user[0][0]; ?>">
            <input type="hidden" name="HOUSEHOLD_ID" value="<?php echo $user[0][1]; ?>">
            <input type="text" name="USERNAME" value="<?php echo $user[0][2]; ?>">
            <input type="text" name="PASSWORD_HASH" value="<?php echo $user[0][3]; ?>">
            <input type="submit" name="deleteuser" value="delete user">
            <form>
    </div>
    <footer>
            <p> Lincolnshire HMS - all rigths reserved</p>
    
        </footer>
    </body>
</html>