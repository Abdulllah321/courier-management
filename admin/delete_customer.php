<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerId = $_POST['id'];
    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();

        // Check if the customer is referenced in the parcels table
        $checkQuery = "SELECT COUNT(*) as count FROM parcels WHERE sender_id = :customer_id OR receiver_id = :customer_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':customer_id', $customerId);
        $checkStmt->execute();
        $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count > 0) {
            throw new Exception('Customer cannot be deleted because they are referenced in parcels.');
        }

        // Perform the deletion
        $query = "UPDATE customers SET deleted = true WHERE customer_id = :customer_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':customer_id', $customerId);

        if ($stmt->execute()) {
            $db->commit();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to delete the customer.');
        }
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>