import { provinciasData, codigosPostales } from './provin_cp.js';

//contenido form_login.php
document.addEventListener('DOMContentLoaded',
    function () {
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
                errorEmail.textContent = errorMsg;
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
                errorPassword.textContent = errorMsg;
                return errorMsg === "";
            }




            // Validar campo individual al cambiar
            email.addEventListener('input',
                function () {
                    validarEmail();
                }
            );
            password.addEventListener('input',
                function () {
                    validarPassword();
                }
            );

            // Validar formulario antes de submit
            form.addEventListener('submit',
                function (e) {
                    const validoEmail = validarEmail();
                    const validoPass = validarPassword();
                    if (!(validoEmail && validoPass)) {
                        e.preventDefault();
                        // Para mostrar el error:
                        mensajeError.textContent = 'NO VALIDO EL INICIO DE SESIÓN';
                        mensajeError.style.display = 'block';
                        // El formulario no se envía correctamente
                    } else {
                        mensajeError.textContent = '';
                        mensajeError.style.display = 'none';
                        // El formulario se envía correctamente
                    }
                }
            );
        }
    }
);

/**
 * --- LÓGICA DEL CARRITO LATERAL (SIDE CART) ---
 */
document.addEventListener('DOMContentLoaded', function () {
    const cartOverlay = document.getElementById('cartOverlay');
    const cartPanel = document.getElementById('cartPanel');
    const openCartBtn = document.getElementById('openCart');
    const closeCartBtn = document.getElementById('closeCart');
    const cartContent = document.getElementById('cartContent');
    const cartTotalAmount = document.getElementById('cartTotalAmount');

    // Función Abrir Carrito
    function openCart() {
        if (!cartOverlay || !cartPanel) return;
        cartOverlay.style.display = 'block';
        setTimeout(() => cartPanel.classList.add('active'), 10);
        updateCartUI(); // Carga los datos actuales
    }

    // Función Cerrar Carrito
    function closeCart() {
        if (!cartPanel || !cartOverlay) return;
        cartPanel.classList.remove('active');
        setTimeout(() => cartOverlay.style.display = 'none', 400);
    }

    if (openCartBtn) openCartBtn.addEventListener('click', (e) => { e.preventDefault(); openCart(); });
    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // Función para actualizar la UI del carrito (AJAX)
    async function updateCartUI() {
        if (!cartContent) return;
        try {
            const response = await fetch('./controllers/carrito_ajax.php?action=get_items');
            const data = await response.json();

            if (data.success) {
                renderCartItems(data.items);
                cartTotalAmount.textContent = data.total_formatted;
            }
        } catch (error) {
            console.error('Error cargando el carrito:', error);
        }
    }

    function renderCartItems(items) {
        if (!items || items.length === 0) {
            cartContent.innerHTML = `
                <div class="empty-cart-message" style="text-align: center; padding: 2rem; opacity: 0.6;">
                    <p>Tu carrito está vacío</p>
                </div>
            `;
            return;
        }

        let html = '';
        items.forEach(item => {
            html += `
                <div class="cart-item" data-id="${item.id_producto}">
                    <img src="${item.url_imagen || './assets/img/productos/default.jpg'}" alt="${item.nombre_producto}">
                    <div class="item-info">
                        <h4>${item.nombre_producto}</h4>
                        <p class="item-price">${item.precio_formatted}</p>
                        <div class="item-controls">
                            <button class="btn-qty decrease" data-id="${item.id_producto}">-</button>
                            <span class="qty">${item.cantidad}</span>
                            <button class="btn-qty increase" data-id="${item.id_producto}">+</button>
                        </div>
                    </div>
                </div>
            `;
        });
        cartContent.innerHTML = html;

        // Re-asignar eventos a los nuevos botones de cantidad
        document.querySelectorAll('.btn-qty.increase').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, 1);
        });
        document.querySelectorAll('.btn-qty.decrease').forEach(btn => {
            btn.onclick = () => updateQuantity(btn.dataset.id, -1);
        });
    }

    // Función para añadir al carrito (se llamará desde los botones de la tienda)
    window.addToCart = async function (id_producto) {
        const formData = new FormData();
        formData.append('id_producto', id_producto);
        formData.append('action', 'add');

        try {
            const response = await fetch('./controllers/carrito_ajax.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                openCart(); // Mostramos el carrito al añadir
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error al añadir:', error);
        }
    }

    async function updateQuantity(id_producto, delta) {
        const formData = new FormData();
        formData.append('id_producto', id_producto);
        formData.append('delta', delta);
        formData.append('action', 'update_qty');

        try {
            const response = await fetch('./controllers/carrito_ajax.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) updateCartUI();
        } catch (error) {
            console.error('Error updating qty:', error);
        }
    }
});





