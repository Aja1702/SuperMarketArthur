<?php
// Conexión a la base de datos

// Cargar variables de entorno
require_once __DIR__ . '/../src/Utilities/EnvLoader.php';

if (session_status() === PHP_SESSION_NONE) {
session_start();
}

$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_NAME') ?: 'supermarketarthur';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try { // $pdo es para la conexión PDO, mas seguro que mysqli.
    $pdo = new PDO($dsn, $username, $password);
    // Configurar PDO para lanzar excepciones en errores
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), 
    (int)$e->getCode());
}
