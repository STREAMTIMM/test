<?php
header('Content-Type: application/json');
require_once '../conn/db.php';
$db = new DatabaseHandler();

// Get the JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['email'], $input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$email = trim($input['email']);
$password = trim($input['password']);

// Additional validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {

    if($db->loginUserSecure($email, $password))
    {
        echo json_encode(['success' => true, 'message' => 'Login successful!']);
    }else{
        echo json_encode(['success' => false, 'message' => 'Login failed.']);
    }


} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
