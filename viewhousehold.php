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
        <h2 class="centered-header">HouseHold Records</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM HOUSEHOLD");
            echo "<p class='count'>Total Records $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM HOUSEHOLD
         WHERE HOUSEHOLD_NAME LIKE :search
         OR HOUSEHOLD_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM HOUSEHOLD ORDER BY HOUSEHOLD_NAME ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>HOUSEHOLD_ID</td>
                <td>HOUSEHOLD_NAME</td> 
                <td>POSTCODE</td> 
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $H_id = $row['HOUSEHOLD_ID'];
            $Hname = $row['HOUSEHOLD_NAME'];
            $post = $row['POSTCODE'];
            echo "
            <tbody>
                <tr> 
                  <td>$H_id</td>
                  <td>$Hname</td> 
                  <td>$post</td>
                  <td><a href='updatehousehold.php?H_id=$H_id'>update</td>
                  <td><a href='deletehousehold.php?H_id=$H_id'>delete</td>
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