<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT access_token FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$accessToken = $user['access_token'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/user/repos');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$repositories = json_decode($response, true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>PieMC - User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
    </header>
    
    <main>
        <h1>User Dashboard</h1>
        
        <h2>Your Repositories</h2>
        <?php if (!empty($repositories)) { ?>
            <ul>
                <?php foreach ($repositories as $repo) { ?>
                    <li><?php echo $repo['name']; ?></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No repositories found.</p>
        <?php } ?>
        
        <!-- Plugin upload functionality, additional dashboard content, etc. -->
    </main>

    <footer>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
