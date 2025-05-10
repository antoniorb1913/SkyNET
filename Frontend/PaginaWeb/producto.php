<?php
// producto.php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Conexión
$conexion = new mysqli("localhost", "root", "root", "SkyNET");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el producto
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

// Obtener categorías y marcas para el sidebar (como en interfaz.php)
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

// Función para construir URL (como en interfaz.php)
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
</head>
<body>
    <header>
        <div style="width: 90px; height: auto; padding: 5px; box-sizing: border-box;">
        <img class="logo"src="../LOGO/Logo sin fondo.png" alt="Logo">
        </div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="interfaz.php">Productos</a>
            <a href="../PaginaWeb/soporte.php">Soporte</a>
        </nav>
        <div class="search-container">
            <input type="text" placeholder="Buscar productos...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="icons">
            <a href="#"><i class="fas fa-user"></i></a>
            <a href="#"><i class="fas fa-heart"></i></a>
            <a href="#"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>

<div class="breadcrumb">
    <a href="index.php">Inicio</a> > <a href="interfaz.php">Productos</a> 
    <?php if ($producto): ?>
        > <?= htmlspecialchars($producto['nombre']) ?>
    <?php endif; ?>
</div>

<h1 class="page-title"><?= $producto ? htmlspecialchars($producto['nombre']) : 'Producto no encontrado' ?></h1>

<main>
    <aside>
       <!-- Contenido del aside -->
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
                    
                    <div class="product-description">
                        <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
                    </div>
                    
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
                        // Verificar si el producto es nuevo (menos de 30 días)
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
                        <!-- Modificamos el botón para que tenga la misma estructura que en interfaz.php -->
                        <button class="add-to-cart-btn" id="btn-comprar">
                            <i class="fas fa-shopping-cart"></i> Añadir al carrito
                        </button>
                    </div>
                    
                    <div class="product-share">
                        <span>Compartir:</span>
                        <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-btn active" data-tab="description">Descripción</button>
                    <button class="tab-btn" data-tab="specs">Especificaciones</button>
                    <button class="tab-btn" data-tab="reviews">Opiniones</button>
                </div>
                
                <div class="tabs-content">
                    <div class="tab-pane active" id="description">
                        <h3>Descripción detallada</h3>
                        <p><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                    </div>
                    
                    <div class="tab-pane" id="specs">
                        <h3>Especificaciones técnicas</h3>
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
                            <!-- Más especificaciones podrían añadirse aquí -->
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
    <p>&copy; 2025 SkyNET. Todos los derechos reservados.</p>
</footer>

<script>
    // Script para manejar las pestañas de información del producto
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Quitar active de todos los botones y paneles
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Añadir active al botón clickeado
                this.classList.add('active');
                
                // Mostrar el panel correspondiente
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Manejar la cantidad
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
    });
</script>
<script src="../Scripts/carrito.js"></script>

</body>
</html>