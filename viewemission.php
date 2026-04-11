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
        <h2 class="centered-header">Emission factors</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM EMISSION_FACTORS");
            echo "<p class='count'>Total Emission Records $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM EMISSION_FACTORS
         WHERE FACTORS_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM EMISSION_FACTORS ORDER BY FACTOR_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>FACTOR_ID</td>
                <td>CATAGORY_ID</td>
                <td>ACTIVITY_NAME</td>
                <td>CO2_PER_UNIT</td>
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $F_id = $row['FACTOR_ID'];
            $C_id = $row['CATAGORY_ID'];
            $Aname = $row['ACTIVITY_NAME'];
            $co2 = $row['CO2_PER_UNIT'];
            echo "
            <tbody>
                <tr> 
                  <td>$F_id</td>
                  <td>$C_id</td> 
                  <td>$Aname</td>  
                  <td>$co2</td> 
                  <td><a href='updateemission.php?F_id=$F_id'>update</td>
                  <td><a href='deleteemission.php?F_id=$F_id'>delete</td>
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