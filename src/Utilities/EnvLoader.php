<?php

/**
 * Cargador de variables de entorno desde archivo .env
 */

class EnvLoader
{
    private static $loaded = false;
    private static $vars = [];

    /**
     * Carga las variables del archivo .env
     */
    public static function load()
    {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../../.env';
        
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parsear variables
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Eliminar comillas si existen
                if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                    $value = $matches[1];
                }

                self::$vars[$name] = $value;
                putenv("$name=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Obtiene una variable de entorno
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        $value = getenv($key);
        
        if ($value === false) {
            return self::$vars[$key] ?? $default;
        }

        return $value;
    }
}

// Cargar automáticamente al incluir el archivo
EnvLoader::load();
