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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Incluir el script del carrito -->
    <script src="../Scripts/carrito.js"></script>
</head>
<body>
    <header>
        <div class="logo">SkyNet</div>
        <nav>
        <a href="#">Inicio</a>
        <a href="interfaz.php">Productos</a>
        <a href="#">Ofertas</a>
        <a href="#">Soporte</a>
        <a href="#">Contacto</a>
        </nav>
        <div class="search-container">
        <input type="text" placeholder="Buscar productos...">
        <button><i class="fas fa-search"></i></button>
        </div>
        <div class="icons">
            <div class="dropdown-content">
            <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></p>
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="signup.php">Registrarse</a>
            <?php endif; ?>
            </div>
        </div>
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
                        <img src="<?= $imagen_url ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="main-image">
                        
                        <div class="thumbnail-container">
                            <!-- Miniaturas adicionales podrían ir aquí -->
                            <div class="thumbnail active"><img src="<?= $imagen_url ?>" alt="Miniatura 1"></div>
                            <div class="thumbnail"><img src="/api/placeholder/150/150" alt="Miniatura 2"></div>
                            <div class="thumbnail"><img src="/api/placeholder/150/150" alt="Miniatura 3"></div>
                        </div>
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
    // Añadir este script al final de producto.php, justo antes del cierre </body>

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar carrito desde localStorage
            let carrito = JSON.parse(localStorage.getItem('skynet_carrito')) || [];
            
            // Actualizar contador al cargar la página
            actualizarContadorCarrito();
            
            // Añadir evento de clic al icono del carrito para mostrar/ocultar el minicarrito
            const iconoCarrito = document.querySelector('.icons a:nth-child(3)');
            if (iconoCarrito) {
                iconoCarrito.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Eliminar minicarrito anterior si existe
                    const minicarritoAnterior = document.querySelector('.minicarrito');
                    if (minicarritoAnterior) {
                        document.body.removeChild(minicarritoAnterior);
                    }
                    
                    // Crear y mostrar el minicarrito actualizado
                    crearMinicarrito();
                });
            }
            
            // Controles de cantidad
            const quantityInput = document.getElementById('quantity');
            const decreaseBtn = document.querySelector('.quantity-btn.decrease');
            const increaseBtn = document.querySelector('.quantity-btn.increase');
            
            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value, 10);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });
                
                increaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(quantityInput.value, 10);
                    if (currentValue < parseInt(quantityInput.max, 10)) {
                        quantityInput.value = currentValue + 1;
                    }
                });
                
                quantityInput.addEventListener('change', function() {
                    const value = parseInt(this.value, 10);
                    if (isNaN(value) || value < 1) {
                        this.value = 1;
                    } else if (value > parseInt(this.max, 10)) {
                        this.value = this.max;
                    }
                });
            }
            
            // Pestañas
            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remover clase active de todos los botones y paneles
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
                    
                    // Añadir clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Mostrar el panel correspondiente
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Miniaturas de imágenes
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.querySelector('.main-image');
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    // Remover clase active de todas las miniaturas
                    thumbnails.forEach(t => t.classList.remove('active'));
                    
                    // Añadir clase active a la miniatura clickeada
                    this.classList.add('active');
                    
                    // Cambiar la imagen principal
                    const imgSrc = this.querySelector('img').getAttribute('src');
                    mainImage.setAttribute('src', imgSrc);
                });
            });
            
            // Añadir funcionalidad de carrito
            const btnComprar = document.getElementById('btn-comprar');
            if (btnComprar && quantityInput) {
                btnComprar.addEventListener('click', function() {
                    // Obtener información del producto desde la página
                    const productoId = document.querySelector('.product-code')?.textContent.replace('Código: ', '') || null;
                    const productoNombre = document.querySelector('.page-title')?.textContent || '';
                    const precioTexto = document.querySelector('.current-price')?.textContent.replace('€', '').trim() || '0';
                    // Reemplazar puntos de miles y usar coma como decimal
                    const precioFormateado = precioTexto.replace(/\./g, '').replace(',', '.');
                    const productoPrecio = parseFloat(precioFormateado);
                    const productoImagen = document.querySelector('.main-image')?.src || '';
                    const cantidad = parseInt(quantityInput.value, 10);
                    
                    if (!productoId) return;
                    
                    // Obtener carrito actual del localStorage
                    let carrito = JSON.parse(localStorage.getItem('skynet_carrito')) || [];
                    
                    // Buscar si el producto ya está en el carrito
                    const productoExistente = carrito.find(item => item.id === productoId);
                    
                    if (productoExistente) {
                        // Incrementar cantidad si ya existe
                        productoExistente.cantidad += cantidad;
                        productoExistente.subtotal = productoExistente.cantidad * productoExistente.precio;
                    } else {
                        // Añadir nuevo producto al carrito
                        carrito.push({
                            id: productoId,
                            nombre: productoNombre,
                            precio: productoPrecio,
                            imagen: productoImagen,
                            cantidad: cantidad,
                            subtotal: productoPrecio * cantidad
                        });
                    }
                    
                    // Guardar carrito en localStorage
                    localStorage.setItem('skynet_carrito', JSON.stringify(carrito));
                    
                    // Actualizar contador visual
                    actualizarContadorCarrito();
                    
                    // Mostrar notificación
                    mostrarNotificacion(productoNombre);
                });
            }
            
            // Funciones auxiliares copiadas de carrito.js
            function actualizarContadorCarrito() {
                // Siempre obtener el carrito más reciente desde localStorage
                const carritoActual = JSON.parse(localStorage.getItem('skynet_carrito')) || [];
                const contador = carritoActual.reduce((total, item) => total + item.cantidad, 0);
                
                // Buscar o crear el elemento contador en el icono del carrito
                let contadorElement = document.querySelector('.cart-counter');
                
                if (!contadorElement) {
                    contadorElement = document.createElement('span');
                    contadorElement.className = 'cart-counter';
                    const iconoCarrito = document.querySelector('.icons a:nth-child(3)');
                    if (iconoCarrito) {
                        iconoCarrito.appendChild(contadorElement);
                    }
                }
                
                // Actualizar contador y visibilidad
                if (contador > 0) {
                    contadorElement.textContent = contador;
                    contadorElement.style.display = 'flex';
                } else {
                    contadorElement.style.display = 'none';
                }
            }
            
            function mostrarNotificacion(nombreProducto) {
                // Crear elemento de notificación
                const notificacion = document.createElement('div');
                notificacion.className = 'cart-notification';
                notificacion.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <p>"${nombreProducto}" añadido al carrito</p>
                `;
                
                // Añadir al DOM
                document.body.appendChild(notificacion);
                
                // Animar entrada
                setTimeout(() => {
                    notificacion.classList.add('show');
                }, 10);
                
                // Eliminar después de 3 segundos
                setTimeout(() => {
                    notificacion.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(notificacion);
                    }, 300);
                }, 3000);
            }
            
            function crearMinicarrito() {
                // Crear elemento minicarrito
                const minicarrito = document.createElement('div');
                minicarrito.className = 'minicarrito';
                
                // Asegurarnos de que esté visible
                minicarrito.style.display = 'block';
                
                // Crear contenido
                if (carrito.length === 0) {
                    minicarrito.innerHTML = `
                        <div class="minicarrito-header">
                            <h3>Tu Carrito</h3>
                            <button class="cerrar-minicarrito"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="minicarrito-empty">
                            <p>Tu carrito está vacío</p>
                            <i class="fas fa-shopping-cart"></i>
                            <a href="#" class="btn-seguir-comprando">Seguir comprando</a>
                        </div>
                    `;
                } else {
                    // Calcular total
                    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);
                    
                    // Crear HTML con los productos
                    let productosHTML = '';
                    
                    carrito.forEach(item => {
                        productosHTML += `
                            <div class="minicarrito-item" data-id="${item.id}">
                                <img src="${item.imagen}" alt="${item.nombre}">
                                <div class="minicarrito-item-info">
                                    <h4>${item.nombre}</h4>
                                    <div class="minicarrito-item-price">
                                        <span>${item.cantidad} x €${item.precio.toFixed(2).replace('.', ',')}</span>
                                        <span class="subtotal">€${item.subtotal.toFixed(2).replace('.', ',')}</span>
                                    </div>
                                </div>
                                <button class="eliminar-item" data-id="${item.id}"><i class="fas fa-trash"></i></button>
                            </div>
                        `;
                    });
                    
                    minicarrito.innerHTML = `
                        <div class="minicarrito-header">
                            <h3>Tu Carrito (${carrito.reduce((total, item) => total + item.cantidad, 0)})</h3>
                            <button class="cerrar-minicarrito"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="minicarrito-items">
                            ${productosHTML}
                        </div>
                        <div class="minicarrito-footer">
                            <div class="minicarrito-total">
                                <span>Total:</span>
                                <span>€${total.toFixed(2).replace('.', ',')}</span>
                            </div>
                            <div class="minicarrito-buttons">
                                <a href="#" class="btn-ver-carrito">Ver carrito</a>
                                <a href="#" class="btn-checkout">Finalizar compra</a>
                            </div>
                        </div>
                    `;
                }
                
                // Añadir al DOM
                document.body.appendChild(minicarrito);
                
                // Añadir eventos
                const btnCerrar = minicarrito.querySelector('.cerrar-minicarrito');
                if (btnCerrar) {
                    btnCerrar.addEventListener('click', function() {
                        minicarrito.style.display = 'none';
                    });
                }
                
                const btnSeguirComprando = minicarrito.querySelector('.btn-seguir-comprando');
                if (btnSeguirComprando) {
                    btnSeguirComprando.addEventListener('click', function(e) {
                        e.preventDefault();
                        minicarrito.style.display = 'none';
                    });
                }
                
                // Añadir evento para eliminar productos
                const botonesEliminar = minicarrito.querySelectorAll('.eliminar-item');
                botonesEliminar.forEach(boton => {
                    boton.addEventListener('click', function() {
                        const productoId = this.getAttribute('data-id');
                        
                        // Eliminar del array
                        carrito = carrito.filter(item => item.id !== productoId);
                        
                        // Guardar en localStorage
                        localStorage.setItem('skynet_carrito', JSON.stringify(carrito));
                        
                        // Actualizar contador
                        actualizarContadorCarrito();
                        
                        // Recrear minicarrito
                        document.body.removeChild(minicarrito);
                        crearMinicarrito();
                    });
                });
            }
        });
    </script>
</body>
</html>