<?php
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alert_id'])) {
    if (resolveAlert($_POST['alert_id'])) {
        echo 'success';
    } else {
        echo 'Error resolving alert';
    }
} else {
    echo 'Invalid request';
}
?>