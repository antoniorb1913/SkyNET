<?php
include("../auth.php"); // Verifica si el usuario ha iniciado sesión

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
    <script src="Scripts/validar_formulario.js"></script>
    <link rel="stylesheet" href="../Estilos/formulario.css">
</head>
<body>
    <div class="logo">
        <a href="/Backend">
        <img src="../logo.png" style="height: 150px; width: 150px;">
        </a>
    </div>
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
            <input type="number" name="stock" id='stock' style="margin-left: 20px; margin-right: 20px; ">
            <span class="error" id="errorStock"></span>

            <label for="peso">Peso:</label>
            <input type="number" name="peso" id='peso' style="margin-left: 20px; margin-right: 20px; ">
            <span class="error" id="errorPeso"></span>
        </div>
        <br>
        <div class="dimensiones" style="display: flex; gap: 5px;">
            <label for="alto">Alto:</label>
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
        <div class="cuimagen" style="display: flex;">
            <label for="nimagen" style="margin-right: 10px;">Nombre imagen:</label>
            <input type="text" name="nimagen" id='nimagen' style="width: 200px;" >
            <input type="file" name="imagen" id='imagen' style="width: 380px; margin-left: 30px; ">
            <span class="error" id="errorImagen"></span>
        </div>
        <br>
        <div class="lista" style="display: flex; gap: 5px;">
            <label for="categoria">Categoría:</label>
            <select name="categoria" id='categoria' style="margin-right: 30px; margin-left: 20px;">
                <option value="0"></option>
                <?php while ($categoria = mysqli_fetch_array($categorias)) { ?>
                <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                <?php } ?>
            </select>
            <span class="error" id="errorCategoria"></span>
            <label for="marca">Marca:</label>
            <select name="marca" id='marca' style="margin-left: 20px;">
                <option value="0"></option>
                <?php while ($marca = mysqli_fetch_array($marcas)) { ?>
                <option value="<?= $marca['id'] ?>"><?= $marca['nombre'] ?></option>
                <?php } ?>
            </select>
            <span class="error" id="errorMarca"></span>
        </div>

        <button type="button" onclick="validarCampo()">Enviar</button>
    </form>
</body>
</html>
