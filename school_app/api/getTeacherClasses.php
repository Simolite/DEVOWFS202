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

if ($_SESSION['role'] === 'teacher') {
    $teacher_id = $_SESSION['linked_id'];
} else {
    if (!isset($_GET['teacher_id']) || !is_numeric($_GET['teacher_id'])) {
        echo json_encode(['error' => 'Invalid or missing teacher_id']);
        exit;
    }
    $teacher_id = $_GET['teacher'];
}

$classes = getTeacherClasses($conn, $teacher_id);

echo json_encode($classes);

?>