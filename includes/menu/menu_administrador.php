<nav class="menu-admin" role="navigation" aria-label="Menú administrador SuperMarketArthur">
    <ul class="menu-admin-lista">
        <!-- 1. DASHBOARD -->
        <li class="menu-item dashboard activo">
            <a href="?vistaMenu=admin_dashboard" class="menu-link-admin" >
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- 2. PRODUCTOS -->
        <li class="menu-item productos">
            <a href="?vistaMenu=admin_productos" class="menu-link-admin" >
                <i class="fas fa-box"></i>
                <span>Productos</span>
                <span class="badge"><?php echo $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(); ?></span>
            </a>
        </li>

        <!-- 3. PEDIDOS -->
        <li class="menu-item pedidos">
            <a href="?vistaMenu=admin_pedidos" class="menu-link-admin" >
                <i class="fas fa-truck"></i>
                <span>Pedidos</span>
                <span class="badge badge-rojo">
                    <?php 
                    echo $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'")->fetchColumn(); 
                    ?>
                </span>
            </a>
        </li>

        <!-- 4. USUARIOS -->
        <li class="menu-item usuarios">
            <a href="?vistaMenu=admin_usuarios" class="menu-link-admin" >
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
                <span class="badge"><?php echo $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usu='u'")->fetchColumn(); ?></span>
            </a>
        </li>

        <!-- 5. STOCK -->
        <li class="menu-item stock">
            <a href="?vistaMenu=admin_stock" class="menu-link-admin" >
                <i class="fas fa-warehouse"></i>
                <span>Stock</span>
                <span class="badge badge-amarillo">
                    <?php 
                    echo $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= 5")->fetchColumn(); 
                    ?>
                </span>
            </a>
        </li>

        <!-- 6. CONFIGURACIÓN -->
        <li class="menu-item config">
            <a href="?vistaMenu=admin_config" class="menu-link-admin" >
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>
</nav>
