<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 

header('Content-Type: application/json'); 

// Check if user is logged in and has valid role
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) { 
    echo json_encode(['error' => 'Unauthorized']); 
    exit; 
} 

require_once 'functions.php'; 

// Validate required fields
$required_fields = ['receiver_id', 'receiver_role', 'message', 'title', 'type'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['error' => "Invalid or missing $field"]);
        exit;
    }
}

// Sanitize inputs
$receiver_id = intval($_POST['receiver_id']);
$receiver_role = trim($_POST['receiver_role']);
$message = trim($_POST['message']);
$title = trim($_POST['title']);
$type = trim($_POST['type']);

// Validate roles and types
if (!in_array($receiver_role, ['student', 'teacher', 'admin'])) {
    echo json_encode(['error' => 'Invalid receiver role']);
    exit;
}



$sender_id = $_SESSION['linked_id']; 
$sender_role = $_SESSION['role']; 

try {
    $result = sendMessages($conn, $receiver_id, $receiver_role, $message, $title, $type, $sender_id, $sender_role);
    
    if ($result) {
        echo json_encode(['success' => 'Message sent successfully']);
    } else {
        echo json_encode(['error' => 'Failed to send message']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>