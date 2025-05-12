<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Incluir archivo de configuración
require_once "config.php";

// Iniciar transacción para garantizar consistencia
$conn->begin_transaction();

try {
    // Procesar datos del formulario
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Método no permitido");
    }

    // Obtener ID del cliente de la sesión
    $cliente_id = $_SESSION['id'];

    // Recoger datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? trim($_POST['metodo_pago']) : '';

    // Validar datos personales
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($metodo_pago)) {
        throw new Exception("Faltan datos obligatorios");
    }

    // Información de dirección
    $direccion_id = null;

    // Verificar si es una dirección existente o una nueva
    if (isset($_POST['direccion_guardada_id']) && !empty($_POST['direccion_guardada_id'])) {
        $direccion_id = (int)$_POST['direccion_guardada_id'];

        $sql_check_direccion = "SELECT id FROM direcciones WHERE id = ? AND cliente_id = ?";
        $stmt_check_direccion = $conn->prepare($sql_check_direccion);
        $stmt_check_direccion->bind_param("ii", $direccion_id, $cliente_id);
        $stmt_check_direccion->execute();
        $result_check_direccion = $stmt_check_direccion->get_result();

        if ($result_check_direccion->num_rows == 0) {
            throw new Exception("Dirección seleccionada no válida");
        }
    } else {
        $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';

        if (empty($direccion)) {
            throw new Exception("Faltan datos de dirección");
        }

        $sql_nueva_direccion = "INSERT INTO direcciones (cliente_id, direccion) VALUES (?, ?)";
        $stmt_direccion = $conn->prepare($sql_nueva_direccion);
        $stmt_direccion->bind_param("is", $cliente_id, $direccion);

        if (!$stmt_direccion->execute()) {
            throw new Exception("Error al guardar la dirección: " . $stmt_direccion->error);
        }

        $direccion_id = $conn->insert_id;
    }

    // Obtener o crear método de pago
    $metodo_pago_id = null;
    $metodo_pago_map = ['tarjeta' => 'Tarjeta', 'paypal' => 'PayPal', 'transferencia' => 'Transferencia'];

    if (!array_key_exists($metodo_pago, $metodo_pago_map)) {
        throw new Exception("Método de pago no válido");
    }

    $tipo_pago = $metodo_pago_map[$metodo_pago];

    $sql_check_metodo = "SELECT id FROM metodo_pago WHERE tipo_pago = ? LIMIT 1";
    $stmt_check_metodo = $conn->prepare($sql_check_metodo);
    $stmt_check_metodo->bind_param("s", $tipo_pago);
    $stmt_check_metodo->execute();
    $result_metodo = $stmt_check_metodo->get_result();

    if ($result_metodo->num_rows > 0) {
        $metodo_row = $result_metodo->fetch_assoc();
        $metodo_pago_id = $metodo_row['id'];
    } else {
        $sql_insert_metodo = "INSERT INTO metodo_pago (tipo_pago, detalles_pago) VALUES (?, ?)";
        $detalles_pago = "Método de pago: " . $tipo_pago;
        $stmt_insert_metodo = $conn->prepare($sql_insert_metodo);
        $stmt_insert_metodo->bind_param("ss", $tipo_pago, $detalles_pago);

        if (!$stmt_insert_metodo->execute()) {
            throw new Exception("Error al crear método de pago: " . $stmt_insert_metodo->error);
        }

        $metodo_pago_id = $conn->insert_id;
    }

    // Obtener datos del carrito
    if (!isset($_POST['cart_items']) || empty($_POST['cart_items'])) {
        throw new Exception("No se recibieron datos del carrito");
    }

    $cart_items = json_decode($_POST['cart_items'], true);

    if (empty($cart_items)) {
        throw new Exception("El carrito está vacío");
    }

    // Crear nuevo carrito
    $gasto_envio = 4.99;

    $sql_carrito = "INSERT INTO carrito (cliente_id, metodoPago_id, direccion_id, gasto_envio) VALUES (?, ?, ?, ?)";
    $stmt_carrito = $conn->prepare($sql_carrito);
    $stmt_carrito->bind_param("iiid", $cliente_id, $metodo_pago_id, $direccion_id, $gasto_envio);

    if (!$stmt_carrito->execute()) {
        throw new Exception("Error al crear el carrito: " . $stmt_carrito->error);
    }

    $carrito_id = $conn->insert_id;

    // Insertar líneas de carrito y actualizar stock
    $importe_total = 0;

    foreach ($cart_items as $item) {
        $producto_id = isset($item['id']) ? (int)$item['id'] : null;
        $cantidad = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $precio = isset($item['price']) ? (float)$item['price'] : 0;

        if (!$producto_id || $cantidad <= 0 || $precio <= 0) {
            throw new Exception("Datos del producto inválidos");
        }

        // Verificar stock y precio
        $sql_check_producto = "SELECT stock, precio FROM productos WHERE id = ? AND deleted_at IS NULL";
        $stmt_check_producto = $conn->prepare($sql_check_producto);
        $stmt_check_producto->bind_param("i", $producto_id);
        $stmt_check_producto->execute();
        $result_producto = $stmt_check_producto->get_result();

        if ($result_producto->num_rows == 0) {
            throw new Exception("Producto no encontrado: ID " . $producto_id);
        }

        $producto = $result_producto->fetch_assoc();

        if ($producto['stock'] < $cantidad) {
            throw new Exception("No hay suficiente stock para el producto ID " . $producto_id . ". Disponible: " . $producto['stock']);
        }

        if (abs($producto['precio'] - $precio) > 0.01) {
            throw new Exception("El precio del producto ID " . $producto_id . " no coincide con la base de datos");
        }

        $subtotal = $precio * $cantidad;
        $importe_total += $subtotal;

        // Insertar línea de carrito
        $sql_linea = "INSERT INTO linea_carrito (carrito_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt_linea = $conn->prepare($sql_linea);
        $stmt_linea->bind_param("iiid", $carrito_id, $producto_id, $cantidad, $precio);

        if (!$stmt_linea->execute()) {
            throw new Exception("Error al insertar línea de carrito: " . $stmt_linea->error);
        }

        // Actualizar stock del producto
        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ? AND deleted_at IS NULL";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ii", $cantidad, $producto_id);

        if (!$stmt_update_stock->execute()) {
            throw new Exception("Error al actualizar el stock del producto ID " . $producto_id . ": " . $stmt_update_stock->error);
        }

        // Opcional: Verificar que el stock se haya actualizado
        $stmt_check_stock = $conn->prepare("SELECT stock FROM productos WHERE id = ? AND deleted_at IS NULL");
        $stmt_check_stock->bind_param("i", $producto_id);
        $stmt_check_stock->execute();
        $result_stock = $stmt_check_stock->get_result()->fetch_assoc();
        if ($result_stock['stock'] < 0) {
            throw new Exception("Stock negativo detectado para el producto ID " . $producto_id . ". Esto no debería ocurrir.");
        }
    }

    // Obtener datos de impuestos
    $sql_impuestos = "SELECT id, porcentaje FROM impuestos WHERE nombre = 'IVA' LIMIT 1";
    $result_impuestos = $conn->query($sql_impuestos);

    if ($result_impuestos->num_rows > 0) {
        $impuesto = $result_impuestos->fetch_assoc();
        $impuesto_id = $impuesto['id'];
        $porcentaje_impuesto = $impuesto['porcentaje'];
    } else {
        $sql_crear_impuesto = "INSERT INTO impuestos (nombre, porcentaje) VALUES ('IVA', 21)";
        if (!$conn->query($sql_crear_impuesto)) {
            throw new Exception("Error al crear impuesto: " . $conn->error);
        }
        $impuesto_id = $conn->insert_id;
        $porcentaje_impuesto = 21;
    }

    // Calcular impuestos
    $importe_impuesto = ($importe_total * $porcentaje_impuesto) / 100;
    $importe_total_con_impuesto = $importe_total + $importe_impuesto + $gasto_envio;

    // Calcular fecha prevista de entrega
    $fecha_prevista = date('Y-m-d H:i:s', strtotime('+5 weekdays'));

    // Crear venta
    $sql_venta = "INSERT INTO ventas (importe, carrito_id, impuesto_id, importe_total, fecha_previstaEntrega) VALUES (?, ?, ?, ?, ?)";
    $stmt_venta = $conn->prepare($sql_venta);
    $stmt_venta->bind_param("diids", $importe_total, $carrito_id, $impuesto_id, $importe_total_con_impuesto, $fecha_prevista);

    if (!$stmt_venta->execute()) {
        throw new Exception("Error al crear la venta: " . $stmt_venta->error);
    }

    $venta_id = $conn->insert_id;

    // Generar número de venta único
    $numero_venta = date('Ymd') . sprintf('%06d', $venta_id);

    // Limpiar el carrito de la sesión
    unset($_SESSION['cart']);

    // Confirmar transacción
    $conn->commit();

    // Redirigir a la página de confirmación
    header('Location: confirmacion_venta.php?id=' . $venta_id . '&numero=' . $numero_venta);
    exit;

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();

    // Mostrar error al usuario con más detalles
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='checkout.php'>Volver al checkout</a>";
    exit;
}

// Cerrar conexión
$conn->close();
?>