<?php

namespace App\Controllers;

require_once __DIR__ . '/../../models/Product.php';
use Product;

class SearchController
{
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function search()
    {
        global $pdo, $simbolo_moneda;

        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            return $this->jsonResponse(['success' => false, 'message' => 'La búsqueda requiere al menos 2 caracteres']);
        }

        $productModel = new Product($pdo);
        $results = $productModel->searchProducts($query, 5); // Limitamos a 5 resultados como en el original

        $formattedResults = [];
        foreach ($results as $item) {
            $item['precio_formatted'] = number_format($item['precio'], 2, ',', '.') . htmlspecialchars($simbolo_moneda);
            $formattedResults[] = $item;
        }

        $this->jsonResponse([
            'success' => true,
            'results' => $formattedResults
        ]);
    }
}
