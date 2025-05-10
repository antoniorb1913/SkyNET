<?php
session_start();
require_once "config.php";

$mensaje_enviado = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si el usuario está logueado y obtener su ID
    if (!isset($_SESSION["id"])) {
        die("Error: Debes iniciar sesión para enviar una consulta.");
    }

    $cliente_id = $_SESSION["id"]; // ID del cliente desde la sesión
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $asunto = $_POST['asunto'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';

    // Preparar la consulta con el cliente_id
    $stmt = $conn->prepare("INSERT INTO soporte (nombre, email, asunto, mensaje, cliente_id, fecha_entrada) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("ssssi", $nombre, $email, $asunto, $mensaje, $cliente_id);

    if ($stmt->execute()) {
        $mensaje_enviado = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto</title>
    <link rel="stylesheet" href="skynet.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #3b81ff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-submit {
            background-color: #3b81ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #2a6de0;
        }
        .success-msg {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <main class="form-container">
        <h2>Formulario de Soporte</h2>

        <?php if ($mensaje_enviado): ?>
            <p class="success-msg">Tu mensaje ha sido enviado. Nos pondremos en contacto contigo pronto.</p>
        <?php endif; ?>

        <form method="POST" action="agradecimiento.php">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="asunto">Asunto:</label>
                <input type="text" id="asunto" name="asunto" required>
            </div>

            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
            </div>

            <input type="submit" class="btn-submit" value="Enviar">
        </form>
    </main>
</body>
</html>
