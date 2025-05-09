<?php
$contraseña_ingresada = '@dminSkN';
$hash_guardado = '$2y$10$vZEYPQ/nb.5DHe2fA96Dr.0sn7JugubiKmtzv9Jcm7aJzYxBj3Ji.'; // Copia el hash exacto de la base de datos

if (password_verify($contraseña_ingresada, $hash_guardado)) {
    echo "¡Contraseña verificada correctamente!";
} else {
    echo "Error: La contraseña no coincide.";
}
?>