//contenido form_registro.php

// Datos de provincias y localidades (totales)
document.addEventListener('DOMContentLoaded',
    function () {
        const form = document.getElementById('registroForm');
        if (form) {

            // Función que pone en mayúscula la primera letra de cada palabra de la caja seleccionada
            function capitalizarPalabras(texto) {
                return texto
                    .toLowerCase()
                    .split(' ')
                    .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1))
                    .join(' ');
            }
            // Lista de IDs de los campos a capitalizar
            const campos = ['nombre', 'apellido1', 'apellido2', 'calle'];
            campos.forEach(id => {
                const campo = document.getElementById(id);
                if (campo) {
                    campo.addEventListener('blur',
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

            //todos los errores de validadcion

            //funcion mostrar/ocultar imagen error

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

            //solo vale en la caja "letras"
            function soloLetras(valor) {
                // Permite letras, tildes, ñ y espacios
                return /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/.test(valor);
            }

            // Solo vale en la caja "numeros"
            function soloNumeros(valor) {
                // Permite solo numeros 
                return /^[0-9]+$/.test(valor);
            }

            function soloNumeroDireccion(valor) {
                // Permite números, y opcionalmente letras y guión después del número
                return /^[0-9]{1,5}(\s[A-Z]*)?$/.test(valor.trim());
            }

            function soloNumeroDireccionPortal(valor) {
                // Permite números, letras, guiones (habituales en direcciones españolas) "3º B"
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


            // para el nombre, debloquea apellido1 si es valido el nombre
            nombre.addEventListener('input',
                function () {
                    const nombre = this.value.trim();
                    if (nombre.length === 0 || !soloLetras(nombre)) {
                        mostrarError('error_nombre', true);
                        apellido1.disabled = true;
                        apellido2.disabled = true;
                    } else {
                        mostrarError('error_nombre', false);
                        apellido1.disabled = false;
                        apellido2.disabled = false;
                    }
                }
            );

            // para el primer apellido, 
            // desbloquea el apellido2,·,·,· si es valido el primer apellido
            apellido1.addEventListener('input',
                function () {
                    const apellido1 = this.value.trim();
                    if (apellido1.length === 0 || !soloLetras(apellido1)) {
                        mostrarError('error_apellido1', true);
                        apellido2.disabled = true;
                        password.disabled = true;
                        confirm_password.disabled = true;
                    } else {
                        mostrarError('error_apellido1', false);
                        apellido2.disabled = false;
                        password.disabled = false;
                        confirm_password.disabled = false;
                    }
                }
            );



            password.addEventListener('input',
                function () {
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
                }
            );

            confirm_password.addEventListener('input',
                function () {
                    const valorConfirmPassword = this.value.trim();
                    const valorPassword = password.value.trim();
                    if (valorConfirmPassword !== valorPassword) {
                        mostrarError('error_confirm_password', true);
                        mostrarError_t('error_confirm_password_s', 'Las contraseñas no coinciden');
                        provincia.disabled = true;
                        localidad.disabled = true;
                        cp.disabled = true;
                    } else {
                        mostrarError('error_confirm_password', false);
                        mostrarError_t('error_confirm_password_s', '');
                        provincia.disabled = false;
                        localidad.disabled = false;
                        cp.disabled = false;
                    }
                }
            );

            // Para las provincias, localidades y cp, desbloquea la calle
            // Ejecutar cada vez que cambia el campo código postal
            function actualizarEstadoCalleNumero() {
                if (cp.value.trim() !== '') {
                    calle.disabled = false;
                } else {
                    calle.disabled = true;
                }
            }
            // Evento sobre el cp (código postal)
            cp.addEventListener('input', actualizarEstadoCalleNumero);

            //para comprobar que se pone coorectamente la localidad y las provincias con su cp
            const provinciaSelect = document.getElementById('provincia');
            const localidadSelect = document.getElementById('localidad');
            const cpInput = document.getElementById('cp');
            // Cargar provincias
            Object.keys(provinciasData).forEach(prov => {
                const option = document.createElement('option');
                option.value = prov;
                option.textContent = prov;
                provinciaSelect.appendChild(option);
            });
            // Cambio de provincia
            provinciaSelect.addEventListener('change',
                () => {
                    // Limpia todas las opciones excepto la primera
                    localidadSelect.innerHTML = '<option value="">Seleccione su localidad</option>';
                    cpInput.value = '';

                    if (provinciaSelect.value) {
                        provinciasData[provinciaSelect.value].forEach(
                            localidad => {
                                const option = document.createElement('option');
                                option.value = localidad;
                                option.textContent = localidad;
                                localidadSelect.appendChild(option);
                            }
                        );
                    }
                }
            );

            // Cambio de localidad
            localidadSelect.addEventListener('change',
                () => {
                    if (provinciaSelect.value && localidadSelect.value) {
                        const index = provinciasData[provinciaSelect.value].indexOf(localidadSelect.value);
                        if (index !== -1) {
                            cpInput.value = codigosPostales[provinciaSelect.value][index];
                            actualizarEstadoCalleNumero();
                        }
                    }
                }
            );

            //para la calle, desbloquear el numero de la calle
            calle.addEventListener('input',
                function () {
                    const calleValue = this.value.trim();
                    if (calleValue.length === 0 || !soloLetras(calleValue)) {
                        mostrarError('error_calle', true);
                        numero.disabled = true;
                    } else {
                        mostrarError('error_calle', false);
                        numero.disabled = false;
                    }
                }
            );

            //para el numero de la calle, desbloquear el portal_piso 
            numero.addEventListener('input',
                function () {
                    const numeroValue = this.value;
                    if (numeroValue.length === 0 || !soloNumeroDireccion(numeroValue)) {
                        mostrarError('error_numero', true);
                        portal_piso.disabled = true;
                    } else {
                        mostrarError('error_numero', false);
                        portal_piso.disabled = false;
                        mostrarError('error_portal_piso', false);
                        tlfn.disabled = false;
                    }
                }
            );

            //para el portal_piso, desbloquear telefono
            portal_piso.addEventListener('input',
                function () {
                    const valorPortalPiso = this.value;
                    if (valorPortalPiso === '' || soloNumeroDireccionPortal(valorPortalPiso)) {
                        mostrarError('error_portal_piso', false);
                        tlfn.disabled = false;
                    } else {
                        mostrarError('error_portal_piso', true);
                        tlfn.disabled = true;
                    }
                }
            );

            //para el telefono, desbloquea la zona de el correo electronico
            tlfn.addEventListener('input',
                function () {
                    const valorTlfn = this.value.trim();
                    if (valorTlfn.length !== 9 || !soloNumeros(valorTlfn)) {
                        mostrarError('error_tlfn', true);
                        mostrarError_t('error_tlfn_s', 'Debe contener 9 dígitos numéricos');
                        email.disabled = true;
                    } else if (!['6', '7', '9'].includes(valorTlfn.charAt(0))) {
                        mostrarError('error_tlfn', true);
                        mostrarError_t('error_tlfn_s', 'El teléfono debe comenzar por 6, 7 o 9');
                        email.disabled = true;
                    } else {
                        mostrarError('error_tlfn', false);
                        mostrarError_t('error_tlfn_s', '');
                        email.disabled = false;
                    }
                }
            );

            email.addEventListener('input',
                function () {
                    const valorEmail = this.value.trim();
                    if (valorEmail === '') {
                        mostrarError('error_email', true);
                        mostrarError_t('error_email_s', 'Campo vacío!!');
                        tipo_doc.disabled = true;
                    } else if (valorEmail.length < 5) {
                        mostrarError('error_email', true);
                        mostrarError_t('error_email_s', 'El correo es muy corto.');
                        tipo_doc.disabled = true;
                    } else if (valorEmail.startsWith("@")) {
                        mostrarError('error_email', true);
                        mostrarError_t('error_email_s', 'No puede empezar por @.');
                        tipo_doc.disabled = true;
                    } else if (valorEmail.indexOf("@") === -1) {
                        mostrarError('error_email', true);
                        mostrarError_t('error_email_s', 'Debe contener @.');
                        tipo_doc.disabled = true;
                    } else if (valorEmail.indexOf("@") !== valorEmail.lastIndexOf("@")) {
                        mostrarError('error_email', true);
                        mostrarError_t('error_email_s', 'No puede haber dos @.');
                        tipo_doc.disabled = true;
                    } else {
                        let posArroba = valorEmail.indexOf("@");
                        let posPunto = valorEmail.lastIndexOf(".");
                        if (posPunto === -1 || posPunto < posArroba + 2) {
                            mostrarError('error_email', true);
                            mostrarError_t('error_email_s', 'Debe haber al menos un punto después del *@**. .');
                            tipo_doc.disabled = true;
                        } else if (posPunto + 1 >= valorEmail.length) {
                            mostrarError('error_email', true);
                            mostrarError_t('error_email_s', 'Dominio incompleto.');
                            tipo_doc.disabled = true;
                        } else {
                            mostrarError('error_email', false);
                            mostrarError_t('error_email_s', '');
                            tipo_doc.disabled = false;
                        }
                    }
                }
            );

            tipo_doc.addEventListener('change',
                function () {
                    const valorTipo_Doc = this.value.trim();

                    if (valorTipo_Doc !== 'DNI' && valorTipo_Doc !== 'NIE') {
                        mostrarError('error_tipo_doc', true);
                        caja_dni_nie.disabled = true;

                    } else {
                        mostrarError('error_tipo_doc', false);
                        caja_dni_nie.disabled = false;
                        if (valorTipo_Doc === 'DNI') {
                            caja_dni_nie.placeholder = 'Introduce tu DNI (ej: 12345678Z)';
                        } else if (valorTipo_Doc === 'NIE') {
                            caja_dni_nie.placeholder = 'Introduce tu NIE (ej: X1234567T)';
                        }
                    }
                }
            );

            caja_dni_nie.addEventListener('input',
                function () {
                    const valorTipo_Doc = tipo_doc.value.trim();
                    if (valorTipo_Doc === 'DNI') {
                        const letras = "TRWAGMYFPDXBNJZSQVHLCKET";
                        const valorDNI = this.value.trim();
                        const longitudDNI = valorDNI.length;

                        if (longitudDNI === 0) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Campo DNI vacío!! (ej: 12345678Z)');
                            fecha_nacer.disabled = true;

                        } else if (longitudDNI > 0 && longitudDNI < 9) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Necesitas 9 caracteres en el DNI!! (ej: 12345678Z)');
                            fecha_nacer.disabled = true;

                        } else if (longitudDNI > 9) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Mas de 9 caracteres en el DNI!! (ej: 12345678Z)');
                            fecha_nacer.disabled = true;

                        } else if (longitudDNI === 9) {
                            const numeroDNI = valorDNI.substring(0, 8);
                            const letraDNI = valorDNI.substring(8, 9).toUpperCase();
                            const numeroEnteroDNI = parseInt(numeroDNI, 10);

                            if (isNaN(numeroEnteroDNI)) {
                                mostrarError('error_caja_dni_nie', true);
                                mostrarError_t('error_dni_nie_s', 'Los 8 primeros caracteres deben ser números (ej: 12345678Z)');
                                fecha_nacer.disabled = true;

                            } else {
                                const enteroDNI = numeroEnteroDNI % 23;
                                const letra_dni = letras[enteroDNI];

                                if (letraDNI === letra_dni) {
                                    mostrarError('error_caja_dni_nie', false);
                                    mostrarError_t('error_dni_nie_s', '');
                                    fecha_nacer.disabled = false;

                                } else {
                                    mostrarError('error_caja_dni_nie', true);
                                    mostrarError_t('error_dni_nie_s', 'Letra final incorrecta del DNI (ej: 12345678Z)');
                                    fecha_nacer.disabled = true;
                                }
                            }
                        }
                    }
                }
            )

            caja_dni_nie.addEventListener('input',
                function () {
                    const valorTipo_Doc = tipo_doc.value.trim();
                    if (valorTipo_Doc === 'NIE') {
                        const letras = "TRWAGMYFPDXBNJZSQVHLCKET";
                        const valorNIE = this.value.trim();
                        const longitudNIE = valorNIE.length;

                        if (longitudNIE === 0) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Campo NIE vacío!! (ej: X1234567T)');
                            fecha_nacer.disabled = true;

                        } else if (longitudNIE > 0 && longitudNIE < 9) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Necesitas 9 caracteres en el NIE!! (ej: X1234567T)');
                            fecha_nacer.disabled = true;

                        } else if (longitudNIE > 9) {
                            mostrarError('error_caja_dni_nie', true);
                            mostrarError_t('error_dni_nie_s', 'Más de 9 caracteres en el NIE!! (ej: X1234567T)');
                            fecha_nacer.disabled = true;

                        } else if (longitudNIE === 9) {
                            const letraInicial = valorNIE.substring(0, 1).toUpperCase();
                            const numerosNIE = valorNIE.substring(1, 8);
                            const letraFinal = valorNIE.substring(8, 9).toUpperCase();

                            let digitosNIE = '';
                            if (letraInicial === 'X') {
                                digitosNIE = '0' + numerosNIE;
                            } else if (letraInicial === 'Y') {
                                digitosNIE = '1' + numerosNIE;
                            } else if (letraInicial === 'Z') {
                                digitosNIE = '2' + numerosNIE;
                            } else {
                                mostrarError('error_caja_dni_nie', true);
                                mostrarError_t('error_dni_nie_s', 'El NIE debe comenzar por X, Y o Z. (ej: X1234567T)');
                                fecha_nacer.disabled = true;
                            }

                            // Verifica que los 7 caracteres intermedios sean numéricos
                            if (!/^[0-9]{7}$/.test(numerosNIE)) {
                                mostrarError('error_caja_dni_nie', true);
                                mostrarError_t('error_dni_nie_s', 'Los caracteres 1-7 deben ser números del NIE. (ej: X1234567T)');
                                fecha_nacer.disabled = true;
                            } else {
                                const numeroNIE = parseInt(digitosNIE, 10);
                                const letraCalculada = letras[numeroNIE % 23];

                                if (letraFinal === letraCalculada) {
                                    mostrarError('error_caja_dni_nie', false);
                                    mostrarError_t('error_dni_nie_s', '');
                                    fecha_nacer.disabled = false;
                                } else {
                                    mostrarError('error_caja_dni_nie', true);
                                    mostrarError_t('error_dni_nie_s', 'Letra final incorrecta del NIE (ej: X1234567T)');
                                    fecha_nacer.disabled = true;
                                }
                            }
                        }
                    }
                }
            );


            fecha_nacer.addEventListener('input', function () {
                const valueFechaNacer = this.value.trim();

                if (valueFechaNacer === '') {
                    // Campo vacío, mostrar error
                    mostrarError('error_fecha_nacer', true);
                    mostrarError_t('error_fecha_nacer_s', 'Campo vacío!!');
                } else {
                    const hoy = new Date();
                    const fechaInput = new Date(valueFechaNacer);

                    // Comprobar que fecha no sea futura
                    if (fechaInput > hoy) {
                        mostrarError('error_fecha_nacer', true);
                        mostrarError_t('error_fecha_nacer_s', 'La fecha no puede ser futura');
                        return;
                    }

                    // Calcular edad
                    let edad = hoy.getFullYear() - fechaInput.getFullYear();
                    const mes = hoy.getMonth() - fechaInput.getMonth();
                    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaInput.getDate())) {
                        edad--;
                    }

                    // Opcional: mostrar mensaje si la edad no cumple algún criterio, por ejemplo, mínimo 18 años
                    if (edad < 18) {
                        mostrarError('error_fecha_nacer', true);
                        mostrarError_t('error_fecha_nacer_s', 'Debes ser mayor de 18 años');
                    } else {
                        // Todo correcto, limpiar errores
                        mostrarError('error_fecha_nacer', false);
                        mostrarError_t('error_fecha_nacer_s', '');
                    }
                }
            });

            // Validar formulario antes de submit
            form.addEventListener('submit',
                function (e) {
                    e.preventDefault(); // Evitar el envío del formulario

                    // Validar campos obligatorios
                    const camposObligatorios = [
                        { id: 'nombre', errorIconId: 'error_nombre', errorTextId: 'error_nombre_s', errorMsg: 'Campo vacío!!' },
                        { id: 'apellido1', errorIconId: 'error_apellido1', errorTextId: 'error_apellido1_s', errorMsg: 'Campo vacío!!' },
                        { id: 'password', errorIconId: 'error_password', errorTextId: 'error_password_s', errorMsg: 'Campo vacío!!' },
                        { id: 'confirm_password', errorIconId: 'error_confirm_password', errorTextId: 'error_confirm_password_s', errorMsg: 'Campo vacío!!' },
                        { id: 'provincia', errorIconId: 'error_provincia', errorTextId: 'error_provincia_s', errorMsg: 'Campo vacío!!' },
                        { id: 'localidad', errorIconId: 'error_localidad', errorTextId: 'error_localidad_s', errorMsg: 'Campo vacío!!' },
                        { id: 'cp', errorIconId: 'error_cp', errorTextId: 'error_cp_s', errorMsg: 'Campo vacío!!' },
                        { id: 'calle', errorIconId: 'error_calle', errorTextId: 'error_calle_s', errorMsg: 'Campo vacío!!' },
                        { id: 'numero', errorIconId: 'error_numero', errorTextId: 'error_numero_s', errorMsg: 'Campo vacío!!' },
                        { id: 'tlfn', errorIconId: 'error_tlfn', errorTextId: 'error_tlfn_s', errorMsg: 'Campo vacío!!' },
                        { id: 'email', errorIconId: 'error_email', errorTextId: 'error_email_s', errorMsg: 'Campo vacío!!' },
                        { id: 'tipo_doc', errorIconId: 'error_tipo_doc', errorTextId: 'error_tipo_doc_s', errorMsg: 'Campo vacío!!' },
                        { id: 'caja_dni_nie', errorIconId: 'error_caja_dni_nie', errorTextId: 'error_caja_dni_nie_s', errorMsg: 'Campo vacío!!' },
                        { id: 'fecha_nacer', errorIconId: 'error_fecha_nacer', errorTextId: 'error_fecha_nacer_s', errorMsg: 'Campo vacío!!' }
                    ];


                    let hayErrores = false;

                    camposObligatorios.forEach(campo => {
                        const elem = document.getElementById(campo.id);
                        const valor = elem ? elem.value.trim() : '';

                        if (valor === '') {
                            mostrarError(campo.errorIconId, true);
                            mostrarError_t(campo.errorTextId, campo.errorMsg);
                            hayErrores = true;
                        } else {
                            mostrarError(campo.errorIconId, false);
                            mostrarError_t(campo.errorTextId, '');
                        }
                    });

                    if (!hayErrores) {
                        form.submit(); // Enviar el formulario si no hay errores
                    }
                }
            );
        }
    }
);