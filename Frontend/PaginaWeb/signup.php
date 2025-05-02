<?php
require_once "config.php";
session_start();

// Definir variables e inicializar con valores vacíos
$nombre = $apellidos = $email = $contrasena = $confirmar_contrasena = $nick = "";
$nombre_err = $apellidos_err = $email_err = $contrasena_err = $confirmar_contrasena_err = $nick_err = "";
$exito = false;

// Procesar datos del formulario cuando se envía
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validar nombre
    if(empty(trim($_POST["nombre"]))){
        $nombre_err = "Por favor ingrese su nombre.";
    } else{
        $nombre = trim($_POST["nombre"]);
    }
    
    // Validar apellidos
    if(empty(trim($_POST["apellidos"]))){
        $apellidos_err = "Por favor ingrese sus apellidos.";
    } else{
        $apellidos = trim($_POST["apellidos"]);
    }
    
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese un email.";
    } else{
        // Preparar una consulta
        $sql = "SELECT id FROM clientes WHERE email = ?";
        
        if($stmt = mysqli_prepare($conexion, $sql)){
            // Vincular variables a la consulta preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Establecer parámetros
            $param_email = trim($_POST["email"]);
            
            // Ejecutar la consulta preparada
            if(mysqli_stmt_execute($stmt)){
                // Almacenar resultado
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Este email ya está registrado.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "¡Ups! Algo salió mal. Por favor inténtelo más tarde.";
            }

            // Cerrar declaración
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validar contraseña
    if(empty(trim($_POST["contrasena"]))){
        $contrasena_err = "Por favor ingrese una contraseña.";     
    } elseif(strlen(trim($_POST["contrasena"])) < 6){
        $contrasena_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $contrasena = trim($_POST["contrasena"]);
    }
    
    // Validar confirmación de contraseña
    if(empty(trim($_POST["confirmar_contrasena"]))){
        $confirmar_contrasena_err = "Por favor confirme la contraseña.";     
    } else{
        $confirmar_contrasena = trim($_POST["confirmar_contrasena"]);
        if(empty($contrasena_err) && ($contrasena != $confirmar_contrasena)){
            $confirmar_contrasena_err = "Las contraseñas no coinciden.";
        }
    }
    
    // Validar nickname
    if(empty(trim($_POST["nick"]))){
        $nick_err = "Por favor ingrese un nickname.";
    } else{
        // Preparar una consulta
        $sql = "SELECT id FROM clientes WHERE nick = ?";
        
        if($stmt = mysqli_prepare($conexion, $sql)){
            // Vincular variables a la consulta preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_nick);
            
            // Establecer parámetros
            $param_nick = trim($_POST["nick"]);
            
            // Ejecutar la consulta preparada
            if(mysqli_stmt_execute($stmt)){
                // Almacenar resultado
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $nick_err = "Este nickname ya está en uso.";
                } else{
                    $nick = trim($_POST["nick"]);
                }
            } else{
                echo "¡Ups! Algo salió mal. Por favor inténtelo más tarde.";
            }

            // Cerrar declaración
            mysqli_stmt_close($stmt);
        }
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if(empty($nombre_err) && empty($apellidos_err) && empty($email_err) && empty($contrasena_err) && empty($confirmar_contrasena_err) && empty($nick_err)){
        
        // Preparar una consulta de inserción
        $sql = "INSERT INTO clientes (nombre, apellidos, email, contrasena, nick, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
         
        if($stmt = mysqli_prepare($conexion, $sql)){
            // Vincular variables a la consulta preparada como parámetros
            mysqli_stmt_bind_param($stmt, "sssss", $param_nombre, $param_apellidos, $param_email, $param_contrasena, $param_nick);
            
            // Establecer parámetros
            $param_nombre = $nombre;
            $param_apellidos = $apellidos;
            $param_email = $email;
            $param_contrasena = password_hash($contrasena, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_nick = $nick;
            
            // Ejecutar la consulta preparada
            if(mysqli_stmt_execute($stmt)){
                $exito = true;
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
    <title>Registro - SkyNet</title>
    <link rel="stylesheet" href="signup.css">
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
    
    <div class="register-container">
        <h1>Registro de usuario</h1>
        
        <?php if($exito): ?>
            <div class="alert alert-success">
                Cuenta creada correctamente. <a href="login.php">Inicia sesión aquí</a>.
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>">
                <span class="error"><?php echo $nombre_err; ?></span>
            </div>
            <div class="form-group">
                <label>Apellidos</label>
                <input type="text" name="apellidos" value="<?php echo $apellidos; ?>">
                <span class="error"><?php echo $apellidos_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Nickname</label>
                <input type="text" name="nick" value="<?php echo $nick; ?>">
                <span class="error"><?php echo $nick_err; ?></span>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena">
                <span class="error"><?php echo $contrasena_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirmar_contrasena">
                <span class="error"><?php echo $confirmar_contrasena_err; ?></span>
            </div>
            <button type="submit" class="submit-btn">Registrarse</button>
            <div class="login-link">
                ¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a>
            </div>
        </form>
    </div>
</body>
</html>