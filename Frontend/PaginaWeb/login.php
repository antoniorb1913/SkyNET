<?php
// Inicializar la sesión
session_start();
 
// Verificar si el usuario ya ha iniciado sesión
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Incluir archivo de configuración
require_once "config.php";
 
// Definir variables e inicializar con valores vacíos
$email = $contrasena = "";
$email_err = $contrasena_err = $login_err = "";
 
// Procesar datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Verificar si el email está vacío
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese su email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Verificar si la contraseña está vacía
    if(empty(trim($_POST["contrasena"]))){
        $contrasena_err = "Por favor ingrese su contraseña.";
    } else{
        $contrasena = trim($_POST["contrasena"]);
    }
    
    // Validar credenciales
    if(empty($email_err) && empty($contrasena_err)){
        // Preparar una consulta select
        $sql = "SELECT id, nombre, email, contrasena FROM clientes WHERE email = ?";
        
        if($stmt = mysqli_prepare($conexion, $sql)){
            // Vincular variables a la consulta preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Establecer parámetros
            $param_email = $email;
            
            // Ejecutar la consulta preparada
            if(mysqli_stmt_execute($stmt)){
                // Almacenar resultado
                mysqli_stmt_store_result($stmt);
                
                // Verificar si existe el email, si sí verificar la contraseña
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Vincular variables de resultado
                    mysqli_stmt_bind_result($stmt, $id, $nombre, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($contrasena, $hashed_password)){
                            // La contraseña es correcta, iniciar una nueva sesión
                            session_start();
                            
                            // Almacenar datos en variables de sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nombre"] = $nombre;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirigir al usuario a la página de inicio
                            header("location: index.php");
                        } else{
                            // La contraseña no es válida
                            $login_err = "Email o contraseña incorrectos.";
                        }
                    }
                } else{
                    // El email no existe
                    $login_err = "Email o contraseña incorrectos.";
                }
            } else{
                echo "¡Ups! Algo salió mal. Por favor inténtelo más tarde.";
            }

            // Cerrar declaración
            mysqli_stmt_close($stmt);
        }
    }
    
    // Cerrar conexión
    mysqli_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkyNet</title>
    <link rel="stylesheet" href="../Estilos/login.css" />
</head>
<body>
    <!-- Barra superior -->
    <div class="top-bar">
        <div class="top-bar-left">
            <a href="#"><i class="fas fa-phone-alt"></i> 900 123 456</a>
            <a href="#"><i class="fas fa-map-marker-alt"></i> Tiendas</a>
            <a href="#"><i class="fas fa-truck"></i> Estado del pedido</a>
        </div>
        <div class="top-bar-right">
            <a href="#"><i class="fas fa-headset"></i> Soporte</a>
            <a href="#"><i class="fas fa-info-circle"></i> Ayuda</a>
            <a href="#"><i class="fas fa-percent"></i> Ofertas</a>
        </div>
    </div>
    
    <!-- Header principal -->
    <header>
        <a href="index.php" class="logo">
            <i class="fas fa-desktop logo-icon"></i>
            SkyNet
        </a>
    </header>
    
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        
        <?php if(!empty($login_err)): ?>
            <div class="alert alert-danger"><?php echo $login_err; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena">
                <span class="error"><?php echo $contrasena_err; ?></span>
            </div>
            <button type="submit" class="submit-btn">Iniciar Sesión</button>
            <div class="register-link">
                ¿No tienes cuenta? <a href="signup.php">Regístrate ahora</a>
            </div>
        </form>
    </div>
</body>
</html>