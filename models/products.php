<?php
class Product {
public function getAllProducts($conn) {
$sql = "SELECT id, nombre, precio FROM productos";
$result = $conn->query($sql);
return $result->fetch_all(MYSQLI_ASSOC);
}
}