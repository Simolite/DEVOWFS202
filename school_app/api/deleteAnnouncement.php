<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!in_array($_SESSION['role'], ['teacher', 'admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
require_once 'functions.php';



if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing id']);
    exit;
}

dellAnnouncement($conn,$_GET['id']);


echo json_encode(200);
?>