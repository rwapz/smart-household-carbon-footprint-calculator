<?php
require_once 'connect.php';

// HOUSEHOLD
function addHousehold($Hname, $post) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO HOUSEHOLD (HOUSEHOLD_NAME, POSTCODE) VALUES (:Hname, :post)");
    $stmt->execute([':Hname' => $Hname, ':post' => $post]);
    return "Household created successfully!";
}

function viewHousehold($H_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM HOUSEHOLD WHERE HOUSEHOLD_ID = :id");
    $stmt->execute([':id' => $H_id]);
    return $stmt->fetchAll();
}

function viewH($H_id) {
    return viewHousehold($H_id);
}

function updateHousehold($H_id, $Hname, $post) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE HOUSEHOLD SET HOUSEHOLD_NAME = :Hname, POSTCODE = :post WHERE HOUSEHOLD_ID = :id");
    $stmt->execute([':Hname' => $Hname, ':post' => $post, ':id' => $H_id]);
    return "success";
}

function deleteHousehold($H_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM HOUSEHOLD WHERE HOUSEHOLD_ID = :H_id");
    $stmt->execute([':H_id' => $H_id]);
    return "success";
}

// USER
function addUser($H_id, $Uname, $Phash) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO USERS (HOUSEHOLD_ID, USERNAME, PASSWORD_HASH) VALUES (:H_id, :Uname, :Phash)");
    $stmt->execute([':H_id' => $H_id, ':Uname' => $Uname, ':Phash' => $Phash]);
    return "User created successfully!";
}

function viewUser($U_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM USERS WHERE USER_ID = :id");
    $stmt->execute([':id' => $U_id]);
    return $stmt->fetchAll();
}

function viewU($U_id) {
    return viewUser($U_id);
}

function updateUser($U_id, $H_id, $Uname, $Phash) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE USERS SET HOUSEHOLD_ID = :H_id, USERNAME = :Uname, PASSWORD_HASH = :Phash WHERE USER_ID = :id");
    $stmt->execute([':H_id' => $H_id, ':Uname' => $Uname, ':Phash' => $Phash, ':id' => $U_id]);
    return "success";
}

function deleteUser($U_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM USERS WHERE USER_ID = :U_id");
    $stmt->execute([':U_id' => $U_id]);
    return "success";
}

// USER TYPES
function addUserType($U_id, $UTname, $des) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO USER_TYPES (USER_ID, USER_TYPE_NAME, DESCRIPTION) VALUES (:U_id, :UTname, :des)");
    $stmt->execute([':U_id' => $U_id, ':UTname' => $UTname, ':des' => $des]);
    return "User type created successfully!";
}

function viewUserType($UT_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM USER_TYPES WHERE USER_TYPE_ID = :id");
    $stmt->execute([':id' => $UT_id]);
    return $stmt->fetchAll();
}

function viewUT($UT_id) {
    return viewUserType($UT_id);
}

function updateUserType($UT_id, $U_id, $UTname, $des) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE USER_TYPES SET USER_ID = :U_id, USER_TYPE_NAME = :UTname, DESCRIPTION = :des WHERE USER_TYPE_ID = :id");
    $stmt->execute([':U_id' => $U_id, ':UTname' => $UTname, ':des' => $des, ':id' => $UT_id]);
    return "success";
}

function deleteUserType($UT_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM USER_TYPES WHERE USER_TYPE_ID = :UT_id");
    $stmt->execute([':UT_id' => $UT_id]);
    return "success";
}

// CATAGORIES
function addCategory($Cname) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO CATAGORIES (CATAGORY_NAME) VALUES (:Cname)");
    $stmt->execute([':Cname' => $Cname]);
    return "Category created successfully!";
}

function viewCategory($C_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM CATAGORIES WHERE CATAGORY_ID = :id");
    $stmt->execute([':id' => $C_id]);
    return $stmt->fetchAll();
}

function viewC($C_id) {
    return viewCategory($C_id);
}

function updateCategory($C_id, $Cname) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE CATAGORIES SET CATAGORY_NAME = :Cname WHERE CATAGORY_ID = :id");
    $stmt->execute([':Cname' => $Cname, ':id' => $C_id]);
    return "success";
}

