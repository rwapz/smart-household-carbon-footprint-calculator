<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete household records</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deletehousehold"])) {
            $H_id = $_POST["HOUSEHOLD_ID"];
            $Hname = $_POST["HOUSEHOLD_NAME"];
            $post = $_POST["POSTCODE"];
            deleteHousehold($H_id);
            header("Location: viewhousehold.php");
            
        }
        $H_id = $_GET["H_id"] ?? null;

        if ($H_id === null) {
            die("error");
        }

       


         $HH = viewH($H_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $HH[0][1] ?> Record</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="hidden" name="HOUSEHOLD_ID" value="<?php echo $HH[0][0]; ?>">
            <input type="text" name="HOUSEHOLD_NAME" value="<?php echo $HH[0][1]; ?>">
            <input type="text" name="POSTCODE" value="<?php echo $HH[0][2]; ?>">
            <input type="submit" name="deletehousehold" value="delete household">
            <form>
    </div>
    <footer>
            <p> </p>
    
        </footer>
    </body>
</html>