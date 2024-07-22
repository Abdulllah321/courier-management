<?php
function createNotification($db, $userId, $message, $status = 'unread', $url)
{
    // Prepare SQL query
    $query = "INSERT INTO notifications (user_id, message, status, url) VALUES (:user_id, :message, :status, :url)";
    $stmt = $db->prepare($query);

    // Bind parameters
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);

    // Execute the statement
    if (!$stmt->execute()) {
        // Handle errors if needed
        throw new Exception('Database error: ' . $stmt->errorInfo()[2]);
    }
}
