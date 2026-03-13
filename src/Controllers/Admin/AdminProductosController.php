<?php

namespace App\Controllers\Admin;

require_once __DIR__ . '/../BaseController.php';
use App\Controllers\BaseController;
require_once __DIR__ . '/../../Models/Product.php'; // Se carga una sola vez

class AdminProductosController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        // 1. Primero, la autenticación
        if (!isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'a') {
            header('Location: /SuperMarketArthur/login');
            exit();
        }

        // 2. Después, se crea el modelo y se hace disponible para toda la clase
        global $pdo;
        $this->productModel = new \Product($pdo);
    }

    /**
     * Muestra la lista de todos los productos, ahora con paginación.
     */
    public function index()
    {
        global $productos_por_pagina_config;

        // 1. Obtener la página actual desde la URL, con validación.
        $pagina_actual = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (!$pagina_actual || $pagina_actual < 1) {
            $pagina_actual = 1;
        }

        // 2. Obtener el total de productos para calcular la paginación.
        $total_productos = $this->productModel->getTotalProducts();
        $productos_por_pagina = $productos_por_pagina_config ?? 10; // Usar config o un valor por defecto
        $total_paginas = ceil($total_productos / $productos_por_pagina);

        // 3. Calcular el offset para la consulta SQL.
        $offset = ($pagina_actual - 1) * $productos_por_pagina;

        // 4. Pedir al modelo SOLO los productos de la página actual.
        $productos = $this->productModel->getAllProducts($productos_por_pagina, $offset);

        // 5. Preparar todos los datos para la vista.
        $data = [
            'productos' => $productos,
            'paginacion' => [
                'pagina_actual' => $pagina_actual,
                'total_paginas' => $total_paginas
            ]
        ];

        $this->view('admin/productos/listado_productos', $data);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function showNewProductForm()
    {
        $categorias = $this->productModel->getCategories();

        $this->view('admin/productos/formulario_producto', [
            'producto' => [], // Array vacío para un nuevo producto
            'categorias' => $categorias,
            'modo' => 'crear'
        ]);
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function showEditForm()
    {
        // 1. Validamos que nos llega un ID numérico
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: /SuperMarketArthur/admin/productos');
            exit();
        }

        // 2. Pedimos los datos del producto al Modelo
        $producto = $this->productModel->getProductById($id);

        // Si el producto no existe, lo mandamos de vuelta al listado
        if (!$producto) {
            // Futuro: Podríamos añadir un mensaje de error a la sesión
            header('Location: /SuperMarketArthur/admin/productos');
            exit();
        }

        // 3. Pedimos también las categorías para rellenar el desplegable
        $categorias = $this->productModel->getCategories();

        // 4. Llamamos a la misma vista del formulario, pero esta vez con datos
        $this->view('admin/productos/formulario_producto', [
            'producto' => $producto,
            'categorias' => $categorias,
            'modo' => 'editar'
        ]);
    }

    /**
     * Procesa la creación de un nuevo producto.
     */
    public function createProduct()
    {
        $productData = $_POST['producto'] ?? [];

        if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/img/productos/';
            $filename = uniqid() . '-' . basename($_FILES['imagen_producto']['name']);
            $uploadFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $uploadFile)) {
                $productData['url_imagen'] = '/SuperMarketArthur/public/assets/img/productos/' . $filename;
            }
        }

        if ($this->productModel->addProduct($productData)) {
            header('Location: /SuperMarketArthur/admin/productos');
        } else {
            // Futuro: Añadir mensaje de error
            header('Location: /SuperMarketArthur/admin/productos');
        }
        exit();
    }

    /**
     * Procesa la actualización de un producto existente.
     */
    public function updateProduct()
    {
        $productData = $_POST['producto'] ?? [];
        $id = $productData['id_producto'] ?? null;

        // Validamos que tenemos un ID para actualizar
        if (!$id) {
            // Error: no sabemos qué producto actualizar
            header('Location: /SuperMarketArthur/admin/productos');
            exit();
        }

        // Gestión de la imagen (similar a createProduct)
        if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/img/productos/';
            $filename = uniqid() . '-' . basename($_FILES['imagen_producto']['name']);
            $uploadFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $uploadFile)) {
                $productData['url_imagen'] = '/SuperMarketArthur/public/assets/img/productos/' . $filename;
            }
        }

        // El ID no debe formar parte de los datos a actualizar
        unset($productData['id_producto']);

        if ($this->productModel->updateProduct($id, $productData)) {
            header('Location: /SuperMarketArthur/admin/productos');
        } else {
            // Futuro: Añadir mensaje de error
            header('Location: /SuperMarketArthur/admin/productos');
        }
        exit();
    }

    /**
     * Muestra la página de confirmación para eliminar un producto.
     */
    public function showDeleteConfirmation()
    {
        // 1. Validamos que nos llega un ID numérico
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: /SuperMarketArthur/admin/productos');
            exit();
        }

        // 2. Pedimos los datos del producto al Modelo
        $producto = $this->productModel->getProductById($id);

        // Si el producto no existe, lo mandamos de vuelta al listado
        if (!$producto) {
            header('Location: /SuperMarketArthur/admin/productos');
            exit();
        }

        // 3. Llamamos a una nueva vista de confirmación
        $this->view('admin/productos/confirmar_eliminacion', [
            'producto' => $producto
        ]);
    }

    /**
     * Procesa la eliminación de un producto.
     */
    public function deleteProduct()
    {
        // 1. Obtenemos el ID del producto desde el formulario POST
        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);

        if ($id) {
            // 2. Le damos la orden al Modelo de eliminar el producto
            $this->productModel->deleteProductById($id); // <-- Tendremos que crear este método
        }

        // 3. Redirigimos siempre al listado de productos
        header('Location: /SuperMarketArthur/admin/productos');
        exit();
    }
}
