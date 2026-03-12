---
description: Cómo crear una nueva página en el proyecto SuperMarketArthur
---

Sigue estos pasos cada vez que necesites crear una nueva vista o página en el proyecto:

1. **Crear el Controlador:**
   - Crea una nueva clase en `app/Controllers/` que herede del namespace adecuado (ej. `[Nombre]Controller.php`).
   - Define el método (acción) que cargará la vista (usando `$this->view('ruta/vista', $data)`).

2. **Registrar la Ruta:**
   - Abre `routes.php` y añade la nueva URL mapeándola al controlador y método creados.

3. **Crear archivo CSS:**
   - Crea un nuevo archivo en `assets/css/` (ej. `[nombre].css`).
   - **IMPORTANTE**: Una vez creado, debes ejecutar `npm run build` para que el estilo se compile en el bundle final.

4. **Crear la Vista:**
   - Crea el archivo PHP en la carpeta `views/`. No hace falta incluir header/footer individualmente si usas el método `view()` del controlador, ya que se inyectará en `layout.php`.

5. **Verificación:**
   - // turbo-all
   - Ejecuta `npm run build` para empaquetar los cambios.
   - Comprueba la nueva URL en el navegador.
