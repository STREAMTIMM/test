<?php
require_once '../conn/db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['title'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$db = new DatabaseHandler();
$db->update('events', ['title' => $data['title']], ['id' => $data['id']]);

echo json_encode(['success' => true]);
