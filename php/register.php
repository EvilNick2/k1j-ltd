<?php
require_once 'config.php';

session_start();

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

if ($conn->connect_error) {
	die("COnnection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$name = $_POST['name'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, name, email, password) VALUES ('$user', '$name', '$email', '$pass')";

if ($conn->query($sql) === TRUE) {
	echo "Registration successful";

	header("Location: login.php");
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>