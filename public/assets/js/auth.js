import { provinciasData, codigosPostales } from './provin_cp.js';

// --- VALIDACIÓN FORMULARIO DE LOGIN ---
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (form) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const errorEmail = document.getElementById('error-email');
        const errorPassword = document.getElementById('error-password');
        const mensajeError = document.getElementById('mensaje-error');

        function validarEmail() {
            let valor = email.value.trim();
            let errorMsg = '';
            if (valor.length === 0) {
                errorMsg = "Campo vacío!!";
            } else if (valor.length < 5) {
                errorMsg = "El correo es muy corto.";
            } else if (valor.startsWith("@")) {
                errorMsg = "No puede empezar por @.";
            } else if (valor.indexOf("@") === -1) {
                errorMsg = "Debe contener '@'.";
            } else if (valor.indexOf("@") !== valor.lastIndexOf("@")) {
                errorMsg = "No puede haber más de una '@'.";
            } else {
                let posArroba = valor.indexOf("@");
                let posPunto = valor.lastIndexOf(".");
                if (posPunto === -1 || posPunto < posArroba + 2) {
                    errorMsg = "Debe haber al menos un punto después del '@'.";
                } else if (posPunto + 2 >= valor.length) {
                    errorMsg = "Dominio incompleto.";
                }
            }
            if(errorEmail) errorEmail.textContent = errorMsg;
            return errorMsg === "";
        }

        function validarPassword() {
            let valor = password.value.trim();
            let errorMsg = '';
            if (valor === "") {
                errorMsg = "Campo vacío!!";
            } else if (valor.length < 8) {
                errorMsg = "Debe contener minimo 8 caracteres.";
            } else if (!/[A-Z]/.test(valor)) {
                errorMsg = "Debe contener minimo una letra mayúscula.";
            } else if (!/[a-z]/.test(valor)) {
                errorMsg = "Debe contener minimo una letra minúscula.";
            } else if (!/[0-9]/.test(valor)) {
                errorMsg = "Debe contener minimo un número.";
            } else if (!/[\W_]/.test(valor)) {
                errorMsg = "Debe contener minimo un carácter especial.";
            } else {
                errorMsg = "";
            }
            if(errorPassword) errorPassword.textContent = errorMsg;
            return errorMsg === "";
        }

        if(email) email.addEventListener('input', validarEmail);
        if(password) password.addEventListener('input', validarPassword);

        form.addEventListener('submit', function (e) {
            const validoEmail = validarEmail();
            const validoPass = validarPassword();
            if (!(validoEmail && validoPass)) {
                e.preventDefault();
                if(mensajeError) {
                    mensajeError.textContent = 'NO VALIDO EL INICIO DE SESIÓN';
                    mensajeError.style.display = 'block';
                }
            } else {
                if(mensajeError) {
                    mensajeError.textContent = '';
                    mensajeError.style.display = 'none';
                }
            }
        });
    }
});

