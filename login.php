<?php
require_once 'db_connect.php';

if (isset($_POST['login'])) {
    $clientID = ' '; // TODO: Add
    $clientSecret = ''; // TODO: Add
    $redirectURI = 'http://localhost/login.php';
    $url = "https://github.com/login/oauth/authorize?client_id=$clientID&redirect_uri=$redirectURI&scope=repo";
    header("Location: $url");
    exit();
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $clientID = ''; // TODO: Add
    $clientSecret = ''; // TODO: Add
    $redirectURI = 'http://localhost/login.php';

    $url = "https://github.com/login/oauth/access_token";
    $params = [
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'code' => $code,
        'redirect_uri' => $redirectURI
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $accessToken = $data['access_token'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/user');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userData = curl_exec($ch);
    curl_close($ch);

    $user = json_decode($userData, true);

    $githubUsername = $user['login'] ?? null;
    $githubUserId = $user['id'] ?? null;

    $stmt = $db->prepare("INSERT INTO users (github_user_id, github_username, access_token) VALUES (:github_user_id, :github_username, :access_token) ON DUPLICATE KEY UPDATE access_token = :access_token");
    $stmt->bindParam(':github_user_id', $githubUserId);
    $stmt->bindParam(':github_username', $githubUsername);
    $stmt->bindParam(':access_token', $accessToken);
    $stmt->execute();

    $_SESSION['user'] = $githubUsername;
    $_SESSION['user_id'] = $githubUserId;

    header("Location: dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PieMC - Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
       
    </header>
    
    <main>
        <h1>Login</h1>
        
        <section>
            <form method="POST" action="">
                <button type="submit" name="login">Login with GitHub</button>
            </form>
        </section>
    </main>
    
    <footer>
        
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
