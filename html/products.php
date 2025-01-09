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

$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
	$str = file_get_contents('../data/products.json');
	$json = json_decode($str, true);

	for ($i = 0; $i < count($json); $i++) { 
		$name = $json[$i]['name'];
		$price = $json[$i]['price'];
		$category = $json[$i]['category'];
		$sale_price = $json[$i]['sale_price'];

		$stmt = $conn->prepare("INSERT INTO products (name, description, price, category, stock) VALUES (?, ?, ?, ?, ?)");
		$description = "Sales Price: " . $sale_price;
		$stock = rand(1, 10000);
		$stmt->bind_param('sssss', $name, $description, $price, $category, $stock);

		if ($stmt->execute()) {
			console_log("Product $name inserted successfully");
		} else {
			console_log("Error inserting product $name: " . $stmt->error);
		}

		$stmt->close();
	}
} else {
	console_log("Products already exist in the database. No new products were inserted");
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
	<title>Products</title>
</head>
<body>
	<?php include 'nav.php'; ?>
	
	<script src="../js/main.js"></script>
</body>
</html>