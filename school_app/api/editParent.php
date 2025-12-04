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

if (!isset($input['parentFname']) || !isset($input['parentLname']) || !isset($input['parentPhone']) || !isset($input['parentEmail'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
$parentId = intval($input['id']);
$parentFname = $input['parentFname'];
$parentLname = $input['parentLname'];
$parentPhone = $input['parentPhone'];
$parentEmail = $input['parentEmail'];

editParent($conn,$parentId, $parentFname, $parentLname, $parentPhone, $parentEmail);
echo json_encode(['success' => true]);;
?>