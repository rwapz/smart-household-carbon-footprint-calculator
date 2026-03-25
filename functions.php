<?php 



function db_connect(){
    return $db = new SQLite3("carbon.db");
}
function addhousehold( $Hname, $post)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO HOUSEHOLD ( HOUSEHOLD_NAME, POSTCODE)
                                    VALUES (:Hname, :post)");
    
    $stmt->bindValue(':Hname', $Hname, SQLITE3_TEXT);
    $stmt->bindValue(':post', $post, SQLITE3_TEXT);
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}


function viewHousehold($H_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM HOUSEHOLD WHERE HOUSEHOLD_id = :id");
    $stmt->bindValue(":id", $H_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $HH_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $HH_d[] = $row;
    }

    $db->close();

    return $HH_d;
}


function updateHousehold($H_id, $Hname, $post)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE HOUSEHOLD
    SET HOUSEHOLD_NAME= :Hname,
    POSTCODE = :post
    WHERE HOUSEHOLD_ID = :id");

   
    $stmt->bindValue(":Hname", $Hname, SQLITE3_TEXT );
    $stmt->bindValue(":post", $post, SQLITE3_TEXT );
    $stmt->bindValue(":id", $H_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}

    function deleteHousehold($H_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM HOUSEHOLD WHERE HOUSEHOLD_ID = :H_id");
    $stmt->bindValue(":H_id", $H_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewH($H_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM HOUSEHOLD WHERE HOUSEHOLD_ID = $H_id ORDER BY HOUSEHOLD_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $HH_d[] = $row;
    }

$db->close();
return $HH_d;

 }

 function adduser($H_id, $Uname, $Phash)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO USERS ( HOUSEHOLD_ID, USERNAME, PASSWORD_HASH)
                                    VALUES (:H_id, :Uname, :Phash)"); 
    $stmt->bindValue(':H_id', $H_id, SQLITE3_INTEGER);
    $stmt->bindValue(':Uname', $Uname, SQLITE3_TEXT);
    $stmt->bindValue(':Phash', $Phash, SQLITE3_TEXT);
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}

function deleteUser($U_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM USERS WHERE USER_ID = :U_id");
    $stmt->bindValue(":U_id", $U_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewU($U_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM USERS WHERE USER_ID = $U_id ORDER BY USER_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $us_d[] = $row;
    }

$db->close();
return $us_d;

 }

 function viewUser($U_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM USERS WHERE USER_id = :id");
    $stmt->bindValue(":id", $U_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $us_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $us_d[] = $row;
    }

    $db->close();

    return $us_d;
}


function updateUser($U_id, $H_id, $Uname, $Phash)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE USERS
    SET HOUSEHOLD_ID = :H_id,
     USERNAME = :Uname,
     PASSWORD_HASH = :Phash
    WHERE USER_ID = :id");

    $stmt->bindValue(":H_id", $H_id, SQLITE3_TEXT );
    $stmt->bindValue(":Uname", $Uname, SQLITE3_TEXT );
    $stmt->bindValue(":Phash", $Phash, SQLITE3_TEXT );
    $stmt->bindValue(":id", $U_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}

function addCatagory( $Cname)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO CATAGORIES ( CATAGORY_NAME)
                                    VALUES (:Cname)");
    
    $stmt->bindValue(':Cname', $Cname, SQLITE3_TEXT);
   
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}


function viewCatagory($C_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM CATAGORIES WHERE CATAGORY_id = :id");
    $stmt->bindValue(":id", $C_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $cg_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $cg_d[] = $row;
    }

    $db->close();

    return $cg_d;
}


function updateCatagory($C_id, $Cname)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE CATAGORIES
    SET CATAGORY_NAME = :Cname
    WHERE CATAGORY_ID = :id");

    $stmt->bindValue(":Cname", $Cname, SQLITE3_TEXT );
    $stmt->bindValue(":id", $C_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}