function deleteCategory($C_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM CATAGORIES WHERE CATAGORY_ID = :C_id");
    $stmt->execute([':C_id' => $C_id]);
    return "success";
}

// EMISSION FACTORS
function addEmission($C_id, $Aname, $co2) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO EMISSION_FACTORS (CATAGORY_ID, ACTIVITY_NAME, CO2_PER_UNIT) VALUES (:C_id, :Aname, :co2)");
    $stmt->execute([':C_id' => $C_id, ':Aname' => $Aname, ':co2' => $co2]);
    return "Emission factor created successfully!";
}

function viewEmission($F_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM EMISSION_FACTORS WHERE FACTOR_ID = :id");
    $stmt->execute([':id' => $F_id]);
    return $stmt->fetchAll();
}

function viewE($F_id) {
    return viewEmission($F_id);
}

function updateEmission($F_id, $C_id, $Aname, $co2) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE EMISSION_FACTORS SET CATAGORY_ID = :C_id, ACTIVITY_NAME = :Aname, CO2_PER_UNIT = :co2 WHERE FACTOR_ID = :id");
    $stmt->execute([':C_id' => $C_id, ':Aname' => $Aname, ':co2' => $co2, ':id' => $F_id]);
    return "success";
}

function deleteEmission($F_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM EMISSION_FACTORS WHERE FACTOR_ID = :F_id");
    $stmt->execute([':F_id' => $F_id]);
    return "success";
}

// HOUSEHOLD GOALS
function addGoal($H_id, $co2, $Tmon) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO HOUSEHOLD_GOALS (HOUSEHOLD_ID, TARGET_CO2_LIMIT, TARGET_MONTH) VALUES (:H_id, :co2, :Tmon)");
    $stmt->execute([':H_id' => $H_id, ':co2' => $co2, ':Tmon' => $Tmon]);
    return "Goal created successfully!";
}

function viewGoal($G_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :id");
    $stmt->execute([':id' => $G_id]);
    return $stmt->fetchAll();
}

function viewG($G_id) {
    return viewGoal($G_id);
}

function updateGoal($G_id, $H_id, $co2, $Tmon) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE HOUSEHOLD_GOALS SET HOUSEHOLD_ID = :H_id, TARGET_CO2_LIMIT = :co2, TARGET_MONTH = :Tmon WHERE GOAL_ID = :id");
    $stmt->execute([':H_id' => $H_id, ':co2' => $co2, ':Tmon' => $Tmon, ':id' => $G_id]);
    return "success";
}

function deleteGoal($G_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM HOUSEHOLD_GOALS WHERE GOAL_ID = :G_id");
    $stmt->execute([':G_id' => $G_id]);
    return "success";
}

// ACTIVITY LOG
function addActivity($U_id, $F_id, $Amot, $Drec) {
    global $CONN;
    $stmt = $CONN->prepare("INSERT INTO ACTIVITY_LOG (USER_ID, FACTOR_ID, AMOUNT, DATE_RECORDED) VALUES (:U_id, :F_id, :Amot, :Drec)");
    $stmt->execute([':U_id' => $U_id, ':F_id' => $F_id, ':Amot' => $Amot, ':Drec' => $Drec]);
    return "Activity created successfully!";
}

function viewActivity($L_id) {
    global $CONN;
    $stmt = $CONN->prepare("SELECT * FROM ACTIVITY_LOG WHERE LOG_ID = :id");
    $stmt->execute([':id' => $L_id]);
    return $stmt->fetchAll();
}

function viewA($L_id) {
    return viewActivity($L_id);
}

function updateActivity($L_id, $U_id, $F_id, $Amot, $Drec) {
    global $CONN;
    $stmt = $CONN->prepare("UPDATE ACTIVITY_LOG SET USER_ID = :U_id, FACTOR_ID = :F_id, AMOUNT = :Amot, DATE_RECORDED = :Drec WHERE LOG_ID = :id");
    $stmt->execute([':U_id' => $U_id, ':F_id' => $F_id, ':Amot' => $Amot, ':Drec' => $Drec, ':id' => $L_id]);
    return "success";
}

function deleteActivity($L_id) {
    global $CONN;
    $stmt = $CONN->prepare("DELETE FROM ACTIVITY_LOG WHERE LOG_ID = :L_id");
    $stmt->execute([':L_id' => $L_id]);
    return "success";
}
