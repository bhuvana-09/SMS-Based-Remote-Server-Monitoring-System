<?php
// admin/settings.php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}

$db = DB::getInstance();
$error = '';
$success = '';

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $alert_email = trim($_POST['alert_email']);
        $sms_number = trim($_POST['sms_number']);
        $cpu_threshold = (int)$_POST['cpu_threshold'];
        $memory_threshold = (int)$_POST['memory_threshold'];

        $stmt = $db->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES
            ('alert_email', ?),
            ('sms_number', ?),
            ('cpu_threshold', ?),
            ('memory_threshold', ?)"
        );
        $stmt->execute([$alert_email, $sms_number, $cpu_threshold, $memory_threshold]);
        $success = "Settings updated successfully.";
    } catch (Exception $e) {
        $error = "Error saving settings: " . $e->getMessage();
    }
}

// Load current settings
try {
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $error = "Error loading settings: " . $e->getMessage();
    $settings = [];
}

// Load admin users
try {
    $stmt = $db->query("SELECT * FROM administrators");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error .= "<br>Failed to load admin users: " . $e->getMessage();
    $admins = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Server Monitor</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        form .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Settings</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="servers.php">Servers</a>
                <a href="alerts.php">Alerts</a>
                <a href="settings.php">Settings</a>
                <a href="../includes/logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <?php if ($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <section>
                <h2>Notification Settings</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="alert_email">Alert Email</label>
                        <input type="email" id="alert_email" name="alert_email" value="<?= htmlspecialchars($settings['alert_email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="sms_number">SMS Number</label>
                        <input type="text" id="sms_number" name="sms_number" value="<?= htmlspecialchars($settings['sms_number'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cpu_threshold">CPU Threshold (%)</label>
                        <input type="number" id="cpu_threshold" name="cpu_threshold" value="<?= htmlspecialchars($settings['cpu_threshold'] ?? '80') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="memory_threshold">Memory Threshold (%)</label>
                        <input type="number" id="memory_threshold" name="memory_threshold" value="<?= htmlspecialchars($settings['memory_threshold'] ?? '80') ?>" required>
                    </div>
                    <button type="submit">Save Settings</button>
                </form>
            </section>

            <section>
                <h2>Administrator Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($admins)): ?>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?= htmlspecialchars($admin['name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                    <td><?= htmlspecialchars($admin['phone'] ?? 'N/A') ?></td>
                                    <td><?= $admin['is_active'] ? 'Active' : 'Inactive' ?></td>
                                    <td><!-- future actions (edit/delete) --></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No administrators found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
