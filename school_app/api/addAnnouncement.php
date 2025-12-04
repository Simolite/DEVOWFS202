<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php'; 


$input = json_decode(file_get_contents("php://input"), true);

$title    = $input['title']    ?? null;
$body     = $input['body']     ?? null;
$audience = $input['audience'] ?? null;
$id       = $input['id']       ?? null; 

if (!$title || !$body || !$audience) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit;
}


$date = (new DateTime())->format("Y-m-d H:i:s");


$ok = addAnnouncement($conn, $title, $body, $date, $audience, $id);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Announcement added successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Database insert failed']);
}
?>
