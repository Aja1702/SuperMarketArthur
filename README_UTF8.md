# SuperMarketArthur - Sistema de E-commerce Profesional

Un sistema completo de supermercado online desarrollado en PHP con MySQL, diseñado con estándares profesionales para ofrecer una experiencia de compra excepcional.

## ✅ Estado del Proyecto

Este es un proyecto en desarrollo activo. A continuación, se detalla el estado actual de las características principales:

- **[x]** Funcionalidades E-commerce (Catálogo, Carrito, Usuarios, Pedidos)
- **[x]** Panel de Administración Avanzado con Dashboard.
- **[x]** **Panel de Configuración Global**: Controla ajustes clave de la tienda sin tocar el código.
- **[x]** Diseño Moderno y Responsive, con **Modo Oscuro** funcional.
- **[x]** Seguridad (Hashing, Prepared Statements, CSRF, Modo Mantenimiento con IP).
- **[x]** Optimización y Rendimiento (Caching, Paginación, Lazy Loading).
- **[x]** Arquitectura MVC con Refactorización y código limpio (CSS externalizado).

## 🚀 Características Principales

### 🛒 Funcionalidades del E-commerce
- **[x]** **Catálogo de Productos**: Navegación por categorías con paginación configurable.
- **[x]** **Carrito de Compras**: Gestión inteligente del carrito con persistencia de sesión.
- **[x]** **Sistema de Usuarios**: Registro, login y gestión de perfiles.
- **[x]** **Gestión de Pedidos**: Historial y detalle de pedidos para usuarios y administradores.
- **[x]** **Panel Administrativo**: Dashboard con estadísticas, gestión de productos, pedidos, usuarios y stock bajo.
- **[x]** **Configuración de la Tienda**: Panel para gestionar Nombre del Sitio, Email, Productos por página, Moneda, Modo Mantenimiento y más, sin necesidad de editar código.

### 🎨 Diseño y UX
- **[x]** **Interfaz Moderna**: Diseño premium con paleta de colores profesional.
- **[x]** **Responsive Design**: Optimizado para desktop, tablet y móvil.
- **[x]** **Modo Oscuro**: Interruptor para cambiar entre tema claro y oscuro, con persistencia y detección del sistema.
- **[x]** **Animaciones Suaves**: Transiciones y efectos hover elegantes.
- **[x]** **Accesibilidad**: Soporte para navegación por teclado y lectores de pantalla.

### 🔒 Seguridad
- **[x]** **Hashing de Contraseñas**: Implementación segura con `password_hash()`.
- **[x]** **Prepared Statements**: Protección contra SQL injection usando PDO.
- **[x]** **Protección CSRF**: Tokens en formularios para prevenir ataques.
- **[x]** **Logging de Errores**: Uso de `Monolog` para registrar eventos y errores del sistema.
- **[x]** **Modo Mantenimiento Profesional**: Permite poner la web "fuera de servicio" con una IP de confianza para acceso exclusivo del desarrollador.

### ⚡ Rendimiento
- **[x]** **Lazy Loading**: Carga diferida de imágenes para mejor performance inicial.
- **[x]** **Paginación**: Navegación eficiente en todos los catálogos y tablas de administración.
- **[x]** **Optimización de Assets**: CSS y JS estructurados y compilados en un único archivo `bundle` para carga rápida.
- **[x]** **Caching del Lado del Servidor**: Sistema de caché para consultas frecuentes (ej. productos destacados), reduciendo la carga de la base de datos.

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8+ con PDO y MySQL
- **Frontend**: HTML5, CSS3 (con variables), JavaScript ES6+ (con sistema de build npm)
- **Dependencias PHP**: Composer, Monolog (para logging), PHPUnit (para testing)
- **Arquitectura**: MVC en proceso de refactorización hacia un modelo más estricto con un Router central.

## 📋 Requisitos Previos

Asegúrate de tener instalados los siguientes programas en tu sistema:

- **PHP 8.1** o superior (recomendado para `Monolog 3.x`).
- **Servidor web** (Apache, Nginx, etc.). Se recomienda **XAMPP** para un entorno de desarrollo rápido.
- **MySQL 8.0** o superior (incluido en XAMPP).
- **[Composer](https://getcomposer.org/)** (gestor de dependencias de PHP).
- **[Node.js](https://nodejs.org/) y npm** (gestor de paquetes de JavaScript).

## 🚀 Instalación y Configuración

Sigue estos pasos en orden para poner en marcha el proyecto:

### 1. Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/SuperMarketArthur.git
cd SuperMarketArthur
```

### 2. Instalar Dependencias de Backend
Usa Composer para instalar las librerías de PHP necesarias (como Monolog y PHPUnit).

```bash
composer install
```

### 3. Instalar Dependencias de Frontend
Usa npm para instalar las herramientas de compilación de CSS y JavaScript.

```bash
npm install
```

### 4. Compilar Assets
Este paso es **crucial**. Compila todos los archivos CSS y JS en `assets/` y los empaqueta en la carpeta `dist/`, que es la que usa la aplicación. **Debes ejecutar este comando cada vez que hagas un cambio en los archivos CSS o JS**.

```bash
npm run build
```

### 5. Configurar la Base de Datos
- Asegúrate de que tu servidor MySQL esté en funcionamiento.
- Crea una nueva base de datos llamada `supermarketarthur`.
- Importa la estructura y los datos iniciales ejecutando el script SQL:
  ```sql
  -- Desde la consola de MySQL o una herramienta como phpMyAdmin
  SOURCE docs/sql/SuperMarketArthur.sql;
  ```

### 6. Configurar la Conexión a la BD
Edita el archivo `config/iniciar_session.php` con tus credenciales de la base de datos:
```php
$username = "root";       // Tu usuario de la base de datos
$password = "";           // Tu contraseña de la base de datos
$database = "supermarketarthur";
```

### 7. ¡Listo!
Abre el proyecto en tu navegador (ej: `http://localhost/SuperMarketArthur`) y ¡listo para funcionar!


## 📖 Uso

### Para Usuarios
1. **Registro**: Crear cuenta con validación completa
2. **Navegación**: Explorar productos por categorías
3. **Carrito**: Agregar productos y gestionar cantidades
4. **Checkout**: Completar pedido con información de envío

### Para Administradores
1. **Login**: Acceder con credenciales de admin
2. **Dashboard**: Ver estadísticas y alertas
3. **Gestión**: CRUD completo de productos y usuarios
4. **Pedidos**: Gestionar estados y seguimiento

## 🔧 Desarrollo

### Ejecutar Tests
Para ejecutar la suite de tests unitarios, usa el siguiente comando:
```bash
./vendor/bin/phpunit tests/
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado con ❤️ para ofrecer la mejor experiencia de compra online**
