<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

$monitor = new ServerMonitor();

$response = [
    'servers' => [],
    'alerts' => []
];


$conn = (new Database())->getConnection();
$servers = $conn->query("SELECT * FROM servers WHERE is_active = TRUE");

while ($server = $servers->fetch_assoc()) {
    $metrics = $monitor->getServerMetrics($server['id'], 1);
    $serverData = [
        'server_id' => $server['id'],
        'server_name' => $server['name'],
        'cpu_usage' => $metrics[0]['cpu_usage'] ?? null,
        'memory_usage' => $metrics[0]['memory_usage'] ?? null,
        'disk_usage' => $metrics[0]['disk_usage'] ?? null,
        'uptime' => isset($metrics[0]['uptime_seconds']) ? 
            gmdate("H:i:s", $metrics[0]['uptime_seconds']) : null,
        'last_check' => $metrics[0]['timestamp'] ?? null
    ];
    $response['servers'][] = $serverData;
}


$alerts = $monitor->getActiveAlerts(5);
foreach ($alerts as $alert) {
    $response['alerts'][] = [
        'server_name' => $alert['server_name'],
        'metric_name' => $alert['metric_name'],
        'metric_value' => $alert['metric_value'],
        'alert_level' => $alert['alert_level'],
        'is_resolved' => $alert['is_resolved'],
        'created_at' => $alert['created_at']
    ];
}

echo json_encode($response);
?>