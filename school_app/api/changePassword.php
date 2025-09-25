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

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id']) || empty($input['id'])) {
    echo json_encode(['error' => 'Invalid or missing id']);
    exit;
}
if (!isset($input['pass']) || empty($input['pass'])) {
    echo json_encode(['error' => 'Invalid or missing password']);
    exit;
}

$id = intval($input['id']);
$pass = $input['pass'];
if (changePassword($conn, $pass, $id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update password']);
}
?>
