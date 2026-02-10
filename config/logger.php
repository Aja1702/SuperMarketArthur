<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;

// Crear el logger
$logger = new Logger('SuperMarketArthur');

// Crear la carpeta de logs si no existe
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Añadir un handler que guarde los logs en un archivo `app.log`
$logger->pushHandler(new StreamHandler($logDir . '/app.log', Logger::DEBUG));

// (Opcional pero muy útil) Añadir información extra a los logs, como la IP y la URL
$logger->pushProcessor(new WebProcessor());

return $logger;
