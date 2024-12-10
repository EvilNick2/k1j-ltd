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
      <form action="../php/authenticate.php" method="post" autocomplete="off">
        <label for="username">
          <i class="fas fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>
        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>
				<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
						<p style="color: red; text-align: center;">Incorrect username and/or password!</p>
				<?php endif; ?>
        <input type="submit" value="Login">
        </form>
        <form action="register.php" method="get">
            <input type="submit" value="Register">
        </form>
    </div>
	<script src="../js/main.js"></script>
</body>
</html>