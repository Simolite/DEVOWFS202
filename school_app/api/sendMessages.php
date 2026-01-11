<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// JSON response
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['role'], $_SESSION['linked_id']) ||
    !in_array($_SESSION['role'], ['student', 'teacher', 'admin'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

require_once 'conn.php';
require_once 'functions.php';

// Required fields
$required_fields = ['receiver_id', 'receiver_role', 'message', 'title', 'type'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        echo json_encode([
            'success' => false,
            'error' => "Missing field: $field"
        ]);
        exit;
    }
}

// Sanitize inputs
$receiver_id   = (int) $_POST['receiver_id'];
$receiver_role = trim($_POST['receiver_role']);
$message       = trim($_POST['message']);
$title         = trim($_POST['title']);
$type          = trim($_POST['type']);

// Validate receiver role
if (!in_array($receiver_role, ['admin', 'teacher', 'student'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid receiver role'
    ]);
    exit;
}

// Sender info
$sender_id   = (int) $_SESSION['linked_id'];
$sender_role = $_SESSION['role'];

try {
    // Call function
    $result = sendMessages(
        $conn,
        $receiver_id,
        $receiver_role,
        $message,
        $title,
        $type,
        $sender_id,
        $sender_role
    );

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to send message'
        ]);
    }

} catch (Exception $e) {
    error_log('Send message error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => 'Server error'
    ]);
}

$conn->close();
