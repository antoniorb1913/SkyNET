<?php
session_start();
require_once(__DIR__ . '/../conectordb/conectordb.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_admin = trim($_POST["nombre_admin"]);
    $password_ingresada = trim($_POST["password"]);

    $stmt = $conexion->prepare("SELECT id, contrasena FROM admin WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_admin);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password_ingresada, $usuario["contrasena"])) {
            $_SESSION["admin_id"] = $usuario["id"];
            header("Location: index.php"); // Redirigir al panel de admin
            exit;
        } else {
            $_SESSION["error_login"] = "Error: El usuario y la contraseña es incorrecta.";
        }
    } else {
        $_SESSION["error_login"] = "Error: Usuario no encontrado.";
    }

    header("Location: login.php"); // Volver al formulario con el error
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SkyNET</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 80vh;
        margin: 0;
    }

    .logo {
        margin-bottom: 20px; /* Separación entre el logo y el login */
        text-align: center;
    }

    .logo img {
        height: 210px; /* Ajusta el tamaño del logo */
        width: 250px;
    }

    .login-container {
        background: rgb(255, 255, 255);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        text-align: center;
        color: rgb(0, 0, 0);
        width: 300px;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        text-align: left;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .input-group input {
        width: 90%;
        padding: 10px;
        border: 1px solid black;
        border-radius: 5px;
        color: rgb(0, 0, 0);
    }

    .btn {
        width: 100%;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .error {
        color: #ff4d4d;
        margin-bottom: 10px;
    }
    </style>
</head>
<body>
    <div class="logo">
        <img src="logo.png" alt="SkyNET Logo">
    </div>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($mensaje_error)) echo "<p class='error'>$mensaje_error</p>"; ?>
        <form method="POST" action="login.php">
    <div class="input-group">
        <label for="nombre_admin">Usuario:</label>
        <input type="text" name="nombre_admin" placeholder="Nombre de administrador" required>
    </div>
    <div class="input-group">
    <label for="password">Contraseña:</label>
    <input type="password" name="password" placeholder="••••••••" required>
    <?php 
    if (isset($_SESSION["error_login"])) { 
        echo "<p class='error'>" . $_SESSION["error_login"] . "</p>"; 
        unset($_SESSION["error_login"]); // Elimina el error después de mostrarlo
    }
    ?>
</div>


    <button type="submit" class="btn">Iniciar sesión</button>
</form>
    </div>
</body>
</html>
