<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add New Catagory</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createCatagory'])) 
    {   
        
        $Cname = $_POST['CATAGORY_NAME'];
        addCatagory( $Cname,);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add  Catagories</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="text" name="CATAGORY_NAME" placeholder=" Catagories" required>
            
            
            
            <input type="submit" name = "createCatagory" value="Create catagories">
        </form>
    </div>
    

     <footer>
            <p></p>
    
        </footer>
       
    </div>
</body>
</html>