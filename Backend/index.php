<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="estilos funeraria.css" rel="stylesheet">
    <title>SkyNET</title>
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyNET</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .fondo {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }

        .titulo h1 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .formulario {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        .paginas {
            list-style-type: none;
            padding: 0;
        }

        .paginas li {
            margin: 10px 0;
        }

        .paginas a {
            display: block;
            padding: 10px;
            font-size: 18px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .paginas a:hover {
            background-color: #0056b3;
        }
    </style>
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
        </ul>
        </form>
    </div>
</body>
</html>