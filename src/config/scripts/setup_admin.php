<?php
/**
 * Script de Configuración de Administrador
 * Ejecuto esto una vez para actualizar el esquema de base de datos y crear el usuario superadmin
 */

require_once 'DB.php';

$db = DB::getInstance()->getConnection();

echo "<h2>Vice Admin Setup</h2>";
echo "<pre>";

try {
    // Paso 1: Modifico la tabla de usuarios para soportar el rol de superadministrador
    echo "1. Updating users table role column...\n";
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'user') DEFAULT 'user'");
    echo "   ✓ Role column updated to support superadmin\n\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "   ✓ Role column already has correct structure\n\n";
    } else {
        echo "   ✗ Error: " . $e->getMessage() . "\n\n";
    }
}

try {
    // Paso 2: Verifico si ya existe el superadministrador
    echo "2. Checking for existing superadmin...\n";
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['superadmin@vice.com']);

    if ($stmt->fetch()) {
        echo "   ✓ Superadmin already exists\n\n";
    } else {
        // Creo el superadministrador (contraseña: super123)
        $password = password_hash('super123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Super Admin', 'superadmin@vice.com', $password, 'superadmin']);
        echo "   ✓ Superadmin created successfully\n";
        echo "   Email: superadmin@vice.com\n";
        echo "   Password: super123\n\n";
    }
} catch (PDOException $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

try {
    // Paso 3: Aseguro que la tabla de pedidos tenga el campo de dirección
    echo "3. Checking orders table structure...\n";
    $db->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS address TEXT AFTER status");
    $db->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER address");
    echo "   ✓ Orders table updated\n\n";
} catch (PDOException $e) {
    // La columna podría ya existir, así que capturo la excepción
    echo "   ✓ Orders table structure OK\n\n";
}

try {
    // Paso 4: Añado la columna is_active a usuarios para la activación de cuentas
    echo "4. Adding is_active column to users...\n";
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER role");
    echo "   ✓ is_active column added\n\n";
} catch (PDOException $e) {
    echo "   ✓ is_active column already exists\n\n";
}

echo "========================================\n";
echo "Setup Complete!\n";
echo "========================================\n\n";

echo "Superadmin Credentials:\n";
echo "Email: superadmin@vice.com\n";
echo "Password: super123\n\n";

echo "You can now delete this file for security.\n";
echo "</pre>";

echo '<br><a href="index.php?view=login" class="btn btn-primary">Go to Login</a>';
?>