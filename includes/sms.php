<?php
function sendSMS($phoneNumber, $message)
{
    $url = "http://api.sms.com/sendSMS";
    $postData = array(
        "sender_id" => "YourSenderId",
        "phone_numbers" => $phoneNumber,
        "message" => $message
    );
}
?>