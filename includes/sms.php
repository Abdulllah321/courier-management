<?php
require_once '../twilio-php/src/Twilio/autoload.php'; // Make sure to include the Composer autoload file

use Twilio\Rest\Client;

function sendSMS($phoneNumber, $message)
{
    $account_sid = 'ACb561f9a05e350f9ef1b9584ab8e008bc';
    $auth_token = '712c237434b39f8bfa1d45440887ae7c';
    $twilio_number = '+17578023589';

    $client = new Client($account_sid, $auth_token);

    try {
        $client->messages->create(
            $phoneNumber,
            [
                'from' => $twilio_number,
                'body' => $message
            ]
        );
        echo $phoneNumber;
        echo $message;
        echo "Message sent!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
if (isset($_POST['phoneNumber']) && isset($_POST['message'])) {
    sendSMS($_POST['phoneNumber'], $_POST['message']);
}
?>