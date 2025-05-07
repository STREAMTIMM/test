<?php
require_once '../conn/db.php';  // assuming your class is saved like this
$db = new DatabaseHandler();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$db->update('users', ['password' => $hashedPassword, 'otp' => null], ['email' => $email]); // clear OTP

echo json_encode(['status' => 'success']);
?>
