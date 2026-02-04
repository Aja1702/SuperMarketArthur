<?php
session_start();
include '../config/iniciar_session.php'; // tu archivo de conexión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errores = '';

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Error CSRF: Token inválido');
    }

    // ✔ Validar que el email no esté vacío y que sea un email válido
    if ($email === '' || $password === '') {
        $errores = 'Campos obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores = 'Debe insertar un correo válido';
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario, pass, tipo_usu FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) { 
            $usuario = $result; 
            $passBD = $usuario['pass'];
            $esHash = (strpos($passBD, '$') === 0); // Si empieza por '$', probablemente ya es un hash

            if (
                // 1. Si la contraseña en la BD es texto plano y coincide "Root_Arturo_2002"
                !$esHash && $password === $passBD
            ) {

                // Migramos al hash seguro
                $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
                $updStmt = $pdo->prepare("UPDATE usuarios SET pass=? WHERE id_usuario=?");
                $updStmt->execute( [$nuevoHash, $usuario['id_usuario']] ); 
                $updStmt = null;

                // Login exitoso
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['tipo_usu'] = $usuario['tipo_usu'];
                header("Location: /SuperMarketArthur/");
                exit();

                // 2. Si la contraseña ya es un hash y verifica correctamente
            } elseif ($esHash && password_verify($password, $passBD)) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['tipo_usu'] = $usuario['tipo_usu'];
                header("Location: /SuperMarketArthur/");
                exit();
            } else {
                $errores = 'Contraseña incorrecta';
            }
        } else {
            $errores = 'Correo electrónico no encontrado en el sistema';
        }
        $stmt = null;
    }
    $pdo = null;
}
if ($errores !== '') {
?>
    <div id="customAlert" class="custom-alert">
        <div class="custom-alert-content">
            <span class="close-btn" onclick="closeAlert()">&times;</span>
            <p>
                <?php
                    echo nl2br(htmlspecialchars($errores));
                ?>
            </p>
            <button onclick="closeAlert()">Cerrar</button>
        </div>
    </div>

    <style>
        .custom-alert {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .custom-alert-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            max-width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .custom-alert-content p {
            margin: 20px 0;
            font-size: 16px;
            color: #333;
            white-space: pre-line;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #555;
        }

        .custom-alert-content button {
            background: #007BFF;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }

        .custom-alert-content button:hover {
            background: #0056b3;
        }
    </style>

    <script>
        function closeAlert() {
            document.getElementById('customAlert').style.display = 'none';
            window.history.back(); // Para volver atrás si quieres
        }
    </script>
<?php
    exit();
}
?>