function deleteCatagory($C_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM CATAGORIES WHERE CATAGORY_ID = :C_id");
    $stmt->bindValue(":C_id", $C_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewC($C_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM CATAGORIES WHERE CATAGORY_ID = $C_id ORDER BY CATAGORY_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $cg_d[] = $row;
    }

$db->close();
return $cg_d;

 }

 function addusert( $U_id, $UTname, $des)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO USER_TYPES ( USER_ID, USER_TYPE_NAME, DESCRIPTION)
                                    VALUES (:U_id, :UTname, :des)");
    
    $stmt->bindValue(':U_id', $U_id, SQLITE3_INTEGER);
    $stmt->bindValue(':UTname', $UTname, SQLITE3_TEXT);
    $stmt->bindValue(':des', $des, SQLITE3_TEXT);
   
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}

function viewusert($UT_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM USER_TYPES WHERE USER_TYPE_ID = :id");
    $stmt->bindValue(":id", $UT_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $ut_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $ut_d[] = $row;
    }

    $db->close();

    return $ut_d;
}


function updateusert($UT_id, $U_id, $UTname, $des)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE USER_TYPES
    SET USER_ID = :U_id,
    USER_TYPE_NAME = :UTname,
    DESCRIPTION = :des
    WHERE USER_TYPE_ID = :id");

    $stmt->bindValue(":U_id", $U_id, SQLITE3_INTEGER );
    $stmt->bindValue(":UTname", $UTname, SQLITE3_TEXT );
    $stmt->bindValue(":des", $des, SQLITE3_TEXT );
    $stmt->bindValue(":id", $UT_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}


function deleteusert($UT_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM USER_TYPES WHERE USER_TYPE_ID = :UT_id");
    $stmt->bindValue(":UT_id", $UT_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewUT($UT_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM USER_TYPES WHERE USER_TYPE_ID = $UT_id ORDER BY USER_TYPE_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $ut_d[] = $row;
    }

$db->close();
return $ut_d;

 }


  function addemiss( $C_id, $Aname, $co2)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO EMISSION_FACTORS ( CATAGORY_ID, ACTIVITY_NAME, CO2_PER_UNIT)
                                    VALUES (:C_id, :Aname, :co2)");
    
    $stmt->bindValue(':C_id', $C_id, SQLITE3_INTEGER);
    $stmt->bindValue(':Aname', $Aname, SQLITE3_TEXT);
    $stmt->bindValue(':co2', $co2, SQLITE3_TEXT);
   
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}

function viewemiss($F_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM EMISSION_FACTORS WHERE FACTOR_ID = :id");
    $stmt->bindValue(":id", $F_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $ft_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $ft_d[] = $row;
    }

    $db->close();

    return $ft_d;
}


function updateemiss($F_id, $C_id, $Aname, $co2)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE EMISSION_FACTORS
    SET CATAGORY_ID = :C_id,
    ACTIVITY_NAME = :Aname,
     CO2_PER_UNIT = :co2
    WHERE FACTOR_ID = :id");

    $stmt->bindValue(":C_id", $C_id, SQLITE3_INTEGER );
    $stmt->bindValue(":Aname", $Aname, SQLITE3_TEXT );
    $stmt->bindValue(":co2", $co2, SQLITE3_TEXT );
    $stmt->bindValue(":id", $F_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}


function deleteemiss($F_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM EMISSION_FACTORS WHERE FACTOR_ID = :F_id");
    $stmt->bindValue(":F_id", $F_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewE($F_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM EMISSION_FACTORS WHERE FACTOR_ID = $F_id ORDER BY FACTOR_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $ft_d[] = $row;
    }

$db->close();
return $ft_d;

 }


 function addgoal( $H_id, $co2, $Tmon)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO HOUSEHOLD_GOALS ( HOUSEHOLD_ID, TARGET_CO2_LIMIT, TARGET_MONTH)
                                    VALUES (:H_id, :co2, :Tmon)");
    
    $stmt->bindValue(':H_id', $H_id, SQLITE3_INTEGER);
    $stmt->bindValue(':co2', $co2, SQLITE3_TEXT);
    $stmt->bindValue(':Tmon', $Tmon, SQLITE3_TEXT);
   
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}

