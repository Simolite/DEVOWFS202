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




$id = $_SESSION['linked_id'];

$role = $_SESSION['role'];

$Announcements = getAnnouncements($conn,$id,$role);


echo json_encode($Announcements);
?>
