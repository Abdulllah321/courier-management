<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['customer_id'])) {
        $_SESSION['error'] = "Customer ID is required.";
        header("Location: edit_customer.php?id=" . $_POST['customer_id']);
        exit();
    }

    $customerId = $_POST['customer_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $database = new Database();
    $db = $database->getConnection();

    // Update customer details
    $query = "UPDATE customers SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, address = :address WHERE customer_id = :customer_id AND deleted = FALSE";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':customer_id', $customerId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Customer details updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update customer details.";
    }

    header("Location: edit_customer.php?id=" . $customerId);
    exit();
}
?>