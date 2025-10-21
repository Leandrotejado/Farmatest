<?php
// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$db = "farmacia";

try {
    // Conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Conexión MySQLi (para compatibilidad con código existente)
    $conn = new mysqli($host, $user, $password, $db);
    if ($conn->connect_error) {
        die("Error de conexión MySQLi: " . $conn->connect_error);
    }
    
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
?>