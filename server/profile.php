<?php
header('Content-Type: application/json');
require_once '../conn/db.php';

$db = new DatabaseHandler();

// Get the JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Get the user ID from session
$user_id = $_SESSION['id'];

// Update the user status if needed (assuming you want to deactivate the user)
if (isset($input['id'])) {
    $db->update('users', [
        'status' => 0
    ], [
        'id' => $user_id,
        'group_id' => $input['id'],
    ]);
}

// Check if the request contains the necessary data (username)
if (isset($input['username'])) {
    $new_username = htmlspecialchars(trim($input['username']));

    // Validate that the username is not empty
    if (empty($new_username)) {
        echo json_encode(['success' => false, 'message' => 'Username cannot be empty']);
        exit();
    }

    // Use the update method from the DatabaseHandler class to update the username
    $updateData = ['name' => $new_username];
    $conditions = ['id' => $user_id];
    
    $updateSuccess = $db->update('users', $updateData, $conditions);

    if ($updateSuccess) {
        // Update session with the new username
        $_SESSION['name'] = $new_username;

        // Send a success response back to the frontend
        echo json_encode(['success' => true, 'message' => 'Username updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update name']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
