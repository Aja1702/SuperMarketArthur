<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];
?>

<div id="loginModal" class="modal-overlay" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
    <div class="modal-content">
        <button class="close-modal" id="closeLoginModal" aria-label="Cerrar modal">&times;</button>
        <div class="form-container-login">
            <h2 id="loginModalTitle">Iniciar sesión para continuar</h2>
            <p>Debes iniciar sesión para finalizar tu compra.</p>
            <form id="loginModalForm" method="post" action="./controllers/procesar_login.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="return_to" value="checkout.php"> <!-- Para la redirección -->

                <div class="form-group-login">
                    <label for="modal_email">Correo electrónico *</label>
                    <input type="email" id="modal_email" name="email" autocomplete="email" required>

                    <label for="modal_password">Contraseña *</label>
                    <input type="password" id="modal_password" name="password" required>
                </div>

                <p>* Campos obligatorios</p>
                <span id="modal_mensaje-error" class="mensaje-error"></span>
                <button type="submit" class="btn-form-acceso">Acceder</button>
            </form>
            <p style="margin-top: 1rem;">¿No tienes cuenta? <a href="./?userSession=registro">Regístrate aquí</a></p>
        </div>
    </div>
</div>
