# TODO: Mejoras Profesionales para SuperMarketArthur

## 1. Mejoras en Modelo de Datos y Lógica Backend
- [x] Expandir clase Product.php con métodos para búsqueda avanzada, filtros por categoría/precio, y paginación.
- [x] Agregar clase User.php para gestión de usuarios, incluyendo hashing de contraseñas con password_hash().
- [x] Implementar clase Order.php para gestión de pedidos, con estados y cálculos de totales.
- [x] Crear clase Cart.php para lógica del carrito, integrando con sesiones o base de datos.
- [x] Agregar tabla de valoraciones en base de datos (ya implementada).
- [ ] Crear modelo Rating.php para gestión de valoraciones de productos.
- [ ] Implementar lógica de cálculo de promedio de valoraciones en Product.php.

## 2. Mejoras en Seguridad
- [ ] Implementar hashing de contraseñas en registro y login usando password_verify().
- [ ] Agregar protección CSRF en formularios.
- [ ] Sanitizar inputs y usar prepared statements en consultas SQL.
- [ ] Implementar rate limiting en login para prevenir ataques de fuerza bruta.

## 3. Mejoras en UI/UX
- [x] Actualizar CSS para mejor responsive (mejor soporte móvil/tablet).
- [x] Agregar animaciones sutiles y transiciones para interacciones (hover, load).
- [x] Mejorar accesibilidad: Alt texts, ARIA labels, navegación por teclado.
- [x] Implementar lazy loading para imágenes de productos.
- [x] Agregar modo oscuro opcional.

## 4. Nuevas Funcionalidades
- [x] Agregar filtros y búsqueda avanzada en catálogo (precio, categoría, stock) (parcialmente en Product.php).
- [ ] Implementar sistema de recomendaciones basado en compras previas.
- [ ] Agregar valoraciones y reseñas de productos (tabla en BD, falta implementación).
- [ ] Integrar pagos con Stripe (simulado inicialmente).
- [ ] Agregar notificaciones push para estado de pedidos.

## 5. Optimización y Rendimiento
- [ ] Implementar caching básico (para productos populares).
- [ ] Optimizar consultas SQL con índices.
- [ ] Comprimir assets (CSS/JS minificados).
- [ ] Agregar PWA features (service worker para offline).

## 6. Testing y QA
- [ ] Crear tests unitarios básicos con PHPUnit para clases modelo.
- [ ] Agregar tests de integración para login/registro.
- [ ] Probar cross-browser y dispositivos.
- [ ] Implementar logging de errores.

## 7. Escalabilidad y Arquitectura
- [ ] Refactorizar a MVC más estricto.
- [ ] Considerar migración a framework como Laravel para mejor estructura.
- [ ] Agregar API REST para futuras apps móviles.
- [ ] Implementar Docker para desarrollo consistente.

## 8. Documentación y Deployment
- [ ] Actualizar README con instalación, configuración y uso.
- [ ] Agregar scripts de deployment.
- [ ] Documentar API endpoints si se crean.
