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

$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

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

if (!isset($_POST['stat'])) {
    echo json_encode(['error' => 'Missing stat']);
    exit;
}
$stat = $_POST['stat'];


if (!isset($_POST['date'])) {
    echo json_encode(['error' => 'Missing date']);
    exit;
}

$date = $_POST['date'];

addAtt($conn,$student_id,$subject_id,$stat,$date);

echo json_encode("Done!");


?>