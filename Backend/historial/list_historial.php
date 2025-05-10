<?php
    require_once(__DIR__ . '/../../conectordb/conectordb.php');

    $consulta_historial = "SELECT 
    ventas.id AS IDVenta,  -- Agregamos la columna IDVenta
    clientes.nombre AS NombreCliente,
    GROUP_CONCAT(CONCAT(productos.nombre, ' (x', linea_carrito.cantidad, ') ', linea_carrito.precio, '€') 
    ORDER BY productos.nombre SEPARATOR '<br>') AS ProductosComprados,
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
    GROUP BY ventas.id, clientes.id, impuestos.porcentaje
    ORDER BY clientes.nombre, ventas.fecha_previstaEntrega";

$filtro_nombre = '';

if(isset($_REQUEST) && count($_REQUEST) > 0) {
    $condiciones = " WHERE 1";  // Se usa "1" para evitar errores de sintaxis
    $filtro_nombre = $_REQUEST['nombre'];

    if ($filtro_nombre){
        $condiciones .= " AND clientes.nombre LIKE '%".$filtro_nombre."%'";
    }

    $consulta_historial .= $condiciones;
}

$listado_historial = mysqli_query($conexion, $consulta_historial);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="../Estilos/list_historial.css">
</head>
<body>
    
    <form id="formulariohistorial" method="post" action="list_historial.php">
        <div class="filtros" style="display: flex; gap: 5px;">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id='nombre' style="width: 500px; height: 15px; margin-right: 30px; margin-left: 10px;">
        <input type="submit" style="width: 100px; font-size: 18px; background-color: #007bff; color: rgb(255, 255, 255)"></input>
        <div class="logo">
        <a href="/Backend">
        <img src="../logo.png" style="height: 150px; width: 150px;">
        </a>
    </div>
        </div>
        <br>
        <br>
        <?php
            echo"<style>table{border: 1px solid rgb(89, 97, 168); width: 100%;} td{border: 1px solid rgb(71, 94, 143)} tr{border: 1px solid rgb(71, 96, 125)} </style>";
                    
            echo "<table border='1'><tr><th>id</th><th>Nombre Cliente</th><th>Productos Comprados</th><th>Fecha Entrega</th>
            <th>Subtotal (€)</th><th>IVA (%)</th><th>Total (€)</th></th><th class='descargar-col'>Descargar</th></tr>";

            while ($fila = mysqli_fetch_array($listado_historial)) {
                echo "<tr>
                    <td>".$fila['IDVenta']."</td>
                    <td>".$fila['NombreCliente']."</td>
                    <td>".nl2br($fila['ProductosComprados'])."</td>
                    <td>".$fila['FechaEntrega']."</td>
                    <td>".$fila['Subtotal']." €</td>
                    <td>".$fila['IVA']."%</td>
                    <td>".$fila['Total']." €</td>
                    <td class='descargar-col'> <a href='descargar_resumen.php?id=".$fila['IDVenta']."'> 
                    <img src='descargar.png' class='bmea'> </a></td>
                </tr>";
            }
            echo  "</table>";
            echo "</div>";
            ?>
    </form>
</html>