<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header('Location: login.php?redirect=confirmacion_venta.php');
    exit;
}

// Obtener los parámetros de la URL
$venta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$numero_venta = isset($_GET['numero']) ? htmlspecialchars($_GET['numero']) : '';

// Incluir archivo de configuración
require_once "config.php";

// Verificar que la venta pertenece al cliente
$sql = "SELECT v.*, c.cliente_id 
        FROM ventas v 
        JOIN carrito c ON v.carrito_id = c.id 
        WHERE v.id = ? AND c.cliente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $venta_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Venta no encontrada o no autorizada.";
    exit;
}

$venta = $result->fetch_assoc();

// Obtener los productos del carrito
$sql_productos = "SELECT lc.producto_id, lc.cantidad, lc.precio, p.nombre 
                  FROM linea_carrito lc 
                  JOIN productos p ON lc.producto_id = p.id 
                  WHERE lc.carrito_id = ?";
$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bind_param("i", $venta['carrito_id']);
$stmt_productos->execute();
$productos_result = $stmt_productos->get_result();
$productos = [];
while ($row = $productos_result->fetch_assoc()) {
    $productos[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Venta - SkyNet</title>
    <link rel="stylesheet" href="../Estilos/skynet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <div style="width: 90px; height: auto; padding: 5px; box-sizing: border-box;">
            <img class="logo" src="../LOGO/Logo sin fondo.png" alt="Logo">
        </div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="interfaz.php">Productos</a>
            <a href="soporte.php">Soporte</a>
        </nav>
        <div class="icons">
            <a href="login.php"><i class="fas fa-user"></i></a>
            <a href="#" id="cart-icon" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-badge" class="cart-badge"></span>
            </a>
        </div>
    </header>

    <div class="breadcrumb">
        <a href="index.php">Inicio</a> > <a href="interfaz.php">Productos</a> > <a href="checkout.php">Checkout</a> > <span>Confirmación</span>
    </div>

    <h1 class="page-title">¡Gracias por tu compra!</h1>

    <main>
        <div class="confirmation-container">
            <p>Tu pedido ha sido procesado con éxito.</p>
            <p><strong>Número de venta:</strong> <?= $numero_venta ?></p>
            <p><strong>Fecha prevista de entrega:</strong> <?= htmlspecialchars($venta['fecha_previstaEntrega']) ?></p>
            <p><strong>Importe total:</strong> <?= number_format($venta['importe_total'], 2, ',', '.') ?> €</p>

            <h3>Productos comprados:</h3>
            <ul>
                <?php foreach ($productos as $producto): ?>
                    <li>
                        <?= htmlspecialchars($producto['nombre']) ?> - 
                        Cantidad: <?= $producto['cantidad'] ?> - 
                        Precio unitario: <?= number_format($producto['precio'], 2, ',', '.') ?> € - 
                        Subtotal: <?= number_format($producto['precio'] * $producto['cantidad'], 2, ',', '.') ?> €
                    </li>
                <?php endforeach; ?>
            </ul>

            <a href="interfaz.php" class="btn-primary">Continuar comprando</a>
        </div>
    </main>

    <footer>
        <p>© 2025 SkyNet. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Limpiar localStorage después de una compra exitosa
        localStorage.removeItem('cartItems');

        // Actualizar el badge del carrito
        document.addEventListener('DOMContentLoaded', function() {
            const cartBadge = document.querySelector('#cart-badge');
            if (cartBadge) {
                cartBadge.textContent = '0';
                cartBadge.style.display = 'none';
            }
        });
    </script>
</body>
</html>