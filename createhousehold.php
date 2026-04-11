<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add New Address</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createhousehold'])) 
    {   
        $Hname = $_POST['HOUSEHOLD_NAME'];
        $post = $_POST['POSTCODE'];
        addhousehold($Hname,$post,);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add HouseHold Details</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="text" name="HOUSEHOLD_NAME" placeholder="HouseHold Name" required>
            <input type="text" name="POSTCODE" placeholder="PostCode"required>
            
            
            
            <input type="submit" name = "createhousehold" value="Create household">
        </form>
    </div>
    

     <footer>
            <p> Lincolnshire HMS - all rigths reserved</p>
    
        </footer>
       
    </div>
</body>
</html>