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

    let alto = document.getElementById('alto').value;
    let errorAlto = document.getElementById('errorAlto');

    let ancho = document.getElementById('ancho').value;
    let errorAncho = document.getElementById('errorAncho');

    let largo = document.getElementById('largo').value;
    let errorLargo = document.getElementById('errorLargo');

    let peso = document.getElementById('peso').value;
    let errorPeso = document.getElementById('errorPeso');

    let imagenInput = document.getElementById('imagen'); 
    let archivo = imagenInput.files[0]; // Obtener el archivo correctamente
    let errorImagen = document.getElementById('errorImagen');

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
    if (descripcion.length > 500) {
        errorDescripcion.textContent = "La descripción es muy larga (máximo 500 letras).";
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

    // Valida que el alto sea un número positivo (solo si no está vacío)
    if (alto !== "" && (isNaN(alto) || alto <= 0)) {
        errorAlto.textContent = "El alto debe ser un número positivo.";
        control = false;
    } else {
        errorAlto.textContent = "";
    }

    // Valida que el ancho sea un número positivo (solo si no está vacío)
    if (ancho !== "" && (isNaN(ancho) || ancho <= 0)) {
        errorAncho.textContent = "El ancho debe ser un número positivo.";
        control = false;
    } else {
        errorAncho.textContent = "";
    }

    // Valida que el largo sea un número positivo (solo si no está vacío)
    if (largo !== "" && (isNaN(largo) || largo <= 0)) {
        errorLargo.textContent = "El largo debe ser un número positivo.";
        control = false;
    } else {
        errorLargo.textContent = "";
    }

    // Valida que el peso sea un número entero positivo (solo si no está vacío)
    if (peso !== "" && (isNaN(peso) || peso <= 0 || !Number.isInteger(parseFloat(peso)))) {
        errorPeso.textContent = "El peso debe ser un número entero positivo.";
        control = false;
    } else {
        errorPeso.textContent = "";
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

    // Si no hay errores, envía el formulario
    if (control) {
        document.getElementById('formularioproductos').submit();
    }
}