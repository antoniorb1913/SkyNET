<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SkyNet | Donde el cielo toca el infinito</title>
    <link rel="stylesheet" href="skynet.css">
    <style>
        .hero {
            background: linear-gradient(to right, #3b81ff, #1e88e5);
            color: white;
            padding: 80px 30px;
            text-align: center;
        }
        .hero img {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
        }
        .hero a {
            background: white;
            color: #3b81ff;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
            margin: 0 10px;
        }
        .hero a:hover {
            background: #e1eaff;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding: 60px 30px;
            background-color: #f5f5f5;
        }
        .feature {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .feature img {
            width: 60px;
            margin-bottom: 15px;
        }
        .feature h3 {
            margin-bottom: 10px;
            color: #3b81ff;
        }
        .about, .catalogo-preview, .testimonios {
            padding: 60px 30px;
            text-align: center;
        }
        .about h2, .catalogo-preview h2, .testimonios h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .about p, .catalogo-preview p {
            max-width: 800px;
            margin: 0 auto 40px;
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }
        .btn-secondary {
            background-color: #3b81ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #2a6de0;
        }
        .testimonial {
            max-width: 500px;
            margin: 0 auto 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .testimonial p {
            font-style: italic;
        }
        .testimonial strong {
            display: block;
            margin-top: 10px;
            color: #3b81ff;
        }
        footer {
            background-color: #333;
            color: white;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="hero">
        <img src="../LOGO/Logo sin fondo.png" alt="SkyNet Logo">
        <h1>"SkyNet, donde el cielo toca el infinito"</h1>
        <p>Innovación, tecnología y soluciones inteligentes para tu vida digital.</p>
        <a href="contacto.php">Contáctanos</a>
        <a href="interfaz.php">Explorar Catálogo</a>
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

    <section class="catalogo-preview">
        <h2>Nuestros Productos</h2>
        <p>Explora una gama de productos diseñados con la más alta calidad y tecnología de vanguardia.</p>
        <a class="btn-secondary" href="interfaz.php">Ver Catálogo Completo</a>
    </section>

    <section class="about">
        <h2>Sobre Nosotros</h2>
        <p>
            En SkynetTech, fusionamos la inteligencia artificial con la ingeniería humana para brindar soluciones reales.
            Desde software personalizado hasta consultoría en transformación digital, estamos aquí para ayudarte a construir el mañana, hoy.
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
        <p>&copy; <?php echo date("Y"); ?> SkynetTech - Todos los derechos reservados</p>
    </footer>
</body>
</html>