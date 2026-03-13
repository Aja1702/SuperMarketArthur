<?php

namespace App\Controllers;

class ContactoController
{
    public function index()
    {
        // Esta página es estática por ahora, solo necesita cargar la vista.
        $this->view('contacto');
    }

    public function send()
    {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contacto');
            exit;
        }

        // Obtener y sanitizar los datos del formulario
        $nombre = isset($_POST['nombre']) ? trim(htmlspecialchars($_POST['nombre'])) : '';
        $email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
        $mensaje = isset($_POST['mensaje']) ? trim(htmlspecialchars($_POST['mensaje'])) : '';

        // Validar campos requeridos
        $errores = [];
        
        if (empty($nombre)) {
            $errores[] = 'El nombre es obligatorio';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico es obligatorio y debe ser válido';
        }
        
        if (empty($mensaje)) {
            $errores[] = 'El mensaje es obligatorio';
        }

        // Si hay errores, volver a mostrar el formulario con errores
        if (!empty($errores)) {
            $this->view('contacto', [
                'errores' => $errores,
                'nombre' => $nombre,
                'email' => $email,
                'mensaje' => $mensaje
            ]);
            return;
        }

        // Enviar correo electrónico al administrador
        $email_admin = 'info@supermarketarthur.com'; // Email del administrador
        $asunto = 'Nuevo mensaje de contacto - SuperMarketArthur';
        
        // Construir el cuerpo del mensaje
        $cuerpo_mensaje = "Nuevo mensaje recibido desde el formulario de contacto:\n\n";
        $cuerpo_mensaje .= "Nombre: " . $nombre . "\n";
        $cuerpo_mensaje .= "Email: " . $email . "\n";
        $cuerpo_mensaje .= "Mensaje:\n" . $mensaje . "\n";
        
        // Cabeceras del correo
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Enviar el correo
        $correo_enviado = mail($email_admin, $asunto, $cuerpo_mensaje, $headers);
        
        // Mostrar mensaje de éxito
        $this->view('contacto', [
            'exito' => true,
            'mensaje_exito' => '¡Gracias por contactar con nosotros! Tu mensaje ha sido enviado correctamente. Te responderemos lo antes posible.'
        ]);
    }

    protected function view($view, $data = [])
    {
        global $nombre_sitio, $cache_version, $rutas, $tipo_usuario, $simbolo_moneda;

        $data = array_merge($data, [
            'nombre_sitio' => $nombre_sitio,
            'cache_version' => $cache_version,
            'rutas' => $rutas,
            'tipo_usuario' => $tipo_usuario,
            'simbolo_moneda' => $simbolo_moneda
        ]);

        extract($data);

        ob_start();
        require_once __DIR__ . "/../../views/{$view}.php";
        $content = (string)ob_get_clean();

        require_once __DIR__ . '/../../views/layout.php';
    }
}
