<?php
include "../php/config.php";
session_start();

$currentPage = 'dashboard';

$analyticsData = [];
$userCount = 0;

if (isset($_SESSION['loggedin'])) {
	$username = $_SESSION['username'];

	$sql = "SELECT username, last_login, login_count FROM user_analytics WHERE username = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $analyticsData = $result->fetch_assoc();
    }

	if (in_array($_SESSION['rank'], ['Employee', 'Supervisor', 'Manager', 'Director'])) {
		$sql = "SELECT COUNT(*) as user_count FROM users";
		$userResult = $conn->query($sql);
		if ($userResult->num_rows > 0) {
			$userRow = $userResult->fetch_assoc();
			$userCount = $userRow['user_count'];
		}
		$sql = "SELECT COUNT(*) as product_count FROM products";
		$productResult = $conn->query($sql);
		if ($productResult->num_rows) {
			$productRow = $productResult->fetch_assoc();
			$productCount = $productRow['product_count'];
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
	<title>Dashboard</title>
</head>
<body>
	<?php include 'nav.php'; ?>

	<div class="content">
		<h2>Analytics</h2>

		<div class="card">
			<h3>Account Details:</h3>
			<?php if ($analyticsData): ?>
				<p><strong>Username:</strong> <?= htmlspecialchars($analyticsData['username'], ENT_QUOTES) ?> </p>
				<p><strong>User Rank:</strong> <?= htmlspecialchars($_SESSION['rank'], ENT_QUOTES) ?> </p>
				<p><strong>Last Login:</strong> <?= htmlspecialchars($analyticsData['last_login'], ENT_QUOTES) ?> </p>
				<p><strong>Login Count:</strong> <?= htmlspecialchars($analyticsData['login_count'], ENT_QUOTES) ?> </p>
			<?php endif; ?>

		</div>
		<div class="card">
			<h3>Users Information:</h3>
			<?php if ($userCount > 0): ?>
				<p><strong>Total Registered Users:</strong> <?= htmlspecialchars($userCount, ENT_QUOTES) ?></p>
			<?php endif; ?>
		</div>
		<div class="card">
			<h3>Product Information:</h3>
			<?php if ($productCount > 0): ?>
				<p><strong>Total Product Amount:</strong> <?= htmlspecialchars($productCount, ENT_QUOTES) ?></p>
			<?php endif; ?>
		</div>
	</div>

	<script src="../js/main.js"></script>
</body>
</html>