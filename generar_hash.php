<?php
$contraseña = '@dminSkN'; // Contraseña original
$hash = password_hash($contraseña, PASSWORD_BCRYPT); // Encripta la contraseña
echo $hash; // Muestra el hash generado para insertarlo en la base de datos

/* Luego meteis el usuario admin donde la contraseña sale simbolos y numeros y letras ahi es lo que teneis
que poner lo que ha generado este php que es la contraseña @dminSkN encriptada, luego para 
iniciar sesion usuario admin contraseña @dminSkN

INSERT INTO admin (nombre, email, contrasena, created_at) 
VALUES ('admin', 'admin@skynet.com', '$2y$10$6p70EFEBldLCTdZArp99SOpm8HM9lZFIyD6niJwAYUnRfXH9FstlG', NOW());*/
?>




