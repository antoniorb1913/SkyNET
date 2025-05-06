<?php
    require_once('conectordb.php');

$consulta_productos = "SELECT 
    clientes.nombre AS NombreCliente,
    productos.nombre AS NombreProducto,
    ventas.id AS PedidoID,
    ventas.importe_total AS ImporteTotal,
    ventas.fecha_previstaEntrega AS FechaEntrega
FROM 
    clientes
JOIN 
    carrito ON clientes.id = carrito.cliente_id
JOIN 
    linea_carrito ON carrito.id = linea_carrito.carrito_id
JOIN 
    productos ON linea_carrito.producto_id = productos.id
JOIN 
    ventas ON carrito.id = ventas.carrito_id";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #333;
    }

    form {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        margin: auto;
    }

    .filtros {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .logo {
        margin-left: auto;
        display: flex;
        align-items: center; 
        margin-right: 1%;
    }


    .logo img {
        height: 130px;
        width: 80px;
    }


    label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }

    select, .filtros input {
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    /* Estilo para la tabla */
    .tabla-container {
        margin-top: 20px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .boton {
        display: inline-block;
        padding: 10px 20px;
        font-size: 18px;
        color: white;
        background-color: #007bff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        text-align: center;
        line-height: 20px;
        margin-left: 20px;
    }

    .boton:hover {
        background-color: #0056b3;
    }
    .bmea {
    width: 18px;
    height: 18px;
    }

</style>
</head>
<body>
    
    <form id="formularioproductos" method="post" action="list_productos.php">
    <div class="filtros" style="display: flex; gap: 5px;">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id='nombre' style="width: 500px; height: 15px; margin-right: 30px; margin-left: 10px;">
    <label for="categoria">Filtro categoria:</label>
    <select name="categoria" style="width: 200px; height: 40px; margin-right: 30px; margin-left: 10px;">
        <option value="0"></option>
        <?php while ($categoria = mysqli_fetch_array( $categorias )) { ?>
        <option style="width: 200px;" value="<?php echo $categoria['id'] ?> "> <?php echo $categoria['nombre']?></option>';
        <?php } ?>
        </select>
    <label for="marca">Filtro marca:</label>
    <select name="marca" style="width: 200px; height: 40px; margin-left: 10px; margin-right: 30px;">
        <option value="0"></option>
        <?php while ($marca = mysqli_fetch_array( $marcas )) { ?>
        <option style="width: 200px;" value="<?php echo $marca['id'] ?> "> <?php echo $marca['nombre']?></option>';
        <?php } ?>
        </select>
    <input type="submit" style="width: 100px; font-size: 18px; background-color: #007bff; color: rgb(255, 255, 255)"></input>
    <?php
        echo "<a href='xml.php?nombre=".$filtro_nombre."&categoria=".$filtro_categoria."&marca=".$filtro_marca."' class='boton'>Exportar XML</a>&nbsp;&nbsp;";
    ?>
    <div class="logo">
        <img src="imagenes/logo.png" style="heght: 150px; width: 150px;">
    </div>
    </div>
    <br>
    <br>
    <?php
        echo"<style>table{border: 1px solid rgb(89, 97, 168); width: 100%;} td{border: 1px solid rgb(71, 94, 143)} tr{border: 1px solid rgb(71, 96, 125)} </style>";
                
                echo "<table class=tabla> <tr> <th> id </th> <th> Referencia </th> <th> Nombre </th> <th> Descripción </th> <th> Precio </th> <th> Stock </th> <th> Alto(mm) </th>
                <th> Ancho(mm) </th> <th> Largo(mm) </th ><th> Peso </th> <th> Categoria </th> <th> Marca </th> <th> Acciones </th></tr>";

                while ($fila = mysqli_fetch_array($listado_productos)) {

                    echo "<tr><td>".$fila['id']."</td>
                    <td>".$fila['referencia']."</td>
                    <td>".$fila['nombre']."</td>
                    <td>".$fila['descripcion']."</td>
                    <td>".$fila['precio']."</td>
                    <td>".$fila['stock']."</td>
                    <td>".$fila['alto']."</td>
                    <td>".$fila['ancho']."</td>
                    <td>".$fila['largo']."</td>
                    <td>".$fila['peso']."</td>
                    <td>".$fila['categoria']."</td>
                    <td>".$fila['marca']."</td> 
                    <td> <a href='edit_productos.php? id=".$fila['id']."'> <img src='imagenes/editar.png' class='bmea'> </a>
                    <a href='borrar_productos.php? id=".$fila['id']."'> <img src='imagenes/borrar.png' class='bmea'> </a></td></tr>";
                    
                }

                echo  "</table>";
                echo "</div>";
                ?>
        </form>
    </div>
</html>