<?php
/**
 * Funciones Auxiliares Globales
 * 
 * Funciones helper para asistencias comunes en toda la aplicación
 */

/**
 * Base URL
 */
function base_url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Renderizar HTML seguro
 */
function html($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirigir a una URL
 */
function redirect($url)
{
    header('Location: ' . base_url($url));
    exit();
}

/**
 * Obtener variable POST de forma segura
 */
function post($key, $default = null)
{
    return $_POST[$key] ?? $default;
}

/**
 * Obtener variable GET de forma segura
 */
function get($key, $default = null)
{
    return $_GET[$key] ?? $default;
}

/**
 * Obtener variable SESSION
 */
function session($key, $default = null)
{
    return $_SESSION[$key] ?? $default;
}

/**
 * Debug (muestra información de forma legible)
 */
function debug($data)
{
    echo '<pre style="background:#f0f0f0;padding:10px;border:1px solid #ccc;">';
    var_dump($data);
    echo '</pre>';
}

/**
 * Verificar si usuario está autenticado
 */
function is_authenticated()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar si usuario es administrador
 */
function is_admin()
{
    return isset($_SESSION['tipo_usu']) && $_SESSION['tipo_usu'] === 'a';
}

/**
 * Obtener URL de recurso público
 */
function asset($path)
{
    return base_url('/public/' . ltrim($path, '/'));
}

/**
 * Formatear precio
 */
function format_price($price, $currency = '€')
{
    return number_format($price, 2, ',', '.') . ' ' . $currency;
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $suffix = '...')
{
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Slug de URL
 */
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = preg_replace('~-+~', '-', $text);
    return strtolower(trim($text, '-'));
}
