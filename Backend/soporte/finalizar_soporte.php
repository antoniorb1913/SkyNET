<?php
// finalizar_soporte.php
session_start();
require_once(__DIR__ . '/../../conectordb/conectordb.php');

// 1. Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 3. Verificar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}

$id_soporte = intval($_GET['id']);

// Por esta:
$sql = "UPDATE soporte SET estado = 'resuelto' WHERE id = ? AND estado = 'en proceso'";
// 5. Preparar consulta con manejo de errores
if ($stmt = $conexion->prepare($sql)) {
    $stmt->bind_param("i", $id_soporte);
    
    if ($stmt->execute()) {
        // Verificar si realmente se actualizó
        if ($stmt->affected_rows > 0) {
            header("Location: list_soporte.php?success=1");
        } else {
            // Posibles razones:
            // - El ID no existe
            // - Ya estaba finalizado
            header("Location: list_soporte.php?error=2");
        }
        exit();
    } else {
        // Registrar error detallado
        error_log("Error al ejecutar: " . $stmt->error);
        header("Location: list_soporte.php?error=3");
        exit();
    }
} else {
    // Error en la preparación
    error_log("Error en preparación: " . $conexion->error);
    header("Location: list_soporte.php?error=4");
    exit();
}
?>