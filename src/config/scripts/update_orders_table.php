<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../DB.php';

echo "<h1>Actualización de Esquema de Base de Datos</h1>";

try {
    $db = DB::getInstance()->getConnection();

    $commands = [
        "ALTER TABLE orders ADD COLUMN delivery_name VARCHAR(100) AFTER status",
        "ALTER TABLE orders ADD COLUMN delivery_city VARCHAR(100) AFTER delivery_address",
        "ALTER TABLE orders ADD COLUMN delivery_postal_code VARCHAR(20) AFTER delivery_city"
    ];

    foreach ($commands as $sql) {
        try {
            $db->exec($sql);
            echo "<div style='color: green'> Ejecutado: " . htmlspecialchars($sql) . "</div>";
        } catch (PDOException $e) {
            // Ignoramos error de columna duplicada (código 42S21)
            if ($e->getCode() == '42S21') {
                echo "<div style='color: orange'> La columna ya existe (Saltado): " . htmlspecialchars($sql) . "</div>";
            } else {
                echo "<div style='color: red'> Error: " . $e->getMessage() . "</div>";
            }
        }
    }

    echo "<h2>Proceso terminado. Intenta realizar el pedido de nuevo.</h2>";

} catch (Exception $e) {
    echo "<div style='color: red'> Error Fatal: " . $e->getMessage() . "</div>";
}
?>