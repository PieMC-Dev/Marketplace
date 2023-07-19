<?php
require_once 'db_connect.php';

$stmt = $db->prepare("SELECT * FROM plugins WHERE status = 'approved'");
$stmt->execute();
$approvedPlugins = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>PieMC - Plugins</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
    </header>
    <main>
        <h1>Plugin List</h1>
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search plugins">
            <button type="submit">Search</button>
        </form>
        <?php if (isset($_SESSION['user'])) { ?>
            <p>Welcome, <?php echo $_SESSION['user']; ?>!</p>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login with GitHub</a>
        <?php } ?>
        <?php
        $search = $_GET['search'] ?? '';
        $filteredPlugins = [];
        $plugins = !empty($search) ? $filteredPlugins : $approvedPlugins;

        if (!empty($plugins)) {
            foreach ($plugins as $plugin) {
                echo "<div class='plugin-listing'>";
                echo "<div class='plugin-logo'><img src='{$plugin['logo']}' alt='Plugin Logo'></div>";
                echo "<div class='plugin-details'>";
                echo "<h3>{$plugin['name']}</h3>";
                echo "<p>Version: {$plugin['version']}</p>";
                echo "<p>Author: {$plugin['author']}</p>";
                echo "<p>Posted: {$plugin['date']}</p>";
                echo "</div>";
                echo "<div class='plugin-actions'>";
                #echo "<a href='plugin.php?id={$plugin['id']}'>View Details</a>"; // TODO:
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No plugins found.</p>";
        }
        ?>
    </main>
    
    <footer>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
