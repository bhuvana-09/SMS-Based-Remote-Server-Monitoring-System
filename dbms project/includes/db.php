<?php
require_once 'config.php';

class DB {
    private static $conn = null;

    public static function getInstance() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("DB Connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
