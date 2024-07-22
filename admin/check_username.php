<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$username = $_GET['username'] ?? '';

$query = "SELECT COUNT(*) FROM agents WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$count = $stmt->fetchColumn();

$response = [
    'available' => $count == 0
];

header('Content-Type: application/json');
echo json_encode($response);
