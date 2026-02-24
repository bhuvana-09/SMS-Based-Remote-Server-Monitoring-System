<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}

$db = DB::getInstance();
$message = '';

// Handle alert resolution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_alert'])) {
    $alertId = (int)$_POST['alert_id'];
    try {
        $stmt = $db->prepare("UPDATE alerts SET is_resolved = TRUE, resolved_at = NOW() WHERE id = ?");
        $stmt->execute([$alertId]);
        $message = "Alert resolved!";
    } catch (PDOException $e) {
        $message = "Error resolving alert: " . $e->getMessage();
    }
}

// Get all alerts (active first)
try {
    $stmt = $db->prepare("
        SELECT a.*, s.name AS server_name 
        FROM alerts a
        JOIN servers s ON a.server_id = s.id
        ORDER BY a.is_resolved ASC, a.created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $alerts = [];
    $message = "Error loading alerts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alerts Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .alert-row.resolved { background-color: #e0ffe0; }
        .alert-row.cpu { background-color: #ffe0e0; }
        .alert-row.memory { background-color: #fff5cc; }
        .alert-row.disk { background-color: #e6f0ff; }
        .btn-resolve {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-resolve:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Alerts Management</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="servers.php">Servers</a>
            <a href="alerts.php">Alerts</a>
            <a href="settings.php">Settings</a>
            <a href="../includes/logout.php">Logout</a>
        </nav>
    </header>

    <?php if ($message): ?>
        <div class="alert-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <main>
        <h2>All Alerts</h2>
        <?php if (empty($alerts)): ?>
            <p>No alerts found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Server</th>
                        <th>Metric</th>
                        <th>Value</th>
                        <th>Threshold</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Resolved</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alerts as $alert): ?>
                        <tr class="alert-row <?= $alert['metric_type'] ?> <?= $alert['is_resolved'] ? 'resolved' : '' ?>">
                            <td><?= htmlspecialchars($alert['server_name']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($alert['metric_type'])) ?></td>
                            <td><?= $alert['metric_value'] ?></td>
                            <td><?= $alert['threshold_value'] ?></td>
                            <td><?= $alert['is_resolved'] ? 'Resolved' : 'Active' ?></td>
                            <td><?= $alert['created_at'] ?></td>
                            <td><?= $alert['resolved_at'] ?? '-' ?></td>
                            <td>
                                <?php if (!$alert['is_resolved']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
                                        <button type="submit" name="resolve_alert" class="btn-resolve">Resolve</button>
                                    </form>
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
