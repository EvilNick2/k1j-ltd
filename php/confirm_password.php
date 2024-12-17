<?php
session_start();
header('Content-Type: application/json');

ob_start();

include 'config.php';

$response = ['success' => false, 'message' => 'An error occurred'];

if (!isset($_SESSION['loggedin'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    ob_end_clean();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmPassword = $_POST['confirm-password'];

    $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('s', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($password);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($confirmPassword, $password)) {
        $_SESSION['password_confirmed'] = true;
        $response['success'] = true;
        $response['message'] = 'Password confirmed';
    } else {
        $response['message'] = 'Password confirmation failed';
    }

    $conn->close();
} else {
    $response['message'] = 'Invalid request method';
}

ob_end_clean();
echo json_encode($response);
?>