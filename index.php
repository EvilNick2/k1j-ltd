<?php
include "php/config.php";

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="fonts/css/all.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="imgs/logo.svg" type="image/svg">
	<title>K1J LTD</title>
</head>
<body>
	<nav>
		<div class="navbar-logo">
			<img src="imgs/logo.svg" alt="K1J LTD Logo">
		</div>

		<div class="navbar-links navbar-left">
			<a class="navbar-link-1-color" href="index.php">Home</a>
			<a class="navbar-link-1-color" href="#">Dashboard</a>
			<a class="navbar-link-1-color" href="#">Placeholder</a>
		</div>

		<div class="navbar-links navbar-right">
			<a class="navbar-link-2-color navbar-button-1" id="light-mode-toggle">Toggle Dark Mode</a>
			<?php if (isset($_SESSION['loggedin'])): ?>
				<?php $firstName = explode(' ', $_SESSION['name'])[0]; ?>
				<a class="navbar-link-2-color navbar-button-2" href="html/profile.php"><?=htmlspecialchars($firstName, ENT_QUOTES)?></a>
      <?php else: ?>
        <a class="navbar-link-2-color navbar-button-2" href="html/login.php">Login</a>
      <?php endif; ?>
		</div>
	</nav>

	<div>Dolor esse id duis tempor esse velit non sit quis sint irure aliqua pariatur minim. Nostrud sunt fugiat ad laboris in aliquip eiusmod veniam ipsum ex non. In amet id sit adipisicing eiusmod sunt Lorem esse sunt ut ea sit mollit. Id culpa labore voluptate laboris commodo eu esse velit id excepteur quis culpa sunt. Id mollit dolor dolor est culpa labore non elit sunt est ut deserunt minim. Exercitation occaecat esse voluptate officia elit aliquip labore excepteur deserunt cupidatat reprehenderit consectetur cillum nostrud.</div>

	<script src="js/main.js"></script>
</body>
</html>