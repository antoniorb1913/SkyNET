-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: skynet
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','admin@skynet.com','$2y$10$OHMD0hhgo36Ky9VyAWD2C.GvVCNowtm6JnALe3hZIt2ZNsZitICya','2025-05-10 03:51:50');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrito`
--

DROP TABLE IF EXISTS `carrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int DEFAULT NULL,
  `metodoPago_id` int DEFAULT NULL,
  `direccion_id` int DEFAULT NULL,
  `gasto_envio` float(4,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `metodoPago_id` (`metodoPago_id`),
  KEY `direccion_id` (`direccion_id`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`metodoPago_id`) REFERENCES `metodo_pago` (`id`),
  CONSTRAINT `carrito_ibfk_3` FOREIGN KEY (`direccion_id`) REFERENCES `direcciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito`
--

LOCK TABLES `carrito` WRITE;
/*!40000 ALTER TABLE `carrito` DISABLE KEYS */;
INSERT INTO `carrito` VALUES (1,5,1,1,4.99,NULL,NULL),(2,5,1,2,4.99,NULL,NULL),(3,5,2,3,4.99,NULL,NULL),(4,11,2,4,4.99,NULL,NULL),(5,5,3,5,4.99,NULL,NULL);
/*!40000 ALTER TABLE `carrito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Ordenadores'),(2,'Moviles'),(3,'Tablets'),(4,'Portatiles'),(5,'Consolas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `nick` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (5,'Antonio','Rodríguez Blaya',NULL,'antoniorb1913@gmail.com','$2y$10$b8xFuspQEvowG0SzEklVHezxjv6Ai2uwor.fGNP1CWf/3XNHZZF4e',NULL,'pollo','2025-05-13 00:28:59','2025-05-13 00:28:59'),(6,'Juan','Pérez Gómez','123456789','juan.perez@email.com','clave123','1990-05-15','juanito','2025-05-14 02:30:06','2025-05-14 02:30:06'),(7,'Ana','Martínez Ruiz','987654321','ana.martinez@email.com','segura456','1985-10-22','anam','2025-05-14 02:30:06','2025-05-14 02:30:06'),(8,'Carlos','Sánchez López',NULL,'carlos.sanchez@email.com','password789','1993-07-30','charly','2025-05-14 02:30:06','2025-05-14 02:30:06'),(9,'Laura','García Fernández','654987321','laura.garcia@email.com','clave654','1988-12-10','laurita','2025-05-14 02:30:06','2025-05-14 02:30:06'),(10,'David','Rodríguez Pérez',NULL,'david.rodriguez@email.com','contraseña321','1995-03-25','dave','2025-05-14 02:30:06','2025-05-14 02:30:06'),(11,'Pedro','Gomez Sierra',NULL,'pedrogs23@gmail.com','$2y$10$ItY403E023pz7EuTAolFme9c8QQR7yHqQWclFdxhXPVTF7xLXch7G',NULL,'pedrito23','2025-05-14 13:10:40','2025-05-14 13:10:40');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direcciones`
--

DROP TABLE IF EXISTS `direcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `direcciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones`
--

