<?php
require_once __DIR__ .'/../config/database.php';

try {
    // 1. Crear instancia de Database
    $database = new Database();
    
    // 2. Obtener conexión
    $db = $database->connect();
    
    // 3. Ejecutar consulta simple
    $stmt = $db->query("SELECT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 4. Mostrar resultados
    echo "<h2>Conexión exitosa!</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // 5. Verificar versión de MySQL/MariaDB
    $version = $db->query("SELECT VERSION()")->fetchColumn();
    echo "<p>Versión de la base de datos: $version</p>";
    
} catch (PDOException $e) {
    echo "<h2>Error de conexión:</h2>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    
    // Mostrar detalles de configuración (solo para desarrollo)
    echo "<h3>Configuración usada:</h3>";
    echo "<pre>";
    print_r([
        'host' => 'localhost',
        'dbname' => 'eventos', // Reemplaza con tu nombre de BD
        'username' => 'root',
        'password' => '' // Reemplaza con tu contraseña si existe
    ]);
    echo "</pre>";
}
?>
