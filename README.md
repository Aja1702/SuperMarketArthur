# SuperMarketArthur - Sistema de E-commerce Profesional

Un sistema completo de supermercado online desarrollado en PHP con MySQL, diseñado con estándares profesionales para ofrecer una experiencia de compra excepcional.

## ✅ Estado del Proyecto

Proyecto en desarrollo activo. Estado actual de las características principales:

- **[x]** Funcionalidades E-commerce (Catálogo, Carrito, Usuarios, Pedidos, Favoritos)
- **[x]** Panel de Administración Avanzado con Dashboard
- **[x]** Panel de Configuración Global (sin tocar el código)
- **[x]** Sistema de Valoraciones y Reseñas de productos
- **[x]** Diseño Moderno y Responsive con paleta de colores profesional
- **[x]** Seguridad (Hashing, Prepared Statements, CSRF, Modo Mantenimiento con IP)
- **[x]** Optimización y Rendimiento (Caching, Paginación, Lazy Loading)
- **[x]** Arquitectura MVC limpia con Router central y assets compilados

## 🚀 Características Principales

### 🛒 E-commerce
- **Catálogo de Productos**: Navegación por categorías con paginación configurable
- **Carrito de Compras**: Persistencia de sesión con desglose de IVA
- **Sistema de Usuarios**: Registro, login y gestión de perfiles
- **Favoritos**: Los usuarios pueden guardar productos como favoritos
- **Valoraciones**: Sistema de reseñas y puntuaciones por producto
- **Gestión de Pedidos**: Historial y detalle de pedidos para usuarios y admins
- **Panel Administrativo**: Dashboard con estadísticas, CRUD de productos, pedidos, usuarios y stock bajo
- **Configuración de la Tienda**: Panel para gestionar nombre, moneda, productos por página, modo mantenimiento, etc.

### 🎨 Diseño y UX
- Interfaz premium con paleta de colores profesional y animaciones suaves
- Responsive para desktop, tablet y móvil
- Accesibilidad: navegación por teclado y soporte para lectores de pantalla

### 🔒 Seguridad
- **Hashing** de contraseñas con `password_hash()`
- **Prepared Statements** (PDO) contra SQL injection
- **Tokens CSRF** en formularios y peticiones AJAX
- **Logging** con `Monolog` para errores y eventos del sistema
- **Modo Mantenimiento** con acceso exclusivo por IP de confianza

### ⚡ Rendimiento
- **Lazy Loading** de imágenes
- **Caching** del lado del servidor para consultas frecuentes
- **Assets compilados**: CSS y JS unificados en `dist/` vía `npm run build`
- **Desglose de IVA** en carrito y checkout con transparencia de precios

## 🏗️ Estructura del Proyecto

```
SuperMarketArthur/
├── app/
│   └── Controllers/        # 22 controladores MVC
├── models/                 # Modelos (Cart, Config, Favorite, Order, Product, Rating, User)
├── views/                  # Vistas organizadas por sección (admin/, auth/, productos/, account/)
├── core/
│   └── Router.php          # Router central
├── config/                 # Configuración (BD, sesión, logger, errores)
├── includes/               # Partiales de layout (cabecera, menú, pie, modals)
├── assets/                 # Fuentes CSS/JS originales
├── dist/                   # CSS/JS compilados (generados por npm run build)
├── scripts/
│   └── limpiar_carritos.php  # Script de mantenimiento de carritos expirados
├── docs/
│   └── sql/
│       └── SuperMarketArthur.sql  # Esquema y datos de la BD
├── tests/                  # Tests unitarios PHPUnit
├── logs/                   # Logs de la aplicación (app.log, error.log)
├── bootstrap.php           # Arranque global de la aplicación
├── routes.php              # Definición de rutas
└── index.php               # Punto de entrada (front controller)
```

## 🛠️ Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.1+, PDO, MySQL 8.0+ |
| Frontend | HTML5, CSS3 (variables), JavaScript ES6+ |
| Dependencias PHP | Composer, Monolog, PHPUnit |
| Build | Node.js + npm |
| Arquitectura | MVC estricto, Router PSR-4 |

## 📋 Requisitos Previos

- **PHP 8.1+** (recomendado para Monolog 3.x)
- **Servidor web**: Apache/Nginx — se recomienda **XAMPP**
- **MySQL 8.0+**
- **[Composer](https://getcomposer.org/)**
- **[Node.js y npm](https://nodejs.org/)**

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/Aja1702/SuperMarketArthur.git
cd SuperMarketArthur
```

### 2. Instalar Dependencias PHP
```bash
composer install
```

### 3. Instalar Dependencias Frontend
```bash
npm install
```

### 4. Compilar Assets
> ⚠️ **Obligatorio**. Ejecutar también tras cualquier cambio en CSS o JS.
```bash
npm run build
```

### 5. Configurar la Base de Datos
- Crea la base de datos `supermarketarthur` en MySQL
- Importa el esquema:
```sql
-- En phpMyAdmin o consola MySQL:
SOURCE docs/sql/SuperMarketArthur.sql;
```

### 6. Configurar Credenciales de BD
Edita `config/iniciar_session.php`:
```php
$username = "root";           // Tu usuario de MySQL
$password = "";               // Tu contraseña de MySQL
$database = "supermarketarthur";
```

### 7. ¡Listo!
Abre `http://localhost/SuperMarketArthur` en tu navegador.

## 📖 Uso

### Usuarios
1. **Registro**: Crear cuenta con validación completa
2. **Catálogo**: Explorar productos por categorías
3. **Carrito**: Añadir productos y gestionar cantidades
4. **Favoritos**: Guardar productos para más tarde
5. **Checkout**: Completar pedido con información de envío

### Administradores
1. **Login**: Acceder con credenciales de admin
2. **Dashboard**: Ver estadísticas y alertas de stock bajo
3. **Gestión**: CRUD completo de productos, categorías y usuarios
4. **Pedidos**: Ver y gestionar estados
5. **Configuración**: Ajustar parámetros globales de la tienda

## 🔧 Desarrollo

### Compilar Assets
```bash
npm run build
```

### Ejecutar Tests
```bash
# Linux/Mac
./vendor/bin/phpunit tests/

# Windows
vendor\bin\phpunit tests\
```

### Limpiar Carritos Expirados (mantenimiento)
```bash
php scripts/limpiar_carritos.php
```

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado con ❤️ para ofrecer la mejor experiencia de compra online**
