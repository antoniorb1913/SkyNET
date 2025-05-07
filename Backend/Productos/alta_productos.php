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


    if(mysqli_query($conexion, $consulta)) {
        header ('Location: list_productos.php');
    } else {
        echo "Mal";
    }

    $target_dir = "../../Frontend/imagenes_productos/";
    $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["imagen"]["name"])). " se ha subido.";
      } else {
        echo "Lo sentimos, hubo un error al cargar tu archivo.";
      }
    
    ?>
    ?>