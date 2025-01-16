<?php
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['password_confirmed']) || !$_SESSION['password_confirmed']) {
    header('Location: ../html/profile.php');
    exit;
}

$currentPage = 'update_profile';

include '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$address = $_POST['address'];
	$password = $_POST['password'];

	$stmt = $conn->prepare('UPDATE users SET username = ?, name = ?, email = ?, address = ?, password = ? WHERE id = ?');
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	$stmt->bind_param('sssssi', $username, $name, $email, $address, $hashedPassword, $_SESSION['id']);
	$stmt->execute();
	$stmt->close();

	unset($_SESSION['password_confirmed']);

	session_destroy();
	header('Location: ../html/login.php');
	exit;
}

$conn->close();
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
	<title>Update Profile</title>
</head>
<body>
	<?php include 'nav.php'; ?>

	<div class='content'>
		<div id="edit-profile-form" class="register">
			<h1>Edit Profile Information</h1>
			<form method="post" autocomplete="off">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" id="username" value="<?=htmlspecialchars($_SESSION["username"], ENT_QUOTES)?>" required>
				<label for="name">
					<i class="fas fa-id-card"></i>
				</label>
				<input type="text" name="name" id="name" value="<?=htmlspecialchars($_SESSION["name"], ENT_QUOTES)?>" required>
				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="text" name="email" id="email" value="<?=htmlspecialchars($_SESSION["email"], ENT_QUOTES)?>" required>
				<label for="address">
					<i class="fas fa-map-location-dot"></i>
				</label>
				<input type="text" name="address" id="address" value="<?=htmlspecialchars($_SESSION["address"], ENT_QUOTES)?>" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<div class="password-container">
					<input type="password" name="password" id="password">
					<i class="fa fa-eye-slash" id="togglePassword"></i>
				</div>
				<input type="submit" value="Submit Change">
			</form>
		</div>
	</div>
	<script src="../js/main.js"></script>
</body>
</html>