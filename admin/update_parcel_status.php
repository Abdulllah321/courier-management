<?php
session_start();
include_once '../config/database.php';

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Parcel ID and status are required']);
    exit();
}

$parcelId = $_POST['id'];
$newStatus = $_POST['status'];

$database = new Database();
$db = $database->getConnection();

// Validate the status value
$validStatuses = ['Pending', 'In Transit', 'Delivered', 'returned'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit();
}

// Update the parcel status
$query = "UPDATE parcels SET status = :status WHERE parcel_id = :parcel_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':status', $newStatus);
$stmt->bindParam(':parcel_id', $parcelId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Parcel status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update parcel status']);
}
?>