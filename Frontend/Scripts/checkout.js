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
    
    // Función para formatear precio en formato español (1.234,56 €)
    function formatPrice(price) {
        return price.toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
    }
    
    // Verificar si hay productos en el carrito
    if (cartItems.length === 0) {
        window.location.href = 'carrito.php';
    } else {
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
        checkoutForm.appendChild(hiddenCartItems);
    }
    
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
                                <p>${address.codigo_postal}, ${address.ciudad}</p>
                                <p>${address.provincia}, ${address.pais}</p>
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
            // Actualizar resumen final
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
        // Ocultar todas las secciones
        sections.forEach(section => {
            section.style.display = 'none';
        });
        
        // Desmarcar todos los pasos
        steps.forEach(step => {
            step.classList.remove('active');
            step.classList.remove('completed');
        });
        
        // Mostrar la sección actual
        document.getElementById('section-' + stepNumber).style.display = 'block';
        
        // Marcar paso actual y anteriores
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
        
        // Validar nombre
        const nombre = document.getElementById('nombre');
        if (!nombre.value.trim()) {
            document.getElementById('nombre-error').style.display = 'block';
            nombre.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('nombre-error').style.display = 'none';
            nombre.classList.remove('error');
        }
        
        // Validar apellidos
        const apellidos = document.getElementById('apellidos');
        if (!apellidos.value.trim()) {
            document.getElementById('apellidos-error').style.display = 'block';
            apellidos.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('apellidos-error').style.display = 'none';
            apellidos.classList.remove('error');
        }
        
        // Validar email
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
        
        // Validar teléfono
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
        // Si hay una dirección guardada seleccionada, no es necesario validar los campos
        if (document.getElementById('direccion_guardada_id')) {
            return true;
        }
        
        let isValid = true;
        
        // Validar dirección
        const direccion = document.getElementById('direccion');
        if (!direccion.value.trim()) {
            document.getElementById('direccion-error').style.display = 'block';
            direccion.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('direccion-error').style.display = 'none';
            direccion.classList.remove('error');
        }
        
        // Validar código postal
        const codigoPostal = document.getElementById('codigo_postal');
        const cpPattern = /^\d{5}$/;
        if (!codigoPostal.value.trim() || !cpPattern.test(codigoPostal.value)) {
            document.getElementById('codigo_postal-error').style.display = 'block';
            codigoPostal.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('codigo_postal-error').style.display = 'none';
            codigoPostal.classList.remove('error');
        }
        
        // Validar ciudad
        const ciudad = document.getElementById('ciudad');
        if (!ciudad.value.trim()) {
            document.getElementById('ciudad-error').style.display = 'block';
            ciudad.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('ciudad-error').style.display = 'none';
            ciudad.classList.remove('error');
        }
        
        // Validar provincia
        const provincia = document.getElementById('provincia');
        if (!provincia.value.trim()) {
            document.getElementById('provincia-error').style.display = 'block';
            provincia.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('provincia-error').style.display = 'none';
            provincia.classList.remove('error');
        }
        
        // Validar país
        const pais = document.getElementById('pais');
        if (!pais.value) {
            document.getElementById('pais-error').style.display = 'block';
            pais.classList.add('error');
            isValid = false;
        } else {
            document.getElementById('pais-error').style.display = 'none';
            pais.classList.remove('error');
        }
        
        return isValid;
    }
    
    function validateStep3() {
        const metodoPago = document.getElementById('metodo_pago').value;
        
        if (!metodoPago) {
            alert('Por favor, selecciona un método de pago');
            return false;
        }
        
        // Validar campos específicos según el método de pago
        if (metodoPago === 'tarjeta') {
            let isValid = true;
            
            // Validar número de tarjeta
            const numTarjeta = document.getElementById('num_tarjeta');
            const numTarjetaPattern = /^\d{16}$/;
            // Eliminamos espacios para la validación
            const numTarjetaValue = numTarjeta.value.replace(/\s/g, '');
            
            if (!numTarjetaValue || !numTarjetaPattern.test(numTarjetaValue)) {
                document.getElementById('num_tarjeta-error').style.display = 'block';
                numTarjeta.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('num_tarjeta-error').style.display = 'none';
                numTarjeta.classList.remove('error');
            }
            
            // Validar fecha de expiración
            const expDate = document.getElementById('exp_date');
            const expDatePattern = /^(0[1-9]|1[0-2])\/\d{2}$/;
            
            if (!expDate.value || !expDatePattern.test(expDate.value)) {
                document.getElementById('exp_date-error').style.display = 'block';
                expDate.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('exp_date-error').style.display = 'none';
                expDate.classList.remove('error');
            }
            
            // Validar CVV
            const cvv = document.getElementById('cvv');
            const cvvPattern = /^\d{3}$/;
            
            if (!cvv.value || !cvvPattern.test(cvv.value)) {
                document.getElementById('cvv-error').style.display = 'block';
                cvv.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('cvv-error').style.display = 'none';
                cvv.classList.remove('error');
            }
            
            // Validar titular
            const titular = document.getElementById('titular');
            
            if (!titular.value.trim()) {
                document.getElementById('titular-error').style.display = 'block';
                titular.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('titular-error').style.display = 'none';
                titular.classList.remove('error');
            }
            
            return isValid;
        }
        
        return true;
    }
    
    // Actualizar resumen de confirmación
    function updateConfirmationDetails() {
        // Datos personales
        document.getElementById('confirm-nombre-apellidos').textContent = document.getElementById('nombre').value + ' ' + document.getElementById('apellidos').value;
        document.getElementById('confirm-email').textContent = document.getElementById('email').value;
        document.getElementById('confirm-telefono').textContent = document.getElementById('telefono').value;
        
        // Dirección
        let direccionText, ciudadCpText, provinciaPaisText;
        
        // Comprobar si hay una dirección guardada seleccionada
        const savedAddressSelected = document.querySelector('.saved-address.selected');
        
        if (savedAddressSelected) {
            const addressInfo = savedAddressSelected.querySelector('.address-info').innerHTML;
            const addressLines = addressInfo.split('<p>').map(line => line.replace('</p>', '').trim()).filter(Boolean);
            
            direccionText = addressLines[0] || '';
            ciudadCpText = addressLines[1] || '';
            provinciaPaisText = addressLines[2] || '';
        } else {
            direccionText = document.getElementById('direccion').value;
            ciudadCpText = document.getElementById('codigo_postal').value + ', ' + document.getElementById('ciudad').value;
            provinciaPaisText = document.getElementById('provincia').value + ', ' + document.getElementById('pais').options[document.getElementById('pais').selectedIndex].text;
        }
        
        document.getElementById('confirm-direccion').textContent = direccionText;
        document.getElementById('confirm-ciudad-cp').textContent = ciudadCpText;
        document.getElementById('confirm-provincia-pais').textContent = provinciaPaisText;
        
        // Método de pago
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
    
    // Formateo visual de campos
    const numTarjeta = document.getElementById('num_tarjeta');
    if (numTarjeta) {
        numTarjeta.addEventListener('input', function(e) {
            // Eliminar caracteres no numéricos
            let value = this.value.replace(/\D/g, '');
            
            // Formatear con espacios cada 4 dígitos
            if (value.length > 0) {
                value = value.match(/.{1,4}/g).join(' ');
            }
            
            // Limitar a 19 caracteres (16 dígitos + 3 espacios)
            this.value = value.substring(0, 19);
        });
    }
    
    const expDate = document.getElementById('exp_date');
    if (expDate) {
        expDate.addEventListener('input', function(e) {
            // Eliminar caracteres no numéricos
            let value = this.value.replace(/\D/g, '');
            
            // Agregar slash después de los primeros 2 dígitos
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            this.value = value;
        });
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