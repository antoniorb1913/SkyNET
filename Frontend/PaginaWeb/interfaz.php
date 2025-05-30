<?php
session_start();
require_once "config.php";

$categorias = [];
$sql_categorias = "SELECT id, nombre, (SELECT COUNT(*) FROM productos WHERE categoria_id = categorias.id AND deleted_at IS NULL) as count FROM categorias";
$resultado_categorias = $conn->query($sql_categorias);
if ($resultado_categorias->num_rows > 0) {
    while ($row = $resultado_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$marcas = [];
$sql_marcas = "SELECT id, nombre, (SELECT COUNT(*) FROM productos WHERE marca_id = marcas.id AND deleted_at IS NULL) as count FROM marcas";
$resultado_marcas = $conn->query($sql_marcas);
if ($resultado_marcas->num_rows > 0) {
    while ($row = $resultado_marcas->fetch_assoc()) {
        $marcas[] = $row;
    }
}

$filtros = [
    'buscar' => isset($_GET['buscar']) ? trim($_GET['buscar']) : '',
    'categorias' => isset($_GET['categorias']) ? $_GET['categorias'] : [],
    'marcas' => isset($_GET['marcas']) ? $_GET['marcas'] : [],
    'min_precio' => isset($_GET['min_precio']) ? $_GET['min_precio'] : 0,
    'max_precio' => isset($_GET['max_precio']) ? $_GET['max_precio'] : 4000,
    'ordenar' => isset($_GET['ordenar']) ? $_GET['ordenar'] : 'relevancia',
    'mostrar' => isset($_GET['mostrar']) ? $_GET['mostrar'] : 12
];

$sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.created_at, p.imagen, c.nombre as categoria, m.nombre as marca 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        LEFT JOIN marcas m ON p.marca_id = m.id 
        WHERE p.deleted_at IS NULL";
$params = [];
$tipos = "";

if (!empty($filtros['buscar'])) {
    $sql .= " AND p.nombre LIKE ?";
    $searchTerm = '%' . $filtros['buscar'] . '%';
    $params[] = $searchTerm;
    $tipos .= "s";
}

if (!empty($filtros['categorias'])) {
    $placeholders = str_repeat("?,", count($filtros['categorias']) - 1) . "?";
    $sql .= " AND p.categoria_id IN ($placeholders)";
    foreach ($filtros['categorias'] as $cat_id) {
        $params[] = $cat_id;
        $tipos .= "i";
    }
}

if (!empty($filtros['marcas'])) {
    $placeholders = str_repeat("?,", count($filtros['marcas']) - 1) . "?";
    $sql .= " AND p.marca_id IN ($placeholders)";
    foreach ($filtros['marcas'] as $marca_id) {
        $params[] = $marca_id;
        $tipos .= "i";
    }
}

$sql .= " AND p.precio BETWEEN ? AND ?";
$params[] = $filtros['min_precio'];
$params[] = $filtros['max_precio'];
$tipos .= "dd";

switch ($filtros['ordenar']) {
    case 'precio_asc':
        $sql .= " ORDER BY p.precio ASC";
        break;
    case 'precio_desc':
        $sql .= " ORDER BY p.precio DESC";
        break;
    case 'recientes':
        $sql .= " ORDER BY p.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $filtros['mostrar'];
$sql .= " LIMIT ? OFFSET ?";
$params[] = $filtros['mostrar'];
$params[] = $offset;
$tipos .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
$productos = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}

$sql_count = "SELECT COUNT(*) as total FROM productos p WHERE p.deleted_at IS NULL";
$count_params = [];
$count_tipos = "";

if (!empty($filtros['buscar'])) {
    $sql_count .= " AND p.nombre LIKE ?";
    $count_params[] = $searchTerm;
    $count_tipos .= "s";
}

if (!empty($filtros['categorias'])) {
    $placeholders = str_repeat("?,", count($filtros['categorias']) - 1) . "?";
    $sql_count .= " AND p.categoria_id IN ($placeholders)";
    foreach ($filtros['categorias'] as $cat_id) {
        $count_params[] = $cat_id;
        $count_tipos .= "i";
    }
}

if (!empty($filtros['marcas'])) {
    $placeholders = str_repeat("?,", count($filtros['marcas']) - 1) . "?";
    $sql_count .= " AND p.marca_id IN ($placeholders)";
    foreach ($filtros['marcas'] as $marca_id) {
        $count_params[] = $marca_id;
        $count_tipos .= "i";
    }
}

$sql_count .= " AND p.precio BETWEEN ? AND ?";
$count_params[] = $filtros['min_precio'];
$count_params[] = $filtros['max_precio'];
$count_tipos .= "dd";

$stmt_count = $conn->prepare($sql_count);
if (!empty($count_params)) {
    $stmt_count->bind_param($count_tipos, ...$count_params);
}
$stmt_count->execute();
$resultado_count = $stmt_count->get_result();
$total_productos = $resultado_count->fetch_assoc()['total'];
$total_paginas = ceil($total_productos / $filtros['mostrar']);

$filtros_activos = [];
if (!empty($filtros['buscar'])) {
    $filtros_activos[] = [
        'tipo' => 'buscar',
        'id' => 'buscar',
        'nombre' => 'Búsqueda: ' . $filtros['buscar']
    ];
}

if (!empty($filtros['categorias'])) {
    foreach ($categorias as $categoria) {
        if (in_array($categoria['id'], $filtros['categorias'])) {
            $filtros_activos[] = [
                'tipo' => 'categoria',
                'id' => $categoria['id'],
                'nombre' => $categoria['nombre']
            ];
        }
    }
}

if (!empty($filtros['marcas'])) {
    foreach ($marcas as $marca) {
        if (in_array($marca['id'], $filtros['marcas'])) {
            $filtros_activos[] = [
                'tipo' => 'marca',
                'id' => $marca['id'],
                'nombre' => $marca['nombre']
            ];
        }
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
    if (isset($_GET['buscar'])) {
        $params['buscar'] = $_GET['buscar'];
    }
    return '?' . http_build_query($params);
}

function isCategoriaSelected($categoria_id) {
    return isset($_GET['categorias']) && in_array($categoria_id, $_GET['categorias']);
}

function isMarcaSelected($marca_id) {
    return isset($_GET['marcas']) && in_array($marca_id, $_GET['marcas']);
}

function removeFilter($tipo, $id) {
    $params = $_GET;
    if ($tipo === 'buscar') {
        unset($params['buscar']);
    } elseif ($tipo === 'categoria' && isset($params['categorias'])) {
        $key = array_search($id, $params['categorias']);
        if ($key !== false) {
            unset($params['categorias'][$key]);
            if (empty($params['categorias'])) {
                unset($params['categorias']);
            } else {
                $params['categorias'] = array_values($params['categorias']);
            }
        }
    } elseif ($tipo === 'marca' && isset($params['marcas'])) {
        $key = array_search($id, $params['marcas']);
        if ($key !== false) {
            unset($params['marcas'][$key]);
            if (empty($params['marcas'])) {
                unset($params['marcas']);
            } else {
                $params['marcas'] = array_values($params['marcas']);
            }
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
    <title>SkyNET</title>
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
        <form method="GET" action="">
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
                <span id="cart-badge" class="cart-badge" style="font-size: 15px; width: 38%; heght: auto;"></span>
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
        <a href="../index.php">Inicio</a> / <span>Productos</span>
    </div>

    <h1 class="page-title">Catálogo de Productos</h1>

    <main>
        <aside>
            <form id="filtroForm" method="GET" action="">
                <div class="filter-section">
                    <h3>Categorías</h3>
                    <ul class="categories">
                        <?php foreach ($categorias as $categoria): ?>
                            <li>
                                <input type="checkbox" name="categorias[]" value="<?= $categoria['id'] ?>" 
                                    <?= isCategoriaSelected($categoria['id']) ? 'checked' : '' ?>
                                    onchange="document.getElementById('filtroForm').submit()">
                                <?= htmlspecialchars($categoria['nombre']) ?> <span>(<?= $categoria['count'] ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="filter-section">
                    <h3>Precio</h3>
                    <div class="price-slider">
                        <div class="price-range">
                            <div class="price-rail">
                                <div class="price-track"></div>
                                <div class="min-handle" id="minHandle"></div>
                                <div class="max-handle" id="maxHandle"></div>
                            </div>
                        </div>
                        <div class="price-inputs">
                            <div class="price-input">
                                <span>€</span>
                                <input type="number" id="minPrice" name="min_precio" value="<?= $filtros['min_precio'] ?>" min="0" max="4000">
                            </div>
                            <div class="price-separator">-</div>
                            <div class="price-input">
                                <span>€</span>
                                <input type="number" id="maxPrice" name="max_precio" value="<?= $filtros['max_precio'] ?>" min="0" max="4000">
                            </div>
                        </div>
                        <button type="submit" class="filter-button" style="background-color: #3b81ff; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 14px; transition: background-color 0.2s; width: 100%; margin-top: 10px;">Aplicar precio</button>
                    </div>
                </div>

                <div class="filter-section">
                    <h3>Marcas</h3>
                    <ul>
                        <?php foreach ($marcas as $marca): ?>
                            <li>
                                <input type="checkbox" name="marcas[]" value="<?= $marca['id'] ?>" 
                                    <?= isMarcaSelected($marca['id']) ? 'checked' : '' ?>
                                    onchange="document.getElementById('filtroForm').submit()">
                                <?= htmlspecialchars($marca['nombre']) ?> <span>(<?= $marca['count'] ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <input type="hidden" name="buscar" value="<?= htmlspecialchars($filtros['buscar']) ?>">
                <input type="hidden" name="ordenar" value="<?= $filtros['ordenar'] ?>">
                <input type="hidden" name="mostrar" value="<?= $filtros['mostrar'] ?>">
                <input type="hidden" name="page" value="1">
            </form>
        </aside>

        <div class="products-container">
            <div class="products-header">
                <div class="showing-products">
                    Mostrando <?= min(($page - 1) * $filtros['mostrar'] + 1, $total_productos) ?>-<?= min($page * $filtros['mostrar'], $total_productos) ?> 
                    de <?= $total_productos ?> productos
                </div>
                <div class="products-sort">
                    <span>Ordenar por:</span>
                    <select onchange="window.location.href=this.value">
                        <option value="<?= buildFilterUrl(['ordenar' => 'relevancia']) ?>" <?= $filtros['ordenar'] == 'relevancia' ? 'selected' : '' ?>>Relevancia</option>
                        <option value="<?= buildFilterUrl(['ordenar' => 'precio_asc']) ?>" <?= $filtros['ordenar'] == 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="<?= buildFilterUrl(['ordenar' => 'precio_desc']) ?>" <?= $filtros['ordenar'] == 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="<?= buildFilterUrl(['ordenar' => 'recientes']) ?>" <?= $filtros['ordenar'] == 'recientes' ? 'selected' : '' ?>>Más recientes</option>
                    </select>
                </div>
                <div class="view-options">
                    <span>Ver:</span>
                    <button class="view-grid active"><i class="fas fa-th"></i></button>
                    <span>Mostrar:</span>
                    <select onchange="window.location.href=this.value">
                        <option value="<?= buildFilterUrl(['mostrar' => 12, 'page' => 1]) ?>" <?= $filtros['mostrar'] == 12 ? 'selected' : '' ?>>12</option>
                        <option value="<?= buildFilterUrl(['mostrar' => 24, 'page' => 1]) ?>" <?= $filtros['mostrar'] == 24 ? 'selected' : '' ?>>24</option>
                        <option value="<?= buildFilterUrl(['mostrar' => 48, 'page' => 1]) ?>" <?= $filtros['mostrar'] == 48 ? 'selected' : '' ?>>48</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($filtros['buscar'])): ?>
            <div class="search-results-info">
                <p>Resultados de búsqueda para: <strong>"<?= htmlspecialchars($filtros['buscar']) ?>"</strong></p>
                <a href="?" class="clear-search">Limpiar búsqueda</a>
            </div>
            <?php endif; ?>

            <?php if (!empty($filtros_activos)): ?>
            <div class="filter-tags">
                <span>Filtros activos:</span>
                <?php foreach ($filtros_activos as $filtro): ?>
                    <div class="tag">
                        <?= htmlspecialchars($filtro['nombre']) ?> 
                        <a href="<?= removeFilter($filtro['tipo'], $filtro['id']) ?>"><button type="button">×</button></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <section class="products">
                <?php if (empty($productos)): ?>
                    <div class="no-products">
                        <p>No se encontraron productos que coincidan con los filtros seleccionados.</p>
                        <?php if (!empty($filtros['buscar'])): ?>
                            <p>Intenta con otros términos de búsqueda.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="product-card" data-id="<?= $producto['id'] ?>">
                            <?php if (isset($producto['descuento']) && $producto['descuento'] > 0): ?>
                                <div class="discount-badge">-<?= $producto['descuento'] ?>%</div>
                            <?php endif; ?>
                            <?php 
                            $fecha_creacion = new DateTime($producto['created_at']);
                            $ahora = new DateTime();
                            $diferencia = $ahora->diff($fecha_creacion);
                            $es_nuevo = $diferencia->days <= 30;
                            if ($es_nuevo && !isset($producto['descuento'])): 
                            ?>
                                <div class="new-badge">Nuevo</div>
                            <?php endif; ?>
                            <a href="producto.php?id=<?= $producto['id'] ?>">
                                <img src="../Imagenes_productos/<?= $producto['id'] ?>.jpg" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                            </a>
                            <h4><a href="producto.php?id=<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></a></h4>
                            <div class="product-specs">
                                <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                            </div>
                            <p class="price">€<?= number_format($producto['precio'], 2, ',', '.') ?></p>
                            <?php 
                            $inStock = isset($producto['stock']) && $producto['stock'] > 0;
                            ?>
                            <div class="availability <?= $inStock ? 'in-stock' : 'out-of-stock' ?>">
                                <?= $inStock ? 'En stock' : 'Agotado' ?>
                            </div>
                            </br>
                            <button class="add-to-cart-btn" <?= !$inStock ? 'disabled' : '' ?>>Añadir al carrito</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
            
            <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?= buildFilterUrl(['page' => $page - 1]) ?>" class="page-link">« Anterior</a>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 2); $i <= min($total_paginas, $page + 2); $i++): ?>
                    <a href="<?= buildFilterUrl(['page' => $i]) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $total_paginas): ?>
                    <a href="<?= buildFilterUrl(['page' => $page + 1]) ?>" class="page-link">Siguiente »</a>
                <?php endif; ?>
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
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            const minHandle = document.getElementById('minHandle');
            const maxHandle = document.getElementById('maxHandle');
            const priceTrack = document.querySelector('.price-track');
            const priceRail = document.querySelector('.price-rail');
            const minPrice = 0;
            const maxPrice = 4000;
            const range = maxPrice - minPrice;
            
            updateHandlePositions();
            updateTrackPosition();
            
            minHandle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                document.addEventListener('mousemove', moveMinHandle);
                document.addEventListener('mouseup', stopDragging);
            });
            
            maxHandle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                document.addEventListener('mousemove', moveMaxHandle);
                document.addEventListener('mouseup', stopDragging);
            });
            
            minPriceInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                if (value < minPrice) value = minPrice;
                if (value > parseInt(maxPriceInput.value) - 50) value = parseInt(maxPriceInput.value) - 50;
                this.value = value;
                updateHandlePositions();
                updateTrackPosition();
            });
            
            maxPriceInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                if (value > maxPrice) value = maxPrice;
                if (value < parseInt(minPriceInput.value) + 50) value = parseInt(minPriceInput.value) + 50;
                this.value = value;
                updateHandlePositions();
                updateTrackPosition();
            });
            
            function moveMinHandle(e) {
                const railRect = priceRail.getBoundingClientRect();
                let position = (e.clientX - railRect.left) / railRect.width;
                position = Math.max(0, Math.min(position, 1));
                const maxHandlePos = (parseInt(maxPriceInput.value) - minPrice) / range;
                position = Math.min(position, maxHandlePos - 0.05);
                const newValue = Math.round(position * range + minPrice);
                minPriceInput.value = newValue;
                updateHandlePositions();
                updateTrackPosition();
            }
            
            function moveMaxHandle(e) {
                const railRect = priceRail.getBoundingClientRect();
                let position = (e.clientX - railRect.left) / railRect.width;
                position = Math.max(0, Math.min(position, 1));
                const minHandlePos = (parseInt(minPriceInput.value) - minPrice) / range;
                position = Math.max(position, minHandlePos + 0.05);
                const newValue = Math.round(position * range + minPrice);
                maxPriceInput.value = newValue;
                updateHandlePositions();
                updateTrackPosition();
            }
            
            function stopDragging() {
                document.removeEventListener('mousemove', moveMinHandle);
                document.removeEventListener('mousemove', moveMaxHandle);
            }
            
            function updateHandlePositions() {
                const minPercentage = ((parseInt(minPriceInput.value) - minPrice) / range) * 100;
                const maxPercentage = ((parseInt(maxPriceInput.value) - minPrice) / range) * 100;
                minHandle.style.left = minPercentage + '%';
                maxHandle.style.left = maxPercentage + '%';
            }
            
            function updateTrackPosition() {
                const minPercentage = ((parseInt(minPriceInput.value) - minPrice) / range) * 100;
                const maxPercentage = ((parseInt(maxPriceInput.value) - minPrice) / range) * 100;
                priceTrack.style.left = minPercentage + '%';
                priceTrack.style.width = (maxPercentage - minPercentage) + '%';
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