<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';



$teachers = getTeachers($conn);


echo json_encode($teachers);
?>