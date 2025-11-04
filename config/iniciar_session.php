<?php
// Conexión a la base de datos
$host = "localhost";
$servername = "localhost";
$username = "root";
$password = "";
$database = "supermarketarthur";

$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password);
    // Configurar PDO para lanzar excepciones en errores
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
