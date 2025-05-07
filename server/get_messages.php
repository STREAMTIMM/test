<?php
require_once '../conn/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    echo json_encode(['success' => false, 'error' => 'Missing group_id']);
    exit;
}

$db = new DatabaseHandler();

// Fetch messages with optional files
$messages = $db->fetchAll("
    SELECT 
        gm.id, 
        gm.message, 
        gm.created_at, 
        gm.user_id, 
        u.name, 
        u.profile,
        gf.file_path as file_name
    FROM group_messages gm
    JOIN users u ON u.id = gm.user_id
    LEFT JOIN group_files gf ON gf.message_id = gm.id
    WHERE gm.group_id = :group_id AND gm.status = 1
    ORDER BY gm.created_at ASC
", ['group_id' => $group_id]);

echo json_encode([
    'success' => true,
    'messages' => $messages
]);
