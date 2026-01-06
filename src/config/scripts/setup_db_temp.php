<?php
require_once 'DB.php';

try {
    // Conecto sin nombre de base de datos primero para crearla si no existe
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS vice_db");
    echo "Database 'vice_db' checked/created.\n";

    // Ahora uso la clase DB que conecta a vice_db
    $db = DB::getInstance()->getConnection();

    // Leo el archivo SQL
    $sql = file_get_contents('database.sql');

    // Ejecuto el SQL dividiéndolo manuamente para evitar problemas con múltiples sentencias
    // PDO::exec a veces tiene problemas con múltiples sentencias dependiendo del driver.
    // Lo divido manualmente para estar seguro.

    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                // Ignoro "Table already exists" o similar si quiero ser idempotente,
                // pero por ahora solo muestro errores.
                echo "Error executing statement: " . substr($statement, 0, 50) . "... -> " . $e->getMessage() . "\n";
            }
        }
    }

    echo "Database import process completed.\n";

    // Verifico la existencia del usuario administrador
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@vice.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "Admin user found: " . $user['email'] . "\n";
        // Verifico la contraseña
        if (password_verify('admin123', $user['password'])) {
            echo "Password 'admin123' is VALID.\n";
        } else {
            echo "Password 'admin123' is INVALID. Hash: " . $user['password'] . "\n";
            // La corrijo si es incorrecta
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$newHash, $user['id']]);
            echo "Password reset to 'admin123'.\n";
        }
    } else {
        echo "Admin user NOT found. Creating...\n";
        $pass = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $ins->execute(['Admin Vice', 'admin@vice.com', $pass, 'admin']);
        echo "Admin user created.\n";
    }

} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
?>