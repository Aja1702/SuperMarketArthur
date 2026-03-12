---
description: Cómo crear una nueva página en el proyecto SuperMarketArthur (Post-refactor PSR-4)
---

Sigue estos pasos cada vez que necesites crear una nueva vista o página en el proyecto:

1. **Crear el Controlador:**
   - Crea una nueva clase en `src/Controllers/` (o subcarpeta temática como `Shop/`, `Admin/`, `Auth/`).
   - Usa el namespace correcto: `namespace App\Controllers;` o `namespace App\Controllers\Shop;` según la carpeta.
   - Define el método (acción) que cargará la vista: `$this->view('ruta/vista', $data)`.

2. **Registrar la Ruta:**
   - Abre `routes.php` y:
     a) Añade `require_once __DIR__ . '/src/Controllers/[CarpetaOpcional]/[NombreController].php';` al principio.
     b) Añade la nueva URL mapeándola: `$router->add('GET', '/SuperMarketArthur/mi-url', ['App\\Controllers\\MiController', 'nombreMetodo']);`

3. **Crear archivo CSS (opcional):**
   - Crea un nuevo archivo en `assets/css/` (ej. `mi-pagina.css`).
   - **IMPORTANTE**: Ejecuta `npm run build` para compilar en el bundle final.

4. **Crear la Vista:**
   - Crea el archivo PHP en `views/` (ej. `views/mi-pagina.php`).
   - No incluyas header/footer individuales; se inyectarán automáticamente via `layout.php`.
   - Usa variables globales disponibles: `$nombre_sitio`, `$tipo_usuario`, `$simbolo_moneda`, etc.

5. **Verificación:**
   - Ejecuta `npm run build` si agregaste CSS.
   - Comprueba la nueva URL en el navegador: `http://localhost/SuperMarketArthur/mi-url`
   - Revisa `logs/app.log` si hay errores.
