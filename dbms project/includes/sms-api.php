<?php
require_once 'config.php';
require_once 'functions.php';

function sendSMSAlert($phone, $message) {
    $data = [
        'api_key' => SMS_API_KEY,
        'sender' => SMS_SENDER_ID,
        'number' => $phone,
        'message' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, SMS_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

function notifyAdministrators($server_name, $metric, $value, $threshold) {
    $admins = getAdministrators();
    foreach ($admins as $admin) {
        $message = "ALERT: Server {$server_name} - {$metric} usage {$value}% exceeds threshold {$threshold}%";
        sendSMSAlert($admin['phone'], $message);
    }
}
?>