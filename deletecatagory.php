<DOCTYPE.html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device=width, initial-scale=1.0">
        <title>delete catagories</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <?php
        require_once("functions.php");

        if (isset($_POST["deletecatagory"])) {
            $C_id = $_POST["CATAGORY_ID"];
            $Cname = $_POST["CATAGORY_NAME"];
            deleteCatagory($C_id);
            header("Location: viewcatagory.php");
            
        }
        $C_id = $_GET["C_id"] ?? null;

        if ($C_id === null) {
            die("error");
        }

       


         $cg = viewC($C_id);
        ?>
        <div>
            <h2 class="centered-header">delete <?php echo $cg[0][1] ?> catagory</h2>
    </div>
    <div class="main">
        <form method="post">
             <input type="hidden" name="CATAGORY_ID" value="<?php echo $cg[0][0]; ?>">
            <input type="text" name="CATAGORY_NAME" value="<?php echo $cg[0][1]; ?>">
            <input type="submit" name="deletecatagory" value="delete catagory">
            <form>
    </div>
    <footer>
            <p> Lincolnshire HMS - all rigths reserved</p>
    
        </footer>
    </body>
</html>