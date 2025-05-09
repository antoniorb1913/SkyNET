<?php
include("../auth.php");
    require_once(__DIR__ . '/../../conectordb/conectordb.php');

$consulta_productos = "SELECT pr.id as id, pr.referencia as referencia, pr.nombre as nombre, pr.descripcion as descripcion, pr.precio as precio, 
                    pr.stock as stock, pr.alto as alto, pr.ancho as ancho, pr.largo as largo, pr.peso as peso, 
                    ca.nombre as categoria, m.nombre as marca, pr.updated_at as fecha_modificacion
                    FROM productos pr
                    LEFT JOIN categorias ca ON pr.categoria_id = ca.id
                    LEFT JOIN marcas m ON m.id = pr.marca_id";
$filtro_nombre='';
$filtro_categoria='';
$filtro_marca='';

if(isset($_REQUEST) && count($_REQUEST)>0) {
    $condiciones = " where true";
    $filtro_nombre = $_REQUEST['nombre'];
    $filtro_categoria = $_REQUEST['categoria'];
    $filtro_marca = $_REQUEST['marca'];

    if ($filtro_nombre){
        $condiciones .= " AND pr.nombre LIKE '%".$filtro_nombre."%'";
    }
        
    if($filtro_categoria > 0){
        $condiciones .= " AND pr.categoria_id = ".$filtro_categoria;
    }
    if($filtro_marca > 0) {
        $condiciones .= " AND pr.marca_id = ".$filtro_marca;
        
    } 

$consulta_productos .= $condiciones;

}

$consulta_productos .= " ORDER BY ca.nombre ASC";
$listado_productos = mysqli_query( $conexion, $consulta_productos);
$consulta_categoria= "SELECT * FROM categorias";
$categorias = mysqli_query( $conexion, $consulta_categoria);
$consulta_marca= "SELECT * FROM marcas";
$marcas = mysqli_query( $conexion, $consulta_marca);  
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
        padding: 10px 10px;
        font-size: 15px;
        color: white;
        background-color: #007bff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        text-align: center;
        margin-left: 10px;
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
    <input type="text" name="nombre" id='nombre' style="width: 250px; height: 15px; margin-right: 30px; margin-left: 10px;">
    <label for="categoria">Filtro categoria:</label>
    <select name="categoria" style="width: 150px; height: 40px; margin-right: 30px; margin-left: 10px;">
        <option value="0"></option>
        <?php while ($categoria = mysqli_fetch_array( $categorias )) { ?>
        <option style="width: 200px;" value="<?php echo $categoria['id'] ?> "> <?php echo $categoria['nombre']?></option>';
        <?php } ?>
        </select>
    <label for="marca">Filtro marca:</label>
    <select name="marca" style="width: 150px; height: 40px; margin-left: 10px; margin-right: 30px;">
        <option value="0"></option>
        <?php while ($marca = mysqli_fetch_array( $marcas )) { ?>
        <option style="width: 200px;" value="<?php echo $marca['id'] ?> "> <?php echo $marca['nombre']?></option>';
        <?php } ?>
        </select>
    <input type="submit" style="width: 100px; font-size: 18px; background-color: #007bff; color: rgb(255, 255, 255)"></input>
    <?php
        echo "<a href='xml.php?nombre=".$filtro_nombre."&categoria=".$filtro_categoria."&marca=".$filtro_marca."' class='boton'>Exportar XML</a>";
        echo "<a href='buscar_xpath.php?nombre=" . urlencode($filtro_nombre) . "&categoria=" . urlencode($filtro_categoria) . "&marca=" . urlencode($filtro_marca) . "' class='boton'>Buscar con XPath</a>";
    ?>
    
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