<?php 

$respuesta = $_GET;
require_once(__DIR__ . '/../../conectordb/conectordb.php');


$idSoporte = intval($_GET['id']); // Asegurar que sea un número

$borrar_soportes = "DELETE FROM soporte WHERE id=$idSoporte";
mysqli_query($conexion, $borrar_soportes);

if(mysqli_query($conexion, $borrar_soportes)){
    echo "El libro se ha borrado correctamente";
    header("Location: list_soporte.php");
} else {
    echo "Error al borrar el producto " . mysqli_error($conexion);
}