<?php
header('Content-Type: application/json');
require_once "config.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    if ($producto_id <= 0 || $cantidad <= 0) {
        throw new Exception("Datos inválidos");
    }

    // Verificar stock en la base de datos
    $sql = "SELECT stock FROM productos WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Producto no encontrado");
    }

    $producto = $result->fetch_assoc();
    $stock_disponible = $producto['stock'];

    // Considerar productos ya en el carrito
    $cart_items = [];
    if (isset($_POST['cart_items'])) {
        $cart_items = json_decode($_POST['cart_items'], true);
        if (is_array($cart_items)) {
            foreach ($cart_items as $item) {
                if (isset($item['id']) && (int)$item['id'] === $producto_id) {
                    $cantidad += (int)$item['quantity'];
                    break;
                }
            }
        }
    }

    if ($stock_disponible >= $cantidad) {
        echo json_encode(['success' => true, 'stock' => $stock_disponible]);
    } else {
        echo json_encode(['success' => false, 'message' => "Stock insuficiente. Disponible: $stock_disponible"]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>