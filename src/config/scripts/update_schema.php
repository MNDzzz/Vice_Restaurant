<?php
require_once 'DB.php';
$db = DB::getInstance()->getConnection();

try {
    // 1. Modifico el ENUM para incluir 'superadmin'
    // Nota: En MySQL, modificar un ENUM requiere redefinir la columna.
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'superadmin') DEFAULT 'user'");
    echo "Table 'users' updated successfully.<br>";

    // 2. Inserto el Superadministrador
    $pass = password_hash('superadmin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Super Admin', 'superadmin@vice.com', $pass, 'superadmin']);
    echo "Superadmin created: superadmin@vice.com / superadmin123<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>