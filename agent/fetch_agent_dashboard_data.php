<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';

// Check if the user is logged in as an agent
if (redirectIfNotLoggedInAgent()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the agent's ID from the session
$agentId = $_SESSION['agent_id'];

// Create a database connection
$database = new Database();
$conn = $database->getConnection();

// Fetch the number of assigned parcels for the agent
$query = "SELECT COUNT(*) as total FROM parcels WHERE agent_id = :agent_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':agent_id', $agentId);
$stmt->execute();
$assignedParcels = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch the number of unique customers associated with the agent's parcels
$query = "
    SELECT COUNT(DISTINCT customers.customer_id) as total 
    FROM customers 
    JOIN parcels ON parcels.sender_id = customers.customer_id OR parcels.receiver_id = customers.customer_id 
    WHERE parcels.agent_id = :agent_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':agent_id', $agentId);
$stmt->execute();
$customersCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch the number of notifications for the agent
$query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = :agent_id AND status = :status";
$stmt = $conn->prepare($query);
$status = "read";
$stmt->bindParam(':agent_id', $agentId);
$stmt->bindParam(':status', $status);
$stmt->execute();
$notificationsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch parcel statistics for the current month
$query = "
    SELECT 
        DATE_FORMAT(created_at, '%d') as day, 
        status, 
        COUNT(*) as total 
    FROM parcels 
    WHERE agent_id = :agent_id AND MONTH(created_at) = MONTH(CURDATE()) 
    GROUP BY day, status";
$stmt = $conn->prepare($query);
$stmt->bindParam(':agent_id', $agentId);
$stmt->execute();
$parcelStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the data as JSON
echo json_encode([
    'assignedParcels' => $assignedParcels,
    'customersCount' => $customersCount,
    'notificationsCount' => $notificationsCount,
    'parcelStats' => $parcelStats
]);

?>