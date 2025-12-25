<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

if($_GET['id']) {
    $class_id = intval($_GET['id']);
}else {
    echo json_encode(['status' => 'error', 'message' => 'Missing class_id']);
    exit;
}

deleteClass($conn, $class_id);

echo json_encode(['status' => 'success']);


?>