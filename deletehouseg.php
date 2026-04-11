<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete Your Goals</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deletegoal"])) {
            $G_id = $_POST["GOAL_ID"];
             $H_id = $_POST["HOUSEHOLD_ID"];
            $co2 = $_POST["TARGET_CO_LIMIT"];
            $Tmon = $_POST["TARGET_MONTH"];
            deletegoal($G_id);
            header("Location: viewhouseg.php");
            
        }
        $G_id = $_GET["G_id"] ?? null;

        if ($G_id === null) {
            die("error");
        }

       


         $gl = viewG($G_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $gl[0][0] ?> Goals</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="hidden" name="GOAL_ID" value="<?php echo $gl[0][0]; ?>">
            <input type="integer" name="HOUSWEHOLD_ID" value="<?php echo $gl[0][1]; ?>">
            <input type="text" name="TARGET_CO2_LIMIT" value="<?php echo $gl[0][2]; ?>">
            <input type="text" name="TARGET_MONTH" value="<?php echo $gl[0][3]; ?>">
            <input type="submit" name="deletegoal" value="delete Goals">
            <form>
    </div>
    <footer>
            <p> </p>
    
        </footer>
    </body>
</html>