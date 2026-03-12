<?php

class Favorite
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Añade un producto a los favoritos de un usuario.
     */
    public function add($id_usuario, $id_producto)
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO favoritos (id_usuario, id_producto) VALUES (?, ?)");
        return $stmt->execute([$id_usuario, $id_producto]);
    }

    /**
     * Elimina un producto de los favoritos de un usuario.
     */
    public function remove($id_usuario, $id_producto)
    {
        $stmt = $this->pdo->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_producto = ?");
        return $stmt->execute([$id_usuario, $id_producto]);
    }

    /**
     * Comprueba si un producto es favorito para un usuario.
     */
    public function isFavorite($id_usuario, $id_producto)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favoritos WHERE id_usuario = ? AND id_producto = ?");
        $stmt->execute([$id_usuario, $id_producto]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene todos los productos favoritos de un usuario.
     */
    public function getByUser($id_usuario)
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.* FROM productos p JOIN favoritos f ON p.id_producto = f.id_producto WHERE f.id_usuario = ?"
        );
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
