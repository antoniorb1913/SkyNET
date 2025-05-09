// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Crear elementos del carrito flotante
    const cartPopup = document.createElement('div');
    cartPopup.className = 'cart-popup';
    cartPopup.innerHTML = `
        <div class="cart-popup-header">
            <h3>Mi Carrito</h3>
            <button class="close-cart"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-popup-body">
            <div class="cart-items">
                <!-- Aquí se mostrarán los productos del carrito -->
            </div>
            <div class="cart-empty">
                <p>Tu carrito está vacío</p>
                <button class="continue-shopping">Continuar comprando</button>
            </div>
        </div>
        <div class="cart-popup-footer">
            <div class="cart-subtotal">
                <span>Subtotal:</span>
                <span class="cart-subtotal-price">€0,00</span>
            </div>
            <div class="cart-buttons">
                <button class="view-cart">Ver carrito</button>
                <button class="checkout">Finalizar compra</button>
            </div>
        </div>
    `;
    document.body.appendChild(cartPopup);

    // Añadir el fondo oscuro
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);

    // Estado del carrito
    let cartItems = [];
    
    // Función para formatear precios en formato español (1.234,56€)
    function formatPrice(price) {
        return price.toFixed(2)
            .replace('.', ',')
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Función para convertir precios de formato español a decimal (1.234,56€ -> 1234.56)
    function parseSpanishPrice(price) {
        // Eliminar el símbolo de euro y espacios
        price = price.replace('€', '').trim();
        // Eliminar los puntos de separación de miles
        price = price.replace(/\./g, '');
        // Cambiar la coma decimal por punto
        price = price.replace(',', '.');
        // Convertir a número
        return parseFloat(price);
    }
    
    let cartOpen = false;

    // Manejador para abrir el carrito
    const cartButton = document.querySelector('.icons a:nth-child(3)');
    if (cartButton) {
        cartButton.addEventListener('click', function(e) {
            e.preventDefault();
            toggleCart();
        });
    }

    // Manejador para cerrar el carrito
    document.querySelector('.close-cart').addEventListener('click', function() {
        closeCart();
    });

    // Cerrar el carrito cuando se hace clic en el overlay
    overlay.addEventListener('click', function() {
        closeCart();
    });

    // Botón continuar comprando
    document.querySelector('.continue-shopping').addEventListener('click', function() {
        closeCart();
    });

    // Función para alternar la visibilidad del carrito
    function toggleCart() {
        if (cartOpen) {
            closeCart();
        } else {
            openCart();
        }
    }

    // Función para abrir el carrito
    function openCart() {
        cartPopup.classList.add('active');
        overlay.style.display = 'block';
        cartOpen = true;
        updateCartView();
    }

    // Función para cerrar el carrito
    function closeCart() {
        cartPopup.classList.remove('active');
        overlay.style.display = 'none';
        cartOpen = false;
    }

    // Función para actualizar la vista del carrito
    function updateCartView() {
        const cartItemsContainer = document.querySelector('.cart-items');
        const cartEmptyContainer = document.querySelector('.cart-empty');
        const subtotalElement = document.querySelector('.cart-subtotal-price');
        
        // Limpiar el contenedor de elementos
        cartItemsContainer.innerHTML = '';
        
        // Mostrar los elementos del carrito o el mensaje de carrito vacío
        if (cartItems.length > 0) {
            cartItemsContainer.style.display = 'block';
            cartEmptyContainer.style.display = 'none';
            
            // Calcular el subtotal
            let subtotal = 0;
            
            cartItems.forEach(item => {
                // Crear elemento de carrito
                const cartItemElement = document.createElement('div');
                cartItemElement.className = 'cart-item';
                cartItemElement.innerHTML = `
                    <img class="cart-item-image" src="../Imagenes_productos/${item.id}.jpg" alt="${item.name}">
                    <div class="cart-item-details">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">€${formatPrice(item.price)}</div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease" data-id="${item.id}">-</button>
                            <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                            <button class="quantity-btn increase" data-id="${item.id}">+</button>
                            <button class="cart-item-remove" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItemElement);
                
                // Añadir al subtotal
                subtotal += item.price * item.quantity;
            });
            
            // Actualizar subtotal
            subtotalElement.textContent = `€${formatPrice(subtotal)}`;
            
            // Agregar event listeners para los botones de cantidad
            document.querySelectorAll('.quantity-btn.decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    decreaseQuantity(id);
                });
            });
            
            document.querySelectorAll('.quantity-btn.increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    increaseQuantity(id);
                });
            });
            
            document.querySelectorAll('.cart-item-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    removeItem(id);
                });
            });
        } else {
            cartItemsContainer.style.display = 'none';
            cartEmptyContainer.style.display = 'block';
            subtotalElement.textContent = '€0,00';
        }
    }

    // Función para añadir un producto al carrito
    function addToCart(product, quantity = 1) {
        // Comprobar si el producto ya está en el carrito
        const existingItem = cartItems.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cartItems.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: quantity
            });
        }
        
        // Actualizar el contador del carrito
        updateCartCount();
        
        // Si el carrito está abierto, actualizar la vista
        if (cartOpen) {
            updateCartView();
        }
        
        // Guardar el carrito en localStorage
        saveCart();
        
        // Mostrar animación de notificación
        if (cartButton) {
            cartButton.classList.add('cart-notification');
            setTimeout(() => {
                cartButton.classList.remove('cart-notification');
            }, 300);
        }
    }

    // Función para aumentar la cantidad de un producto
    function increaseQuantity(id) {
        const item = cartItems.find(item => item.id === id);
        if (item) {
            item.quantity += 1;
            updateCartView();
            updateCartCount();
            saveCart();
        }
    }

    // Función para disminuir la cantidad de un producto
    function decreaseQuantity(id) {
        const item = cartItems.find(item => item.id === id);
        if (item) {
            if (item.quantity > 1) {
                item.quantity -= 1;
            } else {
                removeItem(id);
                return;
            }
            updateCartView();
            updateCartCount();
            saveCart();
        }
    }

    // Función para eliminar un producto del carrito
    function removeItem(id) {
        cartItems = cartItems.filter(item => item.id !== id);
        updateCartView();
        updateCartCount();
        saveCart();
    }

    // Función para actualizar el contador del carrito
    function updateCartCount() {
        const totalItems = cartItems.reduce((total, item) => total + item.quantity, 0);
        
        // Crear o actualizar el badge del carrito
        if (cartButton) {
            let cartBadge = document.querySelector('.cart-badge');
            
            if (!cartBadge) {
                cartBadge = document.createElement('span');
                cartBadge.className = 'cart-badge';
                cartButton.appendChild(cartBadge);
            }
            
            if (totalItems > 0) {
                cartBadge.textContent = totalItems;
                cartBadge.style.display = 'flex';
            } else {
                cartBadge.style.display = 'none';
            }
        }
    }

    // Funciones para guardar y cargar el carrito en localStorage
    function saveCart() {
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
    }

    function loadCart() {
        const saved = localStorage.getItem('cartItems');
        if (saved) {
            cartItems = JSON.parse(saved);
            updateCartCount();
        }
    }

    // Cargar el carrito al iniciar
    loadCart();

    // FUNCIONALIDAD PARA PÁGINA DE LISTADO (interfaz.php)
    // Añadir manejadores de eventos a los botones "Añadir al carrito" en la página de productos
    document.querySelectorAll('.product-card button').forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            if (productCard) {
                const productId = productCard.getAttribute('data-id');
                const productName = productCard.querySelector('h4 a').textContent;
                const priceText = productCard.querySelector('.price').textContent;
                const productPrice = parseSpanishPrice(priceText);
                
                const product = {
                    id: productId,
                    name: productName,
                    price: productPrice
                };
                
                addToCart(product);
                
                // Mostrar notificación
                const notification = document.createElement('div');
                notification.className = 'cart-notification-popup';
                notification.innerHTML = `
                    <div class="notification-content">
                        <i class="fas fa-check-circle"></i>
                        <p>Producto añadido al carrito</p>
                    </div>
                `;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 2000);
            }
        });
    });

    // FUNCIONALIDAD PARA PÁGINA DE DETALLE DE PRODUCTO (producto.php)
    // Verificar si estamos en la página de detalle de producto
    const addToCartBtn = document.getElementById('btn-comprar');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            // Obtener ID del producto de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');
            
            if (productId) {
                // Obtener nombre del producto
                const productName = document.querySelector('.page-title').textContent;
                
                // Obtener precio del producto
                const priceText = document.querySelector('.current-price').textContent;
                const productPrice = parseSpanishPrice(priceText);
                
                // Obtener cantidad seleccionada
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                const product = {
                    id: productId,
                    name: productName,
                    price: productPrice
                };
                
                addToCart(product, quantity);
                
                // Mostrar notificación
                const notification = document.createElement('div');
                notification.className = 'cart-notification-popup';
                notification.innerHTML = `
                    <div class="notification-content">
                        <i class="fas fa-check-circle"></i>
                        <p>Producto añadido al carrito</p>
                    </div>
                `;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 2000);
            }
        });
    }

    // Manejadores de eventos para los botones de navegación del carrito
    document.querySelector('.view-cart').addEventListener('click', function() {
        // Redirigir a la página del carrito
        window.location.href = 'carrito.php';
    });

    document.querySelector('.checkout').addEventListener('click', function() {
        // Redirigir a la página de checkout
        window.location.href = 'checkout.php';
    });
});