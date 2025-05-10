function validarCampo() { // Detenemos el envío del formulario
    let control = true;

    // Obtención de elementos (igual que tu versión original)
    let referencia = document.getElementById('referencia').value.trim();
    let errorReferencia = document.getElementById('errorReferencia');

    let nombre = document.getElementById('nombre').value;
    let errorNombre = document.getElementById('errorNombre');

    let descripcion = document.getElementById('descripcion').value;
    let errorDescripcion = document.getElementById('errorDescripcion');

    let precio = document.getElementById('precio').value;
    let errorPrecio = document.getElementById('errorPrecio');

    let stock = document.getElementById('stock').value;
    let errorStock = document.getElementById('errorStock');

    let peso = document.getElementById('peso').value;
    let errorPeso = document.getElementById('errorPeso');

    let alto = document.getElementById('alto').value;
    let errorAlto = document.getElementById('errorAlto');

    let ancho = document.getElementById('ancho').value;
    let errorAncho = document.getElementById('errorAncho');

    let largo = document.getElementById('largo').value;
    let errorLargo = document.getElementById('errorLargo');
    
    let imagenInput = document.getElementById('imagen'); 
    let archivo = imagenInput.files[0]; // Obtener el archivo correctamente
    let errorImagen = document.getElementById('errorImagen');

    let categoria = document.getElementById('categoria').value;
    let errorCategoria = document.getElementById('errorCategoria');

    let marca = document.getElementById('marca').value;
    let errorMarca = document.getElementById('errorMarca');

    // 🟢 **Validación de caracteres entre 2 y 50**
    if (referencia.length < 2 || referencia.length > 50) {
        console.log("Error: La referencia tiene menos de 2 o más de 50 caracteres.");
        errorReferencia.textContent = "Debe tener entre 2 y 50 caracteres.";
        control = false;
    }

    // 🔴 **Nueva validación para garantizar al menos una letra y un número**
    if (!/[a-zA-Z]/.test(referencia)) {
        console.log("Error: La referencia NO tiene letras.");
        errorReferencia.textContent = "Debe contener al menos una letra.";
        control = false;
    } 

    if (!/[0-9]/.test(referencia)) {
        console.log("Error: La referencia NO tiene números.");
        errorReferencia.textContent = "Debe contener al menos un número.";
        control = false;
    }

    // ✅ **Si la referencia es válida, limpia el mensaje de error**
    if (control) {
        console.log("Referencia válida.");
        errorReferencia.textContent = ""; // Limpia mensaje si está correcto
    }


    // Valida que el nombre tenga entre 2 y 100 caracteres
    if (nombre.length < 2 || nombre.length > 100) {
        errorNombre.textContent = "Número de caracteres inválido.";
        control = false;
    } else {
        errorNombre.textContent = ""; // Limpia el mensaje de error
    }

    // Valida que la descripción no exceda los 500 caracteres
    if (descripcion.length < 2 || descripcion.length > 500) {
        errorDescripcion.textContent = "Descripción debe tener entre 2 y 500 caracteres.";
        control = false;
    } else {
        errorDescripcion.textContent = "";
    }

    // Valida que el precio sea un número mayor que 0
    if (isNaN(precio) || precio <= 0) {
        errorPrecio.textContent = "El precio debe ser un número positivo.";
        control = false;
    } else {
        errorPrecio.textContent = "";
    }

    // Valida que el stock sea un número entero positivo (0 o mayor)
    if (isNaN(stock) || stock < 0 || !Number.isInteger(parseFloat(stock))) {
        errorStock.textContent = "El stock debe ser un número entero positivo.";
        control = false;
    } else {
        errorStock.textContent = "";
    }
        // Valida que el peso sea un número entero positivo (solo si no está vacío)
    if (peso === "" || isNaN(peso) || peso <= 0 || !Number.isInteger(parseFloat(peso))) {
        console.log("Error: Peso inválido.");
        document.getElementById('errorPeso').textContent = "El peso debe ser un número entero positivo.";
        control = false;
    }
    
    if (alto === "" || isNaN(alto) || alto <= 0 || !Number.isInteger(parseFloat(alto))) {
        console.log("Error: Alto inválido.");
        document.getElementById('errorAlto').textContent = "El alto debe ser un número entero positivo.";
        control = false;
    }
    
    if (ancho === "" || isNaN(ancho) || ancho <= 0 || !Number.isInteger(parseFloat(ancho))) {
        console.log("Error: Ancho inválido.");
        document.getElementById('errorAncho').textContent = "El ancho debe ser un número entero positivo.";
        control = false;
    }
    
    if (largo === "" || isNaN(largo) || largo <= 0 || !Number.isInteger(parseFloat(largo))) {
        console.log("Error: Largo inválido.");
        document.getElementById('errorLargo').textContent = "El largo debe ser un número entero positivo.";
        control = false;
    }
        
    // Validación de la imagen
    if (!archivo) {
        errorImagen.textContent = "Debe seleccionar una imagen.";
        control = false;
    } else {
        let extensionesPermitidas = /\.(jpg|jpeg|png|webp|gif)$/i;

        if (!extensionesPermitidas.test(archivo.name)) {
            errorImagen.textContent = "Formato de imagen no válido. Solo se permiten JPG, JPEG, PNG, WEBP o GIF.";
            control = false;
        } else if (archivo.size > 1000000) { // 1 MB máximo
            errorImagen.textContent = "El tamaño de la imagen excede los 1 MB.";
            control = false;
        } else {
            errorImagen.textContent = "";
        }
    }
    
    if (categoria == "0") {
        console.log("Error: No se ha seleccionado una categoría.");
        errorCategoria.textContent = "Debe seleccionar una categoría.";
        control = false;
    } else {
        errorCategoria.textContent = "";
    }
    
    // Validación de marca
    if (marca == "0") {
        console.log("Error: No se ha seleccionado una marca.");
        errorMarca.textContent = "Debe seleccionar una marca.";
        control = false;
    } else {
        errorMarca.textContent = "";
    }

    // Si no hay errores, envía el formulario
    if (control) {
        document.getElementById('formularioproductos').submit();
    }
}