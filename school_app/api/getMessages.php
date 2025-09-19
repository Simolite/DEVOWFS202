<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['teacher', 'admin','student'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';


$messages = getMessages($conn, $_SESSION['linked_id'],$_SESSION['role']);

echo json_encode($messages);

?>