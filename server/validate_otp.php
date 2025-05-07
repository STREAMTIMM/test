<?php
require_once '../conn/db.php';  // assuming your class is saved like this
$db = new DatabaseHandler();

$email = $_POST['email'] ?? '';
$otp = $_POST['otp'] ?? '';

$user = $db->fetchOne("SELECT * FROM users WHERE email = :email AND otp = :otp", [
    'email' => $email,
    'otp' => $otp
]);

if ($user) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
