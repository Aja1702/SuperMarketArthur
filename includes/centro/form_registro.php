<div class="form-container-registro">
    <h2>Registro de usuario</h2>
    <form id="registroForm" method="post" action="./centro/procesar_registro.php">
        <!-- Grupo de campos para nombre y apellidos -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" title="Solo letras y espacios">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_nombre">
            </div>

            <div class="input-group-registro">
                <label for="apellido1">Primer apellido</label>
                <input type="text" id="apellido1" name="apellido1" title="Solo letras y espacios">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_apellido1">
            </div>

            <div class="input-group-registro">
                <label for="apellido2">Segundo apellido</label>
                <input type="text" id="apellido2" name="apellido2" title="Solo letras y espacios">
            </div>
        </div>
        <!-- Grupo para contraseña -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_password">
                <span id="error_password_s" style="color: red; display: none;"></span>
            </div>
            <div class="input-group-registro">
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_confirm_password">
                <span id="error_confirm_password_s" style="color: red; display: none;"></span>
            </div>
        </div>

        <!-- Grupo para dirección -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="provincia">Provincia</label>
                <select id="provincia" name="provincia" required>
                    <option value="">Seleccione su provincia</option>
                </select>
            </div>

            <div class="input-group-registro">
                <label for="localidad">Localidad</label>
                <select id="localidad" name="localidad" required>
                    <option value="">Seleccione su localidad</option>
                </select>
            </div>

            <div class="input-group-registro">
                <label for="cp">Código Postal</label>
                <input type="text" id="cp" name="cp" readonly required>
            </div>
        </div>

        <!-- Grupo para dirección física -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="calle">Calle</label>
                <input type="text" id="calle" name="calle">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_calle">
            </div>
            <div class="input-group-registro">
                <label for="numero">Número (letra)</label>
                <input type="text" id="numero" name="numero">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_numero">
            </div>
            <div class="input-group-registro">
                <label for="portal_piso">Portal</label>
                <input type="text" id="portal_piso" name="portal_piso">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_portal_piso">
            </div>
        </div>

        <!-- Grupo para contacto -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="tlfn">Teléfono</label>
                <input type="text" id="tlfn" name="tlfn">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_tlfn">
                <span id="error_tlfn_s" style="color:red; display: none;"></span>
            </div>

            <div class="input-group-registro">
                <label for="email">Correo electrónico</label>
                <input type="text" id="email" name="email" autocomplete="email">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_email">
                <span id="error_email_s" style="color: red; display: none;"></span>
            </div>
        </div>

        <!-- Grupo para documento de identidad -->
        <div class="form-group-registro">
            <div class="radio-group-registro">
                <label for="tipo_doc">Tipo de documento</label>
                <select id="tipo_doc" name="tipo_doc">
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="DNI">DNI</option>
                    <option value="NIE">NIE</option>
                </select>
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_tipo_doc">
            </div>
            <div class="input-group-registro">
                <label for="caja_dni_nie">Documento</label>
                <input type="text" id="caja_dni_nie" name="caja_dni_nie">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_caja_dni_nie">
                <span id="error_dni_nie_s" style="color: red; display: none;"></span>
            </div>
        </div>

        <!-- Fecha de nacimiento -->
        <div class="form-group-registro">
            <div class="input-group-registro">
                <label for="fecha_nacer">Fecha de nacimiento</label>
                <input type="date" id="fecha_nacer" name="fecha_nacer">
                <img class="img-error-form" src="./assets/img/errores/obligatorio.png" alt="Obligatorio" id="error_fecha_nacer">
                <span id="error_fecha_nacer_s" style="color: red; display: none;"></span>
            </div>
        </div>
        <span id="error-registro-user" class="error-"></span>
        <button type="submit" class="btn-form-registro">Registrarse</button>
    </form>
</div>