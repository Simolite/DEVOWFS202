<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';



if (!isset($_POST['student_id']) || !is_numeric($_POST['student_id'])) {
    echo json_encode(['error' => 'Invalid or missing student_id']);
    exit;
}
$student_id = intval($_POST['student_id']);

if (!isset($_POST['subject_id']) || !is_numeric($_POST['subject_id'])) {
    echo json_encode(['error' => 'Invalid or missing subject_id']);
    exit;
}
$subject_id = intval($_POST['subject_id']);

if (!isset($_POST['mark'])) {
    echo json_encode(['error' => 'Invalid or missing mark']);
    exit;
}
$mark = trim($_POST['mark']);

if (!isset($_POST['term']) || !is_numeric($_POST['term'])) {
    echo json_encode(['error' => 'Invalid or missing term']);
    exit;
}
$term = intval($_POST['term']);

if (!isset($_POST['date'])) {
    echo json_encode(['error' => 'Missing date']);
    exit;
}

$date = $_POST['date'];

addMark($conn,$student_id,$subject_id,$mark,$term,$date);

echo json_encode("Done!");


?>