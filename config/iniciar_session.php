<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "supermarketarthur";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
