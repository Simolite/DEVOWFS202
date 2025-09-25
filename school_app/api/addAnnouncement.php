<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// ✅ Only admin can add announcements
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php'; // must define $conn and addAnnouncement()

// ✅ Handle JSON body (if request comes from fetch with JSON)
$input = json_decode(file_get_contents("php://input"), true);

$title    = $input['title']    ?? null;
$body     = $input['body']     ?? null;
$audience = $input['audience'] ?? null;
$id       = $input['id']       ?? null; // e.g. user_id (the admin who creates the announcement)

if (!$id || !$title || !$body || !$audience) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// ✅ Format date properly
$date = (new DateTime())->format("Y-m-d H:i:s");

// ✅ Call your helper function
$ok = addAnnouncement($conn, $title, $body, $date, $audience, $id);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Announcement added successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Database insert failed']);
}
?>
