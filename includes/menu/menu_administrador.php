<nav class="menu-admin" role="navigation" aria-label="Menú administrador SuperMarketArthur">
    <ul class="menu-admin-lista">
        <!-- 1. DASHBOARD -->
        <li class="menu-item dashboard activo">
            <a href="<?php echo BASE_URL; ?>admin" class="menu-link-admin">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 2. PRODUCTOS -->
        <li class="menu-item productos">
            <a href="<?php echo BASE_URL; ?>admin/productos" class="menu-link-admin">
                <i class="fas fa-box"></i>
                <span>Productos</span>
                <span class="badge"><?php echo $admin_stats['total_products'] ?? 0; ?></span>
            </a>
        </li>

        <!-- 3. CATEGORÍAS -->
        <li class="menu-item categorias">
            <a href="<?php echo BASE_URL; ?>admin/categorias" class="menu-link-admin">
                <i class="fas fa-sitemap"></i>
                <span>Categorías</span>
            </a>
        </li>

        <!-- 4. PEDIDOS -->
        <li class="menu-item pedidos">
            <a href="<?php echo BASE_URL; ?>admin/pedidos" class="menu-link-admin">
                <i class="fas fa-truck"></i>
                <span>Pedidos</span>
                <span class="badge badge-rojo"><?php echo $admin_stats['pending_orders'] ?? 0; ?></span>
            </a>
        </li>

        <!-- 5. USUARIOS -->
        <li class="menu-item usuarios">
            <a href="<?php echo BASE_URL; ?>admin/usuarios" class="menu-link-admin">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
                <span class="badge"><?php echo $admin_stats['total_users'] ?? 0; ?></span>
            </a>
        </li>

        <!-- 6. STOCK -->
        <li class="menu-item stock">
            <a href="<?php echo BASE_URL; ?>admin/stock" class="menu-link-admin">
                <i class="fas fa-warehouse"></i>
                <span>Stock</span>
                <span class="badge badge-amarillo"><?php echo $admin_stats['low_stock_products'] ?? 0; ?></span>
            </a>
        </li>

        <!-- 7. CONFIGURACIÓN -->
        <li class="menu-item config">
            <a href="<?php echo BASE_URL; ?>admin/config" class="menu-link-admin">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>
</nav>
