<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$database = new Database();
$db = $database->getConnection();

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// Fetch total couriers
$query = "SELECT COUNT(*) as total FROM parcels";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCouriers = $row['total'];

// Fetch total customers
$query = "SELECT COUNT(*) as total FROM customers";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCustomers = $row['total'];

// Fetch total agents
$query = "SELECT COUNT(*) as total FROM agents";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalAgents = $row['total'];

// Fetch courier statistics by status and day for the selected month
$query = "SELECT DAY(delivery_date) as day, status, COUNT(*) as total 
          FROM parcels 
          WHERE MONTH(delivery_date) = :month AND YEAR(delivery_date) = YEAR(CURDATE())
          GROUP BY DAY(delivery_date), status";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $selectedMonth);
$stmt->execute();
$courierStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer statistics by day for the selected month
$query = "SELECT DAY(created_at) as day, COUNT(*) as total 
          FROM customers 
          WHERE MONTH(created_at) = :month AND YEAR(created_at) = YEAR(CURDATE())
          GROUP BY DAY(created_at)";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $selectedMonth);
$stmt->execute();
$customerStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the response
$response = [
    'totalCouriers' => $totalCouriers,
    'totalCustomers' => $totalCustomers,
    'totalAgents' => $totalAgents,
    'courierStats' => $courierStats,
    'customerStats' => $customerStats
];

// Set the response header to JSON
header('Content-Type: application/json');
echo json_encode($response);
exit; // Ensure no further output
?>