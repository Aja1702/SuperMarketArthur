<?php

class Config
{
    private $pdo;
    private $settings = [];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->loadAllSettings();
    }

    /**
     * Carga todas las configuraciones de la base de datos y las guarda en un array local.
     */
    private function loadAllSettings()
    {
        try {
            $stmt = $this->pdo->query("SELECT clave, valor FROM configuracion");
            $this->settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            $this->settings = [];
        }
    }

    /**
     * Obtiene un valor de configuración por su clave.
     *
     * @param string $key La clave de la configuración.
     * @param mixed $default El valor a devolver si la clave no existe.
     * @return mixed El valor de la configuración.
     */
    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Obtiene todas las configuraciones.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->settings;
    }

    /**
     * Actualiza una o más configuraciones en la base de datos.
     *
     * @param array $data Un array asociativo [clave => valor].
     * @return bool True si la actualización fue exitosa, False si no.
     */
    public function update(array $data)
    {
        $this->pdo->beginTransaction();

        try {
            $sql = "INSERT INTO configuracion (clave, valor) VALUES (:clave, :valor)
                    ON DUPLICATE KEY UPDATE valor = :valor";
            $stmt = $this->pdo->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->execute(['clave' => $key, 'valor' => $value]);
                $this->settings[$key] = $value;
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
