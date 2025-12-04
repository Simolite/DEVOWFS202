<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';



if (!isset($_GET['name']) || empty($_GET['name'])) {
    echo json_encode(['error' => 'Invalid or missing class name']);
    exit;
}


$className = $_GET['name'];
if (createClass($conn, $className)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to create class']);
}
?>