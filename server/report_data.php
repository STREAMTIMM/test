<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();

$data = json_decode(file_get_contents("php://input"), true);
$start = date('Y-m-d 00:00:00', strtotime($data['start_date']));
$end = date('Y-m-d 23:59:59', strtotime($data['end_date']));

// Fetch all groups (no filter needed)
$groups = $db->fetchAll("SELECT * FROM groups where status = 1 ");

// Fetch data within time range using named parameters
$files = $db->fetchAll(
    "SELECT * FROM group_files WHERE status = 1 AND uploaded_at BETWEEN :start AND :end",
    ['start' => $start, 'end' => $end]
);

$lessons = $db->fetchAll(
    "SELECT * FROM group_lessons WHERE status = 1 AND uploaded_at BETWEEN :start AND :end",
    ['start' => $start, 'end' => $end]
);

$messages = $db->fetchAll(
    "SELECT * FROM group_messages WHERE status = 1 AND created_at BETWEEN :start AND :end",
    ['start' => $start, 'end' => $end]
);

// Build summary
$summary = [
    'total_groups' => count($groups),
    'total_files' => count($files),
    'total_lessons' => count($lessons),
    'total_messages' => count($messages)
];

// Prepare data per group
$labels = [];
$filesData = [];
$lessonsData = [];
$messagesData = [];
$totals = [];

foreach ($groups as $group) {
    $groupId = $group['id'];
    $labels[] = $group['name'];

    $f = count(array_filter($files, fn($x) => $x['group_id'] == $groupId));
    $l = count(array_filter($lessons, fn($x) => $x['group_id'] == $groupId));
    $m = count(array_filter($messages, fn($x) => $x['group_id'] == $groupId));

    $filesData[] = $f;
    $lessonsData[] = $l;
    $messagesData[] = $m;
    $totals[] = $f + $l + $m;
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode([
    'summary' => $summary,
    'chartData' => [
        'labels' => $labels,
        'files' => $filesData,
        'lessons' => $lessonsData,
        'messages' => $messagesData,
        'totals' => $totals
    ]
]);
