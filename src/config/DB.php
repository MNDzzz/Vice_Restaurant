<?php
class DB
{
    private static $instance = null;
    private $conn;

    private $host = 'sql310.infinityfree.com';
    private $user = 'if0_40839903';
    private $pass = 'O3H5GMyYqn4Av';
    private $name = 'if0_40839903_vice_db';

    private function __construct()
    {
        try {
            // Usamos directamente las propiedades de la clase para evitar que el entorno del servidor
            // sobrescriba la configuración con 'localhost' y cause el error de socket.
            $host = $this->host;
            $user = $this->user;
            $pass = $this->pass;
            $name = $this->name;

            $this->conn = new PDO("mysql:host={$host};dbname={$name}", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
?>