<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin'])) {
	header('Location:../html/login.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
	$stmt->bind_param('s', $_SESSION['id']);
	$stmt->execute();
	$stmt->bind_result($password);
	$stmt->fetch();
	$stmt->close();

	if (password_verify($_POST['confirm-password'], $password)) {
		$_SESSION['password_confirmed'] = TRUE;
		$_SESSION['message'] = "Password confirmed";
		header('Location: ../html/update_profile.php');
	} else {
		$_SESSION['message'] = "Password confirmation failed";
		header('Location: ../html/profile.php');
	}

	$conn->close();
} else {
	$_SESSION['message'] = "Invalid request method";
}
?>