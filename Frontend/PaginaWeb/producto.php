<?php
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$conexion = new mysqli("localhost", "root", "root", "SkyNET");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "SELECT p.*, c.nombre as categoria, m.nombre as marca
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN marcas m ON p.marca_id = m.id
        WHERE p.id = ? AND p.deleted_at IS NULL";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();
$stmt->close();

$categorias = [];
$sql_categorias = "SELECT id, nombre, (SELECT COUNT(*) FROM productos WHERE categoria_id = categorias.id AND deleted_at IS NULL) as count FROM categorias";
$resultado_categorias = $conexion->query($sql_categorias);
if ($resultado_categorias->num_rows > 0) {
    while ($row = $resultado_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$marcas = [];
$sql_marcas = "SELECT id, nombre, (SELECT COUNT(*) FROM productos WHERE marca_id = marcas.id AND deleted_at IS NULL) as count FROM marcas";
$resultado_marcas = $conexion->query($sql_marcas);
if ($resultado_marcas->num_rows > 0) {
    while ($row = $resultado_marcas->fetch_assoc()) {
        $marcas[] = $row;
    }
}

function buildFilterUrl($nuevos_params = []) {
    $params = $_GET;
    foreach ($nuevos_params as $key => $value) {
        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $producto ? htmlspecialchars($producto['nombre']) : 'Producto no encontrado' ?> | SkyNET
    </title>
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
        .inst, .pint {
            width: 1em;
            height: auto;
            vertical-align: middle;
        }
        .redes {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .redes p {
            flex: 1; /* Ocupa el espacio disponible */
            text-align: center; /* Centra el texto */
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
                <input type="text" name="buscar" placeholder="Buscar productos..." 
                       value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>" required>
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
                <span id="cart-badge" class="cart-badge" style="width: 45%; heght: auto;"></span>
            </a>
        </div>
    </header>
    </br>
    </br>
    </br>
    </br>
    </br>
    </br>


    <div class="breadcrumb">
        <a href="index.php">Inicio</a> > <a href="interfaz.php">Productos</a> 
        <?php if ($producto): ?>
            > <?= htmlspecialchars($producto['nombre']) ?>
        <?php endif; ?>
    </div>

    <h1 class="page-title"><?= $producto ? htmlspecialchars($producto['nombre']) : 'Producto no encontrado' ?></h1>

    <main>
        <aside>
        </aside>

        <div class="product-detail-container">
            <?php if ($producto): ?>
                <div class="product-detail">
                    <div class="product-gallery">
                        <?php 
                        $imagen_url = !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : '/api/placeholder/600/600';
                        ?>
                        <img src="../Imagenes_productos/<?= $producto['id'] ?>.jpg" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="main-image">
                    </div>
                    
                    <div class="product-info">
                        <?php if (!empty($producto['marca'])): ?>
                        <p class="product-brand">Marca: <strong><?= htmlspecialchars($producto['marca']) ?></strong></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($producto['categoria'])): ?>
                        <p class="product-category">Categoría: <strong><?= htmlspecialchars($producto['categoria']) ?></strong></p>
                        <?php endif; ?>
                        
                        <div class="product-meta">
                            <div class="availability">Disponibilidad: <span class="in-stock">En stock</span></div>
                            <div class="product-code">Código: <?= htmlspecialchars($producto['id']) ?></div>
                        </div>
                        
                        <div class="product-pricing">
                            <?php if (isset($producto['descuento']) && $producto['descuento'] > 0): ?>
                                <div class="price-container">
                                    <span class="original-price"><?= number_format(($producto['precio'] / (1 - $producto['descuento']/100)), 2, ',', '.') ?> €</span>
                                    <span class="discount">-<?= $producto['descuento'] ?>%</span>
                                </div>
                            <?php endif; ?>
                            <div class="current-price"><?= number_format($producto['precio'], 2, ',', '.') ?> €</div>
                            
                            <?php 
                            if (isset($producto['created_at'])) {
                                $fecha_creacion = new DateTime($producto['created_at']);
                                $ahora = new DateTime();
                                $diferencia = $ahora->diff($fecha_creacion);
                                $es_nuevo = $diferencia->days <= 30;
                                if ($es_nuevo && !isset($producto['descuento'])): 
                            ?>
                                <div class="new-product-badge">¡Nuevo!</div>
                            <?php 
                                endif;
                            }
                            ?>
                        </div>
                        
                        <div class="product-actions">
                            <div class="quantity-selector">
                                <button class="quantity-btn decrease">-</button>
                                <input type="number" value="1" min="1" max="99" id="quantity" class="quantity-input">
                                <button class="quantity-btn increase">+</button>
                            </div>
                            <button class="add-to-cart-btn" id="btn-comprar">
                                <i class="fas fa-shopping-cart"></i> Añadir al carrito
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="product-tabs">
                    <div class="tabs-header">
                        <button class="tab-btn active" data-tab="description">Descripción</button>
                        <button class="tab-btn" data-tab="reviews">Opiniones</button>
                    </div>
                    
                    <div class="tabs-content">
                        <div class="tab-pane active" id="description">
                            <h3>Descripción</h3>
                            <table class="specs-table">
                                <tr>
                                    <th>Marca</th>
                                    <td><?= htmlspecialchars($producto['marca'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Categoría</th>
                                    <td><?= htmlspecialchars($producto['categoria'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Código de producto</th>
                                    <td><?= htmlspecialchars($producto['id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="tab-pane" id="reviews">
                            <h3>Opiniones de clientes</h3>
                            <div class="no-reviews">
                                <p>Este producto no tiene opiniones todavía. Sé el primero en opinar.</p>
                                <button class="write-review-btn">Escribir opinión</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-product-found">
                    <h2>Producto no encontrado</h2>
                    <p>El producto que estás buscando no existe o fue eliminado.</p>
                    <a href="interfaz.php" class="btn-primary">Volver a productos</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="redes">
        <p>© 2025 SkyNET. Todos los derechos reservados.</p>
        <a href="https://www.instagram.com/skynet.oficiall/" > <img src='/Backend/Productos/imagenes/instagram.png' class="inst"></a>
        <a href="https://es.pinterest.com/slskynet/"> <img src='/Backend/Productos/imagenes/pinterest.png' class="pint"></a>
        </div>
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
            
            const decreaseBtn = document.querySelector('.quantity-btn.decrease');
            const increaseBtn = document.querySelector('.quantity-btn.increase');
            const quantityInput = document.querySelector('.quantity-input');
            
            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value > 1) {
                        quantityInput.value = value - 1;
                    }
                });
                
                increaseBtn.addEventListener('click', function() {
                    let value = parseInt(quantityInput.value);
                    if (value < 99) {
                        quantityInput.value = value + 1;
                    }
                });
            }

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