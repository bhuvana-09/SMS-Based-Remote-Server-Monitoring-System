<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/functions.php';

// Authentication check
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}

$servers = getServers();
$alerts = getActiveAlerts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Monitoring Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Server Monitoring Dashboard</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="servers.php">Servers</a>
                <a href="alerts.php">Alerts</a>
                <a href="settings.php">Settings</a>
                <a href="../includes/logout.php">Logout</a>
            </nav>
        </header>
        
        <section class="overview">
            <h2>System Overview</h2>
            <div class="stats">
                <div class="stat-card">
                    <h3>Total Servers</h3>
                    <p><?php echo count($servers); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Alerts</h3>
                    <p><?php echo count($alerts); ?></p>
                </div>
            </div>
        </section>
        
        <section class="recent-alerts">
            <h2>Recent Alerts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Server</th>
                        <th>Metric</th>
                        <th>Value</th>
                        <th>Threshold</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alert['server_name']); ?></td>
                        <td><?php echo strtoupper($alert['metric_type']); ?></td>
                        <td><?php echo $alert['metric_value']; ?>%</td>
                        <td><?php echo $alert['threshold_value']; ?>%</td>
                        <td><?php echo date('Y-m-d H:i', strtotime($alert['created_at'])); ?></td>
                        <td>
                            <button onclick="resolveAlert(<?php echo $alert['id']; ?>)">Resolve</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <section class="server-status">
            <h2>Server Status</h2>
            <table>
                <thead>
                    <tr>
                        <th>Server Name</th>
                        <th>IP Address</th>
                        <th>CPU</th>
                        <th>Memory</th>
                        <th>Disk</th>
                        <th>Last Check</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $server): 
                        $metrics = getServerMetrics($server['id']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($server['name']); ?></td>
                        <td><?php echo htmlspecialchars($server['ip_address']); ?></td>
                        <td class="<?php echo getMetricClass($metrics['cpu_usage'], getServerThresholds($server['id'])['cpu_threshold']); ?>">
                            <?php echo $metrics ? $metrics['cpu_usage'].'%' : 'N/A'; ?>
                        </td>
                        <td class="<?php echo getMetricClass($metrics['memory_usage'], getServerThresholds($server['id'])['memory_threshold']); ?>">
                            <?php echo $metrics ? $metrics['memory_usage'].'%' : 'N/A'; ?>
                        </td>
                        <td class="<?php echo getMetricClass($metrics['disk_usage'], getServerThresholds($server['id'])['disk_threshold']); ?>">
                            <?php echo $metrics ? $metrics['disk_usage'].'%' : 'N/A'; ?>
                        </td>
                        <td><?php echo $metrics ? date('Y-m-d H:i', strtotime($metrics['created_at'])) : 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
    
    <script src="../assets/script.js"></script>
</body>
</html>