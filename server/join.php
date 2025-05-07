<?php
header('Content-Type: application/json');
require_once '../conn/db.php';
$db = new DatabaseHandler();
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing action type.']);
    exit;
}
$user_id = $_SESSION['id'];


// Get raw input
$input = json_decode(file_get_contents('php://input'), true);

// Ensure `type` is provided
if (!isset($input['type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing action type.']);
    exit;
}

$type = $input['type'];

try {
    switch ($type) {
        case 'join':
            if (!isset($input['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group data.']);
                exit;
            }

            $db->insert('student_groups', [
                'user_id' => $user_id,
                'group_id' => $input['id'],
            ]);

            echo json_encode(['success' => true, 'message' => 'Joined successfully.']);
            break;
        case 'leave':
            if (!isset($input['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group data.']);
                exit;
            }
            $db->update('student_groups', [
                'status' =>  0
            ], [
                'user_id' => $user_id,
                'group_id' => $input['id'],
            ]);


            echo json_encode(['success' => true, 'message' => 'Leaved successfully.']);
            break;


        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action type.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
