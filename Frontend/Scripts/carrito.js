// carrito.js - Sistema de carrito de compras para SkyNet

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar carrito desde localStorage o crear uno nuevo
    let carrito = JSON.parse(localStorage.getItem('skynet_carrito')) || [];
    
    // Actualizar contador al cargar la página
    actualizarContadorCarrito();
    
    // Asignar evento de clic a todos los botones "Comprar"
    const botonesComprar = document.querySelectorAll('.product-card button');
    botonesComprar.forEach(boton => {
      boton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Obtener información del producto
        const productoCard = this.closest('.product-card');
        const productoId = productoCard.dataset.id || productoCard.getAttribute('data-id');
        const productoNombre = productoCard.querySelector('h4').innerText;
        
        // Corregir la interpretación del precio
        let precioTexto = productoCard.querySelector('.price').innerText.replace('€', '').trim();
        // Reemplazar puntos de miles y usar coma como decimal
        precioTexto = precioTexto.replace(/\./g, '').replace(',', '.');
        const productoPrecio = parseFloat(precioTexto);
        
        const productoImagen = productoCard.querySelector('img').src;
        
        // Buscar si el producto ya está en el carrito
        const productoExistente = carrito.find(item => item.id === productoId);
        
        if (productoExistente) {
          // Incrementar cantidad si ya existe
          productoExistente.cantidad += 1;
          productoExistente.subtotal = productoExistente.cantidad * productoExistente.precio;
        } else {
          // Añadir nuevo producto al carrito
          carrito.push({
            id: productoId,
            nombre: productoNombre,
            precio: productoPrecio,
            imagen: productoImagen,
            cantidad: 1,
            subtotal: productoPrecio
          });
        }
        
        // Guardar carrito en localStorage
        guardarCarrito();
        
        // Actualizar contador visual
        actualizarContadorCarrito();
        
        // Mostrar notificación
        mostrarNotificacion(productoNombre);
      });
    });
    
    // Añadir evento de clic al icono del carrito para mostrar/ocultar el minicarrito
    const iconoCarrito = document.querySelector('.icons a:nth-child(3)');
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
    
    // Funciones auxiliares
    function actualizarContadorCarrito() {
      const contador = carrito.reduce((total, item) => total + item.cantidad, 0);
      
      // Buscar o crear el elemento contador en el icono del carrito
      let contadorElement = document.querySelector('.cart-counter');
      
      if (!contadorElement) {
        contadorElement = document.createElement('span');
        contadorElement.className = 'cart-counter';
        const iconoCarrito = document.querySelector('.icons a:nth-child(3)');
        iconoCarrito.appendChild(contadorElement);
      }
      
      // Actualizar contador y visibilidad
      if (contador > 0) {
        contadorElement.textContent = contador;
        contadorElement.style.display = 'flex';
      } else {
        contadorElement.style.display = 'none';
      }
    }
    
    function guardarCarrito() {
      localStorage.setItem('skynet_carrito', JSON.stringify(carrito));
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
          guardarCarrito();
          
          // Actualizar contador
          actualizarContadorCarrito();
          
          // Recrear minicarrito
          document.body.removeChild(minicarrito);
          crearMinicarrito();
        });
      });
    }
  });