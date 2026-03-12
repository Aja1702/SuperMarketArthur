<?php

// Cargar nuestro logger
$logger = require_once __DIR__ . '/logger.php';

use Monolog\ErrorHandler;

// Registrar el manejador de errores de Monolog
$handler = new ErrorHandler($logger);
$handler->registerExceptionHandler();
$handler->registerErrorHandler();
$handler->registerFatalHandler();

// --- Configuración adicional de PHP para asegurar que todos los errores se reporten al log, pero NO por pantalla ---

// Desactivar visualización de errores por pantalla para el usuario final (Estética Premium)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Seguimos capturándolo TODO, pero solo para el log.
