<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type, Accept');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح لك بهذا الإجراء']);
    exit;
}

require_once 'functions.php';

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'بيانات غير صالحة']);
    exit;
}

$required_fields = ['fname', 'lname', 'email', 'sex', 'birthdate', 'class_id', 'pfname', 'plname', 'phone'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        $errors[] = "الحقل $field مطلوب";
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
    exit;
}


$fname = trim($input['fname']);
$lname = trim($input['lname']);
$email = trim($input['email']);
$sex = trim($input['sex']);
$birth_date = trim($input['birthdate']);
$class_id = trim($input['class_id']);
$pfname = trim($input['pfname']);
$plname = trim($input['plname']);
$phone = trim($input['phone']);

try {
    $result = addStudent($conn, $fname, $lname, $email, $sex, $birth_date, $class_id, $pfname, $plname, $phone);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'تمت إضافة الطالب بنجاح']);
    } else {
        echo json_encode(['success' => false, 'error' => 'فشل في إضافة الطالب']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'خطأ: ' . $e->getMessage()]);
}
?>