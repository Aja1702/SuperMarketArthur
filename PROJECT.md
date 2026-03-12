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
- **[x]** Arquitectura MVC limpia con Router central

## 🚀 Características Principales

### 🛒 E-commerce
- **Catálogo de Productos**: Navegación por categorías con paginación configurable
- **Carrito de Compras**: Persistencia de sesión con desglose de IVA
- **Sistema de Usuarios**: Registro, login y gestión de perfiles
- **Favoritos**: Los usuarios pueden guardar productos como favorito
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
- **Desglose de IVA** en carrito y checkout con transparencia de precios

## 🏗️ Estructura del Proyecto

```
SuperMarketArthur/
├── src/
│   ├── Controllers/          # Controladores (Admin/, Auth/, Shop/)
│   │   ├── Admin/           # Controladores del panel de administración
│   │   ├── Auth/           # Controladores de autenticación
│   │   └── Shop/           # Controladores de la tienda
│   ├── Models/              # Modelos (Cart, Config, Favorite, Order, Product, Rating, User)
│   ├── Core/                # Router central
│   ├── Middleware/          # Middlewares de autenticación
│   ├── Services/            # Servicios (CartService)
│   ├── Utilities/           # Helpers y funciones auxiliares
│   └── cache/               # Caché de productos destacados
├── views/                   # Vistas organizadas por sección
│   ├── admin/               # Vistas del panel de administración
│   ├── auth/                # Vistas de login y registro
│   ├── productos/           # Vistas de catálogo y detalle
│   └── account/             # Vistas de cuenta de usuario
├── public/
│   ├── assets/              # CSS, JS e imágenes públicos
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── .htaccess            # Configuración Apache
├── config/                  # Configuración (BD, sesión, logger, errores)
├── includes/                # Partiales de layout (cabecera, menú, pie, modals)
├── scripts/
│   └── limpiar_carritos.php  # Script de mantenimiento
├── docs/
│   └── sql/
│       └── SuperMarketArthur.sql  # Esquema de la BD
├── storage/
│   ├── cache/               # Caché de la aplicación
│   └── logs/                # Logs de la aplicación
├── tests/                   # Tests unitarios PHPUnit
├── bootstrap.php            # Arranque de la aplicación
├── routes.php               # Definición de rutas
├── index.php                # Punto de entrada
├── composer.json            # Dependencias PHP
└── .env                     # Variables de entorno (NO incluir en git)
```

## 🛠️ Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.1+, PDO, MySQL 8.0+ |
| Frontend | HTML5, CSS3, JavaScript ES6+ |
| Dependencias PHP | Composer, Monolog, PHPUnit |
| Arquitectura | MVC estricto, PSR-4 |

## 📋 Requisitos Previos

- **PHP 8.1+**
- **Servidor web**: Apache/Nginx — se recomienda **XAMPP**
- **MySQL 8.0+**
- **[Composer](https://getcomposer.org/)**

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

### 3. Configurar la Base de Datos
- Crea la base de datos `supermarketarthur` en MySQL
- Importa el esquema:
```sql
-- En phpMyAdmin o consola MySQL:
SOURCE docs/sql/SuperMarketArthur.sql;
```

### 4. Configurar Credenciales de BD
Edita el archivo `.env` en la raíz del proyecto:
```env
DB_HOST=localhost
DB_NAME=supermarketarthur
DB_USER=root
DB_PASSWORD=
```

### 5. ¡Listo!
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

### Ejecutar Tests
```bash
vendor\bin\phpunit tests\
```

### Limpiar Carritos Expirados
```bash
php scripts/limpiar_carritos.php
```

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado con ❤️ para ofrecer la mejor experiencia de compra online**
