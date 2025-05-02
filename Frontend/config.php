<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "SkyNET";

$conn = new mysqli($host, $user, $password, $database);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>