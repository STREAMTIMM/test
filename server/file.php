<?php
header('Content-Type: application/json');
require_once '../conn/db.php';

$db = new DatabaseHandler();

// Make sure both file and group ID are present
if (!isset($_POST['id']) || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Missing file or group ID.']);
    exit;
}

$groupId = $_POST['id'];
$file = $_FILES['file'];

// Optional: Validate file (size/type check here if needed)
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = basename($file['name']);
$uniqueName = uniqid() . '_' . $fileName;
$targetPath = $uploadDir . $uniqueName;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Save file info in DB
    $db->insert('group_lessons', [
        'group_id' => $groupId,
        'file_path' => $targetPath
    ]);

    echo json_encode(['success' => true, 'message' => 'File uploaded successfully.', 'file' => $fileName]);
} else {
    echo json_encode(['success' => false, 'message' => 'File upload failed.']);
}
