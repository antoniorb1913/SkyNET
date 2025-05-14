<!DOCTYPE html>
<html lang="es">
<style>
        .inst, .pint {
        width: 1em;
        height: auto;
        vertical-align: middle;
        }
        .redes {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .redes p {
            flex: 1; /* Ocupa el espacio disponible */
            text-align: center; /* Centra el texto */
        }
</style>
<head>
    <meta charset="UTF-8">
    <title>SkyNet | Donde el cielo toca el infinito</title>
    <link rel="stylesheet" href="Estilos/index.css"/>
</head>
<body>
    <header class="hero">
        <img src="LOGO/logoblanco.png" alt="SkyNet Logo">
        <h1>"SkyNet, donde el cielo toca el infinito"</h1>
        <p>Innovación, tecnología y soluciones inteligentes para tu vida digital.</p>
        <a href="PaginaWeb/soporte.php">Contáctanos</a>
        <a href="PaginaWeb/interfaz.php">Explorar Catálogo</a>
    </header>

    <section class="features">
        <div class="feature">
            <img src="https://cdn-icons-png.flaticon.com/512/189/189664.png" alt="Innovación">
            <h3>Innovación Constante</h3>
            <p>Nos reinventamos todos los días para estar un paso adelante en soluciones tecnológicas.</p>
        </div>
        <div class="feature">
            <img src="https://cdn-icons-png.flaticon.com/512/2089/2089792.png" alt="Seguridad">
            <h3>Seguridad Total</h3>
            <p>Protegemos tus datos con los más altos estándares de ciberseguridad.</p>
        </div>
        <div class="feature">
            <img src="https://cdn-icons-png.flaticon.com/512/3039/3039439.png" alt="Soporte 24/7">
            <h3>Soporte 24/7</h3>
            <p>Estamos contigo en cada paso. Asistencia inmediata y personalizada.</p>
        </div>
    </section>
    <br>
    <br>
    <section class="about">
        <h2>Sobre Nosotros</h2>
        <p>
        Somos tu tienda especializada en tecnología, ofreciendo los mejores dispositivos móviles, portátiles, tablets y consolas con garantía de calidad. Combinamos productos de vanguardia con precios competitivos y asesoramiento experto para que encuentres exactamente lo que necesitas, llevando la innovación tecnológica a tus manos con el respaldo de un servicio confiable.
        </p>
    </section>

    <section class="testimonios">
        <h2>Lo que dicen nuestros clientes</h2>
        <div class="testimonial">
            <p>“El servicio de SkyNet ha transformado por completo la forma en que manejamos nuestro negocio, implementando muchas de sus tecnologias de alta calidad. ¡Increíble!”</p>
            <strong>- Laura M., CEO de FutureCorp</strong>
        </div>
        <div class="testimonial">
            <p>“Tecnología de punta, atención personalizada y resultados reales. ¡Gracias SkyNet!”</p>
            <strong>- Miguel R., Ingeniero de Sistemas</strong>
        </div>
    </section>

    <footer>
        <div class="redes">
        <p>&copy; <?php echo date("Y"); ?> SkynetTech - Todos los derechos reservados</p>
        <a href="https://www.instagram.com/skynet.oficiall/" > <img src='/Backend/Productos/imagenes/instagram.png' class="inst"></a>
        <a href="https://es.pinterest.com/slskynet/"> <img src='/Backend/Productos/imagenes/pinterest.png' class="pint"></a>
        </div>
    </footer>
</body>
</html>