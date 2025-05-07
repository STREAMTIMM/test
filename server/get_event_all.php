<?php
require_once '../conn/db.php';  // Make sure to include the correct database connection file
header('Content-Type: application/json');

// Check for the GET request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  echo json_encode(['success' => false, 'error' => 'Invalid request method']);
  exit;
}

// Assuming DatabaseHandler is a class you use to handle DB interactions
$db = new DatabaseHandler();

// Fetch the events for the given group_id
$events = $db->fetchAll("
  SELECT a.start,a.end,CONCAT('Group ' ,c.name,' has event  ',a.title) as title FROM EVENTS AS a 
JOIN student_groups AS b 
ON a.group_id = b.group_id
JOIN groups AS c 
ON c.id = b.group_id
WHERE b.status = 1 AND c.status = 1 

GROUP BY a.id
");

// Prepare the response
echo json_encode([
  'success' => true,
  'events' => $events // Return the events data
]);
