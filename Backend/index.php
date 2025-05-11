<?php
include("auth.php"); // Protección de la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyNET</title>
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyNET</title>
    <link rel="stylesheet" href="Estilos/index.css">
        
</head>
<body>
    <br>
    <br>
    <div class="titulo">
    <div class="logo">
        <img src="logo.png" style="height: 300px; width: 300px;">
    </div>
    <br>
    <br>
    </div>
    <div class="formulario">
        <form id="formularioproductos" method="post">
        <ul class="paginas">
        <li><a href="Productos/formulario_productos.php">Formulario productos</a></li>
        <li><a href="Productos/list_productos.php">Listado productos</a></li>
        <li><a href="historial/list_historial.php">Historial ventas</a></li>
        <li><a href="soporte/list_soporte.php">Listado incidendcias / preguntas</a></li>
        </ul>
        </form>
    </div>
</body>
</html>