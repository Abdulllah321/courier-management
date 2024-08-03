<?php
function createNotification($db, $userId, $message, $status = 'unread', $url)
{
    // Fetch all admin IDs
    $query = "SELECT id FROM admins";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Check if the userId is an admin
    if (in_array($userId, $adminIds)) {
        // If userId is an admin, use user_id for notification
        $query = "INSERT INTO notifications (user_id, message, status, url) VALUES (:user_id, :message, :status, :url)";
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    } else {
        // Check if userId is an agent
        $query = "SELECT agent_id FROM agents WHERE id = :agent_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':agent_id', $userId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // If userId is an agent, use agent_id for notification
            $query = "INSERT INTO notifications (agent_id, message, status, url) VALUES (:agent_id, :message, :status, :url)";
            $stmt = $db->prepare($query);

            // Bind parameters
            $stmt->bindParam(':agent_id', $userId, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
        } else {
            // User ID is neither admin nor agent
            throw new Exception('Invalid user ID');
        }
    }

    // Execute the statement
    if (!$stmt->execute()) {
        // Handle errors if needed
        throw new Exception('Database error: ' . $stmt->errorInfo()[2]);
    }
}
