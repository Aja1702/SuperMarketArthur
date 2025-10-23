<div class="form-container-login">
    <h2>Iniciar sesión</h2>
    <form id="loginForm" method="post" action="./centro/procesar_login.php">
        <div class="form-group-login">
            <label for="email">Correo electrónico *
                <br>
                <input type="text" id="email" name="email" autocomplete="email">
                <span class="error-icon" id="error-email" style="color:red;" aria-live="polite"></span>
                <br>
            </label>
        </div>
        <div class="form-group-login">
            <label for="password">Contraseña *
                <br>
                <input type="password" id="password" name="password">
                <br>
                <span class="error-icon" id="error-password" style="color:red;" aria-live="polite"></span>
            </label>
        </div>
        <span id="mensaje-error" style="color:red;"></span>
        <p>* Indica campo obligatorio</p>
        <button type="submit" class="btn-form-acceso">Acceder</button>
    </form>
    <div id="mensaje-error" class="mensaje-error" aria-live="polite"></div>
</div>