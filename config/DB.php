<?php
class DB {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $name = 'vice_db';

    private function __construct() {
        try {
            $host = getenv('DB_HOST') ?: $this->host;
            $user = getenv('DB_USER') ?: $this->user;
            $pass = getenv('DB_PASS') ?: $this->pass;
            $name = getenv('DB_NAME') ?: $this->name;

            $this->conn = new PDO("mysql:host={$host};dbname={$name}", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>