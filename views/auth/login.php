<div class="form-container-login">
    <h2>Iniciar sesión</h2>
    <form id="loginForm" method="post" action="/SuperMarketArthur/login">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group-login">
            <label for="email">Correo electrónico * </label>
            <input type="text" id="email" name="email" autocomplete="email">
            <span class="error-icon" id="error-email" aria-live="polite"></span>

            <label for="password">Contraseña *</label>
            <input type="password" id="password" name="password">
            <span class="error-icon" id="error-password" aria-live="polite"></span>
        </div>
        <p>* Campos obligatorios</p>
        <span id="mensaje-error" class="mensaje-error" aria-live="assertive"><?php echo htmlspecialchars($error_message ?? ''); ?></span>
        <button type="submit" class="btn-form-acceso">Acceder</button>
    </form>
</div>
