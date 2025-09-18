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

$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

if (!isset($_GET['role'])) {
    echo json_encode(['error' => 'Missing role']);
    exit;
}
$role = $_GET['role'];

if (!isset($_GET['user_id'])) {
    $user_id = 0;
}else{
    $user_id = $_GET['user_id'];
};

$accounts = getAccount($conn,$role,$user_id);


echo json_encode($accounts);
?>