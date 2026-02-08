<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, apellido1, apellido2, provincia, localidad, cp, calle, numero, telefono, email, tipo_doc, num_doc, fecha_nacimiento, pass, tipo_usu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'u')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nombre'], $data['apellido1'], $data['apellido2'], $data['provincia'],
            $data['localidad'], $data['cp'], $data['calle'], $data['numero'],
            $data['telefono'], $data['email'], $data['tipo_doc'], $data['num_doc'],
            $data['fecha_nacimiento'], $hashedPassword
        ]);
    }

    public function login($email, $password) {
        $sql = "SELECT id_usuario, nombre, pass, tipo_usu FROM usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['pass'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $sql = "SELECT id_usuario, nombre, apellido1, apellido2, email, tipo_usu FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $data) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido1 = ?, apellido2 = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nombre'], $data['apellido1'], $data['apellido2'],
            $data['telefono'], $id
        ]);
    }

    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET pass = ? WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(id_usuario) FROM usuarios WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql .= " AND id_usuario != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllUsers($limit = null, $offset = 0) {
        $sql = "SELECT id_usuario, nombre, apellido1, email, tipo_usu, fecha_registro FROM usuarios WHERE tipo_usu = 'u' ORDER BY fecha_registro DESC";
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = ? AND tipo_usu = 'u'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>