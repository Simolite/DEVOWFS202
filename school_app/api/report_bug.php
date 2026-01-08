<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 

header('Content-Type: application/json'); 


if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) { 
    echo json_encode(['error' => 'Unauthorized']); 
    exit; 
} 

require_once 'functions.php'; 


$required_fields = ['descreption', 'title'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['error' => "Invalid or missing $field"]);
        exit;
    }
}


$descreption = trim($_POST['descreption']);
$title = trim($_POST['title']);


// Get sender info
$linked_id = $_SESSION['linked_id']; 
$role = $_SESSION['role'];

// Additional validation: Check if receiver exists
try {

    
    // Send the report
    $result = report_bug($conn, $linked_id, $role, $descreption, $title);
    
    if ($result) {
        echo json_encode(['success' => 'report sent successfully']);
    } else {
        echo json_encode(['error' => 'Failed to send report']);
    }
} catch (Exception $e) {
    error_log("Report sending error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}

$conn->close();
?>