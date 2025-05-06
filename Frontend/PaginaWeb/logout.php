<?php
// Inicializar la sesión
session_start();
 
// Destruir todas las variables de sesión
$_SESSION = array();
 
// Destruir la sesión
session_destroy();
 
// Redirigir al login
header("location: login.php");
exit;
?>