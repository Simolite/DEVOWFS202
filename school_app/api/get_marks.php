<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Only allow authenticated roles
if (!in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Check required term
if (!isset($_GET['term']) || !is_numeric($_GET['term'])) {
    echo json_encode(['error' => 'Invalid or missing term']);
    exit;
}

// Determine student ID
if ($_SESSION['role'] === 'student') {
    $student_id = $_SESSION['linked_id'];
} else {
    if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
        echo json_encode(['error' => 'Invalid or missing student_id']);
        exit;
    }
    $student_id = $_GET['student_id'];
}

$term = intval($_GET['term']);

// Process subject(s)
if ($_GET['sub'] === 'all') {
    $subjects = getStudentSubjects($conn, $student_id);
} else {
    $subjectsRaw = $_GET['sub'];
    $subjects = json_decode($subjectsRaw, true);

    if (!is_array($subjects)) {
        echo json_encode(['error' => 'Invalid subject data']);
        exit;
    }
}

// Fetch marks
$marks = getStudentMarks($conn, $student_id, $subjects, $term);

// Return JSON
echo json_encode($marks);
?>
