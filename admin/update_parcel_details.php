<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

// Check if POST data is available
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $weight = isset($_POST['weight']) ? $_POST['weight'] : null;
    $dimensions = isset($_POST['dimensions']) ? $_POST['dimensions'] : null;

    if ($id && $weight && $dimensions) {
        // Validate inputs
        $weight = filter_var($weight, FILTER_SANITIZE_STRING);
        $dimensions = filter_var($dimensions, FILTER_SANITIZE_STRING);

        $database = new Database();
        $db = $database->getConnection();

        // Prepare update query
        $query = "UPDATE parcels SET weight = :weight, dimensions = :dimensions WHERE parcel_id = :id";
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':dimensions', $dimensions);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Parcel details updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update parcel details.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>