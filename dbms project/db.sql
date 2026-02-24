CREATE DATABASE server_monitor;
USE server_monitor;

CREATE TABLE servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(15) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE thresholds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NOT NULL,
    cpu_threshold DECIMAL(5,2) DEFAULT 90.00,
    memory_threshold DECIMAL(5,2) DEFAULT 85.00,
    disk_threshold DECIMAL(5,2) DEFAULT 90.00,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);


CREATE TABLE metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NOT NULL,
    cpu_usage DECIMAL(5,2),
    memory_usage DECIMAL(5,2),
    disk_usage DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);


CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NOT NULL,
    metric_type ENUM('cpu', 'memory', 'disk'),
    metric_value DECIMAL(5,2),
    threshold_value DECIMAL(5,2),
    is_resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);


CREATE TABLE administrators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO administrators (id, name, email, phone, is_active, created_at, password) VALUES
(1, 'bhuvana', 'bhuvana@gmail.com', '9948225327', 1, '2025-04-09 16:45:15', '8645dc65648e570de91a3e78d05834b2'),
(4, 'Alice Johnson', 'alice@example.com', '1234567890', 1, '2025-04-09 18:09:52', ''),
(5, 'Bob Smith', 'bob@example.com', '9876543210', 1, '2025-04-09 18:09:52', '');

INSERT INTO alerts (id, server_id, metric_type, metric_value, threshold_value, is_resolved, created_at, resolved_at) VALUES
(1, 2, 'cpu', 89.30, 85.00, 1, '2025-04-09 18:09:52', '2025-04-09 18:16:58'),
(6, 1, 'cpu', 95.00, 85.00, 0, '2025-04-10 12:52:12', NULL);

INSERT INTO metrics (id, server_id, cpu_usage, memory_usage, disk_usage, created_at) VALUES
(1, 1, 72.50, 65.20, 55.00, '2025-04-09 18:09:52'),
(2, 2, 89.30, 82.00, 90.10, '2025-04-09 18:09:52'),
(4, 1, 95.00, 80.00, 85.00, '2025-04-10 12:50:28');

INSERT INTO servers (id, name, ip_address, description, is_active, created_at) VALUES
(1, 'Apache Web Server', '192.168.1.101', 'Apache Web Server', 1, '2025-04-09 18:09:52'),
(2, 'Database Server', '192.168.1.102', 'MySQL DB Server', 1, '2025-04-09 18:09:52'),
(4, 'xampp server', '11.01.231', 'php server', 1, '2025-04-09 18:18:27'),
(5, 'hp server', '11.222.3333', 'file storage', 1, '2025-04-09 22:42:58'),
(6, 'file server', '11.000.101', 'file system server', 1, '2025-04-10 12:46:16');

INSERT INTO settings (id, setting_key, setting_value) VALUES
(5, 'alert_email', 'bhu@gmail.com'),
(6, 'sms_number', '9347279069'),
(7, 'cpu_threshold', '80'),
(8, 'memory_threshold', '80');

INSERT INTO thresholds (id, server_id, cpu_threshold, memory_threshold, disk_threshold) VALUES
(1, 1, 85.00, 80.00, 90.00),
(2, 2, 90.00, 85.00, 95.00);
