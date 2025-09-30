<?php
include('./sesion_bbdd/iniciar_session.php');

$id_categoria = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($id_categoria > 0) {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si no hay categoría, puedes decidir mostrar un mensaje o todos los productos
    $result = $conn->query("SELECT * FROM productos");
}
?>

<h2>Productos <?php
                if ($id_categoria > 0) {
                    $cat_res = $conn->query("SELECT nombre_categoria FROM categorias WHERE id_categoria = $id_categoria");
                    if ($cat_res && $cat_res->num_rows > 0) {
                        echo htmlspecialchars($cat_res->fetch_assoc()['nombre_categoria']);
                    }
                } else {
                    echo "Todos";
                }
                ?></h2>

<div class="lista-productos">
    <?php while ($producto = $result->fetch_assoc()): ?>
        <article class="producto">
            <h3><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
            <p>Precio: €<?php echo number_format($producto['precio'], 2); ?></p>
            <!-- Más detalles o botones dependiendo -->
        </article>
    <?php endwhile; ?>
</div>