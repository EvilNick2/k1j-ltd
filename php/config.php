<?php
$DATABASE_HOST = "localhost";
$DATABASE_USER = "root";
$DATABASE_PASS = "";
$DATABASE_NAME = "k1j_ltd";

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS);

$sql = "CREATE DATABASE IF NOT EXISTS " . $DATABASE_NAME;

function console_log($message) {
    echo "<script>console.log(" . json_encode($message) . ");</script>";
}

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

function sendSQLQuery($conn, $sqlQuery, $successMessage, $errorMessage) {
	if ($conn->query($sqlQuery) === TRUE) {
		console_log("{$successMessage}");
	} else {
		console_log("{$errorMessage}: " . $conn->error);
	}
}

$sql = "CREATE TABLE IF NOT EXISTS users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(30) NOT NULL UNIQUE,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	email VARCHAR(50) NOT NULL UNIQUE,
	address VARCHAR(50) NOT NULL
)";
sendSQLQuery($conn, $sql, "Table users created successfully", "Error creating users table");

$sql = "CREATE TABLE IF NOT EXISTS employees (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(30) NOT NULL UNIQUE,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(255),
	email VARCHAR(50) NOT NULL UNIQUE,
	address VARCHAR(50) NOT NULL,
	rank VARCHAR(40) NOT NULL
)";
sendSQLQuery($conn, $sql, "Table employees created successfully", "Error creating users table");
?>