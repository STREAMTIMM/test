<?php
require_once '../conn/db.php';  // assuming your class is saved like this
$db = new DatabaseHandler();

$email = $_POST['email'] ?? '';

$user = $db->fetchOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);

if ($user) {
    $otp = rand(100000, 999999); // generate 6-digit OTP
    $db->update('users', ['otp' => $otp], ['email' => $email]); // save OTP

    echo json_encode(['status' => 'success', 'otp' => $otp]);  // returning OTP for JS to send via emailjs
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email not found']);
}
?>
