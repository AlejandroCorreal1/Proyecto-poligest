<?php
/**
 * CONEXIÓN A LA BASE DE DATOS CON PDO
 * 
 * PDO = PHP Data Objects (objetos de datos de PHP)
 * Permite conectar con MySQL de forma SEGURA
 */

// Datos de conexión al servidor MySQL
$host = 'localhost';      // Servidor donde está MySQL
$dbname = 'poligest';     // Nombre de nuestra base de datos
$username = 'root';       // Usuario de MySQL
$password = '';           // Contraseña (vacía en XAMPP)

try {
    // Crear la conexión. Los ? no van aquí, van en las consultas
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar PDO para que muestre errores cuando algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Si hay error de conexión, mostrar mensaje y parar
    die("Error de conexión: " . $e->getMessage());
}
?>