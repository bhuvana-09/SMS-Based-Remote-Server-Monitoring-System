<?php
// admin/servers.php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/functions.php';

// Session and authentication
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php');
    exit;
}

$db = DB::getInstance();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_server'])) {
            // Validate inputs
            $name = trim($_POST['name']);
            $ip = trim($_POST['ip_address']);
            $description = trim($_POST['description']);
            
            if (empty($name) || empty($ip)) {
                throw new Exception("Name and IP address are required");
            }

            // Insert new server
            $stmt = $db->prepare("INSERT INTO servers (name, ip_address, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $ip, $description]);
            $success = "Server added successfully!";
            
        } elseif (isset($_POST['delete_server'])) {
            $server_id = (int)$_POST['server_id'];
            
            if ($server_id <= 0) {
                throw new Exception("Invalid server ID");
            }

            // Delete server
            $stmt = $db->prepare("DELETE FROM servers WHERE id = ?");
            $stmt->execute([$server_id]);
            $success = "Server deleted successfully!";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all servers
try {
    $stmt = $db->prepare("SELECT * FROM servers ORDER BY name");
    $stmt->execute();
    $servers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error loading servers: " . $e->getMessage();
    $servers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .alert {
            padding: 15px;
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Server Monitoring System</h1>
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
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <section>
                <h2>Add New Server</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Server Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_address">IP Address</label>
                        <input type="text" id="ip_address" name="ip_address" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_server">Add Server</button>
                </form>
            </section>

            <section>
                <h2>Server List</h2>
                <?php if (empty($servers)): ?>
                    <p>No servers found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>IP Address</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servers as $server): ?>
                            <tr>
                                <td><?= htmlspecialchars($server['id']) ?></td>
                                <td><?= htmlspecialchars($server['name']) ?></td>
                                <td><?= htmlspecialchars($server['ip_address']) ?></td>
                                <td><?= htmlspecialchars($server['description']) ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this server?');">
                                        <input type="hidden" name="server_id" value="<?= $server['id'] ?>">
                                        <button type="submit" name="delete_server" class="btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
