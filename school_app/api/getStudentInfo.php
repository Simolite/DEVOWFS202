<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo json_encode(['error' => 'Missing student ID']);
    exit;
}

$studentInfo = getStudentInfo($conn,$id);


echo json_encode($studentInfo);
?>