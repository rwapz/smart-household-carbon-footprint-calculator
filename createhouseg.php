<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add Goals</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['creategoals'])) 
    {  
        $H_id = $_POST['HOUSEHOLD_ID']; 
        $co2 = $_POST['TARGET_CO2_LIMIT'];
        $Tmon = $_POST['TARGET_MONTH'];
        addgoal($H_id,$co2, $Tmon);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add Goals</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="integer" name="HOUSEHOLD_ID" placeholder="HouseHold" required>
            <input type="text" name="TARGET_CO2_LIMIT" placeholder="CO2 Target" required>
            <input type="text" name="TARGET_MONTH" placeholder="Monthly Target"required>
            
            
            
            <input type="submit" name = "creategoals" value="Create new Goals">
        </form>
    </div>
    

     <footer>
            <p> </p>
    
        </footer>
       
    </div>
</body>
</html>