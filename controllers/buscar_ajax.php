<?php
require_once '../config/iniciar_session.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'results' => []]);
    exit;
}

try {
    $sql = "
        SELECT p.id_producto, p.nombre_producto, p.precio, p.url_imagen, c.nombre_categoria 
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.nombre_producto LIKE :q OR c.nombre_categoria LIKE :q
        LIMIT 6
    ";

    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->execute(['q' => $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as &$res) {
        $res['precio_formatted'] = number_format($res['precio'], 2, ',', '.') . '€';
    }

    echo json_encode([
        'success' => true,
        'results' => $results
    ]);

}
catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
