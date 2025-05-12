<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "SkyNET";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida']);
    exit;
}

// Obtener datos del carrito desde el cuerpo de la solicitud
$input = file_get_contents('php://input');
$cart_items = json_decode($input, true);

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit;
}

try {
    foreach ($cart_items as $item) {
        $producto_id = isset($item['id']) ? (int)$item['id'] : null;
        $cantidad = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $precio = isset($item['price']) ? (float)$item['price'] : 0;
        
        if (!$producto_id || $cantidad <= 0 || $precio <= 0) {
            throw new Exception("Datos del producto inválidos");
        }
        
        // Verificar producto en la base de datos
        $sql = "SELECT stock, precio FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Producto no encontrado: ID " . $producto_id);
        }
        
        $producto = $result->fetch_assoc();
        
        if ($producto['stock'] < $cantidad) {
            throw new Exception("No hay suficiente stock para el producto ID " . $producto_id . ". Disponible: " . $producto['stock']);
        }
        
        if (abs($producto['precio'] - $precio) > 0.01) {
            throw new Exception("El precio del producto ID " . $producto_id . " no coincide con la base de datos");
        }
    }
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>