LOCK TABLES `direcciones` WRITE;
/*!40000 ALTER TABLE `direcciones` DISABLE KEYS */;
INSERT INTO `direcciones` VALUES (1,5,'Calle Hermanos Álvarez Quintero nº 25',NULL,NULL),(2,5,'Calle Hermanos Álvarez Quintero nº 25',NULL,NULL),(3,5,'Calle Hermanos Álvarez Quintero nº 25',NULL,NULL),(4,11,'Calle Hermanos Álvarez Quintero nº 25',NULL,NULL),(5,5,'Calle Hermanos Álvarez Quintero nº 25',NULL,NULL);
/*!40000 ALTER TABLE `direcciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impuestos`
--

DROP TABLE IF EXISTS `impuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impuestos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `porcentaje` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impuestos`
--

LOCK TABLES `impuestos` WRITE;
/*!40000 ALTER TABLE `impuestos` DISABLE KEYS */;
INSERT INTO `impuestos` VALUES (1,'IVA',21.00);
/*!40000 ALTER TABLE `impuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `linea_carrito`
--

DROP TABLE IF EXISTS `linea_carrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `linea_carrito` (
  `id` int NOT NULL AUTO_INCREMENT,
  `carrito_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio` float(6,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrito_id` (`carrito_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `linea_carrito_ibfk_1` FOREIGN KEY (`carrito_id`) REFERENCES `carrito` (`id`),
  CONSTRAINT `linea_carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linea_carrito`
--

LOCK TABLES `linea_carrito` WRITE;
/*!40000 ALTER TABLE `linea_carrito` DISABLE KEYS */;
INSERT INTO `linea_carrito` VALUES (1,1,30,2,750.00,NULL,NULL),(2,1,31,1,549.00,NULL,NULL),(3,1,16,1,349.00,NULL,NULL),(4,2,19,1,299.00,NULL,NULL),(5,2,16,1,349.00,NULL,NULL),(6,3,31,2,549.00,NULL,NULL),(7,4,36,1,459.00,NULL,NULL),(8,4,28,1,469.00,NULL,NULL),(9,5,35,2,929.00,NULL,NULL),(10,5,36,1,459.00,NULL,NULL);
/*!40000 ALTER TABLE `linea_carrito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Samsung'),(2,'Xiaomi'),(3,'Apple'),(4,'Google Pixel'),(5,'Realme'),(6,'Asus'),(7,'Gigabyte'),(8,'Lenovo'),(9,'MSI'),(10,'Acer'),(11,'Nintendo'),(12,'Sony'),(13,'Microsoft'),(14,'PCNET');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodo_pago` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_pago` varchar(100) DEFAULT NULL,
  `detalles_pago` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodo_pago`
--

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` VALUES (1,'Transferencia','Método de pago: Transferencia',NULL,NULL),(2,'Tarjeta','Método de pago: Tarjeta',NULL,NULL),(3,'PayPal','Método de pago: PayPal',NULL,NULL);
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referencia` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `precio` float(6,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `alto` float(4,2) DEFAULT NULL,
  `ancho` float(4,2) DEFAULT NULL,
  `largo` float(4,2) DEFAULT NULL,
  `peso` int DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `marca_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referencia` (`referencia`),
  KEY `categoria_id` (`categoria_id`),
  KEY `marca_id` (`marca_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'IPH16P128','iPhone 16 Pro','Último flagship de Apple con pantalla Super Retina XDR de 6.7\", chip A18 Pro, cámara triple de 48MP con estabilización sensor-shift y batería de 4500mAh. Incluye Dynamic Island y soporte para Apple Intelligence.',1199.00,50,16.07,7.81,0.78,221,2,3,'2025-05-10 03:46:38',NULL,NULL),(2,'S24U512','Samsung Galaxy S24 Ultra','Smartphone premium con pantalla Dynamic AMOLED 2X de 6.8\", Snapdragon 8 Gen 3, cámara de 200MP con zoom óptico 10x, S Pen integrado y batería de 5000mAh con carga rápida de 45W.',1299.00,35,16.29,7.89,0.88,233,2,1,'2025-05-10 03:46:38',NULL,NULL),(3,'PX8P256','Google Pixel 8 Pro','Teléfono insignia de Google con Tensor G3, pantalla LTPO OLED de 6.7\" a 120Hz, cámara triple con Super Res Zoom 30x y las mejores funciones de IA para fotografía. Incluye 7 años de actualizaciones.',999.00,40,16.24,7.69,0.88,213,2,4,'2025-05-10 03:46:38',NULL,NULL),(4,'RM11P512','Realme 11 Pro+','Gama alta asequible con pantalla AMOLED de 6.7\" a 120Hz, Dimensity 7050, cámara de 200MP con OIS, carga rápida de 100W y diseño en cuero vegano. Excelente relación calidad-precio.',499.00,60,16.12,7.45,0.89,189,2,5,'2025-05-10 03:46:38',NULL,NULL),(5,'IPH16STD','iPhone 16','Nuevo modelo base con pantalla Super Retina XDR de 6.1\", chip A17 Bionic, cámara dual de 48MP con nueva lente periscopio 5x, Dynamic Island y diseño de titanio. Batería de 4000mAh con carga rápida de 30W.',899.00,45,14.61,7.12,0.78,187,2,3,'2025-05-10 03:46:38',NULL,NULL),(6,'IPH16E','iPhone 16e','Edición económica con pantalla OLED de 6.1\", chip A16 Bionic, cámara principal de 48MP y diseño de aluminio. Mantiene funciones clave como Face ID y iOS 18 a precio más accesible.',699.00,60,14.61,7.12,0.78,174,2,3,'2025-05-10 03:46:38',NULL,NULL),(7,'IP15PMX','iPhone 15 Pro Max','Modelo anterior flagship con pantalla 6.7\" ProMotion, chip A17 Pro, cámara triple 48MP+12MP+12MP con zoom 5x, diseño de titanio y puerto USB-C. Todavía potente para 2025.',1099.00,25,15.99,7.81,0.80,221,2,3,'2025-05-10 03:46:38',NULL,NULL),(8,'IP14PMX','iPhone 14 Pro Max','Generación anterior con pantalla Always-On de 6.7\", chip A16 Bionic, cámara de 48MP y notch dinámico Dynamic Island. Excelente opción renovada.',799.00,20,16.07,7.81,0.78,240,2,3,'2025-05-10 03:46:38',NULL,NULL),(9,'SGS24STD','Samsung Galaxy S24','Modelo base con pantalla Dynamic AMOLED 2X de 6.2\" a 120Hz, Snapdragon 8 Gen 3, triple cámara 50MP+12MP+10MP y diseño Armor Aluminum 2.0.',799.00,40,14.61,7.06,0.79,168,2,1,'2025-05-10 03:46:38',NULL,NULL),(10,'SGS25ULT','Samsung Galaxy S25 Ultra','El más avanzado con pantalla 6.9\" LTPO 3.0 a 144Hz, Snapdragon 8 Gen 4, cámara de 250MP con zoom 15x, S Pen integrado y batería de 5500mAh con carga 100W.',1399.00,30,16.39,7.92,0.85,228,2,1,'2025-05-10 03:46:38',NULL,NULL),(11,'SGS25PLUS','Samsung Galaxy S25+','Modelo intermedio con pantalla 6.7\" QHD+, triple cámara 200MP+12MP+10MP, Snapdragon 8 Gen 4 y carga inalámbrica reversible.',999.00,35,15.69,7.32,0.82,196,2,1,'2025-05-10 03:46:38',NULL,NULL),(12,'IPADP13','iPad Pro 13\" M3','Tablet profesional con chip Apple M3, pantalla Liquid Retina XDR de 13\", compatibilidad con Apple Pencil 2 y Magic Keyboard. Perfecta para creativos con soporte para apps como Procreate y Final Cut Pro.',1299.00,25,28.06,21.49,0.59,682,3,3,'2025-05-10 03:46:38',NULL,NULL),(13,'STAB9P','Samsung Galaxy Tab S9 Ultra','Tablet premium con pantalla Dynamic AMOLED 2X de 14.6\", Snapdragon 8 Gen 2, S Pen incluido y batería de 11200mAh. Ideal para multitarea con DeX y modo libro para productividad.',1199.00,20,32.68,20.86,0.55,732,3,1,'2025-05-10 03:46:38',NULL,NULL),(14,'XIAOPAD6','Xiaomi Pad 6 Pro','Tablet de alto rendimiento con pantalla LCD de 11\" a 144Hz, Snapdragon 8+ Gen 1 y stylus compatible. Excelente para gaming y streaming con cuatro altavoces Dolby Atmos.',599.00,30,25.38,16.53,0.65,490,3,2,'2025-05-10 03:46:38',NULL,NULL),(15,'IPADAIR6','iPad Air 6','Tablet versátil con chip M2, pantalla Liquid Retina de 10.9\", compatibilidad con Apple Pencil 2 y Magic Keyboard Folio. 5G opcional y diseño todo pantalla.',699.00,35,24.76,17.85,0.61,460,3,3,'2025-05-10 03:46:38',NULL,NULL),(16,'SGTABFE','Samsung Galaxy Tab FE','Edición Fan Edition con pantalla LCD 11\" 120Hz, Snapdragon 7 Gen 1 y S Pen incluido. Versión más asequible manteniendo calidad Samsung.',349.00,38,25.40,16.50,0.67,480,3,1,'2025-05-10 03:46:38',NULL,NULL),(17,'XIPADULT','Xiaomi Pad Ultra','Tablet premium con pantalla AMOLED 12.4\" 3K a 144Hz, Snapdragon 8 Gen 2 y chasis de magnesio. Cámara trasera de 50MP y carga de 120W.',799.00,18,28.50,19.20,0.59,520,3,2,'2025-05-10 03:46:38',NULL,NULL),(18,'GPIXPRO','Google Pixel Tablet Pro','Tablet con Tensor G3, pantalla 11\" OLED 120Hz y base de carga con Nest Hub integrado. Android puro con 5 años de actualizaciones.',899.00,22,25.80,16.90,0.83,490,3,4,'2025-05-10 03:46:38',NULL,NULL),(19,'RLMPAD2','Realme Pad 2','Tablet media con pantalla 10.8\" 2K 90Hz, Dimensity 1080 y diseño de 6.9mm de grosor. Batería de 8360mAh con carga rápida de 33W.',299.00,29,25.70,16.10,0.69,440,3,5,'2025-05-10 03:46:38',NULL,NULL),(20,'MBP14M3','MacBook Pro 14\" M3 Pro','Portátil profesional con chip M3 Pro, pantalla Liquid Retina XDR de 14.2\", hasta 18h de autonomía y seis altavoces con spatial audio. Ideal para desarrolladores y diseñadores.',2199.00,15,22.12,31.26,1.55,1600,4,3,'2025-05-10 03:46:39',NULL,NULL),(21,'ZPH16','Asus ROG Zephyrus G16','Portátil gaming con pantalla Nebula de 16\" QHD a 240Hz, Intel Core i9-13900H y RTX 4070. Diseño ultradelgado con iluminación AniMe Matrix y sistema de refrigeración avanzada.',2299.00,12,19.90,35.50,1.79,2100,4,6,'2025-05-10 03:46:39',NULL,NULL),(22,'LENSLIM7','Lenovo Slim 7 Pro X','Ultrabook potente con AMD Ryzen 9 7940HS, RTX 3050, pantalla IPS de 14.5\" 3K a 120Hz y construcción metálica de solo 1.45kg. Perfecto para profesionales móviles.',1499.00,18,21.50,32.40,1.59,1450,4,8,'2025-05-10 03:46:39',NULL,NULL),(23,'SURFP9','Microsoft Surface Pro 9','Tablet convertible con teclado Type Cover incluido, Intel Core i7-1255U, pantalla PixelSense Flow de 13\" a 120Hz y soporte para Surface Slim Pen 2. Máxima versatilidad Windows.',1599.00,20,20.90,28.70,0.94,891,4,13,'2025-05-10 03:46:39',NULL,NULL),(24,'ACASPI3','Acer Aspire 3','Portátil básico ideal para estudiantes con pantalla Full HD de 15.6\", Intel Core i3-1215U, 8GB RAM y 256GB SSD. Diseño ligero y batería de hasta 8 horas.',449.00,30,20.90,36.30,1.99,1900,4,10,'2025-05-10 03:46:39',NULL,NULL),(25,'LENIDE3','Lenovo IdeaPad 3','Portátil económico con AMD Ryzen 5 5500U, 8GB RAM, 512GB SSD y pantalla IPS de 14\". Perfecto para trabajo ofimático y navegación web.',479.00,25,19.90,32.40,1.99,1400,4,8,'2025-05-10 03:46:39',NULL,NULL),(26,'ASVIVO15','Asus Vivobook 15','Portátil delgado y ligero con Intel Core i5-1235U, 8GB RAM, 512GB SSD y pantalla NanoEdge de 15.6\". Incluye teclado numérico y fingerprint sensor.',529.00,20,18.90,35.90,1.99,1700,4,6,'2025-05-10 03:46:39',NULL,NULL),(27,'ACSWIFT3','Acer Swift 3','Ultrabook asequible con AMD Ryzen 7 5700U, 16GB RAM, 512GB SSD y pantalla IPS 14\" Full HD. Chasis de aluminio y solo 1.2kg de peso.',599.00,18,17.90,32.30,1.59,1200,4,10,'2025-05-10 03:46:39',NULL,NULL),(28,'NS2STD','Nintendo Switch 2','Nueva generación de la consola híbrida con pantalla OLED de 8\", DLSS para 4K en dock, backward compatibility y nuevo mando con retroalimentación háptica. Incluye 64GB almacenamiento.',469.00,29,10.20,24.10,1.45,420,5,11,'2025-05-10 03:46:39',NULL,NULL),(29,'PS5SLIM','PlayStation 5 Slim','Versión compacta de PS5 con mismo rendimiento, 1TB SSD, lector Ultra HD Blu-ray y diseño renovado. Incluye DualSense con retroalimentación adaptativa y gatillos.',449.00,25,9.60,35.80,21.60,3200,5,12,'2025-05-10 03:46:39',NULL,NULL),(30,'PS5PRO','PlayStation 5 Pro','Edición premium de PS5 con GPU mejorada para 8K/60fps y 4K/120fps, 2TB SSD ultra rápido, nuevo diseño con iluminación RGB y DualSense Pro con gatillos mejorados. Compatibilidad total con PS VR2.',750.00,13,10.20,35.80,21.60,3400,5,12,'2025-05-10 03:46:39',NULL,NULL),(31,'XSXS24','Xbox Series X 2024','Edición especial con 2TB SSD, diseño vertical u horizontal, soporte para juegos en 4K/120fps y Quick Resume. Incluye Game Pass Ultimate por 3 meses.',549.00,17,15.10,15.10,30.10,4400,5,13,'2025-05-10 03:46:39',NULL,NULL),(32,'RSH3FL','PCNET King','Procesador: AMD Ryzen 7 7800X3D / RAM: Acer Predator Vesta II RGB DDR5 6000MHz 32GB 2x16GB CL30 / Disco duro: WD BLACK SN770 2TB NVMe SSD / Tarjeta grafica: MSI Ventus GeForce RTX 4070 Ti SUPER 3X OC 16 GB / Placa base: MSI PRO B650-S WIFI',2129.00,30,46.00,23.00,49.05,20,1,14,'2025-05-10 03:46:39',NULL,NULL),(33,'ABX9YZ','PCNET Ready','Procesador: Intel Core i7-14700KF / RAM: Kingston FURY Beast DDR4 3200 MHz 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 2TB Disco SSD / Tarjeta grafica: ASUS Dual GeForce RTX 4060 Ti EVO OC 8GB / Placa base: Gigabyte B760M DS3H DDR4',1600.00,30,40.90,21.40,48.50,12,1,14,'2025-05-10 03:46:39',NULL,NULL),(34,'QWE4RT','PCNET Lite','Procesador: Intel Core i5-12400F  / RAM: Kingston FURY Beast DDR4 3200 MHz 16GB 2x8GB CL16 / Disco duro: WD Blue SN580 1TB SSD M.2 PCIe 4.0 NVMe / Tarjeta grafica: GeForce RTX 3050 6GB GDDR6 / Placa base: Gigabyte H610M S2H V2 DDR4 ',700.00,30,36.60,21.00,21.00,10,1,14,'2025-05-10 03:46:39',NULL,NULL),(35,'MKL7PN','PCNET Legit','Procesador: AMD Ryzen 7 5700X / RAM: Corsair Vengeance LPX DDR4 3200MHz PC4-25600 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 1TB Disco SSD / Tarjeta grafica: GeForce RTX 4060 2X BLACK 8GB / Placa base: ASUS PRIME B550M-A/CSM',929.00,28,40.00,21.00,42.80,14,1,14,'2025-05-10 03:46:39',NULL,NULL),(36,'ZYX2BV','PCNET Work','Procesador: AMD Ryzen 7 5700G / RAM: DDR4 3200 MHz 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 1TB Disco SSD / Tarjeta grafica: AMD Integrada / Placa base: Gigabyte B550M K Rev. 1.0',459.00,28,40.00,21.00,42.80,8,1,14,'2025-05-10 03:46:39',NULL,NULL),(37,'DFG5HJ','PCNET Office','Procesador: Intel Core i3-12100 3.3 GHz / RAM: 3200 MHz 16GB 2x8GB CL16 / Disco duro: 500GB NVMe PCIe 3.0 / Tarjeta grafica: Gráficos HD Intel® 630 / Placa base: Gigabyte H610M S2H V3 DDR4',375.00,30,33.00,21.00,43.90,8,1,14,'2025-05-10 03:46:39',NULL,NULL),(38,'WSX4CV','PCNET Extreme','Procesador: AMD Ryzen 7 9800X3D / RAM: Biwin Black Opal DW100 DDR5 RGB 6000MHz 32GB 2x16GB CL30 Blanca / Disco duro:  Forgeon Nimbus PLUS Disco SSD 2TB / Tarjeta grafica: RTX 5080 Gaming 16GB / Placa base: ASUS ROG STRIX B850-A GAMING WIFI',3350.00,30,44.50,28.80,38.60,21,1,14,'2025-05-10 03:46:39',NULL,NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `soporte`
--

DROP TABLE IF EXISTS `soporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `soporte` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_entrada` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','en proceso','resuelto') DEFAULT 'pendiente',
  `cliente_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `soporte_ibfk_1` (`cliente_id`),
  CONSTRAINT `soporte_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `soporte`
--

LOCK TABLES `soporte` WRITE;
/*!40000 ALTER TABLE `soporte` DISABLE KEYS */;
INSERT INTO `soporte` VALUES (13,'Laura','lauraes03@gmail.com','Entrega','¿Cuántos días tarda en llegar producto al domicilio?','2025-05-14 02:28:06','resuelto',NULL,'2025-05-14 02:28:06');
/*!40000 ALTER TABLE `soporte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `importe` float(6,2) DEFAULT NULL,
  `carrito_id` int DEFAULT NULL,
  `impuesto_id` int DEFAULT NULL,
  `importe_total` float(6,2) DEFAULT NULL,
  `fecha_previstaEntrega` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrito_id` (`carrito_id`),
  KEY `impuesto_id` (`impuesto_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`carrito_id`) REFERENCES `carrito` (`id`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,2398.00,1,1,2906.57,'2025-05-20',NULL),(2,648.00,2,1,789.07,'2025-05-20',NULL),(3,1098.00,3,1,1333.57,'2025-05-21',NULL),(4,928.00,4,1,1127.87,'2025-05-21',NULL),(5,2317.00,5,1,2808.56,'2025-05-23','2025-05-17 13:25:46');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-17 23:30:59
