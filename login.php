<?php
require_once 'db_connect.php';

if (isset($_POST['login'])) {
    $clientID = '0f6faba649a71051f167';
    $clientSecret = '66c5a1001e24351b10ebd309fa3ec12a1ced8e59';
    $redirectURI = 'http://localhost/login.php';
    $url = "https://github.com/login/oauth/authorize?client_id=$clientID&redirect_uri=$redirectURI&scope=repo";
    header("Location: $url");
    exit();
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $clientID = '0f6faba649a71051f167';
    $clientSecret = '66c5a1001e24351b10ebd309fa3ec12a1ced8e59';
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
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-5WD76XPR07"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-5WD76XPR07');
  </script>
  <title>PieMC Login</title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <link rel="icon" type="image/x-icon" href="./assets/images/icons/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login" style="background-image: url('assets/images/background.png');">
			<div class="wrap-login p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login-form validate-form">
					<span class="login-form-title p-b-49">
						LOGIN🍰
					</span>

					<div class="wrap-input validate-input m-b-23" data-validate = "Username is required">
						<span class="label-input">Username</span>
						<input class="input" type="text" name="username" placeholder="Type your username">
						<span class="focus-input" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input validate-input" data-validate = "Password is required">
						<span class="label-input">Password</span>
						<input class="input" type="password" name="pass" placeholder="Type your password">
						<span class="focus-input" data-symbol="&#xf190;"></span>
					</div>
					
					<div class="text-right p-t-8 p-b-31">
						<a href="#">
							Forgot password?
						</a>
					</div>
					
					<div class="container-login-form-btn">
						<div class="wrap-login-form-btn">
							<div class="login-form-bgbtn"></div>
							<button class="login-form-btn">
								LOGIN
							</button>
						</div>
					</div>

					<div class="txt1 text-center p-t-10 p-b-20">
					</div>

					<div class="flex-c-m">
						<a href="#" class="social-login bg1">
							<i class="fa fa-github"></i>
						</a>

						<a href="#" class="social-login bg2">
							<i class="fa fa-twitter"></i>
						</a>

						<a href="#" class="social-login bg3">
							<i class="fa fa-google"></i>
						</a>
					</div>

					<div class="flex-col-c p-t-40">
						<span class="txt1 p-b-17">
            New in PieMC Marketplace?
						</span>

						<a href="#" class="txt2">
							Sign Up
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>

<script src="https://kit.fontawesome.com/662c538273.js" crossorigin="anonymous"></script>
</body>
</html>