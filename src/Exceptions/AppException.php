<?php

namespace App\Exceptions;

/**
 * Excepción de No Encontrado
 * Se lanza cuando un recurso no existe
 */
class NotFoundException extends \Exception
{
    public function __construct($message = 'Recurso no encontrado', $code = 404)
    {
        parent::__construct($message, $code);
    }
}

/**
 * Excepción de No Autorizado
 * Se lanza cuando el usuario no tiene permisos
 */
class UnauthorizedException extends \Exception
{
    public function __construct($message = 'No autorizado', $code = 403)
    {
        parent::__construct($message, $code);
    }
}

/**
 * Excepción de Validación
 * Se lanza cuando los datos tienen errores de validación
 */
class ValidationException extends \Exception
{
    protected $errors = [];

    public function __construct(array $errors = [], $message = 'Error de validación', $code = 422)
    {
        $this->errors = $errors;
        parent::__construct($message, $code);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

/**
 * Excepción del Servidor
 * Se lanza cuando hay errores en el servidor
 */
class ServerException extends \Exception
{
    public function __construct($message = 'Error interno del servidor', $code = 500)
    {
        parent::__construct($message, $code);
    }
}
