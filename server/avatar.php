<?php 
header('Content-Type: application/json');
require_once '../conn/db.php';
$db = new DatabaseHandler();
$input = json_decode(file_get_contents('php://input'), true);


if (!isset($input['avatar'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$avatar = $input['avatar'];
$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}
// Update the user's avatar
$updateSuccess = $db->update('users', ['profile' => $avatar], ['id' => $userId]);

if ($updateSuccess) {
    echo json_encode(['success' => true, 'message' => 'Avatar updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update avatar.']);
}
