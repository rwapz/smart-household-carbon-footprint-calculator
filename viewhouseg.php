<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Footprint Tracker</title>
    <link rel="stylesheet" href="style.css" />
</head>

<header>  
         

           
        
</header>
<body>

    <div>
        <h2 class="centered-header"> HouseHold Goals</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM HOUSEHOLD_GOALS");
            echo "<p class='count'>Total Record $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM HOUSEHOLD_GOAL
         WHERE GOAL_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM HOUSEHOLD_GOALS ORDER BY GOAL_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>GOAL_ID</td>
                <td>HOUSEHOLD_ID</td> 
                <td>TARGET_CO2_LIMIT</td> 
                <td>TARGET_MONTH</td> 
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $G_id = $row['GOAL_ID'];
            $H_id = $row['HOUSEHOLD_ID'];
            $co2 = $row['TARGET_CO2_LIMIT'];
            $Tmon = $row['TARGET_MONTH'];
            echo "
            <tbody>
                <tr> 
                  <td>$G_id</td>
                  <td>$H_id</td>
                  <td>$co2</td> 
                  <td>$Tmon</td>
                  <td><a href='updatehouseg.php?G_id=$G_id'>update</td>
                  <td><a href='deletehouseg.php?G_id=$G_id'>delete</td>
                </tr>
            </tbody>";
        }
        echo "</table>";
        $db->close();
        ?>
    </div>
</body>
 <footer>
            <p>  </p>
    
        </footer>
</html>