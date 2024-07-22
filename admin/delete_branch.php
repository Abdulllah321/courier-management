<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branchId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($branchId > 0) {
        $query = "UPDATE branches SET deleted = true WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $branchId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
