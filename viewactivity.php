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
        <h2 class="centered-header"> The Activity Log </h2>
    </div>
    <div class="main">

      <form method="get" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="search by name or ID">
        <input type="submit" value="search">
    </form>
        <?php
        $db = new SQLite3('carbon.db');

         $count = $db->querySingle("SELECT COUNT(*) FROM ACTIVITY_LOG");
            echo "<p class='count'>Total Records $count</p>";

       if (isset($_GET['search']) && !empty($_GET['search'])) { 

        $search = $_GET['search'];

         $stmt = $db->prepare("
         SELECT * FROM ACTIVITY_LOG
         WHERE LOG_ID LIKE :search   
         ");

         $stmt->bindValue(':search',"%$search%", SQLITE3_TEXT);
         $result = $stmt->execute();
    
    } else {
          $result = $db->query("SELECT * FROM ACTIVITY_LOG ORDER BY LOG_ID ASC");
    }
        
       
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>LOG_ID</td>
                <td>USER_ID</td> 
                <td>FACTOR_ID</td> 
                <td>AMOUNT</td> 
                <td>DATE_RECORDED</td>
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $L_id = $row['LOG_ID'];
            $U_id = $row['USER_ID'];
            $F_id = $row['FACTOR_ID'];
            $Amot = $row['AMOUNT'];
            $Drec = $row['DATE_RECORDED'];
            echo "
            <tbody>
                <tr> 
                  <td>$L_id</td>
                  <td>$U_id</td>
                  <td>$F_id</td> 
                  <td>$Amot</td>
                  <td>$Drec</td>
                  <td><a href='updateactivity.php?L_id=$L_id'>update</td>
                  <td><a href='deleteactivity.php?L_id=$L_id'>delete</td>
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