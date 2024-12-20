<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['rank'], ['Supervisor', 'Manager', 'Director'])) {
	header('Location: ../html/login.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'];
	$new_rank = $_POST['rank'];

	if (!array_key_exists($new_rank, $rankHierarchy)) {
		$_SESSION['message'] = "Invalid rank selected";
		header('Location: ../html/profile.php');
		exit;
	}

	$stmt = $conn->prepare("SELECT rank FROM users WHERE username = ?");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->bind_result($current_rank);
	$stmt->fetch();
	$stmt->close();

	if ($rankHierarchy[$_SESSION['rank']] < $rankHierarchy[$current_rank]) {
		$_SESSION['message'] = "You do not have permission to change the rank of {$username}";
		$_SESSION['message_type'] = "warning";
		header('Location: ../html/profile.php');
		exit;
	}

	if ($rankHierarchy[$_SESSION['rank']] < $rankHierarchy[$new_rank]) {
		$_SESSION['message'] = "You do not have permission to assign {$new_rank}";
		$_SESSION['message_type'] = "warning";
		header('Location: ../html/profile.php');
		exit;
	}

	$stmt = $conn->prepare("UPDATE users SET rank = ? WHERE username = ?");
	$stmt->bind_param('ss', $new_rank, $username);

	if ($stmt->execute()) {
		$_SESSION['message'] = "{$username}'s rank updated successfully";
		$_SESSION['message_type'] = "success";
	} else {
		$_SESSION['message'] = "Error updating {$username}'s rank";
		$_SESSION['message_type'] = "warning";
	}

	$stmt->close();
	$conn->close();

	header('Location: ../html/profile.php');
	exit;
}
?>