<?php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, apellido1, apellido2, provincia, localidad, cp, calle, numero, telefono, email, tipo_doc, num_doc, fecha_nacimiento, pass, tipo_usu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'u')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssssssssss", $data['nombre'], $data['apellido1'], $data['apellido2'], $data['provincia'], $data['localidad'], $data['cp'], $data['calle'], $data['numero'], $data['telefono'], $data['email'], $data['tipo_doc'], $data['num_doc'], $data['fecha_nacimiento'], $hashedPassword);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $sql = "SELECT id_usuario, nombre, pass, tipo_usu FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['pass'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $sql = "SELECT id_usuario, nombre, apellido1, apellido2, email, tipo_usu FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateProfile($id, $data) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido1 = ?, apellido2 = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $data['nombre'], $data['apellido1'], $data['apellido2'], $data['telefono'], $id);
        return $stmt->execute();
    }

    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET pass = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
        if ($excludeId) {
            $sql .= " AND id_usuario != ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($excludeId) {
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getAllUsers($limit = null, $offset = 0) {
        $sql = "SELECT id_usuario, nombre, apellido1, email, tipo_usu, fecha_registro FROM usuarios WHERE tipo_usu = 'u' ORDER BY fecha_registro DESC";
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($limit !== null) {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = ? AND tipo_usu = 'u'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
