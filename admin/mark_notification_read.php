<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $notificationId = intval($_POST['id']);
        $userId = intval($_SESSION['admin_id']); // Assuming you store the user ID in the session

        // Get the database connection
        $database = new Database();
        $conn = $database->getConnection();

        // Update the notification as read in the database
        $sql = "UPDATE notifications SET status = 'read' WHERE id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(1, $notificationId, PDO::PARAM_INT);
            $stmt->bindParam(2, $userId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo 'Notification marked as read';
            } else {
                http_response_code(500);
                echo 'Failed to mark notification as read';
            }
        } else {
            http_response_code(500);
            echo 'Failed to prepare SQL statement';
        }
    } else {
        http_response_code(400);
        echo 'Invalid request';
    }
} else {
    http_response_code(405);
    echo 'Method not allowed';
}
