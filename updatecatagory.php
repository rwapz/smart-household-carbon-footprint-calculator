<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["updatecatagory"])) {
    $C_id = $_POST["CATAGORY_ID"];
    $Cname = $_POST["CATAGORY_NAME"];
    try {
        $stmt = $CONN->prepare("UPDATE CATAGORIES SET CATAGORY_NAME = :Cname WHERE CATAGORY_ID = :id");
        $stmt->execute([':Cname' => $Cname, ':id' => $C_id]);
        header("Location: viewcatagory.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

if (!isset($_GET["C_id"]) || $_GET["C_id"] === "") {
    die("No category ID given");
}
$C_id = (int)$_GET["C_id"];

try {
    $stmt = $CONN->prepare("SELECT * FROM CATAGORIES WHERE CATAGORY_ID = :id");
    $stmt->execute([':id' => $C_id]);
    $cat = $stmt->fetch();
    if (!$cat) {
        die("Category not found");
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script src="scripts/init-accessibility.js"></script>
    <script src="scripts/toggle-dark.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Update Category</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
<a href="logout.php" class="header-btn logout">Logout</a>
            <button id="dark-btn" class="header-btn" onclick="toggleDarkMode()" title="Toggle dark mode">🌙</button>
        </div>
    </header>

    <main class="admin-container">
        <?php if (!empty($error)) { echo "<div class='alert alert-error'>Error: " . htmlspecialchars($error) . "</div>"; } ?>
        <div class="form-card">
            <form method="post">
                <input type="hidden" name="CATAGORY_ID" value="<?php echo htmlspecialchars($cat['CATAGORY_ID']); ?>">
                <div class="form-group">
                    <label for="CATAGORY_NAME">Category Name</label>
                    <input type="text" name="CATAGORY_NAME" value="<?php echo htmlspecialchars($cat['CATAGORY_NAME']); ?>" required>
                </div>
                <button type="submit" name="updatecatagory" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </main>
</body>
</html>
