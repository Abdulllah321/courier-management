<?php
session_start();
require '../config/database.php';
include "../includes/functions.php";
include "../includes/sms.php";
include "../includes/email.php";
include "create_notification.php";

// Function to generate a UUID
function generateUUID()
{
    return bin2hex(random_bytes(6));
}

redirectIfNotLoggedIn();

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_email = $_POST['sender_email'];
    $receiver_email = $_POST['receiver_email'];

    // Fetch existing users by email
    $sender_user = getUserByEmail($conn, $sender_email);
    $receiver_user = getUserByEmail($conn, $receiver_email);

    // Determine sender ID
    if ($sender_user) {
        $sender_id = $sender_user['customer_id'];
    } else {
        $sender_id = 'customer_' . generateUUID();
        insertCustomer($conn, $sender_id, $_POST['sender_first_name'], $_POST['sender_last_name'], $_POST['sender_email'], $_POST['sender_phone'], $_POST['sender_address']);
    }

    // Determine receiver ID
    if ($receiver_user) {
        $receiver_id = $receiver_user['customer_id'];
    } else {
        $receiver_id = 'customer_' . generateUUID();
        insertCustomer($conn, $receiver_id, $_POST['receiver_first_name'], $_POST['receiver_last_name'], $_POST['receiver_email'], $_POST['receiver_phone'], $_POST['receiver_address']);
    }

    // Insert new parcel
    $parcel_id = 'courier_' . generateUUID();
    $stmt = $conn->prepare("INSERT INTO parcels (parcel_id, sender_id, receiver_id, weight, dimensions, status, delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$parcel_id, $sender_id, $receiver_id, $_POST['weight'], $_POST['dimensions'], $_POST['status'], $_POST['delivery_date']]);
    $_SESSION['message'] = 'Courier created successfully';

    // Fetch sender and receiver details for report
    $sender = getCustomerById($conn, $sender_id);
    $receiver = getCustomerById($conn, $receiver_id);
    $reportContent = generateReport($parcel_id, $sender, $receiver);

    // Send SMS and Email notifications
    sendSMS($sender['phone'], "Your parcel is pending and will be processed soon. Tracking ID: $parcel_id");
    sendSMS($receiver['phone'], "You have a pending parcel from {$sender['first_name']} {$sender['last_name']}. Tracking ID: $parcel_id");

    sendEmail($sender['email'], "Parcel Pending", "Your parcel is pending and will be processed soon. Tracking ID: $parcel_id", $reportContent);
    sendEmail($receiver['email'], "Pending Parcel Notification", "You have a pending parcel from {$sender['first_name']} {$sender['last_name']}. Tracking ID: $parcel_id", $reportContent);


    $userId = $_SESSION['admin_id'] ?? $_SESSION['agent_id'];
    createNotification($conn, $userId, "Your parcel with tracking ID $parcel_id has been created.", 'unread', "report.php?parcel_id=$parcel_id");
    header("Location: report.php?parcel_id=$parcel_id");
    exit;
}

function getUserByEmail($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCustomerById($conn, $customer_id)
{
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertCustomer($conn, $customer_id, $first_name, $last_name, $email, $phone, $address)
{
    $stmt = $conn->prepare("INSERT INTO customers (customer_id, first_name, last_name, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$customer_id, $first_name, $last_name, $email, $phone, $address]);
}

function generateReport($parcel_id, $sender, $receiver)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM parcels WHERE parcel_id = ?");
    $stmt->execute([$parcel_id]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    ob_start();
    include "../includes/report_template.php";
    return ob_get_clean();
}
?>