<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

if ($_SESSION['role'] === 'student' || !isset($_GET['student_id']) || !is_numeric($_GET['student_id'])){
    $student_id = $_SESSION['linked_id'];
}else {
    $student_id = $_GET['student_id'];
}

$subjects = getStudentSubjects($conn, $student_id);

echo json_encode($subjects);
?>