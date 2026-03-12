<?php
class Cart {
    private $pdo;
    private $userId;
    private $sessionId;

    public function __construct($pdo, $userId = null) {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->sessionId = session_id();
    }

    public function addItem($productId, $quantity = 1) {
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
        $cartId = $this->getOrCreateCartId();

        $sql = "SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cartId, $productId]);
        $existingItem = $stmt->fetch();

        if ($existingItem) {
            $newQuantity = $existingItem['cantidad'] + $quantity;
            if (!$this->checkStock($productId, $newQuantity)) {
                return false;
            }
            $sql = "UPDATE carrito_items SET cantidad = ? WHERE id_item = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$newQuantity, $existingItem['id_item']]);
        } else {
            $sql = "INSERT INTO carrito_items (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cartId, $productId, $quantity]);
        }
    }

    private function addToSessionCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $currentQuantity = $_SESSION['cart'][$productId] ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        if (!$this->checkStock($productId, $newQuantity)) {
            return false;
        }

        $_SESSION['cart'][$productId] = $newQuantity;
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
                $sql = "SELECT id_item FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$cartId, $productId]);
                if($stmt->fetch()){
                    $sql = "UPDATE carrito_items SET cantidad = ? WHERE id_carrito = ? AND id_producto = ?";
                    $stmt = $this->pdo->prepare($sql);
                    return $stmt->execute([$quantity, $cartId, $productId]);
                } else {
                    return $this->addItem($productId, $quantity);
                }
            }
        } else {
            if (isset($_SESSION['cart'][$productId]) || $this->checkStock($productId, $quantity)) {
                $_SESSION['cart'][$productId] = $quantity;
                return true;
            }
        }
        return false;
    }

    public function adjustQuantity($productId, $delta) {
        $items = $this->getItems();
        $currentQuantity = 0;
        foreach($items as $item) {
            if ($item['id_producto'] == $productId) {
                $currentQuantity = $item['cantidad'];
                break;
            }
        }
        $newQuantity = $currentQuantity + $delta;
        return $this->updateQuantity($productId, $newQuantity);
    }

    public function removeItem($productId) {
        if ($this->userId) {
            $cartId = $this->getCartId();
            if ($cartId) {
                $sql = "DELETE FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$cartId, $productId]);
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSessionCartItems() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return [];

        $items = [];
        $productIds = array_keys($_SESSION['cart']);
        if(empty($productIds)) return [];

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $sql = "SELECT id_producto, nombre_producto, precio, url_imagen, stock FROM productos WHERE id_producto IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productsById = [];
        foreach($products as $product){
            $productsById[$product['id_producto']] = $product;
        }

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            if (isset($productsById[$productId])) {
                $product = $productsById[$productId];
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
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$cartId]);
            }
        } else {
            unset($_SESSION['cart']);
        }
    }

    private function getOrCreateCartId() {
        $cartId = $this->getCartId();
        if (!$cartId && $this->userId) {
            $sql = "INSERT INTO carrito_temp (id_usuario) VALUES (?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->userId]);
            $cartId = $this->pdo->lastInsertId();
        }
        return $cartId;
    }

    private function getCartId() {
        if (!$this->userId) return null;
        $sql = "SELECT id_carrito FROM carrito_temp WHERE id_usuario = ? AND creado_en > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY creado_en DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->userId]);
        $cart = $stmt->fetch();
        return $cart ? $cart['id_carrito'] : null;
    }

    private function checkStock($productId, $quantity) {
        $product = $this->getProductDetails($productId);
        return $product && $product['stock'] >= $quantity;
    }

    private function getProductDetails($productId) {
        $sql = "SELECT id_producto, nombre_producto, precio, url_imagen, stock FROM productos WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function mergeSessionCartToDb() {
        if (!$this->userId || !isset($_SESSION['cart'])) return;

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $this->addToDbCart($productId, $quantity);
        }
        unset($_SESSION['cart']);
    }
}
?>