<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add Emissions</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>
    
</header>


 
<body>
    <?php
    
    require_once("functions.php");
    
    

    
    if(isset($_POST['createemiss'])) 
    {  
        $C_id = $_POST['CATAGORY_ID']; 
        $Aname = $_POST['ACTIVITY_NAME'];
        $co2 = $_POST['CO2_PER_UNIT'];
        addemiss($C_id,$Aname, $co2);       
    }
    ?>
    <div>
        <h2 class="centered-header">Add Activity</h2>
    </div>
    <div class="main">
        <form method="post">
            <input type="integer" name="CATAGORY_ID" placeholder="Catagory" required>
            <input type="text" name="ACTIVITY_NAME" placeholder="activity name" required>
            <input type="text" name="CO2_PER_UNIT" placeholder="Co2 per unit "required>
            
            
            
            <input type="submit" name = "createemiss" value="Create Activity">
        </form>
    </div>
    

     <footer>
            <p> </p>
    
        </footer>
       
    </div>
</body>
</html>