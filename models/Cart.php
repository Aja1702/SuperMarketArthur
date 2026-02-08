<?php
class Cart {
    private $conn;
    private $userId;
    private $sessionId;

    public function __construct($conn, $userId = null) {
        $this->conn = $conn;
        $this->userId = $userId;
        $this->sessionId = session_id();
    }

    public function addItem($productId, $quantity = 1) {
        // Verificar stock
        if (!$this->checkStock($productId, $quantity)) {
            return false;
        }

        if ($this->userId) {
            return $this->addToDbCart($productId, $quantity);
        } else {
            return $this->addToSessionCart($productId, $quantity);
        }
    }

    private function addToDbCart($productId, $quantity) {
        // Obtener o crear carrito
        $cartId = $this->getOrCreateCartId();

        // Verificar si el producto ya está en el carrito
        $sql = "SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cartId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingItem = $result->fetch_assoc();

        if ($existingItem) {
            $newQuantity = $existingItem['cantidad'] + $quantity;
            if (!$this->checkStock($productId, $newQuantity)) {
                return false;
            }
            $sql = "UPDATE carrito_items SET cantidad = ? WHERE id_item = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $newQuantity, $existingItem['id_item']);
        } else {
            $sql = "INSERT INTO carrito_items (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $cartId, $productId, $quantity);
        }
        return $stmt->execute();
    }

    private function addToSessionCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $newQuantity = $_SESSION['cart'][$productId] + $quantity;
            if (!$this->checkStock($productId, $newQuantity)) {
                return false;
            }
            $_SESSION['cart'][$productId] = $newQuantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        return true;
    }

    public function updateQuantity($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        if (!$this->checkStock($productId, $quantity)) {
            return false;
        }

        if ($this->userId) {
            $cartId = $this->getCartId();
            if ($cartId) {
                $sql = "UPDATE carrito_items SET cantidad = ? WHERE id_carrito = ? AND id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iii", $quantity, $cartId, $productId);
                return $stmt->execute();
            }
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = $quantity;
                return true;
            }
        }
        return false;
    }

    public function removeItem($productId) {
        if ($this->userId) {
            $cartId = $this->getCartId();
            if ($cartId) {
                $sql = "DELETE FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $cartId, $productId);
                return $stmt->execute();
            }
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                return true;
            }
        }
        return false;
    }

    public function getItems() {
        if ($this->userId) {
            return $this->getDbCartItems();
        } else {
            return $this->getSessionCartItems();
        }
    }

    private function getDbCartItems() {
        $cartId = $this->getCartId();
        if (!$cartId) return [];

        $sql = "SELECT ci.id_producto, ci.cantidad, p.nombre_producto, p.precio, p.url_imagen, p.stock
                FROM carrito_items ci
                JOIN productos p ON ci.id_producto = p.id_producto
                WHERE ci.id_carrito = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getSessionCartItems() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return [];

        $items = [];
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->getProductDetails($productId);
            if ($product) {
                $product['cantidad'] = $quantity;
                $items[] = $product;
            }
        }
        return $items;
    }

    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }

    public function getItemCount() {
        $items = $this->getItems();
        $count = 0;
        foreach ($items as $item) {
            $count += $item['cantidad'];
        }
        return $count;
    }

    public function clearCart() {
        if ($this->userId) {
            $cartId = $this->getCartId();
            if ($cartId) {
                $sql = "DELETE FROM carrito_items WHERE id_carrito = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $cartId);
                $stmt->execute();
            }
        } else {
            unset($_SESSION['cart']);
        }
    }

    private function getOrCreateCartId() {
        $cartId = $this->getCartId();
        if (!$cartId) {
            $sql = "INSERT INTO carrito_temp (id_usuario) VALUES (?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $cartId = $this->conn->insert_id;
        }
        return $cartId;
    }

    private function getCartId() {
        $sql = "SELECT id_carrito FROM carrito_temp WHERE id_usuario = ? AND creado_en > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY creado_en DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart = $result->fetch_assoc();
        return $cart ? $cart['id_carrito'] : null;
    }

    private function checkStock($productId, $quantity) {
        $product = $this->getProductDetails($productId);
        return $product && $product['stock'] >= $quantity;
    }

    private function getProductDetails($productId) {
        $sql = "SELECT id_producto, nombre_producto, precio, url_imagen, stock FROM productos WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function mergeSessionCartToDb() {
        if (!$this->userId || !isset($_SESSION['cart'])) return;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $this->addToDbCart($productId, $quantity);
        }
        unset($_SESSION['cart']);
    }
}
