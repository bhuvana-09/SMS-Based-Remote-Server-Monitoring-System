<?php
require_once 'db.php';

function getServers() {
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT * FROM servers WHERE is_active = TRUE");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getServerMetrics($server_id) {
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT * FROM metrics WHERE server_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$server_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getServerThresholds($server_id) {
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT * FROM thresholds WHERE server_id = ?");
    $stmt->execute([$server_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getActiveAlerts() {
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT a.*, s.name as server_name FROM alerts a JOIN servers s ON a.server_id = s.id WHERE a.is_resolved = FALSE");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAdministrators() {
    $db = DB::getInstance();
    $stmt = $db->prepare("SELECT * FROM administrators WHERE is_active = TRUE");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addAlert($server_id, $metric_type, $metric_value, $threshold_value) {
    $db = DB::getInstance();
    $stmt = $db->prepare("INSERT INTO alerts (server_id, metric_type, metric_value, threshold_value) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$server_id, $metric_type, $metric_value, $threshold_value]);
}

function resolveAlert($alert_id) {
    $db = DB::getInstance();
    $stmt = $db->prepare("UPDATE alerts SET is_resolved = TRUE, resolved_at = NOW() WHERE id = ?");
    return $stmt->execute([$alert_id]);
}
?>