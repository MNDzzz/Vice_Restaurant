<?php
require_once 'DB.php';

try {
    $db = DB::getInstance()->getConnection();

    // 1. Actualizo el ENUM para el rol e incluyo 'superadmin'
    // Nota: modificar ENUMs puede ser complicado en algunos dialectos SQL, pero en MySQL:
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'user') DEFAULT 'user'");
    echo "Role ENUM updated.\n";

    // 2. Creo el Usuario Superadministrador
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['superadmin@vice.com']);
    $super = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$super) {
        $pass = password_hash('superadmin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Super Admin', 'superadmin@vice.com', $pass, 'superadmin']);
        echo "Superadmin created: superadmin@vice.com / superadmin123\n";
    } else {
        echo "Superadmin already exists.\n";
        // Aseguro que el rol sea superadmin
        if ($super['role'] !== 'superadmin') {
            $db->prepare("UPDATE users SET role='superadmin' WHERE id=?")->execute([$super['id']]);
            echo "Updated existing superadmin user to role 'superadmin'.\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>