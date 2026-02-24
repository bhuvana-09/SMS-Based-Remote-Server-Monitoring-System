<?php

class ServerMonitor {
    private $db;
    
    public function __construct() {
        $this->db = DB::getInstance();
    }
    
    public function getServers() {
        $stmt = $this->db->prepare("SELECT * FROM servers WHERE is_active = TRUE");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}
?>