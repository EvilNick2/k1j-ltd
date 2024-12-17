<?php
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['password_confirmed']) || !$_SESSION['password_confirmed']) {
    header('Location: ../html/profile.php');
    exit;
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('UPDATE users SET username = ?, name = ?, email = ?, address = ?, password = ? WHERE id = ?');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sssssi', $username, $name, $email, $address, $hashedPassword, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['password_confirmed']);

    header('Location: profile.php');
    exit;
}
?>