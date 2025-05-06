<?php
require_once(__DIR__ . '/../../conectordb/conectordb.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$consulta_nombre_cliente = "SELECT clientes.nombre AS NombreCliente FROM clientes
JOIN carrito ON clientes.id = carrito.cliente_id
JOIN ventas ON carrito.id = ventas.carrito_id
WHERE ventas.id = $id LIMIT 1";

$resultado_cliente = mysqli_query($conexion, $consulta_nombre_cliente);
$fila_cliente = mysqli_fetch_assoc($resultado_cliente);

$nombre_cliente = $fila_cliente ? $fila_cliente['NombreCliente'] : 'Cliente_Desconocido';
$nombre_archivo = "Resumen_Compra_" . str_replace(' ', '_', $nombre_cliente) . ".txt";

header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");

$consulta_historial = "SELECT 
    clientes.nombre AS NombreCliente,
    GROUP_CONCAT(CONCAT(productos.nombre, ' (x', linea_carrito.cantidad, ') ', linea_carrito.precio, '€') ORDER BY productos.nombre SEPARATOR ', ') AS ProductosComprados,
    ventas.fecha_previstaEntrega AS FechaEntrega,
    SUM(linea_carrito.cantidad * linea_carrito.precio) AS Subtotal,
    impuestos.porcentaje AS IVA,
    SUM(ventas.importe_total) AS Total
    FROM clientes
    JOIN carrito ON clientes.id = carrito.cliente_id
    JOIN linea_carrito ON carrito.id = linea_carrito.carrito_id
    JOIN productos ON linea_carrito.producto_id = productos.id
    JOIN ventas ON carrito.id = ventas.carrito_id
    JOIN impuestos ON ventas.impuesto_id = impuestos.id
    WHERE ventas.id = $id
    GROUP BY clientes.id, ventas.id, impuestos.porcentaje";

$resultado = mysqli_query($conexion, $consulta_historial);

while ($fila = mysqli_fetch_array($resultado)) {
    echo "Cliente: " . $fila['NombreCliente'] . "\n";
    echo "Productos: " . $fila['ProductosComprados'] . "\n";
    echo "Fecha de Entrega: " . $fila['FechaEntrega'] . "\n";
    echo "Subtotal: " . $fila['Subtotal'] . " €\n";
    echo "IVA: " . $fila['IVA'] . "%\n";
    echo "Total: " . $fila['Total'] . " €\n";
    echo "------------------------------\n";
}
?>
