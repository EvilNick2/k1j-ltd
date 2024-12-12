<?php
include '../php/config.php';

session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}

$range = 100;

$stmt = $conn->prepare("SELECT password, email, address FROM users WHERE id = ?");
$stmt->bind_param("s", $_SESSION["id"]);
$stmt->execute();
$stmt->bind_result($password, $email, $address);
$stmt->fetch();

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_users'])) {
	$randomUser = "user" . rand(1, $range);
	$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE username = '$randomUser'");
	$row = $result->fetch_assoc();

	if ($row['count'] == 0) {
		for ($i = 1; $i <= $range; $i++) {
			$randomUsername = "user$i";
			$randomName = "User $i";
			$randomEmail = "user$i@example.com";
			$randomAddress = "Address $i";
			$randomPassword = password_hash("password$i", PASSWORD_DEFAULT);

			$sql = "INSERT INTO users (username, name, email, address, password) VALUES ('$randomUsername', '$randomName', '$randomEmail', '$randomAddress', '$randomPassword')";
			if ($conn->query($sql) === TRUE) {
				console_log("User $i created successfully");
			} else {
				console_log("Error creating user $i: " . $conn->error);
			}
		}
	} else {
		console_log("Users already exist in the database");
	}
}

$conn->close();
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
		<div class="content">
			<h2>Profile Page</h2>
			<div>
					<p>Your account details are below:</p>
					<table>
							<tr>
									<td>Username:</td>
									<td><?=htmlspecialchars($_SESSION['username'], ENT_QUOTES)?></td>
							</tr>							
							<tr>
									<td>Name:</td>
									<td><?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?></td>
							</tr>
							<tr>
									<td>Password:</td>
									<td><?=htmlspecialchars($password, ENT_QUOTES)?></td>
							</tr>
							<tr>
									<td>Email:</td>
									<td><?=htmlspecialchars($email, ENT_QUOTES)?></td>
							</tr>
							<tr>
								<td>Address:</td>
								<td><?=htmlspecialchars($address, ENT_QUOTES)?></td>
							</tr>
					</table>
						<form method="post">
              <button type="submit" name="generate_users">Generate <?php echo $range ?> Users</button>
            </form>
			</div>
	</div>
	<script src="../js/main.js"></script>
</body>
</html>