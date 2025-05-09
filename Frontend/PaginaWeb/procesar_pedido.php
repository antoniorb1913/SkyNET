<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['cliente_id'])) {
    // Redirigir al login si no hay sesión
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto por tu usuario de MySQL
$password = "root"; // Cambia esto por tu contraseña de MySQL
$dbname = "SkyNET";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener ID del cliente de la sesión
    $cliente_id = $_SESSION['cliente_id'];
    
    // Recoger datos del formulario
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    
    // Información de dirección
    $direccion_id = null;
    
    // Verificar si es una dirección existente o una nueva
    if (isset($_POST['direccion_guardada_id']) && !empty($_POST['direccion_guardada_id'])) {
        // Usar dirección guardada
        $direccion_id = $_POST['direccion_guardada_id'];
    } else {
        // Crear nueva dirección
        $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
        $codigo_postal = isset($_POST['codigo_postal']) ? $_POST['codigo_postal'] : '';
        $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
        $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : '';
        $pais = isset($_POST['pais']) ? $_POST['pais'] : '';
        
        if (!empty($direccion) && !empty($codigo_postal) && !empty($ciudad) && !empty($provincia) && !empty($pais)) {
            // Insertar nueva dirección
            $sql_nueva_direccion = "INSERT INTO direcciones (cliente_id, direccion, codigo_postal, ciudad, provincia, pais, created_at, updated_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt_direccion = $conn->prepare($sql_nueva_direccion);
            $stmt_direccion->bind_param("isssss", $cliente_id, $direccion, $codigo_postal, $ciudad, $provincia, $pais);
            
            if ($stmt_direccion->execute()) {
                $direccion_id = $conn->insert_id;
                
                // Guardar dirección para futuras compras si se ha marcado la opción
                if (isset($_POST['guardar_direccion']) && $_POST['guardar_direccion'] == 'on') {
                    // Ya se ha guardado en la tabla direcciones, no es necesario hacer nada más
                }
            } else {
                echo "Error al guardar la dirección: " . $stmt_direccion->error;
                exit;
            }
        } else {
            echo "Faltan datos de dirección";
            exit;
        }
    }
    
    // Obtener o crear método de pago en la base de datos
    $metodo_pago_id = null;
    
    switch ($metodo_pago) {
        case 'tarjeta':
            // Si se paga con tarjeta, verificar que existe el método de pago para tarjetas
            $sql_check_metodo = "SELECT id FROM metodo_pago WHERE nombre = 'Tarjeta' LIMIT 1";
            $result_metodo = $conn->query($sql_check_metodo);
            
            if ($result_metodo->num_rows > 0) {
                $metodo_row = $result_metodo->fetch_assoc();
                $metodo_pago_id = $metodo_row['id'];
            } else {
                // Crear método de pago si no existe
                $sql_insert_metodo = "INSERT INTO metodo_pago (nombre, descripcion, created_at, updated_at) 
                                    VALUES ('Tarjeta', 'Pago con tarjeta de crédito/débito', NOW(), NOW())";
                if ($conn->query($sql_insert_metodo) === TRUE) {
                    $metodo_pago_id = $conn->insert_id;
                }
            }
            break;
            
        case 'paypal':
            // Verificar que existe el método de pago para PayPal
            $sql_check_metodo = "SELECT id FROM metodo_pago WHERE nombre = 'PayPal' LIMIT 1";
            $result_metodo = $conn->query($sql_check_metodo);
            
            if ($result_metodo->num_rows > 0) {
                $metodo_row = $result_metodo->fetch_assoc();
                $metodo_pago_id = $metodo_row['id'];
            } else {
                // Crear método de pago si no existe
                $sql_insert_metodo = "INSERT INTO metodo_pago (nombre, descripcion, created_at, updated_at) 
                                    VALUES ('PayPal', 'Pago a través de PayPal', NOW(), NOW())";
                if ($conn->query($sql_insert_metodo) === TRUE) {
                    $metodo_pago_id = $conn->insert_id;
                }
            }
            break;
            
        case 'transferencia':
            // Verificar que existe el método de pago para transferencia bancaria
            $sql_check_metodo = "SELECT id FROM metodo_pago WHERE nombre = 'Transferencia' LIMIT 1";
            $result_metodo = $conn->query($sql_check_metodo);
            
            if ($result_metodo->num_rows > 0) {
                $metodo_row = $result_metodo->fetch_assoc();
                $metodo_pago_id = $metodo_row['id'];
            } else {
                // Crear método de pago si no existe
                $sql_insert_metodo = "INSERT INTO metodo_pago (nombre, descripcion, created_at, updated_at) 
                                    VALUES ('Transferencia', 'Pago mediante transferencia bancaria', NOW(), NOW())";
                if ($conn->query($sql_insert_metodo) === TRUE) {
                    $metodo_pago_id = $conn->insert_id;
                }
            }
            break;
            
        default:
            echo "Método de pago no válido";
            exit;
    }
    
    // Obtener datos del carrito desde el formulario
    if (!isset($_POST['cart_items']) || empty($_POST['cart_items'])) {
        echo "Error: No se recibieron datos del carrito";
        exit;
    }
    
    $cart_items = json_decode($_POST['cart_items'], true);
    
    if (empty($cart_items)) {
        echo "El carrito está vacío";
        exit;
    }
    
    // Crear nuevo carrito
    $gasto_envio = 4.99; // Gasto de envío fijo
    
    $sql_carrito = "INSERT INTO carrito (cliente_id, metodoPago_id, direccion_id, gasto_envio, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, NOW(), NOW())";
    $stmt_carrito = $conn->prepare($sql_carrito);
    $stmt_carrito->bind_param("iiid", $cliente_id, $metodo_pago_id, $direccion_id, $gasto_envio);
    
    if ($stmt_carrito->execute()) {
        $carrito_id = $conn->insert_id;
        
        // Insertar líneas de carrito
        $importe_total = 0;
        $errores_linea = false;
        
        foreach ($cart_items as $item) {
            // CORRECCIÓN: Asegurarse de que tenemos un ID válido del producto
            // En el localStorage, el ID puede estar guardado como una propiedad diferente, comprobar varias opciones
            $producto_id = isset($item['id']) ? $item['id'] : (isset($item['producto_id']) ? $item['producto_id'] : null);
            
            // Si no tenemos un ID válido, intentar buscar el producto por nombre
            if ($producto_id === null && isset($item['name'])) {
                $sql_buscar_producto = "SELECT id FROM productos WHERE nombre = ? LIMIT 1";
                $stmt_buscar = $conn->prepare($sql_buscar_producto);
                $stmt_buscar->bind_param("s", $item['name']);
                $stmt_buscar->execute();
                $result_buscar = $stmt_buscar->get_result();
                
                if ($result_buscar->num_rows > 0) {
                    $producto_encontrado = $result_buscar->fetch_assoc();
                    $producto_id = $producto_encontrado['id'];
                } else {
                    echo "Producto no encontrado en la base de datos: " . $item['name'];
                    $errores_linea = true;
                    break;
                }
            }
            
            // Si aún no tenemos un ID válido, no podemos continuar
            if ($producto_id === null) {
                echo "Error: No se pudo identificar el producto en el carrito";
                $errores_linea = true;
                break;
            }
            
            $cantidad = $item['quantity'];
            $precio = $item['price'];
            $subtotal = $precio * $cantidad;
            $importe_total += $subtotal;
            
            // Verificar stock disponible
            $sql_check_stock = "SELECT stock FROM productos WHERE id = ?";
            $stmt_stock = $conn->prepare($sql_check_stock);
            $stmt_stock->bind_param("i", $producto_id);
            $stmt_stock->execute();
            $result_stock = $stmt_stock->get_result();
            
            if ($result_stock->num_rows > 0) {
                $producto = $result_stock->fetch_assoc();
                if ($producto['stock'] < $cantidad) {
                    echo "No hay suficiente stock para " . $item['name'] . ". Disponible: " . $producto['stock'];
                    $errores_linea = true;
                    break;
                }
            } else {
                echo "Producto no encontrado en la base de datos: ID " . $producto_id;
                $errores_linea = true;
                break;
            }
            
            // Insertar línea de carrito
            $sql_linea = "INSERT INTO linea_carrito (carrito_id, producto_id, cantidad, precio, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, NOW(), NOW())";
            $stmt_linea = $conn->prepare($sql_linea);
            $stmt_linea->bind_param("iiid", $carrito_id, $producto_id, $cantidad, $precio);
            
            if (!$stmt_linea->execute()) {
                echo "Error al insertar línea de carrito: " . $stmt_linea->error;
                $errores_linea = true;
                break;
            }
            
            // Actualizar stock
            $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update_stock);
            $stmt_update->bind_param("ii", $cantidad, $producto_id);
            $stmt_update->execute();
        }
        
        if (!$errores_linea) {
            // Obtener datos de impuestos
            $sql_impuestos = "SELECT * FROM impuestos WHERE activo = 1 LIMIT 1";
            $result_impuestos = $conn->query($sql_impuestos);
            
            if ($result_impuestos->num_rows > 0) {
                $impuesto = $result_impuestos->fetch_assoc();
                $impuesto_id = $impuesto['id'];
                $porcentaje_impuesto = $impuesto['porcentaje'];
            } else {
                // Si no hay impuestos definidos, usar IVA 21% por defecto
                $sql_crear_impuesto = "INSERT INTO impuestos (nombre, porcentaje, activo, created_at, updated_at) 
                                      VALUES ('IVA', 21, 1, NOW(), NOW())";
                $conn->query($sql_crear_impuesto);
                $impuesto_id = $conn->insert_id;
                $porcentaje_impuesto = 21;
            }
            
            // Calcular impuestos
            $importe_impuesto = ($importe_total * $porcentaje_impuesto) / 100;
            $importe_total_con_impuesto = $importe_total + $importe_impuesto + $gasto_envio;
            
            // Crear pedido
            $sql_pedido = "INSERT INTO pedidos (cliente_id, carrito_id, impuesto_id, importe_productos, importe_impuestos, gasto_envio, importe_total, estado, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW(), NOW())";
            $stmt_pedido = $conn->prepare($sql_pedido);
            $stmt_pedido->bind_param("iiidddd", $cliente_id, $carrito_id, $impuesto_id, $importe_total, $importe_impuesto, $gasto_envio, $importe_total_con_impuesto);
            
            if ($stmt_pedido->execute()) {
                $pedido_id = $conn->insert_id;
                
                // Generar un número de pedido único
                $numero_pedido = date('Ymd') . sprintf('%06d', $pedido_id);
                
                // Actualizar el número de pedido
                $sql_update_pedido = "UPDATE pedidos SET numero_pedido = ? WHERE id = ?";
                $stmt_update_pedido = $conn->prepare($sql_update_pedido);
                $stmt_update_pedido->bind_param("si", $numero_pedido, $pedido_id);
                $stmt_update_pedido->execute();
                
                // Limpiar el carrito de la sesión
                unset($_SESSION['cart']);
                
                // Redirigir a la página de confirmación de pedido
                header('Location: confirmacion_pedido.php?id=' . $pedido_id);
                exit;
            } else {
                echo "Error al crear el pedido: " . $stmt_pedido->error;
            }
        } else {
            // En caso de error, eliminar el carrito creado
            $sql_delete_carrito = "DELETE FROM carrito WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete_carrito);
            $stmt_delete->bind_param("i", $carrito_id);
            $stmt_delete->execute();
            
            echo "<p>Ha ocurrido un error al procesar tu pedido. Por favor, inténtalo de nuevo.</p>";
            echo "<a href='checkout.php'>Volver al checkout</a>";
        }
    } else {
        echo "Error al crear el carrito: " . $stmt_carrito->error;
    }
} else {
    // Si se accede directamente a esta página sin POST, redirigir al checkout
    header('Location: checkout.php');
    exit;
}

// Cerrar conexión
$conn->close();
?>