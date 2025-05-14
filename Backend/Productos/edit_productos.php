<?php 

$respuesta = $_REQUEST;
require_once(__DIR__ . '/../../conectordb/conectordb.php');

$editar=FALSE;
if (isset($_REQUEST['id'])) {
    $editar=TRUE;
    $idProducto = $_REQUEST['id'];
    $consulta = " SELECT * from productos WHERE id=".$idProducto;
    $resultado_consulta = mysqli_query($conexion,$consulta);
    $producto = mysqli_fetch_array($resultado_consulta);
    $consulta_categorias = "SELECT * FROM categorias";
    $categorias = mysqli_query($conexion, $consulta_categorias) or die("Error en la consulta");
    $consulta_marcas = "SELECT * FROM marcas";
    $marcas = mysqli_query($conexion, $consulta_marcas) or die("Error en la consulta");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIENTES</title>
    <script src="/Scripts/validar_formulario.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .formulario {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select, textarea {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 97%;
        }

        .botones {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .bmea {
            width: 24px;
            height: 24px;
            margin: 2px;
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
        h1 {
            text-align: center;
        }
        textarea {
            resize: both;
            max-width: 100%;
            box-sizing: border-box; 
            overflow: auto;
        }
    </style>
    </head>
<body>
    <div class="fondo"></div>
    <div class="titulo">
        <h1><?php echo $editar?"Editar":"Nuevo";?> Producto</h1>  
    </div>
    <div class="formulario">
        <form id="formularioproductos" method="post" action="alta_productos.php" enctype="multipart/form-data">
            <?php
            if ($editar) {
                echo '<input type="hidden" value="'.$producto['id'].'"name="idProducto"/>';
            }
            ?>
            <label for="referencia">Referencia:</label>
            <input type="text" name="referencia" id='referencia' value="<?php echo $editar?$producto['referencia']:''?>">
            <span class="error" id="errorReferencia"></span>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id='nombre' value="<?php echo $editar?$producto['nombre']:''?>">
            <span class="error" id="errorNombre"></span>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?php echo $editar ? $producto['descripcion'] : ''; ?></textarea>
            <span class="error" id="errorDescripcion"></span>
            <br>
            <br>
            <div class="ps" style="display: flex; gap: 5px;">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" name="precio" id='precio' style="margin-right: 30px; margin-left: 20px;" value="<?php echo $editar?$producto['precio']:''?>">
                <span class="error" id="errorPrecio"></span>

                <label for="stock">Stock:</label>
                <input type="number" name="stock" id='stock' style="margin-left: 20px;" value="<?php echo $editar?$producto['stock']:''?>">
                <span class="error" id="errorStock"></span>

                <label for="peso">Peso:</label>
                <input type="number" name="peso" id='peso' style="margin-left: 20px; "value="<?php echo $editar?$producto['peso']:''?>">
                <span class="error" id="errorPeso"></span>
            </div>
            <br>
            <div class="dimensiones" style="display: flex; gap: 5px;">
                <label for="alto">Alto:</label>
                <input type="number" name="alto" id='alto' style="margin-left: 20px;" value="<?php echo $editar?$producto['alto']:''?>">
                <span class="error" id="errorAlto"></span>
                

                <label for="ancho">ancho:</label>
                <input type="number" name="ancho" id='ancho' style="margin-left: 20px; " value="<?php echo $editar?$producto['ancho']:''?>">
                <span class="error" id="errorAncho"></span>

                <label for="largo">Largo:</label>
                <input type="number" name="largo" id='largo' style="margin-left: 20px; " value="<?php echo $editar?$producto['largo']:''?>">
                <span class="error" id="errorLargo"></span>
            </div>
            <br>
            <div class="cuimagen" style="display: flex;">
                <label for="nimagen" style="margin-right: 10px;">Nombre imagen:</label>
                <input type="text" name="nimagen" id='nimagen' style="width: 200px;" >
                <input type="file" name="imagen" id='imagen' style="width: 380px; margin-left: 30px; ">
                <span class="error" id="errorImagen"></span>
            </div>
            <br>
            <br>
            <div class="lista" style="display: flex; gap: 5px;">
            <label for="categoria">Categoría:</label>
            <select name="categoria" style="margin-right: 30px; margin-left: 20px;">
                <option value="0"></option>
                <?php while ($categoria = mysqli_fetch_array($categorias)) { ?>
                    <option value="<?= $categoria['id'] ?>" <?= ($editar && $producto['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                        <?= $categoria['nombre'] ?>
                    </option>
                <?php } ?>
            </select>
            <label for="marca">Marca:</label>
            <select name="marca" style="margin-left: 20px;">
                <option value="0"></option>
                <?php while ($marca = mysqli_fetch_array($marcas)) { ?>
                    <option value="<?= $marca['id'] ?>" <?= ($editar && $producto['marca_id'] == $marca['id']) ? 'selected' : ''; ?>>
                        <?= $marca['nombre'] ?>
                    </option>
                <?php } ?>
            </select>
            </div>
            <br>
            <br>
            <input class="re" type="submit" style="width: 100%; height: 30px; color: white; background-color: #007bff; font-size: 18px;">
        </form>
    </div>
</body>
</html>