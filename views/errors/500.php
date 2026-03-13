<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del servidor | <?php echo htmlspecialchars($nombre_sitio ?? 'SuperMarketArthur'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
        }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: #e53e3e;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 28px;
            color: #1a202c;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .error-message {
            font-size: 16px;
            color: #718096;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(229, 62, 62, 0.4);
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .contact-info {
            font-size: 14px;
            color: #a0aec0;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚙️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Error del servidor</h1>
        <p class="error-message">
            Lo sentimos, algo salió mal en nuestro servidor.
            <br>Por favor, inténtalo de nuevo más tarde.
        </p>
        <a href="<?php echo isset($rutas) ? $rutas['base_url'] : '/SuperMarketArthur/'; ?>" class="btn-home">
            🏠 Volver al inicio
        </a>
        <p class="contact-info">
            Si el problema persiste, contacta con nuestro equipo de soporte.
        </p>
    </div>
</body>
</html>
