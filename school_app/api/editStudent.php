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

if (!isset($input['Fname']) || !isset($input['Lname']) || !isset($input['bd']) || !isset($input['sex'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
$studentId = intval($input['id']);
$Fname = $input['Fname'];
$Lname = $input['Lname'];
$bd = $input['bd'];
$sex = $input['sex'];

editStudent($conn,$studentId, $Fname, $Lname, $bd, $sex);

echo json_encode(['success' => true]);;
?>