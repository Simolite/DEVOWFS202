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

if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    echo json_encode(['error' => 'Invalid or missing class_id']);
    exit;
}

$class_id = $_GET['class_id'];

$students = getClassStudents($conn, $class_id);

echo json_encode($students);

?>