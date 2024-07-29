<?php
header('Content-Type: application/json');
require '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];

$response = ['exists' => false];

if (!empty($email)) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        $response['exists'] = true;
        $response['first_name'] = $customer['first_name'];
        $response['last_name'] = $customer['last_name'];
        $response['phone'] = $customer['phone'];
        $response['address'] = $customer['address'];
        $response['created_at'] = $customer['created_at'];
    }
}

echo json_encode($response);
