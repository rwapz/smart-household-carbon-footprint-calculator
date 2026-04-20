<?php
require_once 'auth-admin.php';
require_once 'connect.php';

$error = '';

if (isset($_POST["deletecatagory"])) {
    $C_id = $_POST["CATAGORY_ID"];
    try {
        $stmt = $CONN->prepare("DELETE FROM CATAGORIES WHERE CATAGORY_ID = :id");
        $stmt->execute([':id' => $C_id]);
        header("Location: viewcatagory.php");
        exit;
    } catch(PDOException $e) {
        $error = $e->getMessage();
    }
}

$C_id = $_GET["C_id"] ?? null;
if ($C_id === null) {
    die("Error: No category ID provided");
}

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
    <title>Delete Category | Admin</title>
    <link rel="stylesheet" href="stylesheets/admin-style.css">
    <link rel="stylesheet" href="stylesheets/accessibility-global.css">
    <script>
        (function() {
            const theme = localStorage.getItem('eco-theme') || 'light';
            const contrast = localStorage.getItem('eco-contrast') === 'true' ? 'high' : 'normal';
            const font = localStorage.getItem('eco-fontsize') || 'normal';
            const fontMap = { small: '14px', normal: '16px', large: '19px' };
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-contrast', contrast);
            document.documentElement.style.fontSize = fontMap[font] || '16px';
        })();
    </script>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <h1>Delete Category</h1>
        </div>
        <div class="header-right">
            <a href="admin-dashboard.php" class="header-btn">← Back to Admin</a>
            <a href="dashboard.php" class="header-btn">Dashboard</a>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>
    </header>
    <main class="admin-container">
        <?php if (!empty($error)) { echo "<div class='alert alert-error'>Error: " . htmlspecialchars($error) . "</div>"; } ?>
        <div class="form-card">
            <form method="post">
                <p class="confirm-text">Are you sure you want to delete this category?</p>
                <input type="hidden" name="CATAGORY_ID" value="<?php echo htmlspecialchars($cat['CATAGORY_ID']); ?>">
                <div class="confirm-info">
                    <p><strong>Category Name:</strong> <?php echo htmlspecialchars($cat['CATAGORY_NAME']); ?></p>
                </div>
                <button type="submit" name="deletecatagory" class="btn btn-danger">Delete Category</button>
            </form>
        </div>
    </main>
</body>
</html>
