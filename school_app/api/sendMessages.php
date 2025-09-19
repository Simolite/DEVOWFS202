<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';

if (!isset($_POST['reciver_id'])) {
    echo json_encode(['error' => 'Invalid or missing reciver_id']);
    exit;
}

$reciver_id = $_POST['reciver_id'];

if (!isset($_POST['reciver_role'])) {
    echo json_encode(['error' => 'Invalid or missing reciver_role']);
    exit;
}

$reciver_role = $_POST['reciver_role'];

if (!isset($_POST['message'])) {
    echo json_encode(['error' => 'Invalid or missing message']);
    exit;
}

$message = $_POST['message'];


if (!isset($_POST['title'])) {
    echo json_encode(['error' => 'Invalid or missing title']);
    exit;
}

$title = $_POST['title'];

if (!isset($_POST['type'])) {
    echo json_encode(['error' => 'Invalid or missing type']);
    exit;
}

$type = $_POST['type'];

$date = date("Y-m-d H:i:s");


$sender_id = $_SESSION['linked_id'];

$sender_role = $_SESSION['role'];


$result = sendMessages ($conn,$reciver_id,$reciver_role,$message,$title,$type,$date,$sender_id,$sender_role);

echo json_encode(200);
?>