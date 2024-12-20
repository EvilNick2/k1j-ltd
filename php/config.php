<?php
$DATABASE_HOST = "localhost";
$DATABASE_USER = "root";
$DATABASE_PASS = "";
$DATABASE_NAME = "k1j_ltd";

function console_log($message) {
    echo "<script>console.log(" . json_encode($message) . ");</script>";
}

function sendSQLQuery($conn, $sqlQuery, $successMessage, $errorMessage) {
	if ($conn->query($sqlQuery) === TRUE) {
		console_log("{$successMessage}");
	} else {
		console_log("{$errorMessage}: " . $conn->error);
	}
}

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS);

$sql = "CREATE DATABASE IF NOT EXISTS " . $DATABASE_NAME;

if ((mysqli_query($conn, $sql))) {
  if (mysqli_warning_count($conn) == 0) {
		console_log("Database created successfully");

  }
} else {
	console_log("Error creating database: " . mysqli_error($mysql));
}

$conn ->close();

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(30) NOT NULL UNIQUE,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	email VARCHAR(50) NOT NULL UNIQUE,
	address VARCHAR(50) NOT NULL,
  rank ENUM('Customer', 'Employee', 'Supervisor', 'Manager', 'Director') NOT NULL DEFAULT 'Customer'
)";
sendSQLQuery($conn, $sql, "Table users created successfully", "Error creating users table");

$sql = "SELECT * FROM users WHERE username = 'Jimmi'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
	$directorPassword = password_hash('password', PASSWORD_DEFAULT);
	$sql = "INSERT INTO users (username, name, password, email, address, rank) VALUES (
		'Jimmi', 'Jimmi Browning', '$directorPassword', 'jimmi@example.com', '4 Privet Drive', 'Director'
	)";
	sendSQLQuery($conn, $sql, "Default Director user created successfully", "Error creating default Director user");
} else {
	console_log("Default Director user already exists");
}

$rankHierarchy = [
	'Customer' => 1,
	'Employee' => 2,
	'Supervisor' => 3,
	'Manager' => 4,
	'Director' => 5
];

$sql = "CREATE TABLE IF NOT EXISTS products (
	product_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	description TEXT,
	price DECIMAL(10, 2),
	category VARCHAR(255),
	stock INT UNSIGNED
)";
sendSQLQuery($conn, $sql, "Products table created successfully", "Error creating products table");
?>