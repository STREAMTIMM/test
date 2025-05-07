<?php
require_once '../conn/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['id'] ?? null;
$group_id = $_POST['group_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$user_id || !$group_id || ($message === '' && empty($_FILES['files']))) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$db = new DatabaseHandler();

$message_id = null;

// ✅ Always insert a message record, even if empty (if files are uploaded)
if ($message !== '' || !empty($_FILES['files'])) {
    $message_id = $db->insert('group_messages', [
        'user_id' => $user_id,
        'group_id' => $group_id,
        'message' => $message,
        'status' => 1
    ]);

    if (!$message_id) {
        echo json_encode(['success' => false, 'error' => 'Failed to insert message']);
        exit;
    }
}

// ✅ Handle file uploads
if (!empty($_FILES['files']) && $message_id) {
    $upload_dir = '../uploads/group_files/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['files']['name'][$key]);
        $file_path = $upload_dir . uniqid() . "_" . $file_name;

        if (move_uploaded_file($tmp_name, $file_path)) {
            $db->insert('group_files', [
                'group_id' => $group_id,
                'user_id' => $user_id,
                'message_id' => $message_id, // ✅ link file to message
                'file_name' => $file_name,
                'file_path' => $file_path,
                'file_type' => $_FILES['files']['type'][$key],
                'status' => 1
            ]);
        }
    }
}

echo json_encode(['success' => true]);
