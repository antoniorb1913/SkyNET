<?php
session_start();
require_once "config.php";

$mensaje_enviado = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener y sanitizar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    
    // Inicializar cliente_id como NULL
    $cliente_id = null;

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no tiene un formato válido";
    } else {
        try {
            // Verificar si el email existe en la tabla de clientes
            $stmt_check = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $cliente_id = $row['id'];
            }
            
            $stmt_check->close();
            
            // Preparar la consulta para insertar en soporte
            $stmt = $conn->prepare("INSERT INTO soporte 
                                  (nombre, email, asunto, mensaje, cliente_id, fecha_entrada, estado) 
                                  VALUES (?, ?, ?, ?, ?, NOW(), 'pendiente')");
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $conn->error);
            }
            
            $stmt->bind_param("ssssi", $nombre, $email, $asunto, $mensaje, $cliente_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: agradecimiento.php");
                exit();
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
            
        } catch (Exception $e) {
            $error = "Error al procesar tu solicitud: " . $e->getMessage();
            error_log($e->getMessage());
        }
    }
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
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="asunto">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" required
                           value="<?php echo isset($_POST['asunto']) ? htmlspecialchars($_POST['asunto']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required><?php 
                        echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; 
                    ?></textarea>
                </div>
                <input type="submit" class="btn-submit" value="Enviar">
            </form>
        <?php endif; ?>
    </main>
</body>
</html>