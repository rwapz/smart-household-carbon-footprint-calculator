<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add User Type</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createusert'])) 
    {  
        $U_id = $_POST['USER_ID']; 
        $UTname = $_POST['USER_TYPE_NAME'];
        $des = $_POST['DESCRIPTION'];
        addusert($U_id,$UTname, $des);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add User Type</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="text" name="USER_ID" placeholder="user type id" required>
            <input type="text" name="USER_TYPE_NAME" placeholder="user type" required>
            <input type="text" name="DESCRIPTION" placeholder="Description"required>
            
            
            
            <input type="submit" name = "createusert" value="Create new user type">
        </form>
    </div>
    

     <footer>
            <p> </p>
    
        </footer>
       
    </div>
</body>
</html>