<?php

require_once __DIR__ . '/vendor/autoload.php';

// Incluir el manejador de errores ANTES de cualquier otra cosa
require_once __DIR__ . '/config/error_handler.php';

// --- CARGAR .ENV AL INICIO ---
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                $value = $matches[1];
            }
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// --- CONFIGURACIÓN DE RUTA BASE Y CACHÉ ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $protocol . $host . $path . '/');
$cache_version = time();

// Iniciar la sesión y la conexión a la base de datos
session_start();
require_once __DIR__ . '/config/iniciar_session.php';

// --- CARGA GLOBAL DE CONFIGURACIÓN ---
require_once __DIR__ . '/src/Models/Config.php';
$configModel = new Config($pdo);
$nombre_sitio = $configModel->get('NOMBRE_SITIO', 'SuperMarketArthur');
$stock_bajo_umbral = (int)$configModel->get('STOCK_BAJO_UMBRAL', 5);
$productos_por_pagina_config = (int)$configModel->get('PRODUCTOS_POR_PAGINA', 12);
$simbolo_moneda = $configModel->get('SIMBOLO_MONEDA', '€');
$modo_mantenimiento = $configModel->get('MODO_MANTENIMIENTO', '0');
$ip_confianza = $configModel->get('IP_CONFIANZA', '::1');
$valoraciones_habilitadas = (bool)$configModel->get('VALORACIONES_HABILITADAS', true);

// --- LÓGICA DE USUARIO ---
$tipo_usu = $_SESSION['tipo_usu'] ?? 'invitado';
$tipos_validos = ['a', 'u', 'i'];
if (!in_array($tipo_usu, $tipos_validos)) {
    $tipo_usu = 'invitado';
}
$tipo_usuario = $tipo_usu === 'a' ? 'administrador' : ($tipo_usu === 'u' ? 'usuario' : 'invitado');

// --- MODO MANTENIMIENTO (VERSIÓN MEJORADA) ---
if ($modo_mantenimiento == '1') {
    $ip_visitante = $_SERVER['REMOTE_ADDR'];
    if ($tipo_usuario !== 'administrador' && $ip_visitante !== $ip_confianza) {
        include __DIR__ . '/includes/mantenimiento.php';
        exit();
    }
}

// --- DEFINICIÓN DE RUTAS DE VISTAS (PARA LA PLANTILLA MAESTRA) ---
$rutas = [
    'administrador' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_administrador.php',
        'menu'     => __DIR__ . '/includes/menu/menu_administrador.php',
        'centro'   => '',
        'pie'      => __DIR__ . '/includes/pie/pie_administrador.php'
    ],
    'usuario' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_logueado.php',
        'menu'     => __DIR__ . '/includes/menu/menu_logueado.php',
        'centro'   => '',
        'pie'      => __DIR__ . '/includes/pie/pie_logueado.php'
    ],
    'invitado' => [
        'cabecera' => __DIR__ . '/includes/cabecera/cabecera_invitado.php',
        'menu'     => __DIR__ . '/includes/menu/menu_invitado.php',
        'centro'   => '',
        'pie'      => __DIR__ . '/includes/pie/pie_invitado.php'
    ]
];
