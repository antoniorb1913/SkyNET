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

    $consulta_imagenes = "INSERT INTO  imagenes_productos(nombre)
                VALUE  ('".$respuesta['id']."')";


    if(mysqli_query($conexion, $consulta)) {
        header ('Location: list_productos.php');
    } else {
        echo "Mal";
    }