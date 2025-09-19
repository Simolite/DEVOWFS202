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


if (!isset($_GET['audience'])) {
    echo json_encode(['error' => 'Invalid or missing audience']);
    exit;
}

$audience = $_GET['audience'];

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $Announcements = getAnnouncements($conn,$audience,$class_id);
}else{
    $Announcements = getAnnouncements($conn,$audience);
}

echo json_encode($Announcements);
?>
