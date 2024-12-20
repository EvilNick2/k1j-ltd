<?php
include '../php/config.php';
$message = "";

$currentPage = 'register';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST["username"];
	$name = $_POST["name"];
	$email = $_POST["email"];
	$address = $_POST["address"];
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

	$existingEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
	$existingEmail->bind_param("s", $email);
	$existingEmail->execute();
	$existingEmail->store_result();

	if ($existingEmail->num_rows>0) {
		$message = "Email already exists";
	} else {
		$stmt = $conn->prepare("INSERT INTO users (username, name, email, address, password) VALUES (?,?,?,?,?)");
		$stmt->bind_param("sssss", $username, $name, $email, $address, $password);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$message = "Email doesn't have the right format";
		} elseif ($stmt->execute()) {
			$message = "Account created successfully";
		} else {
			$message = "Error: " . $stmt->error;
		}
		$stmt->close();
	}
	$existingEmail->close();
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
	<title>Register</title>
</head>
<body>
	<?php include 'nav.php'; ?>

	<div class="register">
		<h1>Register</h1>
		<form method="post" autocomplete="off">
			<label for="username">
				<i class="fas fa-user"></i>
			</label>
			<input type="text" name="username" placeholder="Username" id="username" required>
			<label for="name">
				<i class="fas fa-id-card"></i>
			</label>
			<input type="text" name="name" placeholder="John Doe" id="name" required>
			<label for="email">
				<i class="fas fa-envelope"></i>
			</label>
			<input class="email" name="email" placeholder="johndoe@gmail.com" id="email" required>
			<label for="address">
				<i class="fas fa-map-location-dot"></i>
			</label>
			<input type="text" name="address" placeholder="Address Line 1" id="address" required>
			<label for="password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" name="password" placeholder="Password" id="password" required>
			<input type="submit" value="Register">
		</form>
		<?php if ($message === "Account created successfully"): ?>
			<?php header("Location: login.php"); ?>
		<?php elseif ($message === "Email already exists" || $message === "Email doesn't have the right format"): ?>
			<style>
				.register form input[type="submit"] {
					border-radius: 0;
				}
			</style>
			<div class="dialog-warning"><?php echo $message ?></div>
		<?php endif; ?>
	</div>
	<script src="../js/main.js"></script>
</body>
</html>