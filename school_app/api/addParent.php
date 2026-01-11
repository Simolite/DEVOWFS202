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

if (!isset($input['fname']) || empty($input['fname'])) {
    echo json_encode(['error' => 'Invalid or missing fname']);
    exit;
}
if (!isset($input['lname']) || empty($input['lname'])) {
    echo json_encode(['error' => 'Invalid or missing lname']);
    exit;
}
if (!isset($input['email']) || empty($input['email'])) {
    echo json_encode(['error' => 'Invalid or missing email']);
    exit;
}
if (!isset($input['phone']) || empty($input['phone'])) {
    echo json_encode(['error' => 'Invalid or missing phone']);
    exit;
}


$fname = $input['fname'];
$lname = $input['lname'];
$email = $input['email'];
$phone = $input['phone'];

if(addParent($conn, $fname, $lname, $email, $phone)){
    echo json_encode(['success' => 'Parent added successfully']);
} else {
    echo json_encode(['error' => 'Failed to add parent']);

}
?>
