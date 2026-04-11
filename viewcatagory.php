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
        <h2 class="centered-header">Catagories</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM CATAGORIES");
            echo "<p class='count'>Total catagories $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM CATAGORIES
         WHERE CATAGORY_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM CATAGORIES ORDER BY CATAGORY_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>GATAGORY_ID</td>
                <td>CATAGORY_NAME</td>
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $C_id = $row['CATAGORY_ID'];
            $Cname = $row['CATAGORY_NAME'];
            echo "
            <tbody>
                <tr> 
                  <td>$C_id</td> 
                  <td>$Cname</td> 
                  <td><a href='updatecatagory.php?C_id=$C_id'>update</td>
                  <td><a href='deletecatagory.php?C_id=$C_id'>delete</td>
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