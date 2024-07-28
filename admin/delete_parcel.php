<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $deleteCustomer = isset($_POST['deleteCustomer']) ? filter_var($_POST['deleteCustomer'], FILTER_VALIDATE_BOOLEAN) : false;

    if ($id) {
        $database = new Database();
        $db = $database->getConnection();

        // Begin transaction
        $db->beginTransaction();

        try {
            // Update parcel to mark as deleted
            $updateQuery = "UPDATE parcels SET deleted = TRUE WHERE parcel_id = :id";
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($deleteCustomer) {
                // Get sender_id and receiver_id from the parcel
                $query = "SELECT sender_id, receiver_id FROM parcels WHERE parcel_id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($parcel) {
                    // Mark associated customers as deleted
                    $updateCustomerQuery = "UPDATE customers SET deleted = TRUE WHERE customer_id IN (:sender_id, :receiver_id)";
                    $stmt = $db->prepare($updateCustomerQuery);
                    $stmt->bindParam(':sender_id', $parcel['sender_id']);
                    $stmt->bindParam(':receiver_id', $parcel['receiver_id']);
                    $stmt->execute();
                }
            }

            // Commit transaction
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Parcel deleted successfully.']);
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to delete parcel: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>