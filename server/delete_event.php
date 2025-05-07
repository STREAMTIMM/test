<?php
require_once '../conn/db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing ID']);
    exit;
}

$db = new DatabaseHandler();
$db->delete('events', ['id' => $data['id']]);

echo json_encode(['success' => true]);
