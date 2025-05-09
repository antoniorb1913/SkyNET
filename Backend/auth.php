<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php"); // Redirige al login si no hay sesión activa
    exit;
}
?>