// --- VALIDACIÓN FORMULARIO DE REGISTRO ---
document.addEventListener('DOMContentLoaded',
    function () {
        const form = document.getElementById('registroForm');
        if (form) {
            function capitalizarPalabras(texto) {
                return texto
                    .toLowerCase()
                    .split(' ')
                    .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1))
                    .join(' ');
            }
            const campos = ['nombre', 'apellido1', 'apellido2', 'calle'];
            campos.forEach(id => {
                const campo = document.getElementById(id);
                if (campo) {
                    campo.addEventListener('input',
                        function () {
                            this.value = capitalizarPalabras(this.value.trim());
                        }
                    );
                }
            });

            const nombre = document.getElementById('nombre');
            const apellido1 = document.getElementById('apellido1');
            apellido1.disabled = true;
            const apellido2 = document.getElementById('apellido2');
            apellido2.disabled = true;
            const password = document.getElementById('password');
            password.disabled = true;
            const confirm_password = document.getElementById('confirm_password');
            confirm_password.disabled = true;
            const provincia = document.getElementById('provincia');
            provincia.disabled = true;
            const localidad = document.getElementById('localidad');
            localidad.disabled = true;
            const cp = document.getElementById('cp');
            cp.disabled = true;
            const calle = document.getElementById('calle');
            calle.disabled = true;
            const numero = document.getElementById('numero');
            numero.disabled = true;
            const portal_piso = document.getElementById('portal_piso');
            portal_piso.disabled = true;
            const tlfn = document.getElementById('tlfn');
            tlfn.disabled = true;
            const email = document.getElementById('email');
            email.disabled = true;
            const tipo_doc = document.getElementById('tipo_doc');
            tipo_doc.disabled = true;
            const caja_dni_nie = document.getElementById('caja_dni_nie');
            caja_dni_nie.disabled = true;
            const fecha_nacer = document.getElementById('fecha_nacer');
            fecha_nacer.disabled = true;

            function mostrarError(id, mostrar) {
                const img = document.getElementById(id);
                if (img) {
                    img.style.display = mostrar ? 'inline' : 'none';
                }
            }

            function mostrarError_t(id, mensaje) {
                const errorElem = document.getElementById(id);
                if (!errorElem) {
                    console.error(`Elemento con ID ${id} no encontrado.`);
                    return;
                }
                if (mensaje) {
                    errorElem.textContent = mensaje;
                    errorElem.style.display = 'block';
                } else {
                    errorElem.textContent = '';
                    errorElem.style.display = 'none';
                }
            }

            function soloLetras(valor) {
                return /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/.test(valor);
            }

            function soloNumeros(valor) {
                return /^[0-9]+$/.test(valor);
            }

            function soloNumeroDireccion(valor) {
                return /^[0-9]{1,5}(\s[A-Z]*)?$/.test(valor.trim());
            }

            function soloNumeroDireccionPortal(valor) {
                return /^[0-9]{1,5}[ºª]\s[A-Z]*$/.test(valor.trim());
            }

            function tipo_documento(valor) {
                valor = valor.toUpperCase();
                if (/^[0-9]{8}[A-Z]$/.test(valor)) {
                    return "DNI";
                }
                if (/^[XYZ][0-9]{7}[A-Z]$/.test(valor)) {
                    return "NIE";
                }
                return "OTRO";
            }

            nombre.addEventListener('input', function () {
                const nombreVal = this.value.trim();
                if (nombreVal.length === 0 || !soloLetras(nombreVal)) {
                    mostrarError('error_nombre', true);
                    apellido1.disabled = true;
                    apellido2.disabled = true;
                } else {
                    mostrarError('error_nombre', false);
                    apellido1.disabled = false;
                    apellido2.disabled = false;
                }
            });

            apellido1.addEventListener('input', function () {
                const apellido1Val = this.value.trim();
                if (apellido1Val.length === 0 || !soloLetras(apellido1Val)) {
                    mostrarError('error_apellido1', true);
                    password.disabled = true;
                    confirm_password.disabled = true;
                } else {
                    mostrarError('error_apellido1', false);
                    password.disabled = false;
                    confirm_password.disabled = false;
                }
            });

            password.addEventListener('input', function () {
                const valorPassword = this.value.trim();
                if (valorPassword.length < 8) {
                    mostrarError('error_password', true);
                    mostrarError_t('error_password_s', 'La contraseña debe tener al menos 8 caracteres');
                    confirm_password.disabled = true;
                } else if (!/[A-Z]/.test(valorPassword)) {
                    mostrarError('error_password', true);
                    mostrarError_t('error_password_s', 'Debe contener al menos una letra mayúscula');
                    confirm_password.disabled = true;
                } else if (!/[a-z]/.test(valorPassword)) {
                    mostrarError('error_password', true);
                    mostrarError_t('error_password_s', 'Debe contener al menos una letra minúscula');
                    confirm_password.disabled = true;
                } else if (!/[0-9]/.test(valorPassword)) {
                    mostrarError('error_password', true);
                    mostrarError_t('error_password_s', 'Debe contener al menos un número');
                    confirm_password.disabled = true;
                } else if (!/[!@#$%^&*(),._?":{}|<>]/.test(valorPassword)) {
                    mostrarError('error_password', true);
                    mostrarError_t('error_password_s', 'Debe contener al menos un carácter especial');
                    confirm_password.disabled = true;
                } else {
                    mostrarError('error_password', false);
                    mostrarError_t('error_password_s', '');
                    confirm_password.disabled = false;
                }
            });

            confirm_password.addEventListener('input', function () {
                const valorConfirmPassword = this.value.trim();
                const valorPassword = password.value.trim();
                if (valorConfirmPassword !== valorPassword) {
                    mostrarError('error_confirm_password', true);
                    mostrarError_t('error_confirm_password_s', 'Las contraseñas no coinciden');
                    provincia.disabled = true;
                } else {
                    mostrarError('error_confirm_password', false);
                    mostrarError_t('error_confirm_password_s', '');
                    provincia.disabled = false;
                }
            });

            const provinciaSelect = document.getElementById('provincia');
            const localidadSelect = document.getElementById('localidad');
            const cpInput = document.getElementById('cp');

            provinciaSelect.addEventListener('change', () => {
                localidadSelect.innerHTML = '<option value="">Seleccione su localidad</option>';
                cpInput.value = '';
                localidadSelect.disabled = true;
                calle.disabled = true;

                if (provinciaSelect.value) {
                    provinciasData[provinciaSelect.value].forEach(localidad => {
                        const option = document.createElement('option');
                        option.value = localidad;
                        option.textContent = localidad;
                        localidadSelect.appendChild(option);
                    });
                    localidadSelect.disabled = false;
                }
            });

            localidadSelect.addEventListener('change', () => {
                if (provinciaSelect.value && localidadSelect.value) {
                    const index = provinciasData[provinciaSelect.value].indexOf(localidadSelect.value);
                    if (index !== -1) {
                        cpInput.value = codigosPostales[provinciaSelect.value][index];
                        calle.disabled = false;
                    }
                }
            });

            calle.addEventListener('input', function() {
                if (this.value.trim().length > 0) {
                    numero.disabled = false;
                } else {
                    numero.disabled = true;
                }
            });

            numero.addEventListener('input', function() {
                const valorNumero = this.value.trim();
                if (valorNumero.length === 0) {
                    mostrarError('error_numero', true);
                    mostrarError_t('error_numero_s', 'Campo obligatorio.');
                    portal_piso.disabled = true;
                    tlfn.disabled = true;
                } else if (!soloNumeroDireccion(valorNumero)) {
                    mostrarError('error_numero', true);
                    mostrarError_t('error_numero_s', 'Número no válido.');
                    portal_piso.disabled = true;
                    tlfn.disabled = true;
                } else {
                    mostrarError('error_numero', false);
                    mostrarError_t('error_numero_s', '');
                    portal_piso.disabled = false;
                    tlfn.disabled = false;
                }
            });

            tlfn.addEventListener('input', function() {
                const valorTlfn = this.value.trim();
                if (valorTlfn.length === 9 && soloNumeros(valorTlfn) && ['6','7','9'].includes(valorTlfn.charAt(0))) {
                    mostrarError('error_tlfn', false);
                    mostrarError_t('error_tlfn_s', '');
                    email.disabled = false;
                } else {
                    mostrarError('error_tlfn', true);
                    mostrarError_t('error_tlfn_s', 'Teléfono no válido.');
                    email.disabled = true;
                }
            });

            email.addEventListener('input', function() {
                let valor = this.value.trim();
                let errorMsg = '';
                if (valor.length < 5 || valor.indexOf("@") === -1 || valor.indexOf("@") !== valor.lastIndexOf("@")) {
                    errorMsg = "Email no válido.";
                }
                if(errorMsg === '') {
                    mostrarError('error_email', false);
                    mostrarError_t('error_email_s', '');
                    tipo_doc.disabled = false;
                } else {
                    mostrarError('error_email', true);
                    mostrarError_t('error_email_s', errorMsg);
                    tipo_doc.disabled = true;
                }
            });

            tipo_doc.addEventListener('change', function() {
                if (this.value) {
                    caja_dni_nie.disabled = false;
                } else {
                    caja_dni_nie.disabled = true;
                }
            });

            function validarDNI_NIE(valor) {
                valor = valor.toUpperCase().trim();
                if (!/^[0-9]{8}[A-Z]$|^[XYZ][0-9]{7}[A-Z]$/.test(valor)) {
                    return false;
                }
                let numero, letra, letraCalculada;
                if (/^[XYZ]/.test(valor)) {
                    numero = valor.replace('X', '0').replace('Y', '1').replace('Z', '2');
                    numero = numero.substring(0, 8);
                    letra = valor.charAt(8);
                } else {
                    numero = valor.substring(0, 8);
                    letra = valor.charAt(8);
                }
                const letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
                letraCalculada = letras.charAt(parseInt(numero) % 23);
                return letra === letraCalculada;
            }

            caja_dni_nie.addEventListener('input', function() {
                const valorDNI = this.value.trim().toUpperCase();
                if (valorDNI.length === 0) {
                    mostrarError('error_dni_nie', true);
                    mostrarError_t('error_dni_nie_s', 'Campo obligatorio.');
                    fecha_nacer.disabled = true;
                } else if (!validarDNI_NIE(valorDNI)) {
                    mostrarError('error_dni_nie', true);
                    mostrarError_t('error_dni_nie_s', 'DNI/NIE no válido.');
                    fecha_nacer.disabled = true;
                } else {
                    mostrarError('error_dni_nie', false);
                    mostrarError_t('error_dni_nie_s', '');
                    fecha_nacer.disabled = false;
                }
            });

            form.addEventListener('submit', function (e) {
                let isValid = true;
                let errors = [];

                // Validar nombre
                const nombreVal = nombre.value.trim();
                if (nombreVal.length === 0 || !soloLetras(nombreVal)) {
                    errors.push('Nombre inválido.');
                    isValid = false;
                }

                // Validar apellido1
                const apellido1Val = apellido1.value.trim();
                if (apellido1Val.length === 0 || !soloLetras(apellido1Val)) {
                    errors.push('Primer apellido inválido.');
                    isValid = false;
                }

                // Validar apellido2 (opcional, pero si se llena, validar)
                const apellido2Val = apellido2.value.trim();
                if (apellido2Val.length > 0 && !soloLetras(apellido2Val)) {
                    errors.push('Segundo apellido inválido.');
                    isValid = false;
                }

                // Validar password
                const passwordVal = password.value.trim();
                if (passwordVal.length < 8 || !/[A-Z]/.test(passwordVal) || !/[a-z]/.test(passwordVal) || !/[0-9]/.test(passwordVal) || !/[!@#$%^&*(),._?":{}|<>]/.test(passwordVal)) {
                    errors.push('Contraseña inválida.');
                    isValid = false;
                }

                // Validar confirm_password
                const confirmVal = confirm_password.value.trim();
                if (confirmVal !== passwordVal) {
                    errors.push('Las contraseñas no coinciden.');
                    isValid = false;
                }

                // Validar provincia
                if (!provinciaSelect.value) {
                    errors.push('Provincia obligatoria.');
                    isValid = false;
                }

                // Validar localidad
                if (!localidadSelect.value) {
                    errors.push('Localidad obligatoria.');
                    isValid = false;
                }

                // Validar calle
                const calleVal = calle.value.trim();
                if (calleVal.length === 0) {
                    errors.push('Calle obligatoria.');
                    isValid = false;
                }

                // Validar numero
                const numeroVal = numero.value.trim();
                if (numeroVal.length === 0 || !soloNumeroDireccion(numeroVal)) {
                    errors.push('Número inválido.');
                    isValid = false;
                }

                // Validar portal_piso (opcional, pero si se llena, validar)
                const portalPisoVal = portal_piso.value.trim();
                if (portalPisoVal.length > 0 && !soloNumeroDireccionPortal(portalPisoVal)) {
                    errors.push('Portal/Piso inválido.');
                    isValid = false;
                }

                // Validar teléfono
                const tlfnVal = tlfn.value.trim();
                if (tlfnVal.length !== 9 || !soloNumeros(tlfnVal) || !['6','7','9'].includes(tlfnVal.charAt(0))) {
                    errors.push('Teléfono inválido.');
                    isValid = false;
                }

                // Validar email
                const emailVal = email.value.trim();
                if (emailVal.length < 5 || emailVal.indexOf("@") === -1 || emailVal.indexOf("@") !== emailVal.lastIndexOf("@")) {
                    errors.push('Email inválido.');
                    isValid = false;
                }

                // Validar tipo_doc
                if (!tipo_doc.value) {
                    errors.push('Tipo de documento obligatorio.');
                    isValid = false;
                }

                // Validar DNI/NIE
                const dniVal = caja_dni_nie.value.trim().toUpperCase();
                if (dniVal.length === 0 || !validarDNI_NIE(dniVal)) {
                    errors.push('DNI/NIE inválido.');
                    isValid = false;
                }

                // Validar fecha_nacer
                const fechaVal = fecha_nacer.value.trim();
                if (fechaVal.length === 0) {
                    errors.push('Fecha de nacimiento obligatoria.');
                    isValid = false;
                } else {
                    const fecha = new Date(fechaVal);
                    const hoy = new Date();
                    const edad = hoy.getFullYear() - fecha.getFullYear();
                    if (edad < 18 || edad > 120) {
                        errors.push('Fecha de nacimiento inválida (debe ser mayor de 18 años).');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Errores en el formulario:\n' + errors.join('\n'));
                }
            });
        }
    });
