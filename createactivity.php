<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add Activitys</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createact'])) 
    {  
        $U_id = $_POST['USER_ID']; 
        $F_id = $_POST['FACTOR_ID'];
        $Amot = $_POST['AMOUNT'];
        $Drec = $_POST['DATE_RECORDED'];
        addact($U_id,$F_id, $Amot, $Drec);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add User Type</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="text" name="USER_ID" placeholder="users" required>
            <input type="text" name="FACTOR_ID" placeholder=" activitys" required>
            <input type="text" name="AMOUNT" placeholder="Amount"required>
            <input type="text" name="DATE_RECORDED" placeholder="Date Recorded"required>
            
            
            
            <input type="submit" name = "createact" value="Add new Activity">
        </form>
    </div>
    

     <footer>
            <p> </p>
    
        </footer>
       
    </div>
</body>
</html>