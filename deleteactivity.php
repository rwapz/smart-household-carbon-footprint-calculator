<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete Log</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deleteact"])) {
            $L_id = $_POST["LOG_ID"];
             $U_id = $_POST["USER_ID"];
            $F_id = $_POST["FACTOR_NAME"];
            $Amot = $_POST["AMOUNT"];
            $Drec = $_POST["DATE_RECORDED"];
            deleteact($L_id);
            header("Location: viewactivity.php");
            
        }
        $L_id = $_GET["L_id"] ?? null;

        if ($L_id === null) {
            die("error");
        }

       


         $lg = viewA($L_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $lg[0][1] ?> Activity</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="hidden" name="LOG_ID" value="<?php echo $lg[0][0]; ?>">
            <input type="integer" name="USER_ID" value="<?php echo $lg[0][1]; ?>">
            <input type="integer" name="FACTOR" value="<?php echo $lg[0][2]; ?>">
            <input type="text" name="AMOUNT" value="<?php echo $lg[0][3]; ?>">
            <input type="text" name=DATE_RECORDED" value="<?php echo $lg[0][4]; ?>">
            <input type="submit" name="deleteact" value="delete the Activity Log">
            <form>
    </div>
    <footer>
            <p> </p>
    
        </footer>
    </body>
</html>