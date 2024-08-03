<?php
session_start();
include_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->getConnection();

// Check if the agent is an agent
if (!isset($_SESSION['agent_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$agentId = $_SESSION['agent_id'];

// Prepare query to fetch notifications for the agent
$query = "SELECT * FROM notifications WHERE agent_id = :agent_id ORDER BY created_at DESC";
$stmt = $db->prepare($query);

// Bind agent ID parameter (agent_id is varchar)
$stmt->bindParam(':agent_id', $agentId, PDO::PARAM_STR);

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
?>