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


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Invalid or missing id']);
    exit;
}


$id = intval($_GET['id']);

if(deleteParent($conn,$id)){
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to delete parent']);

}

exit;

?>

