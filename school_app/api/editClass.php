<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'functions.php';



if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing id']);
    exit;
}elseif (!isset($_GET['name'])) {
    echo json_encode(['error' => 'Missing name']);
    exit;
}elseif (!isset($_GET['timetable_url'])) {
    echo json_encode(['error' => 'Missing timetable_url']);
    exit;
}
$id = $_GET['id'];
$name = $_GET['name'];
$timetable_url = $_GET['timetable_url'];


editClass($conn, $id, $name, $timetable_url);

echo json_encode(200);
?>