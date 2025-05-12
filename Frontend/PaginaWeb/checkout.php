<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SkyNET</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Estilos/checkout.css">
</head>
<body>
    <div class="checkout-container">
        <button class="btn-back" onclick="window.location.href='interfaz.php'">
            <i class="fas fa-arrow-left"></i> Volver al carrito
        </button>
        
        <div class="checkout-header">
            <h1 class="checkout-title">Completar compra</h1>
            <p class="checkout-subtitle">Estás a pocos pasos de finalizar tu pedido</p>
        </div>
        
        <div class="checkout-steps">
            <div class="step active" id="step-1">
                <div class="step-number">1</div>
                <div class="step-title">Datos personales</div>
            </div>
            <div class="step" id="step-2">
                <div class="step-number">2</div>
                <div class="step-title">Dirección de envío</div>
            </div>
            <div class="step" id="step-3">
                <div class="step-number">3</div>
                <div class="step-title">Método de pago</div>
            </div>
            <div class="step" id="step-4">
                <div class="step-number">4</div>
                <div class="step-title">Confirmación</div>
            </div>
        </div>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <form id="checkout-form" method="post" action="procesar_pedido.php">
                    <!-- Paso 1: Datos personales -->
                    <div class="form-section" id="section-1">
                        <h2 class="form-title"><i class="fas fa-user"></i> Datos personales</h2>
                        
                        <?php if (!isset($_SESSION['cliente_id'])): ?>
                            <div class="form-group">
                                <p>¿Ya tienes una cuenta? <a href="login.php?redirect=checkout.php">Inicia sesión</a></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="col form-group">
                                <label for="nombre" class="required-field">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                    <?php if(isset($_SESSION['cliente_id'])) echo 'value="'.$_SESSION['nombre'].'"'; ?>>
                                <div class="error-message" id="nombre-error">Por favor ingresa tu nombre</div>
                            </div>
                            <div class="col form-group">
                                <label for="apellidos" class="required-field">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required
                                    <?php if(isset($_SESSION['cliente_id'])) echo 'value="'.$_SESSION['apellidos'].'"'; ?>>
                                <div class="error-message" id="apellidos-error">Por favor ingresa tus apellidos</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                <?php if(isset($_SESSION['cliente_id'])) echo 'value="'.$_SESSION['email'].'"'; ?>>
                            <div class="error-message" id="email-error">Por favor ingresa un email válido</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono" class="required-field">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required
                                <?php if(isset($_SESSION['cliente_id'])) echo 'value="'.$_SESSION['telefono'].'"'; ?>>
                            <div class="error-message" id="telefono-error">Por favor ingresa un teléfono válido</div>
                        </div>
                        
                        <button type="button" class="btn-checkout" id="btn-continue-1">Continuar</button>
                    </div>
                    
                    <!-- Paso 2: Dirección de envío -->
                    <div class="form-section" id="section-2" style="display: none;">
                        <h2 class="form-title"><i class="fas fa-map-marker-alt"></i> Dirección de envío</h2>
                        
                        <?php if(isset($_SESSION['cliente_id'])): ?>
                            <div class="address-list" id="saved-addresses">
                                <!-- Las direcciones guardadas se cargarán aquí -->
                            </div>
                            
                            <div class="form-group">
                                <button type="button" class="btn-checkout" id="btn-new-address">Añadir nueva dirección</button>
                            </div>
                        <?php endif; ?>
                        
                        <div id="new-address-form" <?php if(isset($_SESSION['cliente_id'])) echo 'style="display: none;"'; ?>>
                            <div class="form-group">
                                <label for="direccion" class="required-field">Dirección completa</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required placeholder="Ej: Calle Ejemplo 123, 28001 Madrid, Madrid, España">
                                <div class="error-message" id="direccion-error">Por favor ingresa tu dirección completa</div>
                            </div>
                            
                            <?php if(isset($_SESSION['cliente_id'])): ?>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="guardar_direccion" name="guardar_direccion">
                                        <label for="guardar_direccion">Guardar esta dirección para futuras compras</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <button type="button" class="btn-back" id="btn-back-1">Volver</button>
                            <button type="button" class="btn-checkout" id="btn-continue-2">Continuar</button>
                        </div>
                    </div>
                    
                    <!-- Paso 3: Método de pago -->
                    <div class="form-section" id="section-3" style="display: none;">
                        <h2 class="form-title"><i class="fas fa-credit-card"></i> Método de pago</h2>
                        
                        <div class="payment-methods">
                            <div class="payment-method" data-method="tarjeta">
                                <div class="payment-method-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Tarjeta de crédito/débito</div>
                                    <div class="payment-method-desc">Pago seguro con VISA, Mastercard o American Express</div>
                                </div>
                            </div>
                            
                            <div class="payment-details" id="tarjeta-details">
                                <p>Los pagos con tarjeta se procesan de forma segura a través de nuestra pasarela de pago externa. Serás redirigido al confirmar el pedido.</p>
                            </div>
                            
                            <div class="payment-method" data-method="paypal">
                                <div class="payment-method-icon">
                                    <i class="fab fa-paypal"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">PayPal</div>
                                    <div class="payment-method-desc">Pago rápido y seguro con tu cuenta de PayPal</div>
                                </div>
                            </div>
                            
                            <div class="payment-method" data-method="transferencia">
                                <div class="payment-method-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">Transferencia bancaria</div>
                                    <div class="payment-method-desc">Realiza el pago mediante transferencia a nuestra cuenta</div>
                                </div>
                            </div>
                            
                            <div class="payment-details" id="transferencia-details">
                                <div class="form-group">
                                    <p>Por favor, realiza una transferencia a la siguiente cuenta bancaria:</p>
                                    <p><strong>Banco:</strong> Banco SkyNET</p>
                                    <p><strong>IBAN:</strong> ES12 3456 7890 1234 5678 9012</p>
                                    <p><strong>Titular:</strong> SkyNET Technologies S.L.</p>
                                    <p><strong>Concepto:</strong> Tu número de pedido (se generará tras confirmar)</p>
                                    <p>Tu pedido se procesará una vez confirmemos el pago.</p>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="metodo_pago" name="metodo_pago" value="">
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <button type="button" class="btn-back" id="btn-back-2">Volver</button>
                            <button type="button" class="btn-checkout" id="btn-continue-3">Continuar</button>
                        </div>
                    </div>
                    
                    <!-- Paso 4: Confirmación -->
                    <div class="form-section" id="section-4" style="display: none;">
                        <h2 class="form-title"><i class="fas fa-check-circle"></i> Confirmar pedido</h2>
                        
                        <div class="confirmation-details">
                            <div class="confirmation-section">
                                <h3>Datos personales</h3>
                                <p id="confirm-nombre-apellidos"></p>
                                <p id="confirm-email"></p>
                                <p id="confirm-telefono"></p>
                            </div>
                            
                            <div class="confirmation-section">
                                <h3>Dirección de envío</h3>
                                <p id="confirm-direccion"></p>
                            </div>
                            
                            <div class="confirmation-section">
                                <h3>Método de pago</h3>
                                <p id="confirm-pago"></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" id="terminos" name="terminos" required>
                                <label for="terminos">He leído y acepto los <a href="terminos.php" target="_blank">términos y condiciones</a> y la <a href="privacidad.php" target="_blank">política de privacidad</a></label>
                                <div class="error-message" id="terminos-error">Debes aceptar los términos y condiciones</div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <button type="button" class="btn-back" id="btn-back-3">Volver</button>
                            <button type="submit" class="btn-checkout" id="btn-finalizar">Finalizar compra</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="checkout-summary">
                <h2 class="summary-title">Resumen del pedido</h2>
                
                <div class="cart-items" id="checkout-items">
                    <!-- Aquí se cargarán los productos del carrito -->
                </div>
                
                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span id="checkout-subtotal">€0,00</span>
                    </div>
                    <div class="summary-line">
                        <span>Gastos de envío</span>
                        <span id="checkout-shipping">€4,99</span>
                    </div>
                    <div class="summary-line">
                        <span>IVA (21%)</span>
                        <span id="checkout-tax">€0,00</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <span id="checkout-total">€0,00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../Scripts/checkout.js"></script>
</body>
</html>