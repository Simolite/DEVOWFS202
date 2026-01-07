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

if(isset($_GET['id'])){
    $id = $_GET['id'];
}else {
    echo json_encode(['error' => 'Missing id parameter']);
    exit;
}


$result = markBugAsSolved($conn,$id);

if ($result) {
    echo json_encode(['success' => 'bug marked as solved successfully']);
    exit;
} else {
    echo json_encode(['error' => 'Failed to mark bug as solved']);
    exit;
}
?>