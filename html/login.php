<?php
include "../php/config.php";
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST["username"];

	$stmt = $conn->prepare("SELECT id, password, name FROM users WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->store_result();
	
	if (!$stmt->num_rows > 0) {
		$message = "User does not exist";
	} else {
		$stmt->bind_result($id, $password, $name);
		$stmt->fetch();

		if (!password_verify($_POST["password"], $password)) {
			$message = "Password is incorrect";
		} else {
			$message = "Logged in successfully";
			session_regenerate_id();
			$_SESSION["loggedin"] = TRUE;
			$_SESSION["username"] = $username;
			$_SESSION["name"] = $name;
			$_SESSION["id"] = $id;
			header("Location: ../index.php");
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
	<link rel="stylesheet" href="../fonts/css/all.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/profile.css">
	<link rel="icon" href="../imgs/logo.svg" type="image/svg">
	<title>Login</title>
</head>
<body>
	<nav>
		<div class="navbar-logo">
			<img src="../imgs/logo.svg" alt="K1J LTD Logo">
		</div>

		<div class="navbar-links navbar-left">
			<a class="navbar-link-1-color" href="../index.php">Home</a>
			<a class="navbar-link-1-color" href="#">Dashboard</a>
			<a class="navbar-link-1-color" href="#">Placeholder</a>
		</div>

		<div class="navbar-links navbar-right">
			<a class="navbar-link-2-color navbar-button-1" id="light-mode-toggle">Toggle Dark Mode</a>
			<a class="navbar-link-2-color navbar-button-2" href="profile.php">Profile</a>
		</div>
	</nav>

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
        <input type="password" name="password" placeholder="Password" id="password" required>
        <input type="submit" value="Login">
        </form>
        <form action="register.php" method="get">
            <input type="submit" value="Register">
        </form>
				<?php if ($message === "User does not exist" || $message === "Password is incorrect"): ?>
					<style>
						.login form[action="register.php"] input[type="submit"] {
							border-radius: 0;
						}
					</style>
					<div class="block"><?php echo $message ?></div>
				<?php endif; ?>
    </div>
	<script src="../js/main.js"></script>
</body>
</html>