<?php
require_once "config.php"; // Include the configuration file for database credentials

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

function sendSQLQuery($conn, $sqlQuery, $successMessage, $errorMessage) {
	if ($conn->query($sqlQuery) === TRUE) {
		echo "{$successMessage}<br>";
	} else {
		echo "{$errorMessage}: " . $conn->error; 
	}
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DATABASE_NAME;
sendSQLQuery($conn, $sql, "Database created successfully", "Error creating database");

$conn->select_db(DATABASE_NAME);

$sql = "CREATE TABLE IF NOT EXISTS users (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(30) NOT NULL UNIQUE,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	email VARCHAR(50) NOT NULL UNIQUE
)";
sendSQLQuery($conn, $sql, "Table users created successfully", "Error creating users table");

$conn->close();
?>