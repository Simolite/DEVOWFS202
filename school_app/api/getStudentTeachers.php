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



$teachers = getStudentTeachers($conn, $_SESSION['linked_id']);

echo json_encode($teachers);
?>
