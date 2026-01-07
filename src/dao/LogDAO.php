<?php
require_once __DIR__ . '/../config/DB.php';

class LogDAO
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    // Registrar una acción en el log
    public function logAction($userId, $action, $details = null)
    {
        $sql = "INSERT INTO logs (user_id, action, details, created_at) VALUES (:user_id, :action, :details, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        return $stmt->execute();
    }

    // Obtener todos los logs (con nombre de usuario)
    public function getAll()
    {
        $sql = "SELECT l.*, u.name as user_name, u.email as user_email 
                FROM logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                ORDER BY l.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>