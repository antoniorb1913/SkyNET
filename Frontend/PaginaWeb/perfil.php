<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

// Fetch customer data
$cliente_id = $_SESSION["id"];
$sql_cliente = "SELECT nombre, apellidos, email, telefono, fecha_nacimiento, nick FROM clientes WHERE id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $cliente_id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();
$stmt_cliente->close();

// Fetch purchase history
$sql_compras = "SELECT v.id, v.importe_total, v.fecha_previstaEntrega, v.created_at, c.id as carrito_id
                FROM ventas v
                JOIN carrito c ON v.carrito_id = c.id
                WHERE c.cliente_id = ?
                ORDER BY v.created_at DESC";
$stmt_compras = $conn->prepare($sql_compras);
$stmt_compras->bind_param("i", $cliente_id);
$stmt_compras->execute();
$result_compras = $stmt_compras->get_result();
$compras = [];
while ($row = $result_compras->fetch_assoc()) {
    $compras[] = $row;
}
$stmt_compras->close();

// Fetch purchase details for each order
$detalles_compras = [];
foreach ($compras as $compra) {
    $sql_detalles = "SELECT p.nombre, p.referencia, lc.cantidad, lc.precio
                     FROM linea_carrito lc
                     JOIN productos p ON lc.producto_id = p.id
                     WHERE lc.carrito_id = ?";
    $stmt_detalles = $conn->prepare($sql_detalles);
    $stmt_detalles->bind_param("i", $compra['carrito_id']);
    $stmt_detalles->execute();
    $result_detalles = $stmt_detalles->get_result();
    $detalles = [];
    while ($row = $result_detalles->fetch_assoc()) {
        $detalles[] = $row;
    }
    $detalles_compras[$compra['id']] = $detalles;
    $stmt_detalles->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($cliente['nombre']); ?> | SkyNET</title>
    <link rel="stylesheet" href="../Estilos/skynet.css">
    <link rel="stylesheet" href="../Estilos/carrito.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color:rgb(255, 255, 255);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            max-width: 99%;
            z-index: 600;

        }
        .icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .icons a {
            text-decoration: none;
            color: #333;
            font-size: 1.5em;
            transition: color 0.3s;
        }
        .icons a:hover {
            color: #3b81ff;
        }
        .cart-icon {
            position: relative;
            margin-right: 30px;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: #ff3b3b;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8em;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #ff3b3b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <div style="width: 90px; height: auto; padding: 5px; box-sizing: border-box;">
            <img class="logo" src="../LOGO/Logo sin fondo.png" alt="Logo">
        </div>
        <nav>
            <a href="../index.php">Inicio</a>
            <a href="interfaz.php">Productos</a>
            <a href="../PaginaWeb/soporte.php">Soporte</a>
        </nav>
        <form method="GET" action="interfaz.php">
            <div class="search-container">
                <input type="text" name="buscar" placeholder="Buscar productos..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <div class="icons">
            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <a href="perfil.php" title="Perfil"><i class="fas fa-user-circle"></i></a>
            <?php else: ?>
                <a href="login.php" title="Iniciar Sesión"><i class="fas fa-sign-in-alt"></i></a>
            <?php endif; ?>
            <a href="#" id="cart-icon" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-badge" class="cart-badge" style="font-size: 15px; width: 38%; heght: auto;"></span>
            </a>
        </div>
    </header>

    <div class="breadcrumb">
        <a href="index.php">Inicio</a> > <span>Perfil</span>
    </div>

    <h1 class="page-title">Mi Perfil</h1>

    <main>
        <div class="product-detail-container">
            <div class="product-detail">
                <div class="product-info">
                    <h3>Datos Personales</h3>
                    <table class="specs-table">
                        <tr>
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        </tr>
                        <?php if (!empty($cliente['telefono'])): ?>
                        <tr>
                            <th>Teléfono</th>
                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($cliente['fecha_nacimiento'])): ?>
                        <tr>
                            <th>Fecha de Nacimiento</th>
                            <td><?php echo htmlspecialchars($cliente['fecha_nacimiento']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($cliente['nick'])): ?>
                        <tr>
                            <th>Nick</th>
                            <td><?php echo htmlspecialchars($cliente['nick']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <a href="logout.php?redirect=interfaz.php" class="logout-btn">Cerrar Sesión</a>
                </div>
            </div>

            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-btn active" data-tab="compras">Historial de Compras</button>
                </div>
                <div class="tabs-content">
                    <div class="tab-pane active" id="compras">
                        <h3>Historial de Compras</h3>
                        <?php if (empty($compras)): ?>
                            <div class="no-reviews">
                                <p>No tienes compras realizadas.</p>
                                <a href="interfaz.php" class="btn-primary">Explorar Productos</a>
                            </div>
                        <?php else: ?>
                            <table class="specs-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Productos</th>
                                        <th>Total</th>
                                        <th>Entrega Prevista</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compras as $compra): ?>
                                        <tr>
                                            <td><?php echo $compra['id']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($compra['created_at'])); ?></td>
                                            <td>
                                                <ul>
                                                    <?php foreach ($detalles_compras[$compra['id']] as $detalle): ?>
                                                        <li>
                                                            <?php echo htmlspecialchars($detalle['nombre']); ?> 
                                                            (Ref: <?php echo htmlspecialchars($detalle['referencia']); ?>) 
                                                            x <?php echo $detalle['cantidad']; ?> 
                                                            a <?php echo number_format($detalle['precio'], 2, ',', '.'); ?> €
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                            <td><?php echo number_format($compra['importe_total'], 2, ',', '.'); ?> €</td>
                                            <td><?php echo date('d/m/Y', strtotime($compra['fecha_previstaEntrega'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 SkyNET. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    this.classList.add('active');
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Ensure cart icon works
            const cartIcon = document.getElementById('cart-icon');
            if (cartIcon) {
                const newCartIcon = cartIcon.cloneNode(true);
                cartIcon.parentNode.replaceChild(newCartIcon, cartIcon);
                newCartIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof toggleCart === 'function') {
                        toggleCart();
                        console.log('Cart toggled');
                    } else {
                        console.error('toggleCart function not found in carrito.js');
                    }
                });
            }
        });
    </script>
    <script src="../Scripts/carrito.js"></script>
</body>
</html>