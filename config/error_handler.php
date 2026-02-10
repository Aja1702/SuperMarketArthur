<?php

// Cargar nuestro logger
$logger = require_once __DIR__ . '/logger.php';

use Monolog\ErrorHandler;

// Registrar el manejador de errores de Monolog
$handler = new ErrorHandler($logger);
$handler->registerExceptionHandler();
$handler->registerErrorHandler();
$handler->registerFatalHandler();

// --- Configuración adicional de PHP para asegurar que todos los errores se reporten ---

// Mostrar todos los errores (ideal para desarrollo)
// En producción, esto debería ser 0 y los errores solo irían al log.
ini_set('display_errors', 1);
error_reporting(E_ALL);
