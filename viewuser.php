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
        <h2 class="centered-header">User Records</h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM USERS");
            echo "<p class='count'>Total Records $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM USERS
         WHERE USER_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM USERS ORDER BY USER_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>USER_ID</td>
                <td>HOUSEHOLD_ID</td>
                <td>USERNAME</td> 
                <td>PASSWORD_HASH</td> 
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $U_id = $row['USER_ID'];
            $H_id = $row['HOUSEHOLD_ID'];
            $Uname = $row['USERNAME'];
            $Phash = $row['PASSWORD_HASH'];
            echo "
            <tbody>
                <tr> 
                  <td>$U_id</td> 
                  <td>$H_id</td> 
                  <td>$Uname</td> 
                  <td>$Phash</td>
                  <td><a href='updateuser.php?U_id=$U_id'>update</td>
                  <td><a href='deleteuser.php?U_id=$U_id'>delete</td>
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