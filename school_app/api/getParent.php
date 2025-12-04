<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

if(!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing parent id']);
    exit;
}

$parent = getParent($conn,$_GET['id']);

echo json_encode($parent);

?>