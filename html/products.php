<?php
session_start();

include '../php/config.php';

$currentPage = 'products';

if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}

if (!in_array($_SESSION['rank'], ['Employee', 'Supervisor', 'Manager', 'Director'])) {
	header('Location: ../index.php');
	exit;
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
	<title>Products</title>
</head>
<body>
	<?php include 'nav.php'; ?>
</body>
</html>