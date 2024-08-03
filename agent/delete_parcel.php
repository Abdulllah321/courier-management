<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedInAgent();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $deleteCustomer = isset($_POST['deleteCustomer']) ? filter_var($_POST['deleteCustomer'], FILTER_VALIDATE_BOOLEAN) : false;

    error_log('ID: ' . $id);
    error_log('Delete Customer: ' . ($deleteCustomer ? 'true' : 'false'));

    if ($id) {
        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();

        try {
            // Update parcel to mark as deleted
            $updateQuery = "UPDATE parcels SET deleted = TRUE WHERE parcel_id = :id";
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':id', $id);
            if (!$stmt->execute()) {
                var_dump($stmt->errorInfo());
                throw new Exception('Failed to update parcel.');
            }

            if ($deleteCustomer) {
                // Get sender_id and receiver_id from the parcel
                $query = "SELECT sender_id, receiver_id FROM parcels WHERE parcel_id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                if (!$stmt->execute()) {
                    var_dump($stmt->errorInfo());
                    throw new Exception('Failed to fetch parcel details.');
                }
                $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($parcel) {
                    // Mark associated customers as deleted
                    $updateCustomerQuery = "UPDATE customers SET deleted = TRUE WHERE customer_id IN (:sender_id, :receiver_id)";
                    $stmt = $db->prepare($updateCustomerQuery);
                    $stmt->bindParam(':sender_id', $parcel['sender_id']);
                    $stmt->bindParam(':receiver_id', $parcel['receiver_id']);
                    if (!$stmt->execute()) {
                        var_dump($stmt->errorInfo());
                        throw new Exception('Failed to update customers.');
                    }
                }
            }

            // Commit transaction
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Parcel deleted successfully.']);
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to delete parcel: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>