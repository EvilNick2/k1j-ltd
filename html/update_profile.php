<?php
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['password_confirmed']) || !$_SESSION['password_confirmed']) {
    header('Location: ../html/profile.php');
    exit;
}

$currentPage = 'update_profile';

include '../php/config.php';
$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$address = $_POST['address'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

	$currentUserID = $_SESSION['id'];

	$usernameQuery = "SELECT id FROM users WHERE username = ? AND id != ?";
	$usernameStmt = $conn->prepare($usernameQuery);
	$usernameStmt->bind_param("si", $username, $currentUserID);
	$usernameStmt->execute();
	$usernameStmt->store_result();

	if ($usernameStmt->num_rows > 0) {
		$message = "Username already exists";
		$messageType = "failure";
	} else {
		$emailQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
		$emailStmt = $conn->prepare($emailQuery);
		$emailStmt->bind_param("si", $email, $currentUserID);
		$emailStmt->execute();
		$emailStmt->store_result();

		if ($emailStmt->num_rows > 0) {
			$message = "Email already exists";
			$messageType = "failure";
		} else {
			$stmt = $conn->prepare('UPDATE users SET username = ?, name = ?, email = ?, address = ?, password = ? WHERE id = ?');
			$stmt->bind_param('sssssi', $username, $name, $email, $address, $password, $currentUserID);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$message = "Email doesn't have the right format";
				$messageType = "failure";
			} elseif ($stmt->execute()) {
				$message = "Account successfully updated";
				$messageType = "success";

				unset($_SESSION['password_confirmed']);
				session_destroy();
				header('Location: ../html/login.php');
			exit;
			} else {
				$message = "Error: " . $stmt->error;
				$messageType = "failure";
			}
			$stmt->close();
		}
		$emailStmt->close();
	}
	$usernameStmt->close();
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
					<input type="password" name="password" id="password" required>
					<i class="fa fa-eye-slash" id="togglePassword"></i>
				</div>
				<input type="submit" value="Submit Change">
			</form>
			<?php if ($messageType === "success"): ?>
				<?php header("Location: login.php"); ?>
			<?php elseif ($messageType === "failure"): ?>
				<style>
				.register form input[type="submit"] {
					border-radius: 0;
				}
				</style>
				<div class="dialog-warning"><?php echo $message ?></div>
			<?php endif; ?>
		</div>
	</div>
	<script src="../js/main.js"></script>
</body>
</html>