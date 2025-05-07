<?php
header('Content-Type: application/json');
require_once '../conn/db.php';
$db = new DatabaseHandler();

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
        case 'add':
            if (!isset($input['name'], $input['group_number'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group data.']);
                exit;
            }

            $db->insert('groups', [
                'name' => $input['name'],
                'group_number' => $input['group_number'],
                'status' => $input['status'] ?? 1
            ]);

            echo json_encode(['success' => true, 'message' => 'Group added successfully.']);
            break;

        case 'edit':
            if (!isset($input['id'], $input['name'], $input['group_number'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group data for edit.']);
                exit;
            }

            $db->update('groups', [
                'name' => $input['name'],
                'group_number' => $input['group_number'],
                'status' => $input['status'] ?? 1
            ], ['id' => $input['id']]);

            echo json_encode(['success' => true, 'message' => 'Group updated successfully.']);
            break;

        case 'delete':
            if (!isset($input['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group ID for deletion.']);
                exit;
            }

            $db->update('groups', [
                'status' => 0
            ], ['id' => $input['id']]);

            echo json_encode(['success' => true, 'message' => 'Group deleted successfully.']);
            break;
        case 'delete_lesson':
            if (!isset($input['id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing group ID for deletion.']);
                exit;
            }

            $db->update('group_lessons', [
                'status' => 0
            ], ['id' => $input['id']]);

            echo json_encode(['success' => true, 'message' => 'Lesson deleted successfully.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action type.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
