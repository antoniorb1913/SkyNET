function validarCampo() {

    let control = true;

    let referencia = document.getElementById('referencia').value;
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

    let imagen = document.getElementById('imagen').value;
    let errorImagen = document.getElementById('errorImagen');

    
    // Valida que la referencia tenga entre 2 y 50 caracteres
    if (referencia.length < 2 || referencia.length > 50) {
        errorReferencia.textContent = "Número de caracteres inválido.";
        control = false;
    }

    // Valida que el nombre tenga entre 2 y 100 caracteres
    if (nombre.length < 2 || nombre.length > 100) {
        errorNombre.textContent = "Número de caracteres inválido.";
        control = false;
    }

    // Valida que la descripción no exceda los 500 caracteres
    if (descripcion.length > 500) {
        document.getElementById('errorDescripcion').textContent = "La descripción es muy larga (máximo 500 letras).";
        control = false;
    } else {
        document.getElementById('errorDescripcion').textContent = "";
    }

    // Valida que el precio sea un número mayor que 0
    if (isNaN(precio) || precio <= 0) {
        document.getElementById('errorPrecio').textContent = "El precio debe ser un número positivo.";
        control = false;
    } else {
        document.getElementById('errorPrecio').textContent = "";
    }

    // Valida que el stock sea un número entero positivo (0 o mayor)
    if (isNaN(stock) || stock < 0 || !Number.isInteger(parseFloat(stock))) {
        document.getElementById('errorStock').textContent = "El stock debe ser un número entero positivo.";
        control = false;
    } else {
        document.getElementById('errorStock').textContent = "";
    }

    // Valida que el alto sea un número positivo (solo si no está vacío)
    if (alto !== "" && (isNaN(alto) || alto <= 0)) {
        document.getElementById('errorAlto').textContent = "El alto debe ser un número positivo.";
        control = false;
    } else {
        document.getElementById('errorAlto').textContent = "";
    }

    // Valida que el ancho sea un número positivo (solo si no está vacío)
    if (ancho !== "" && (isNaN(ancho) || ancho <= 0)) {
        document.getElementById('errorAncho').textContent = "El ancho debe ser un número positivo.";
        control = false;
    } else {
        document.getElementById('errorAncho').textContent = "";
    }

    // Valida que el largo sea un número positivo (solo si no está vacío)
    if (largo !== "" && (isNaN(largo) || largo <= 0)) {
        document.getElementById('errorLargo').textContent = "El largo debe ser un número positivo.";
        control = false;
    } else {
        document.getElementById('errorLargo').textContent = "";
    }

    // Valida que el peso sea un número entero positivo (solo si no está vacío)
    if (peso !== "" && (isNaN(peso) || peso <= 0 || !Number.isInteger(parseFloat(peso)))) {
        document.getElementById('errorPeso').textContent = "El peso debe ser un número entero positivo.";
        control = false;
    } else {
        document.getElementById('errorPeso').textContent = "";
    }
    
    if (imagen === "") {
        errorImagen.textContent = "Debe seleccionar una imagen.";
        control = false;
    } else {
        let extensionesPermitidas = /(\.jpg|\.jpeg|\.png|\.webp)$/i;
        if (!extensionesPermitidas.exec(imagen)) {
            errorImagen.textContent = "Formato de imagen no válido. Solo se permiten JPG, JPEG, PNG o WEBP.";
            control = false;
        } else {
            errorImagen.textContent = "";
        }
    }
    
    if (control == true){
        formularioproductos.submit(document.getElementById('formularioproductos'));
        
    } else {

        
        return;

    }

}
