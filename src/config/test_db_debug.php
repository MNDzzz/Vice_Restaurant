<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostico de Base de Datos</h1>";

require_once 'DB.php';

echo "<h2>Variables de Entorno</h2>";
echo "<p>DB_HOST: " . (getenv('DB_HOST') ?: 'No definido') . "</p>";
echo "<p>DB_USER: " . (getenv('DB_USER') ?: 'No definido') . "</p>";
echo "<p>DB_NAME: " . (getenv('DB_NAME') ?: 'No definido') . "</p>";

echo "<h2>Prueba de Conexión via DB Class</h2>";
try {
    $db = DB::getInstance();
    $conn = $db->getConnection();
    echo "<div style='color: green'> Conexión exitosa via clase DB.</div>";

    // Test query simple
    $stmt = $conn->query("SELECT 1");
    echo "<p>Query de prueba exitosa.</p>";
} catch (Exception $e) {
    echo "<div style='color: red'> Error via clase DB: " . $e->getMessage() . "</div>";

    // Introspeccionar los valores de la clase de base de datos si es posible, o intentar una conexión PDO directa.
    echo "<h3>Intento Directo PDO con credenciales hardcoded (Debugging)</h3>";
    $host = 'sql310.infinityfree.com'; // Tus credenciales de DB.php
    $user = 'if0_40839903';
    $pass = 'O3H5GMyYqn4Av';
    $name = 'if0_40839903_vice_db';

    try {
        $pdo = new PDO("mysql:host={$host};dbname={$name}", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div style='color: green'> Conexión exitosa directa PDO (Credenciales Hardcoded).</div>";
        echo "<p>Esto significa que las variables de entorno o la logica en DB.php estan fallando.</p>";
    } catch (PDOException $ex) {
        echo "<div style='color: red'> Error intento directo PDO: " . $ex->getMessage() . "</div>";
        echo "<p>Esto significa que el servidor no puede contactar a la BD externa, o las credenciales son incorrectas.</p>";
    }
}
?>