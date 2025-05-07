<?php
header('Content-Type: application/json');
require_once '../conn/db.php';

$db = new DatabaseHandler();

// Get raw input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['group_id'], $input['start'], $input['title'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

$group_id = $input['group_id'];
$start = $input['start'];

// Check if the event already exists for the same group and date
$existing = $db->fetchAll("SELECT * FROM events WHERE group_id = :group_id AND start = :start", [
    'group_id' => $group_id,
    'start' => $start,
]);

if ($existing) {
    echo json_encode(['success' => false, 'error' => 'An event already exists on this date for this group.']);
    exit;
}

// Insert new event
$db->insert('events', [
    'group_id' => $group_id,
    'start' => $start,
    'end' => $start, // Default end same as start
    'title' => $input['title'],
]);

echo json_encode(['success' => true, 'message' => 'Event added successfully.']);
