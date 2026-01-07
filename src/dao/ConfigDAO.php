<?php
require_once __DIR__ . '/BaseDAO.php';

class ConfigDAO extends BaseDAO
{
    protected $table = 'config';

    public function get($key)
    {
        $stmt = $this->db->prepare("SELECT value FROM config WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value'] : null;
    }

    public function set($key, $value)
    {
        // Upsert logic (Insert or Update)
        $stmt = $this->db->prepare("INSERT INTO config (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        return $stmt->execute([$key, $value, $value]);
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM config");
        $config = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $config[$row['key']] = $row['value'];
        }
        return $config;
    }
}
?>