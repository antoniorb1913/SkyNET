<?php
require_once('conectordb.php');
$respuesta = $_POST;

// Verificar si es una edición (tiene idProducto)
if(isset($respuesta['idProducto']) && !empty($respuesta['idProducto'])) {
    // ES UNA EDICIÓN - HACER UPDATE
    $consulta = "UPDATE productos SET 
                referencia = '".$respuesta['referencia']."',
                nombre = '".$respuesta['nombre']."',
                descripcion = '".$respuesta['descripcion']."',
                precio = ".$respuesta['precio'].",
                stock = ".$respuesta['stock'].",
                alto = '".$respuesta['alto']."',
                ancho = ".$respuesta['ancho'].",
                largo = ".$respuesta['largo'].",
                peso = ".$respuesta['peso'].",
                categoria_id = '".$respuesta['categoria']."',
                marca_id = '".$respuesta['marca']."'
                WHERE id = ".$respuesta['idProducto'];
} else {
    // ES UN NUEVO PRODUCTO - HACER INSERT (tu código original)
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
}

// Procesar imagen solo si se subió un archivo
if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../../Frontend/imagenes_productos/";
    $nombre_imagen_personalizado = $respuesta['nimagen'];
    $imageFileType = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $nombre_imagen_personalizado . "." . $imageFileType;
    
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
        echo "La imagen se ha subido con el nombre: " . htmlspecialchars($nombre_imagen_personalizado . "." . $imageFileType);
    } else {
        echo "Lo sentimos, hubo un error al cargar tu archivo.";
    }
}

// Ejecutar la consulta
if(mysqli_query($conexion, $consulta)) {
    header('Location: list_productos.php');
    exit;
} else {
    echo "Error: " . mysqli_error($conexion);
}
?>