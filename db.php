<?php
// Configuración centralizada de base de datos
$host = '127.0.0.1';
$db   = 'soporte_db';
$user = 'root';
$pass = '';
$port = 3306;

mysqli_report(MYSQLI_REPORT_OFF);

function getDBConnection() {
    global $host, $db, $user, $pass, $port;
    
    // Intento 1: Puerto 3306 (XAMPP / MariaDB por defecto)
    $conn = @new mysqli($host, $user, $pass, $db, $port);
    
    // Intento 2: Si falla, intentar puerto 3307
    if ($conn->connect_error) {
        $conn = @new mysqli($host, $user, $pass, $db, 3307);
    }
    
    // Intento 3: Conexión localhost estándar
    if ($conn->connect_error) {
        $conn = @new mysqli('localhost', $user, $pass, $db);
    }

    if ($conn->connect_error) {
        return false;
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}
