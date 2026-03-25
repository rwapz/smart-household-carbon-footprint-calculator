<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete Activity</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deleteemiss"])) {
            $F_id = $_POST["FACTOR_ID"];
             $C_id = $_POST["CATAGORY_ID"];
            $Aname = $_POST["ACTIVITY_NAME"];
            $des = $_POST["CO2_PER_UNIT"];
            deleteemiss($F_id);
            header("Location: viewemission.php");
            
        }
        $F_id = $_GET["F_id"] ?? null;

        if ($F_id === null) {
            die("error");
        }

       


         $ft = viewE($F_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $ft[0][1] ?> Activity</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="hidden" name="FACTOR_ID" value="<?php echo $ft[0][0]; ?>">
            <input type="integer" name="CATAGORY_ID" value="<?php echo $ft[0][1]; ?>">
            <input type="text" name="ACTIVITY_NAME" value="<?php echo $ft[0][2]; ?>">
            <input type="text" name="CO2_PER_UNIT" value="<?php echo $ft[0][3]; ?>">
            <input type="submit" name="deleteemiss" value="delete Activity">
            <form>
    </div>
    <footer>
            <p> </p>
    
        </footer>
    </body>
</html>