function viewgoal($G_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :id");
    $stmt->bindValue(":id", $G_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $gl_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $gl_d[] = $row;
    }

    $db->close();

    return $gl_d;
}


function updategoal($G_id, $H_id, $co2, $Tmon)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE HOUSEHOLD_GOALS
    SET HOUSEHOLD_ID = :H_id,
    TARGET_CO2_LIMIT = :co2,
     TARGET_MONTH = :Tmon
    WHERE GOAL_ID = :id");

    $stmt->bindValue(":H_id", $H_id, SQLITE3_INTEGER );
    $stmt->bindValue(":co2", $co2, SQLITE3_TEXT );
    $stmt->bindValue(":Tmon", $Tmon, SQLITE3_TEXT );
    $stmt->bindValue(":id", $G_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}


function deletegoal($G_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :G_id");
    $stmt->bindValue(":G_id", $G_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewG($G_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID = $G_id ORDER BY GOAL_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $gl_d[] = $row;
    }

$db->close();
return $gl_d;

 }

   function addact( $U_id, $F_id, $Amot, $Drec)
{
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO ACTIVITY_LOG ( USER_ID, FACTOR_ID, AMOUNT, DATE_RECORDED)
                                    VALUES (:U_id, :F_id, :Amot, :Drec)");
    
    $stmt->bindValue(':U_id', $U_id, SQLITE3_INTEGER);
    $stmt->bindValue(':F_id', $F_id, SQLITE3_INTEGER);
    $stmt->bindValue(':Amot', $Amot, SQLITE3_TEXT);
    $stmt->bindValue(':Drec', $Drec, SQLITE3_TEXT);
   
 
    
   

    
    if ($stmt->execute()) {
        echo "A new record  created successfully!";
    } else {
        echo "Failed to create record.";
    }

}


function viewact($L_id)
{
    $db = db_connect();

    $stmt = $db->prepare("SELECT * FROM ACTIVITY_LOG WHERE LOG_ID = :id");
    $stmt->bindValue(":id", $L_id, SQLITE3_INTEGER);

    $result = $stmt->execute();

    $lg_d = [];

    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $lg_d[] = $row;
    }

    $db->close();

    return $lg_d;
}


function updateact($L_id, $U_id, $F_id, $Amot, $Drec)
{
    $db = db_connect();

    $stmt = $db->prepare("
    UPDATE ACTIVITY_LOG
    SET USER_ID = :U_id,
    FACTOR_ID = :F_id,
     AMOUNT = :Amot,
     DATE_RECORDED = :Drec
    WHERE LOG_ID = :id");

    $stmt->bindValue(":U_id", $U_id, SQLITE3_INTEGER );
    $stmt->bindValue(":F_id", $F_id, SQLITE3_TEXT );
    $stmt->bindValue(":Amot", $Amot, SQLITE3_TEXT );
    $stmt->bindValue(":Drec", $Drec, SQLITE3_TEXT );
    $stmt->bindValue(":id", $L_id, SQLITE3_INTEGER );

    $stmt->execute();
    $db->close();
}



function deleteact($L_id)
{
    $db = db_connect();

    $stmt = $db->prepare("DELETE FROM ACTIVITY_LOG WHERE LOG_ID = :L_id");
    $stmt->bindValue(":L_id", $L_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "failure" . $db->lastErrorMsg();
    }
    $db->close();
}

 function viewA($L_id)
    { 

    $db = db_connect();
    $query = "SELECT * FROM ACTIVITY_LOG WHERE LOG_ID = $L_id ORDER BY LOG_ID DESC";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
        $lg_d[] = $row;
    }

$db->close();
return $lg_d;

 }
?>