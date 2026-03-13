# Changelog - SuperMarketArthur

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-03-13

### Added
- ✅ Sistema de E-commerce completo (catálogo, carrito, usuarios, pedidos, favoritos)
- ✅ Panel de Administración avanzado con Dashboard
- ✅ Panel de Configuración Global
- ✅ Sistema de Valoraciones y Reseñas de productos
- ✅ Diseño Moderno y Responsive
- ✅ Seguridad (Hashing, Prepared Statements, CSRF, Modo Mantenimiento)
- ✅ Optimización y Rendimiento (Caching, Paginación, Lazy Loading)
- ✅ Arquitectura MVC limpia con Router central

### Fixed
- Corregido FavoriteController para usar método getByUser
- Corregida ruta de views en FavoriteController (de /../../ a /../../../)
- Corregida imagen favoritos.svg → favoritos.png → far fa-heart (Font Awesome)
- Copiados assets faltantes de public/assets a assets/
- Activado .htaccess para rewrite URLs
- Corregido package.json para usar public/assets/
- Añadido CSS para centrar icono fa-heart en menú

---

## [0.9.0] - Pre-release

### Added
- Catálogo de productos con categorías
- Carrito de compras con persistencia
- Sistema de usuarios (registro, login, perfil)
- Favoritos
- Valoraciones de productos
- Gestión de pedidos
- Panel administrativo completo

---

**Para más información, visita: https://github.com/Aja1702/SuperMarketArthur**
