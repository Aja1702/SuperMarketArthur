<div class="admin-usuarios">
    <h2 class="titulo-seccion-premium">Gestión de Usuarios</h2>

    <?php if (empty($usuarios)): ?>
        <div class="empty-state">
            <i class="fas fa-user-slash"></i>
            <h3>No hay usuarios registrados</h3>
            <p>Cuando los clientes comiencen a registrarse, los verás aquí.</p>
        </div>
    <?php else: ?>
        <div class="tabla-responsive">
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><strong>#<?php echo htmlspecialchars($usuario['id_usuario']); ?></strong></td>
                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido1']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($usuario['fecha_registro'])); ?></td>
                            <td>
                                <div class="acciones">
                                    <a href="#" class="btn-sm ver" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn-sm eliminar" title="Eliminar Usuario">
                                        <i class="fas fa-user-times"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="paginacion">
            <?php if ($total_paginas > 1): ?>
                <a href="?vistaMenu=admin_usuarios&page=<?php echo max(1, $pagina_actual - 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                    &laquo; Anterior
                </a>

                <span class="info-paginacion">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <a href="?vistaMenu=admin_usuarios&page=<?php echo min($total_paginas, $pagina_actual + 1); ?>"
                   class="btn-paginacion <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                    Siguiente &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
