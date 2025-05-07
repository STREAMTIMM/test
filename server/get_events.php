<?php
require_once '../conn/db.php';  // Make sure to include the correct database connection file
header('Content-Type: application/json');

// Check for the GET request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get the group_id from the query parameter
$group_id = $_GET['group_id'] ?? null;

// Check if group_id is provided
if (!$group_id) {
    echo json_encode(['success' => false, 'error' => 'Missing group_id']);
    exit;
}

// Assuming DatabaseHandler is a class you use to handle DB interactions
$db = new DatabaseHandler();

// Fetch the events for the given group_id
$events = $db->fetchAll("
    SELECT id, title, start, end FROM events WHERE group_id = :group_id
", ['group_id' => $group_id]);

// Prepare the response
echo json_encode([
    'success' => true,
    'events' => $events // Return the events data
]);
