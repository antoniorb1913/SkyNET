document.addEventListener('DOMContentLoaded', function() {
    // Elementos principales
    const checkoutForm = document.getElementById('checkout-form');
    const sections = document.querySelectorAll('.form-section');
    const steps = document.querySelectorAll('.step');
    
    // Botones de navegación entre pasos
    const btnContinue1 = document.getElementById('btn-continue-1');
    const btnContinue2 = document.getElementById('btn-continue-2');
    const btnContinue3 = document.getElementById('btn-continue-3');
    const btnBack1 = document.getElementById('btn-back-1');
    const btnBack2 = document.getElementById('btn-back-2');
    const btnBack3 = document.getElementById('btn-back-3');
    const btnNewAddress = document.getElementById('btn-new-address');
    const btnFinalizar = document.getElementById('btn-finalizar');
    
    // Cargar datos del carrito desde localStorage
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    const checkoutItemsContainer = document.getElementById('checkout-items');
    
    // Función para formatear precio en formato español
    function formatPrice(price) {
        return price.toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
    }
    
    // Verificar si hay productos en el carrito
    if (cartItems.length === 0) {
        alert('El carrito está vacío');
        window.location.href = 'carrito.php';
        return;
    }
    
    // Validar stock antes de mostrar el carrito
    fetch('validar_carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cartItems)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error en el carrito: ' + data.message);
            window.location.href = 'carrito.php';
            return;
        }
        
        // Mostrar productos en el resumen
        let subtotal = 0;
        
        checkoutItemsContainer.innerHTML = '';
        
        cartItems.forEach(item => {
            const itemSubtotal = item.price * item.quantity;
            subtotal += itemSubtotal;
            
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.innerHTML = `
                <div class="item-info">
                    <div class="item-name">${item.name}</div>
                    <div class="item-quantity">Cantidad: ${item.quantity}</div>
                </div>
                <div class="item-price">${formatPrice(itemSubtotal)}</div>
            `;
            
            checkoutItemsContainer.appendChild(itemElement);
        });
        
        // Calcular totales
        const shipping = 4.99;
        const taxRate = 0.21;
        const tax = subtotal * taxRate;
        const total = subtotal + shipping + tax;
        
        // Actualizar resumen
        document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);
        document.getElementById('checkout-shipping').textContent = formatPrice(shipping);
        document.getElementById('checkout-tax').textContent = formatPrice(tax);
        document.getElementById('checkout-total').textContent = formatPrice(total);
        
        // Crear campo oculto con los productos del carrito
        const hiddenCartItems = document.createElement('input');
        hiddenCartItems.type = 'hidden';
        hiddenCartItems.name = 'cart_items';
        hiddenCartItems.value = JSON.stringify(cartItems);
        console.log('Cart Items enviados:', cartItems);
        checkoutForm.appendChild(hiddenCartItems);
    })
    .catch(error => {
        console.error('Error al validar carrito:', error);
        alert('Error al cargar el carrito');
        window.location.href = 'carrito.php';
    });
    
    // Cargar direcciones guardadas si el usuario está logueado
    const savedAddressesContainer = document.getElementById('saved-addresses');
    
    if (savedAddressesContainer) {
        fetch('obtener_direcciones.php')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    savedAddressesContainer.innerHTML = '<h3>Direcciones guardadas</h3>';
                    
                    data.forEach(address => {
                        const addressElement = document.createElement('div');
                        addressElement.className = 'saved-address';
                        addressElement.setAttribute('data-id', address.id);
                        addressElement.innerHTML = `
                            <div class="address-info">
                                <p>${address.direccion}</p>
                            </div>
                            <div class="address-select">
                                <button type="button" class="btn-select-address">Seleccionar</button>
                            </div>
                        `;
                        
                        savedAddressesContainer.appendChild(addressElement);
                    });
                    
                    // Agregar evento a los botones de selección de dirección
                    const selectButtons = document.querySelectorAll('.btn-select-address');
                    selectButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const addressDiv = this.closest('.saved-address');
                            
                            // Quitar selección de otras direcciones
                            document.querySelectorAll('.saved-address').forEach(addr => {
                                addr.classList.remove('selected');
                            });
                            
                            // Marcar esta dirección como seleccionada
                            addressDiv.classList.add('selected');
                            
                            // Crear input oculto con la dirección seleccionada
                            let dirInput = document.getElementById('direccion_guardada_id');
                            if (!dirInput) {
                                dirInput = document.createElement('input');
                                dirInput.type = 'hidden';
                                dirInput.id = 'direccion_guardada_id';
                                dirInput.name = 'direccion_guardada_id';
                                checkoutForm.appendChild(dirInput);
                            }
                            
                            dirInput.value = addressDiv.getAttribute('data-id');
                            
                            // Ocultar formulario de nueva dirección
                            document.getElementById('new-address-form').style.display = 'none';
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar direcciones:', error);
            });
    }
    
    // Navegación entre pasos
    btnContinue1.addEventListener('click', function() {
        if (validateStep1()) {
            goToStep(2);
        }
    });
    
    btnContinue2.addEventListener('click', function() {
        if (validateStep2()) {
            goToStep(3);
        }
    });
    
    btnContinue3.addEventListener('click', function() {
        if (validateStep3()) {
            updateConfirmationDetails();
            goToStep(4);
        }
    });
    
    btnBack1.addEventListener('click', function() {
        goToStep(1);
    });
    
    btnBack2.addEventListener('click', function() {
        goToStep(2);
    });
    
    btnBack3.addEventListener('click', function() {
        goToStep(3);
    });
    
    if (btnNewAddress) {
        btnNewAddress.addEventListener('click', function() {
            document.getElementById('new-address-form').style.display = 'block';
            
            // Quitar selección de direcciones guardadas
            document.querySelectorAll('.saved-address').forEach(addr => {
                addr.classList.remove('selected');
            });
            
            // Eliminar el input oculto de dirección guardada
            const dirInput = document.getElementById('direccion_guardada_id');
            if (dirInput) {
                dirInput.remove();
            }
        });
    }
    
    // Selección de método de pago
    const paymentMethods = document.querySelectorAll('.payment-method');
    const methodInput = document.getElementById('metodo_pago');
    
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            const paymentMethod = this.getAttribute('data-method');
            
            // Quitar selección anterior
            paymentMethods.forEach(m => {
                m.classList.remove('selected');
            });
            
            // Ocultar todos los detalles de pago
            document.querySelectorAll('.payment-details').forEach(detail => {
                detail.style.display = 'none';
            });
            
            // Seleccionar este método
            this.classList.add('selected');
            methodInput.value = paymentMethod;
            
            // Mostrar detalles si corresponde
            const detailsElement = document.getElementById(paymentMethod + '-details');
            if (detailsElement) {
                detailsElement.style.display = 'block';
            }
        });
    });
    
    // Función para ir a un paso específico
    function goToStep(stepNumber) {
        sections.forEach(section => {
            section.style.display = 'none';
        });
        
        steps.forEach(step => {
            step.classList.remove('active');
            step.classList.remove('completed');
        });
        
        document.getElementById('section-' + stepNumber).style.display = 'block';
        
        for (let i = 1; i <= 4; i++) {
            const step = document.getElementById('step-' + i);
            if (i < stepNumber) {
                step.classList.add('completed');
            } else if (i === stepNumber) {
                step.classList.add('active');
            }
        }
    }
    
    // Validación de cada paso
    function validateStep1() {
        let isValid = true;
        
        const nombre = document.getElementById('nombre');
        if (!nombre.value.trim()) {
            document.getElementById('nombre-error').style.display = 'block';
            nombre.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('nombre-error').style.display = 'none';
            nombre.classList.remove('error');
        }
        
        const apellidos = document.getElementById('apellidos');
        if (!apellidos.value.trim()) {
            document.getElementById('apellidos-error').style.display = 'block';
            apellidos.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('apellidos-error').style.display = 'none';
            apellidos.classList.remove('error');
        }
        
        const email = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim() || !emailPattern.test(email.value)) {
            document.getElementById('email-error').style.display = 'block';
            email.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('email-error').style.display = 'none';
            email.classList.remove('error');
        }
        
        const telefono = document.getElementById('telefono');
        const telefonoPattern = /^\d{9}$/;
        if (!telefono.value.trim() || !telefonoPattern.test(telefono.value)) {
            document.getElementById('telefono-error').style.display = 'block';
            telefono.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('telefono-error').style.display = 'none';
            telefono.classList.remove('error');
        }
        
        return isValid;
    }
    
    function validateStep2() {
        if (document.getElementById('direccion_guardada_id')) {
            return true;
        }
        
        let isValid = true;
        
        const direccion = document.getElementById('direccion');
        if (!direccion.value.trim()) {
            document.getElementById('direccion-error').style.display = 'block';
            direccion.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('direccion-error').style.display = 'none';
            direccion.classList.remove('error');
        }
        
        return isValid;
    }
    
    function validateStep3() {
        const metodoPago = document.getElementById('metodo_pago').value;
        
        if (!metodoPago) {
            alert('Por favor, selecciona un método de pago');
            return false;
        }
        
        return true;
    }
    
    // Actualizar resumen de confirmación
    function updateConfirmationDetails() {
        document.getElementById('confirm-nombre-apellidos').textContent = document.getElementById('nombre').value + ' ' + document.getElementById('apellidos').value;
        document.getElementById('confirm-email').textContent = document.getElementById('email').value;
        document.getElementById('confirm-telefono').textContent = document.getElementById('telefono').value;
        
        let direccionText = '';
        const savedAddressSelected = document.querySelector('.saved-address.selected');
        
        if (savedAddressSelected) {
            direccionText = savedAddressSelected.querySelector('.address-info p').textContent;
        } else {
            direccionText = document.getElementById('direccion').value;
        }
        
        document.getElementById('confirm-direccion').textContent = direccionText;
        
        const metodoPago = document.getElementById('metodo_pago').value;
        let metodoPagoText = '';
        switch (metodoPago) {
            case 'tarjeta':
                metodoPagoText = 'Tarjeta de crédito/débito';
                break;
            case 'paypal':
                metodoPagoText = 'PayPal';
                break;
            case 'transferencia':
                metodoPagoText = 'Transferencia bancaria';
                break;
        }
        
        document.getElementById('confirm-pago').textContent = metodoPagoText;
    }
    
    // Validar el formulario antes de enviar
    checkoutForm.addEventListener('submit', function(e) {
        if (!document.getElementById('terminos').checked) {
            e.preventDefault();
            document.getElementById('terminos-error').style.display = 'block';
        } else {
            document.getElementById('terminos-error').style.display = 'none';
        }
    });
});