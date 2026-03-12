---
name: Analizador del Proyecto
description: Instrucciones sobre cómo analizar el estado general del código y la estructura post-refactor PSR-4.
---

# Instrucciones para el Agente

Cuando el usuario pida "revisar el proyecto", "analizar el código" o "estado del proyecto", debes seguir estos pasos:

## 1. Revisar estructura PSR-4:

Verifica que existan las carpetas principales post-refactor:

- ✅ `src/Controllers/` - Controllers por dominio (Shop/, Auth/, Admin/)
- ✅ `src/Models/` - Entidades de datos
- ✅ `src/Core/` - Router.php y framework core
- ✅ `src/Services/` - Lógica de negocio
- ✅ `src/Middleware/` - Filtros HTTP
- ✅ `src/Exceptions/` - Excepciones personalizadas
- ✅ `src/Utilities/` - Funciones helper
- ✅ `views/` - Plantillas PHP
- ✅ `assets/` - CSS, JS, imágenes
- ❌ Verificar que NO existan carpetas antiguas: `app/`, `models/`, `core/`

## 2. Auditoría de Logs:

```bash
Get-Content logs/app.log -Tail 20  # Ver últimos 20 errores
```

- Busca `ERROR`, `WARNING`, `Exception`
- Reporta cualquier problema de namespacing o rutas

## 3. Verificar Rutas (routes.php):

- Confirma que todos los controllers usen `require_once` al inicio
- Verifica que los namespaces sean correctos: `App\Controllers\Shop\ProductoController`
- Valida que las rutas apunten a `['App\\Controllers\\...', 'metodo']`

## 4. Testar Endpoints principales:

```bash
curl http://localhost/SuperMarketArthur/                    # Inicio
curl http://localhost/SuperMarketArthur/productos          # Catálogo
curl http://localhost/SuperMarketArthur/login              # Autenticación
```

## 5. Reporte formato Markdown:

- Estructura clara con secciones
- Estado de cada componente (✅ OK / ⚠️ Warning / ❌ Error)
- Recomendaciones cuando sea necesario

## Notas:

- Prioriza la estructura PSR-4 y namespacing correcto
- Valida que no haya rutas obsoletas en la estructura antigua
- Recomienda actualizar documentación si es necesario
