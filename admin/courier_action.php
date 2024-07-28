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
        // Insert new parcel
        $stmt = $conn->prepare("INSERT INTO parcels (sender_first_name, sender_last_name, sender_email, sender_phone, sender_address, receiver_first_name, receiver_last_name, receiver_email, receiver_phone, receiver_address, weight, dimensions, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['sender_first_name'],
            $_POST['sender_last_name'],
            $_POST['sender_email'],
            $_POST['sender_phone'],
            $_POST['sender_address'],
            $_POST['receiver_first_name'],
            $_POST['receiver_last_name'],
            $_POST['receiver_email'],
            $_POST['receiver_phone'],
            $_POST['receiver_address'],
            $_POST['weight'],
            $_POST['dimensions'],
            $_POST['status']
        ]);
        $parcel_id = $conn->lastInsertId();
    } else {
        // Update existing parcel
        $parcel_id = $_POST['parcel_id'];
        $stmt = $conn->prepare("UPDATE parcels SET sender_first_name = ?, sender_last_name = ?, sender_email = ?, sender_phone = ?, sender_address = ?, receiver_first_name = ?, receiver_last_name = ?, receiver_email = ?, receiver_phone = ?, receiver_address = ?, weight = ?, dimensions = ?, status = ? WHERE parcel_id = ?");
        $stmt->execute([
            $_POST['sender_first_name'],
            $_POST['sender_last_name'],
            $_POST['sender_email'],
            $_POST['sender_phone'],
            $_POST['sender_address'],
            $_POST['receiver_first_name'],
            $_POST['receiver_last_name'],
            $_POST['receiver_email'],
            $_POST['receiver_phone'],
            $_POST['receiver_address'],
            $_POST['weight'],
            $_POST['dimensions'],
            $_POST['status'],
            $parcel_id
        ]);
    }

    // Generate report
    $reportContent = generateReport($parcel_id);

    // Send SMS and email to sender and receiver
    sendSMS($_POST['sender_phone'], "Your parcel has been created. Tracking ID: $parcel_id");
    sendSMS($_POST['receiver_phone'], "You have a parcel from {$_POST['sender_first_name']} {$_POST['sender_last_name']}. Tracking ID: $parcel_id");

    sendEmail($_POST['sender_email'], "Parcel Created", "Your parcel has been created. Tracking ID: $parcel_id", $reportContent);
    sendEmail($_POST['receiver_email'], "Parcel Received", "You have a parcel from {$_POST['sender_first_name']} {$_POST['sender_last_name']}. Tracking ID: $parcel_id", $reportContent);

    // Redirect to report page
    header("Location: report.php?parcel_id=$parcel_id");
    exit;
}

// Function to generate the report content
function generateReport($parcel_id)
{
    // Fetch parcel details from the database
    global $conn;
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