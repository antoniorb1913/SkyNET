<?php
include("../auth.php");
    require_once(__DIR__ . '/../../conectordb/conectordb.php');

    
    if (isset($_GET['success'])) {
        $codigo = $_GET['success'];
        if (isset($mensajes[$codigo])) {
            echo "<div class='alert alert-{$mensajes[$codigo]['tipo']}'>{$mensajes[$codigo]['texto']}</div>";
        }
    }
    
    if (isset($_GET['error'])) {
        $codigo = $_GET['error'];
        if (isset($mensajes[$codigo])) {
            echo "<div class='alert alert-{$mensajes[$codigo]['tipo']}'>{$mensajes[$codigo]['texto']}</div>";
        }
    }

$consulta_soporte = "SELECT s.id as id, s.nombre as nombre, s.email as email, s.asunto as asunto, s.mensaje as mensaje,
                    s.fecha_entrada as fecha_entrada, s.estado as estado, s.cliente_id as cliente, s.created_at
                    FROM soporte s
                    LEFT JOIN clientes c ON s.cliente_id = c.id
                    ORDER BY s.fecha_entrada ASC";

$listado_soporte = mysqli_query( $conexion, $consulta_soporte);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="../Estilos/list_soporte.css">
    
</head>
<body>
        <div class="logo">
        <a href="/Backend">
        <img src="../logo.png" style="height: 120px; width: 120px;">
        </a>
    </div>
    <br>
    <form id="formularioproductos" method="post" action="list_soporte.php">
    </div>
    </div>
    <br>
    <br>
    <?php
        echo"<style>table{border: 1px solid rgb(89, 97, 168); width: 100%;} td{border: 1px solid rgb(71, 94, 143)} tr{border: 1px solid rgb(71, 96, 125)} </style>";
                
                echo "<table class=tabla> <tr> <th> id </th> <th> Nombre </th> <th> Email </th> <th> Asunto </th> <th> Mensaje </th><th> Fecha Entrada </th> <th> Estado </th> <th> Cliente</th><th> Acciones</th></tr>";

                while ($fila = mysqli_fetch_array($listado_soporte)) {

                    echo "<tr><td>".$fila['id']."</td>
                    <td>".$fila['nombre']."</td>
                    <td>".$fila['email']."</td>
                    <td>".$fila['asunto']."</td>
                    <td>".$fila['mensaje']."</td>
                    <td>".$fila['fecha_entrada']."</td>
                    <td>".$fila['estado']."</td>
                    <td>".$fila['cliente']."</td>
                    <td> <a href='proceso_soporte.php? id=".$fila['id']."'> <img src='../Productos/imagenes/borrar.png' class='bmea'> </a>
                    <a href='finalizar_soporte.php? id=".$fila['id']."'> <img src='../Productos/imagenes/borrar.png' class='bmea'> </a>
                    <a href='borrar_soporte.php? id=".$fila['id']."'> <img src='../Productos/imagenes/borrar.png' class='bmea'> </a></td></tr>";
                }

                echo  "</table>";
                echo "</div>";
                ?>
        </form>
    </div>
</html>