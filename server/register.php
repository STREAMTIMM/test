<?php
header('Content-Type: application/json');
require_once '../conn/db.php';
$db = new DatabaseHandler();

// Get the JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['email'], $input['username'], $input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$email = trim($input['email']);
$username = trim($input['username']);
$password = trim($input['password']);

// Additional validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

try {
    // Check if email already exists
    $user = $db->fetchOne("SELECT id FROM users WHERE email = :email", ['email' => $email]);

    if (!empty($user)) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit;
    }

    // Insert new user
    $db->insert('users', [
        'name' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'token' => $token
    ]);

    if($db->loginUserSecure($email, $password))
    {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    }else{
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }


} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
