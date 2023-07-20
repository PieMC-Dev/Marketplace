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
<html lang="en">
<head>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-5WD76XPR07"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-5WD76XPR07');
  </script>
  <title>PieMC Login</title>
  <meta name="description" content="Welcome to Piemc Marketplace. Log in to access your account, manage your plugins, and explore a world of endless plugins!!">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <link rel="icon" type="image/x-icon" href="./assets/images/icons/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/css/styles.css">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login" style="background-image: url('assets/images/background.png');">
			<div class="wrap-login" style="padding-left: 55px; padding-right: 55px; padding-top: 65px; padding-bottom: 55px;">
				<form class="login-form validate-form">
					<span class="login-form-title" style="padding-bottom: 49px">
						LOGIN🍰
					</span>

					<div class="wrap-input validate-input" style="margin-bottom: 23px;" data-validate = "Username is required">
						<span class="label-input">Username</span>
						<input class="input" type="text" name="username" placeholder="Type your username">
						<span class="focus-input" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input validate-input" data-validate = "Password is required">
						<span class="label-input">Password</span>
						<input class="input" type="password" name="pass" placeholder="Type your password">
						<span class="focus-input" data-symbol="&#xf190;"></span>
					</div>
					
					<div class="text-right" style="text-align: right; padding-top: 8px; padding-bottom: 31px;">
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

					<div class="txt1 text-center" style="padding-top: 10px; padding-bottom: 20px;">
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

					<div class="flex-col-c" style="padding-top: 40px;">
						<span class="txt1" style="padding-bottom: 17px">
            New in PieMC Marketplace?
						</span>

						<a href="signup.php" class="txt2">
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