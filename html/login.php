<?php
include "../php/config.php";
session_start();

$message = "";
$messageType = "";

$currentPage = 'login';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST["username"];

	$stmt = $conn->prepare("SELECT id, password, name, rank, email, address FROM users WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->store_result();
	
	if (!$stmt->num_rows > 0) {
		$message = "User does not exist";
		$messageType = "failure";
	} else {
		$stmt->bind_result($id, $password, $name, $rank, $email, $address);
		$stmt->fetch();

		if (!password_verify($_POST["password"], $password)) {
			$message = "Password is incorrect";
			$messageType = "failure";
		} else {
			$message = "Logged in successfully";
			$messageType = "success";
			session_regenerate_id();
			$_SESSION["loggedin"] = TRUE;
			$_SESSION["username"] = $username;
			$_SESSION["name"] = $name;
			$_SESSION["email"] = $email;
			$_SESSION["address"] = $address;
			$_SESSION["id"] = $id;
			$_SESSION["rank"] = $rank;

			$analyticsStmt = $conn->prepare("INSERT INTO user_analytics (username, last_login, login_count) VALUES (?, NOW(), 1) ON DUPLICATE KEY UPDATE LAST_LOGIN = NOW(), LOGIN_COUNT = LOGIN_COUNT + 1");
			$analyticsStmt->bind_param("s", $username);
			$analyticsStmt->execute();
			$analyticsStmt->close();

			header("Location: profile.php");
		}
	}
	$stmt->close();
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../fonts/css/all.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/profile.css">
	<link rel="icon" href="../imgs/logo.svg" type="image/svg">
	<title>Login</title>
</head>
<body>
	<?php include 'nav.php'; ?>

	<div class="login">
		<h1>Login</h1>
		<form method="post" autocomplete="off">
			<label for="username">
				<i class="fas fa-user"></i>
			</label>
			<input type="text" name="username" placeholder="Username" id="username" required>
			<label for="password">
					<i class="fas fa-lock"></i>
			</label>
			<div class="password-container">
				<input type="password" name="password" placeholder="Password" id="password" required>
				<i class="fa fa-eye-slash" id="togglePassword"></i>
			</div>
			<input type="submit" value="Login">
			</form>
			<?php if ($messageType === "failure"): ?>
				<style>
					.login form input[type="submit"] {
						border-radius: 0;
					}
				</style>
				<div class="dialog-warning"><?php echo $message ?></div>
			<?php endif; ?>
	</div>
	
	<script src="../js/toggle_password.js"></script>
	<script src="../js/main.js"></script>
</body>
</html>