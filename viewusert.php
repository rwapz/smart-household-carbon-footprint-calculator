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
        <h2 class="centered-header"> User Types</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM USER_TYPES");
            echo "<p class='count'>Total Records $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM USER_TYPES
         WHERE USER_TYPE_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM USER_TYPES ORDER BY USER_TYPE_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>USER_TYPE_ID</td>
                <td>USER_ID</td> 
                <td>USER_TYPE_NAME</td> 
                <td>DESCRIPTION</td> 
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $UT_id = $row['USER_TYPE_ID'];
            $U_id = $row['USER_ID'];
            $UTname = $row['USER_TYPE_NAME'];
            $des = $row['DESCRIPTION'];
            echo "
            <tbody>
                <tr> 
                  <td>$UT_id</td>
                  <td>$U_id</td>
                  <td>$UTname</td> 
                  <td>$des</td>
                  <td><a href='updateusert.php?UT_id=$UT_id'>update</td>
                  <td><a href='deleteusert.php?UT_id=$UT_id'>delete</td>
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