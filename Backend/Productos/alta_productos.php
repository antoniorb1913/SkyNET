<?php
    require_once('conectordb.php');
    $respuesta = $_POST;

    $consulta = "INSERT INTO productos (referencia,nombre,descripcion,precio,stock,alto,ancho,largo,
                            peso, categoria_id, marca_id)
                values ('".$respuesta['referencia']."',
                '".$respuesta['nombre']."',
                '".$respuesta['descripcion']."',
                ".$respuesta['precio'].",
                ".$respuesta['stock'].",
                '".$respuesta['alto']."',
                ".$respuesta['ancho'].",
                ".$respuesta['largo'].",
                ".$respuesta['peso'].",
                '".$respuesta['categoria']."',
                '".$respuesta['marca']."')";


    $target_dir = "../../Frontend/imagenes_productos/";

    // Obtener el nombre personalizado ingresado en el formulario
    $nombre_imagen_personalizado = $respuesta['imagen']; // El campo donde introduces el nombre de la imagen
    $imageFileType = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
    
    // Construir la ruta con el nuevo nombre
    $target_file = $target_dir . $nombre_imagen_personalizado . "." . $imageFileType;
    
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
        echo "La imagen se ha subido con el nombre: " . htmlspecialchars($nombre_imagen_personalizado . "." . $imageFileType);
    } else {
        echo "Lo sentimos, hubo un error al cargar tu archivo.";
    }
    if(mysqli_query($conexion, $consulta)) {
        header ('Location: list_productos.php');
    } else {
        echo "Mal";
    }
    ?>