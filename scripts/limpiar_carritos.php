<?php
// ============== SuperMarketArthur - Cron Job para Limpiar Carritos Abandonados ==============
//
// Este script está diseñado para ser ejecutado periódicamente (ej. una vez al día)
// para mantener la base de datos limpia y eficiente.
//
// ------------------------------------------------------------------------------------------

// Modo consola para una salida más limpia
echo "======================================================\n";
echo "== INICIANDO SCRIPT DE LIMPIEZA DE CARRITOS        ==\n";
echo "======================================================\n";

// 1. Cargar el entorno de la aplicación (BD, configuraciones, etc.)
// Nos situamos en el directorio del script para que las rutas funcionen
chdir(__DIR__);
require_once '../bootstrap.php';

if (!isset($pdo)) {
    echo "[ERROR] No se pudo establecer la conexión con la base de datos.\n";
    exit(1); // Salir con código de error
}

echo "[INFO] Conexión con la base de datos establecida con éxito.\n";

// 2. Definir la consulta SQL para eliminar carritos de invitados de más de 24 horas
// Esta consulta es eficiente porque usa un JOIN para borrar de ambas tablas a la vez.
$sql = "
    DELETE ci, ct
    FROM carrito_temp AS ct
    LEFT JOIN carrito_items AS ci ON ct.id_carrito = ci.id_carrito
    WHERE ct.id_usuario IS NULL
      AND ct.creado_en < NOW() - INTERVAL 24 HOUR;
";

echo "[INFO] Ejecutando la consulta de limpieza...\n";

try {
    // 3. Ejecutar la consulta y obtener el número de filas afectadas
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $carritos_eliminados = $stmt->rowCount();

    // El rowCount en un DELETE con múltiples tablas puede no ser 100% preciso
    // para el número de carritos, pero nos indica si se ha borrado algo.
    if ($carritos_eliminados > 0) {
        echo "[SUCCESS] ¡Limpieza completada! Se han eliminado los datos de carritos abandonados. (Filas afectadas: {$carritos_eliminados})\n";
    } else {
        echo "[INFO] No se encontraron carritos abandonados para limpiar. ¡La base de datos ya está impecable!\n";
    }

} catch (PDOException $e) {
    echo "[ERROR] Ocurrió un error durante la ejecución de la consulta: " . $e->getMessage() . "\n";
    exit(1);
}

echo "======================================================\n";
echo "== SCRIPT FINALIZADO                               ==\n";
echo "======================================================\n";

exit(0); // Salir con código de éxito
