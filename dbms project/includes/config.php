<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'bhuvana');
define('DB_PASS', 'bhuvana@3096');
define('DB_NAME', 'server_monitoring');


define('SMS_API_KEY', 'your_sms_api_key');
define('SMS_SENDER_ID', 'SERVERALERT');
define('SMS_API_URL', 'https://api.smsprovider.com/v1/send');


define('CHECK_INTERVAL_MINUTES', 5);
define('ALERT_COOLDOWN_MINUTES', 30);


error_reporting(E_ALL);
ini_set('display_errors', 1);
?>