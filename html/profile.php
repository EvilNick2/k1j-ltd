<?php
require_once '../php/config.php';

session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}

$conn = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
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
	<title>Profile</title>
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
			<?php if (isset($_SESSION['loggedin'])): ?>
				<a class="navbar-link-2-color navbar-button-2" href="../php/logout.php">Logout</a>
      <?php else: ?>
        <a class="navbar-link-2-color navbar-button-2" href="html/login.php">Login</a>
      <?php endif; ?>
		</div>
	</nav>
	<script src="../js/main.js"></script>
</body>
</html>