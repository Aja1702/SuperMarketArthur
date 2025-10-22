<div class="form-container-login">
    <h2>Iniciar sesión</h2>
    <form id="loginForm" method="post" action="./includes/centro/procesar_login.php">
        <div class="form-group-login">
            <label for="email">Correo electrónico * </label>
            <input type="text" id="email" name="email" autocomplete="email">
            <span class="error-icon" id="error-email" aria-live="polite"></span>
        </div>
        <div class="form-group-login">
            <label for="password">Contraseña *</label>
            <input type="password" id="password" name="password">
            <span class="error-icon" id="error-password" aria-live="polite"></span>
        </div>
        <p>* Campos obligatorios</p>
        <span id="mensaje-error" class="mensaje-error" aria-live="assertive"></span>
        <button type="submit" class="btn-form-acceso">Acceder</button>
    </form>
</div>