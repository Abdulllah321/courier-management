<?php
session_start();
include_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->getConnection();

// Check if the user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['agent_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Determine user role and ID
$isAdmin = isset($_SESSION['admin_id']);
$userId = $isAdmin ? $_SESSION['admin_id'] : $_SESSION['agent_id'];

// Prepare query based on user role
$query = $isAdmin ? "SELECT * FROM notifications ORDER BY created_at DESC" : "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";

// Prepare statement
$stmt = $db->prepare($query);

if (!$isAdmin) {
    // Bind user ID parameter if not an admin
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
}

// Execute statement
if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $error[2]]);
    exit;
}

// Fetch notifications
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output JSON
header('Content-Type: application/json');
echo json_encode($notifications);
