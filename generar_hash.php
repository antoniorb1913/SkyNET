<?php
$contraseña = '@dminSkN'; // Contraseña original
$hash = password_hash($contraseña, PASSWORD_BCRYPT); // Encripta la contraseña
echo $hash; // Muestra el hash generado para insertarlo en la base de datos

/* Luego meteis el usuario admin donde sale simbolos y numeros y letras ya es lo que teneis
que poner lo que ha generado este php que es la contraseña @dminSkN encriptada, luego para 
iniciar sesion usuario admin contraseña @dminSkN

INSERT INTO admin (nombre, email, contrasena, created_at) 
VALUES ('admin', 'admin@skynet.com', '$2y$10$OHMD0hhgo36Ky9VyAWD2C.GvVCNowtm6JnALe3hZIt2ZNsZitICya', NOW());*/
?>




