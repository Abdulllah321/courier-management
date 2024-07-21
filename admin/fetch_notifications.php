<?php
session_start();
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$userId = $_SESSION['user_id']; // Assuming user ID is stored in session

$query = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($notifications);
