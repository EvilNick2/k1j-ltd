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
		$brand = $json[$i]['brand'];
		$name = $json[$i]['name'];
		$price = $json[$i]['price'];
		$category = $json[$i]['category'];
		$description = $json[$i]['description'];
		$stock = $json[$i]['stock'];
		$stocked = $json[$i]['stocked'];
		$created_at = DateTime::createFromFormat('d/m/Y H:i:s', $json[$i]['created_at'])->format('Y-m-d H:i:s');
		$updated_at = DateTime::createFromFormat('d/m/Y H:i:s', $json[$i]['updated_at'])->format('Y-m-d H:i:s');

		$stmt = $conn->prepare("INSERT INTO products (brand, name, description, price, category, stock, stocked, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('sssssssss', $brand, $name, $description, $price, $category, $stock, $stocked, $created_at, $updated_at);

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
	<link rel="stylesheet" href="../css/products.css">
	<link rel="icon" href="../imgs/logo.svg" type="image/svg">
	<title>Products</title>
</head>
<body>
	<?php include 'nav.php'; ?>
	
	<div class="container">
		<h1 class="heading-lv1">Products</h1>

		<div class="table-app" id="product-table-app">
			<div class="table-handler">
				<div class="table-handler-dropdown-cell">
					<div class="dropdown">
						<h3 class="dropdown-heading">
							<i class="fas fa-filter"></i> Brand
						</h3>
						<select class="select js-handle-table js-filter" id="filter-brand">
							<option value="all">All</option>
						</select>
					</div>
				</div>

				<div class="table-handler-dropdown-cell">
					<div class="dropdown">
						<h3 class="dropdown-heading">
							<i class="fas fa-filter"></i> Category
						</h3>
						<select class="select js-handle-table js-filter" id="filter-category">
							<option value="all">All</option>
						</select>
					</div>
				</div>

				<div class="table-handler-dropdown-cell">
					<div class="dropdown">
						<h3 class="dropdown-heading">
							<i class="fas fa-sort-amount-up-alt"></i> Sort by
						</h3>
						<select class="select js-handle-table" id="sort-by">
							<option value="none">-</option>
							<option value="price">Price</option>
							<option value="created_at">Created at</option>
							<option value="updated_at">Updated at</option>
						</select>
					</div>
				</div>

				<div class="table-handler-dropdown-cell">
					<input
						type="checkbox"
						class="js-handle-table"
						id="toggle"
						value="hiding-out-of-stock"
					/>
					<label for="toggle">Hiding out-of-stock products</label>
				</div>
			</div>

			<div class="table-wrapper">
				<table class="table" id="table">
					<thead>
						<tr class="table-head">
							<th class="table-cell align-right">ID</th>
							<th class="table-cell align-left">Brand</th>
							<th class="table-cell align-left">Name</th>
							<th class="table-cell align-left">Category</th>
							<th class="table-cell align-right">Price</th>
							<th class="table-cell align-left">Status</th>
							<th class="table-cell align-left">Stock</th>
							<th class="table-cell align-left">Created at</th>
							<th class="table-cell align-left">Updated at</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>

				<div class="no-results hidden" id="no-results">
					<p class="no-results-message">No results found.</p>
				</div>
			</div>

			<div class="pagination-controls">
				<button class="button js-prev-page">Prev</button>
				
				<span class="page-info">
					Page <span class="js-current-page">1</span> of <span class="js-total-pages">1</span>
				</span>
				
				<button class="button js-next-page">Next</button>
				
				<input type="number" class="pagination-input js-page-input" min="1" placeholder="Go to page"/>

				<button class="button js-go-page">Go</button>
			</div>
		</div>
	</div>


	<script src="../js/jquery.min.js"></script>
	<script src="../js/products.js"></script>
	<script src="../js/main.js"></script>
</body>
</html>