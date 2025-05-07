<?php
    $respuesta = $_REQUEST;
    require_once(__DIR__ . '/../../conectordb/conectordb.php');

    $consulta_categorias = "SELECT * FROM categorias";
    $categorias = mysqli_query($conexion, $consulta_categorias) or die("Error en la consulta");

    $consulta_marcas = "SELECT * FROM marcas";
    $marcas = mysqli_query($conexion, $consulta_marcas) or die("Error en la consulta");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Productos</title>
    <script src="Script/verificar.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 25%;
            margin: auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea, select {
            width: 96%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #0056b3;
        }
        select {
            height: 40px;
        }
        .error {
            color: red;
            font-size: 12px;
        }
        textarea {
            resize: both;
            max-width: 100%;
            box-sizing: border-box; 
            overflow: auto;
        }
        .lista select {
            width: 35%;
        }
        .ps input {
            width: 35%;
        }
        .peso input {
            width: 20%;
            
        }
        h2 {
            font-size: 300%;
        }
    </style>
</head>
<body>
    <h2>Formulario de Productos</h2>
    <form id="formularioproductos" method="post" action="alta_productos.php" enctype="multipart/form-data">
        <label for="referencia">Referencia:</label>
        <input type="text" name="referencia" id='referencia' required>
        <span class="error" id="errorReferencia"></span>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id='nombre' required>
        <span class="error" id="errorNombre"></span>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id='descripcion'></textarea>
        <span class="error" id="errorDescripcion"></span>
        <br>
        <br>
        <div class="ps" style="display: flex; gap: 5px;">
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" name="precio" id='precio' style="margin-right: 30px; margin-left: 20px;">
            <span class="error" id="errorPrecio"></span>

            <label for="stock">Stock:</label>
            <input type="number" name="stock" id='stock' style="margin-left: 20px; ">
            <span class="error" id="errorStock"></span>
        </div>
        <br>
        <div class="dimensiones" style="display: flex; gap: 5px;">
            <label for="stock">Alto:</label>
            <input type="number" name="alto" id='alto' style="margin-left: 20px; ">
            <span class="error" id="errorAlto"></span>

            <label for="ancho">ancho:</label>
            <input type="number" name="ancho" id='ancho' style="margin-left: 20px; ">
            <span class="error" id="errorAncho"></span>

            <label for="largo">Largo:</label>
            <input type="number" name="largo" id='largo' style="margin-left: 20px; ">
            <span class="error" id="errorLargo"></span>
        </div>
        <br>
        <div class="peso" style="display: flex;">
            <label for="peso">Peso:</label>
            <input type="number" name="peso" id='peso' style="margin-left: 20px; margin-right: 20px; ">
            <span class="error" id="errorPeso"></span>
        </div>
        <div class="imagen" style="display: flex;">
            <label for="imagen">Nombre imagen:</label>
            <input type="text" name="imagen" id='imagen'>
            <input type="file" name="imagen" id='imagen' style="width: 420px; margin-left: 30px; ">
            <span class="error" id="errorImagen"></span>
        </div>
        <br>
        <div class="lista" style="display: flex; gap: 5px;">
            <label for="categoria">Categoría:</label>
            <select name="categoria" style="margin-right: 30px; margin-left: 20px;">
                <option value="0"></option>
                <?php while ($categoria = mysqli_fetch_array($categorias)) { ?>
                <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                <?php } ?>
            </select>

            <label for="marca">Marca:</label>
            <select name="marca" style="margin-left: 20px;">
                <option value="0"></option>
                <?php while ($marca = mysqli_fetch_array($marcas)) { ?>
                <option value="<?= $marca['id'] ?>"><?= $marca['nombre'] ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="button" onclick="validarCampo()">Enviar</button>
    </form>
</body>
</html>
