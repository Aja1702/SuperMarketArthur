---
description: Cómo agregar nuevas funcionalidades manteniendo la estructura PSR-4
---

# Workflow: Agregar Features en Estructura PSR-4

Usa este workflow cuando necesites implementar nuevas funciones en el proyecto.

## 1. Planificar la Feature

Define:
- **Nombre:** Qué hace (ej: "Sistema de Wishlist")
- **Ruta URL:** Dónde será accesible (ej: `/SuperMarketArthur/wishlist`)
- **Tipo:** ¿Es Shop, Admin, Auth, o General?
- **Modelos necesarios:** ¿Necesitas crear nuevos modelos?

## 2. Crear el Modelo (si es necesario)

Pasos:
```php
// src/Models/Wishlist.php
namespace App\Models;

class Wishlist {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Implementar métodos de negocio
}
```

- Coloca el archivo en `src/Models/MiModelo.php`
- Usa namespace: `namespace App\Models;`

## 3. Crear el Controller

Pasos:
```php
// src/Controllers/Shop/WishlistController.php
namespace App\Controllers\Shop;

require_once __DIR__ . '/../../Models/Wishlist.php';
use Wishlist;

class WishlistController {
    public function index() {
        global $pdo, $tipo_usuario;
        $wishlistModel = new \Wishlist($pdo);
        
        $data = [
            'items' => $wishlistModel->getUserWishlist($_SESSION['id_usuario'] ?? null)
        ];
        
        $this->view('wishlist/lista', $data);
    }
    
    protected function view($view, $data = []) {
        // Mismo patrón que BaseController
        extract($data);
        ob_start();
        require_once __DIR__ . "/../../../views/{$view}.php";
        $content = (string)ob_get_clean();
        require_once __DIR__ . '/../../../views/layout.php';
    }
}
```

Ubicación según tipo:
- **Shop:** `src/Controllers/Shop/NombreController.php`
- **Admin:** `src/Controllers/Admin/AdminNombreController.php`
- **Auth:** `src/Controllers/Auth/NombreController.php`
- **General:** `src/Controllers/NombreController.php`

## 4. Registrar Ruta

En `routes.php`:
```php
// Agregar require_once al inicio
require_once __DIR__ . '/src/Controllers/Shop/WishlistController.php';

// Registrar rutas
$router->add('GET', '/SuperMarketArthur/wishlist', ['App\\Controllers\\Shop\\WishlistController', 'index']);
$router->add('POST', '/SuperMarketArthur/api/wishlist/add', ['App\\Controllers\\Shop\\WishlistController', 'addItem']);
```

## 5. Crear Vista

En `views/wishlist/lista.php`:
```php
<?php
// Esta vista será inyectada en layout.php automáticamente
// $items estará disponible del controller
?>
<div class="wishlist-container">
    <h1>Mi Wishlist</h1>
    <!-- Contenido -->
</div>
```

## 6. Estilos (si aplica)

- Crear `assets/css/wishlist.css`
- Ejecutar `npm run build` para compilar

## 7. Verificar

```bash
curl http://localhost/SuperMarketArthur/wishlist
# Debe retornar HTML sin errores en logs/app.log
```

## Checklist

- [ ] Modelo creado en `src/Models/`
- [ ] Controller creado con namespace correcto
- [ ] `require_once` agregado en `routes.php`
- [ ] Ruta registrada
- [ ] Vista creada en `views/`
- [ ] CSS compilado (si aplica)
- [ ] Testeado sin errores
- [ ] Commit a git con mensaje descriptivo
