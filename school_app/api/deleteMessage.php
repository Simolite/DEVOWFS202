<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

if (!in_array($_SESSION['role'], ['teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}


require_once 'functions.php';

if($_GET['id']) {
    $message_id = intval($_GET['id']);
}else {
    echo json_encode(['status' => 'error', 'message' => 'Missing Message id']);
    exit;
}

deleteMessage($conn, $message_id);

echo json_encode(['status' => 'success']);

?>