<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "supermarketarthur";

$conn = mysqli_connect($servername, $username, $password, $database);

try {
    $conn = new PDO("mysql:dbname=$database;charset=utf8mb4", $username, $password);
    // Configurar PDO para lanzar excepciones en errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
