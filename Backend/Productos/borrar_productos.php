<?php 

$respuesta = $_GET;
require_once(__DIR__ . '/../../conectordb/conectordb.php');


$idProductos = intval($_GET['id']); // Asegurar que sea un número

$borrar_productos = "DELETE FROM productos WHERE id=$idProductos";
mysqli_query($conexion, $borrar_productos);

if(mysqli_query($conexion, $borrar_productos)){
    echo "El libro se ha borrado correctamente";
    header("Location: list_productos.php");
} else {
    echo "Error al borrar el producto " . mysqli_error($conexion);
}