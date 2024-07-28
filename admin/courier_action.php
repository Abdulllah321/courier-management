<?php
session_start();
require '../config/database.php';
include "../includes/functions.php";
include "../includes/sms.php"; // Include your SMS sending function
include "../includes/email.php"; // Include your email sending function

redirectIfNotLoggedIn();

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'];
    $parcel_id = null;

    if ($mode === 'create') {
        // Insert new parcel with sender and receiver IDs
        $stmt = $conn->prepare("INSERT INTO parcels (sender_id, receiver_id, weight, dimensions, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['sender_id'],
            $_POST['receiver_id'],
            $_POST['weight'],
            $_POST['dimensions'],
            $_POST['status']
        ]);
        $parcel_id = $conn->lastInsertId();
    } else {
        // Update existing parcel
        $parcel_id = $_POST['parcel_id'];
        $stmt = $conn->prepare("UPDATE parcels SET sender_id = ?, receiver_id = ?, weight = ?, dimensions = ?, status = ? WHERE parcel_id = ?");
        $stmt->execute([
            $_POST['sender_id'],
            $_POST['receiver_id'],
            $_POST['weight'],
            $_POST['dimensions'],
            $_POST['status'],
            $parcel_id
        ]);
    }

    // Fetch sender and receiver details
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");

    // Fetch sender details
    $stmt->execute([$_POST['sender_id']]);
    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch receiver details
    $stmt->execute([$_POST['receiver_id']]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate report content with sender and receiver details
    $reportContent = generateReport($parcel_id, $sender, $receiver);

    // Send SMS and email to sender and receiver
    sendSMS($sender['phone'], "Your parcel has been created. Tracking ID: $parcel_id");
    sendSMS($receiver['phone'], "You have a parcel from {$sender['first_name']} {$sender['last_name']}. Tracking ID: $parcel_id");

    sendEmail($sender['email'], "Parcel Created", "Your parcel has been created. Tracking ID: $parcel_id", $reportContent);
    sendEmail($receiver['email'], "Parcel Received", "You have a parcel from {$sender['first_name']} {$sender['last_name']}. Tracking ID: $parcel_id", $reportContent);

    // Redirect to report page
    header("Location: report.php?parcel_id=$parcel_id");
    exit;
}

// Function to generate the report content
function generateReport($parcel_id, $sender, $receiver)
{
    global $conn;

    // Fetch parcel details from the database
    $stmt = $conn->prepare("SELECT * FROM parcels WHERE parcel_id = ?");
    $stmt->execute([$parcel_id]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate HTML content for the report
    ob_start();
    include "../includes/report_template.php";
    $content = ob_get_clean();

    return $content;
}
?>  