<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add New User</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createuser'])) 
    {   
        $H_id = $_POST['HOUSEHOLD_ID'];
        $Uname = $_POST['USERNAME'];
        $Phash = $_POST['PASSWORD_HASH'];
        adduser($H_id, $Uname, $Phash,);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add User Details</h2>
    </div>
    <div class="main">
        <form method="post">
             <input type="integer" name="HOUSEHOLD_ID" placeholder="Household id" required>
            <input type="text" name="USERNAME" placeholder=" userName" required>
            <input type="text" name="PASSWORD_HASH" placeholder="password"required>
            
            
            
            <input type="submit" name = "createuser" value="Create user">
        </form>
    </div>
    

     <footer>
            <p></p>
    
        </footer>
       
    </div>
</body>
</html>