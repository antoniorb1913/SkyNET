create database SkyNET;

use SkyNET;

create table productos (  /*gestor de stock*/
    id int primary key auto_increment,
    referencia varchar(50) unique,
    nombre varchar(100),
    descripcion varchar(500),
    precio float(6,2),
    stock int,
    alto float(4,2),
    ancho float(4,2),
    largo float(4,2),
    peso int,
    categoria_id int,
    marca_id int,
    created_at datetime,
    deleted_at datetime,
    updated_at datetime

);

create table categorias (
    id int primary key auto_increment,
    nombre varchar(100)
);

create table clientes ( /*leiva, poner direccion*/
    id int primary key auto_increment,
    nombre varchar(100) not null,
    apellidos varchar(100) not null,
    telefono varchar(50),
    email varchar(50) unique not null,
    contrasena varchar(255),
    fecha_nacimiento date,
    nick varchar(100),
    created_at datetime,
    updated_at datetime

);

create table carrito (
    id int primary key auto_increment,
    cliente_id int,
    metodoPago_id int,
    direccion_id int,
    gasto_envio float(4,2),
    created_at datetime,
    updated_at datetime

);

create table linea_carrito (
    id int primary key auto_increment,
    carrito_id int,
    producto_id int,
    cantidad int,
    precio float(6,2),
    created_at datetime,
    updated_at datetime
);


create table ventas ( /*asociada a un carrito*/
    id int primary key auto_increment,
    importe float(6,2),
    carrito_id int,
    impuesto_id int,
    importe_total float(6,2),
    fecha_previstaEntrega date,
    created_at datetime

);

create table impuestos (
    id int primary key auto_increment,
    nombre varchar(100),
    porcentaje decimal(5,2),
    created_at datetime,
    updated_at datetime

);


create table marcas (
    id int primary key auto_increment,
    nombre varchar(100)

);

create table direcciones (
    id int primary key auto_increment,
    cliente_id int,
    direccion varchar(255),
    created_at datetime,
    updated_at datetime

);

create table metodo_pago (
    id int primary key auto_increment,
    tipo_pago varchar(100),
    detalles_pago varchar(255),
    created_at datetime,
    updated_at datetime

);
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE soporte (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    asunto VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_entrada DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en proceso', 'resuelto') DEFAULT 'pendiente',
    cliente_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);



alter table productos add imagen varchar(255);

/* FOREIGN KEYS */

alter table productos add foreign key (categoria_id) references categorias(id);
alter table productos add foreign key (marca_id) references marcas(id);
alter table carrito add foreign key (cliente_id) references clientes(id);
alter table carrito add foreign key (metodoPago_id) references metodo_pago(id);
alter table carrito add foreign key (direccion_id) references direcciones(id);
alter table linea_carrito add foreign key (carrito_id) references carrito(id);
alter table linea_carrito add foreign key (producto_id) references productos(id);
alter table ventas add foreign key (carrito_id) references carrito(id);
alter table ventas add foreign key (impuesto_id) references impuestos(id);
alter table direcciones add foreign key (cliente_id) references clientes(id);


INSERT INTO admin (nombre, contrasena) values ('admin', '@dminSkN');

/* CATEGORIAS */

INSERT INTO categorias (nombre) VALUES ('Ordenadores'), ('Moviles'), ('Tablets'),('Portatiles'),('Consolas');


/* MARCAS */

INSERT INTO marcas (nombre) VALUES ('Samsung'),('Xiaomi'),('Apple'),('Google Pixel'),('Realme'),
('Asus'),('Gigabyte'),('Lenovo'),('MSI'),('Acer'),('Nintendo'),('Sony'),('Microsoft'),('PCNET');


/* PRODUCTOS */


-- moviles

INSERT INTO productos (referencia, nombre, descripcion, precio, stock, alto, ancho, largo, peso, categoria_id, marca_id, created_at) VALUES
('IPH16P128', 'iPhone 16 Pro', 'Último flagship de Apple con pantalla Super Retina XDR de 6.7", chip A18 Pro, cámara triple de 48MP con estabilización sensor-shift y batería de 4500mAh. Incluye Dynamic Island y soporte para Apple Intelligence.', 1199.00, 50, 16.07, 7.81, 0.78, 221, 2, 3, NOW()),
('S24U512', 'Samsung Galaxy S24 Ultra', 'Smartphone premium con pantalla Dynamic AMOLED 2X de 6.8", Snapdragon 8 Gen 3, cámara de 200MP con zoom óptico 10x, S Pen integrado y batería de 5000mAh con carga rápida de 45W.', 1299.00, 35, 16.29, 7.89, 0.88, 233, 2, 1, NOW()),
('PX8P256', 'Google Pixel 8 Pro', 'Teléfono insignia de Google con Tensor G3, pantalla LTPO OLED de 6.7" a 120Hz, cámara triple con Super Res Zoom 30x y las mejores funciones de IA para fotografía. Incluye 7 años de actualizaciones.', 999.00, 40, 16.24, 7.69, 0.88, 213, 2, 4, NOW()),
('RM11P512', 'Realme 11 Pro+', 'Gama alta asequible con pantalla AMOLED de 6.7" a 120Hz, Dimensity 7050, cámara de 200MP con OIS, carga rápida de 100W y diseño en cuero vegano. Excelente relación calidad-precio.', 499.00, 60, 16.12, 7.45, 0.89, 189, 2, 5, NOW()),
('IPH16STD', 'iPhone 16', 'Nuevo modelo base con pantalla Super Retina XDR de 6.1", chip A17 Bionic, cámara dual de 48MP con nueva lente periscopio 5x, Dynamic Island y diseño de titanio. Batería de 4000mAh con carga rápida de 30W.', 899.00, 45, 14.61, 7.12, 0.78, 187, 2, 3, NOW()),
('IPH16E', 'iPhone 16e', 'Edición económica con pantalla OLED de 6.1", chip A16 Bionic, cámara principal de 48MP y diseño de aluminio. Mantiene funciones clave como Face ID y iOS 18 a precio más accesible.', 699.00, 60, 14.61, 7.12, 0.78, 174, 2, 3, NOW()),
('IP15PMX', 'iPhone 15 Pro Max', 'Modelo anterior flagship con pantalla 6.7" ProMotion, chip A17 Pro, cámara triple 48MP+12MP+12MP con zoom 5x, diseño de titanio y puerto USB-C. Todavía potente para 2025.', 1099.00, 25, 15.99, 7.81, 0.80, 221, 2, 3, NOW()),
('IP14PMX', 'iPhone 14 Pro Max', 'Generación anterior con pantalla Always-On de 6.7", chip A16 Bionic, cámara de 48MP y notch dinámico Dynamic Island. Excelente opción renovada.', 799.00, 20, 16.07, 7.81, 0.78, 240, 2, 3, NOW()),
('SGS24STD', 'Samsung Galaxy S24', 'Modelo base con pantalla Dynamic AMOLED 2X de 6.2" a 120Hz, Snapdragon 8 Gen 3, triple cámara 50MP+12MP+10MP y diseño Armor Aluminum 2.0.', 799.00, 40, 14.61, 7.06, 0.79, 168, 2, 1, NOW()),
('SGS25ULT', 'Samsung Galaxy S25 Ultra', 'El más avanzado con pantalla 6.9" LTPO 3.0 a 144Hz, Snapdragon 8 Gen 4, cámara de 250MP con zoom 15x, S Pen integrado y batería de 5500mAh con carga 100W.', 1399.00, 30, 16.39, 7.92, 0.85, 228, 2, 1, NOW()),
('SGS25PLUS', 'Samsung Galaxy S25+', 'Modelo intermedio con pantalla 6.7" QHD+, triple cámara 200MP+12MP+10MP, Snapdragon 8 Gen 4 y carga inalámbrica reversible.', 999.00, 35, 15.69, 7.32, 0.82, 196, 2, 1, NOW());

-- tablets

INSERT INTO productos (referencia, nombre, descripcion, precio, stock, alto, ancho, largo, peso, categoria_id, marca_id, created_at) VALUES
('IPADP13', 'iPad Pro 13" M3', 'Tablet profesional con chip Apple M3, pantalla Liquid Retina XDR de 13", compatibilidad con Apple Pencil 2 y Magic Keyboard. Perfecta para creativos con soporte para apps como Procreate y Final Cut Pro.', 1299.00, 25, 28.06, 21.49, 0.59, 682, 3, 3, NOW()),
('STAB9P', 'Samsung Galaxy Tab S9 Ultra', 'Tablet premium con pantalla Dynamic AMOLED 2X de 14.6", Snapdragon 8 Gen 2, S Pen incluido y batería de 11200mAh. Ideal para multitarea con DeX y modo libro para productividad.', 1199.00, 20, 32.68, 20.86, 0.55, 732, 3, 1, NOW()),
('XIAOPAD6', 'Xiaomi Pad 6 Pro', 'Tablet de alto rendimiento con pantalla LCD de 11" a 144Hz, Snapdragon 8+ Gen 1 y stylus compatible. Excelente para gaming y streaming con cuatro altavoces Dolby Atmos.', 599.00, 30, 25.38, 16.53, 0.65, 490, 3, 2, NOW()),
('IPADAIR6', 'iPad Air 6', 'Tablet versátil con chip M2, pantalla Liquid Retina de 10.9", compatibilidad con Apple Pencil 2 y Magic Keyboard Folio. 5G opcional y diseño todo pantalla.', 699.00, 35, 24.76, 17.85, 0.61, 460, 3, 3, NOW()),
('SGTABFE', 'Samsung Galaxy Tab FE', 'Edición Fan Edition con pantalla LCD 11" 120Hz, Snapdragon 7 Gen 1 y S Pen incluido. Versión más asequible manteniendo calidad Samsung.', 349.00, 40, 25.4, 16.5, 0.67, 480, 3, 1, NOW()),
('XIPADULT', 'Xiaomi Pad Ultra', 'Tablet premium con pantalla AMOLED 12.4" 3K a 144Hz, Snapdragon 8 Gen 2 y chasis de magnesio. Cámara trasera de 50MP y carga de 120W.', 799.00, 18, 28.5, 19.2, 0.59, 520, 3, 2, NOW()),
('GPIXPRO', 'Google Pixel Tablet Pro', 'Tablet con Tensor G3, pantalla 11" OLED 120Hz y base de carga con Nest Hub integrado. Android puro con 5 años de actualizaciones.', 899.00, 22, 25.8, 16.9, 0.83, 490, 3, 4, NOW()),
('RLMPAD2', 'Realme Pad 2', 'Tablet media con pantalla 10.8" 2K 90Hz, Dimensity 1080 y diseño de 6.9mm de grosor. Batería de 8360mAh con carga rápida de 33W.', 299.00, 30, 25.7, 16.1, 0.69, 440, 3, 5, NOW());

-- portatiles

INSERT INTO productos (referencia, nombre, descripcion, precio, stock, alto, ancho, largo, peso, categoria_id, marca_id, created_at) VALUES
('MBP14M3', 'MacBook Pro 14" M3 Pro', 'Portátil profesional con chip M3 Pro, pantalla Liquid Retina XDR de 14.2", hasta 18h de autonomía y seis altavoces con spatial audio. Ideal para desarrolladores y diseñadores.', 2199.00, 15, 22.12, 31.26, 1.55, 1600, 4, 3, NOW()),
('ZPH16', 'Asus ROG Zephyrus G16', 'Portátil gaming con pantalla Nebula de 16" QHD a 240Hz, Intel Core i9-13900H y RTX 4070. Diseño ultradelgado con iluminación AniMe Matrix y sistema de refrigeración avanzada.', 2299.00, 12, 19.9, 35.5, 1.79, 2100, 4, 6, NOW()),
('LENSLIM7', 'Lenovo Slim 7 Pro X', 'Ultrabook potente con AMD Ryzen 9 7940HS, RTX 3050, pantalla IPS de 14.5" 3K a 120Hz y construcción metálica de solo 1.45kg. Perfecto para profesionales móviles.', 1499.00, 18, 21.5, 32.4, 1.59, 1450, 4, 8, NOW()),
('SURFP9', 'Microsoft Surface Pro 9', 'Tablet convertible con teclado Type Cover incluido, Intel Core i7-1255U, pantalla PixelSense Flow de 13" a 120Hz y soporte para Surface Slim Pen 2. Máxima versatilidad Windows.', 1599.00, 20, 20.9, 28.7, 0.94, 891, 4, 13, NOW()),
('ACASPI3', 'Acer Aspire 3', 'Portátil básico ideal para estudiantes con pantalla Full HD de 15.6", Intel Core i3-1215U, 8GB RAM y 256GB SSD. Diseño ligero y batería de hasta 8 horas.', 449.00, 30, 20.9, 36.3, 1.99, 1900, 4, 10, NOW()),
('LENIDE3', 'Lenovo IdeaPad 3', 'Portátil económico con AMD Ryzen 5 5500U, 8GB RAM, 512GB SSD y pantalla IPS de 14". Perfecto para trabajo ofimático y navegación web.', 479.00, 25, 19.9, 32.4, 1.99, 1400, 4, 8, NOW()),
('ASVIVO15', 'Asus Vivobook 15', 'Portátil delgado y ligero con Intel Core i5-1235U, 8GB RAM, 512GB SSD y pantalla NanoEdge de 15.6". Incluye teclado numérico y fingerprint sensor.', 529.00, 20, 18.9, 35.9, 1.99, 1700, 4, 6, NOW()),
('ACSWIFT3', 'Acer Swift 3', 'Ultrabook asequible con AMD Ryzen 7 5700U, 16GB RAM, 512GB SSD y pantalla IPS 14" Full HD. Chasis de aluminio y solo 1.2kg de peso.', 599.00, 18, 17.9, 32.3, 1.59, 1200, 4, 10, NOW());

-- consolas

INSERT INTO productos (referencia, nombre, descripcion, precio, stock, alto, ancho, largo, peso, categoria_id, marca_id, created_at) VALUES
('NS2STD', 'Nintendo Switch 2', 'Nueva generación de la consola híbrida con pantalla OLED de 8", DLSS para 4K en dock, backward compatibility y nuevo mando con retroalimentación háptica. Incluye 64GB almacenamiento.', 469.00, 30, 10.2, 24.1, 1.45, 420, 5, 11, NOW()),
('PS5SLIM', 'PlayStation 5 Slim', 'Versión compacta de PS5 con mismo rendimiento, 1TB SSD, lector Ultra HD Blu-ray y diseño renovado. Incluye DualSense con retroalimentación adaptativa y gatillos.', 449.00, 25, 9.6, 35.8, 21.6, 3200, 5, 12, NOW()),
('PS5PRO', 'PlayStation 5 Pro', 'Edición premium de PS5 con GPU mejorada para 8K/60fps y 4K/120fps, 2TB SSD ultra rápido, nuevo diseño con iluminación RGB y DualSense Pro con gatillos mejorados. Compatibilidad total con PS VR2.', 750.00, 15, 10.2, 35.8, 21.6, 3400, 5, 12, NOW()),
('XSXS24', 'Xbox Series X 2024', 'Edición especial con 2TB SSD, diseño vertical u horizontal, soporte para juegos en 4K/120fps y Quick Resume. Incluye Game Pass Ultimate por 3 meses.', 549.00, 20, 15.1, 15.1, 30.1, 4400, 5, 13, NOW());

-- ordenadores

INSERT INTO productos (referencia, nombre, descripcion, precio, stock, alto, ancho, largo, peso, categoria_id, marca_id, created_at) VALUES
('RSH3FL', 'PCNET King', 'Procesador: AMD Ryzen 7 7800X3D / RAM: Acer Predator Vesta II RGB DDR5 6000MHz 32GB 2x16GB CL30 / Disco duro: WD BLACK SN770 2TB NVMe SSD / Tarjeta grafica: MSI Ventus GeForce RTX 4070 Ti SUPER 3X OC 16 GB / Placa base: MSI PRO B650-S WIFI', 2129.00, 30, 46.0, 23.0, 49.05, 20, 1, 14, NOW()),
('ABX9YZ', 'PCNET Ready', 'Procesador: Intel Core i7-14700KF / RAM: Kingston FURY Beast DDR4 3200 MHz 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 2TB Disco SSD / Tarjeta grafica: ASUS Dual GeForce RTX 4060 Ti EVO OC 8GB / Placa base: Gigabyte B760M DS3H DDR4', 1600.00, 30, 40.90, 21.40, 48.50, 12, 1, 14, NOW()),
('QWE4RT', 'PCNET Lite', 'Procesador: Intel Core i5-12400F  / RAM: Kingston FURY Beast DDR4 3200 MHz 16GB 2x8GB CL16 / Disco duro: WD Blue SN580 1TB SSD M.2 PCIe 4.0 NVMe / Tarjeta grafica: GeForce RTX 3050 6GB GDDR6 / Placa base: Gigabyte H610M S2H V2 DDR4 ', 700.00, 30, 36.60, 21.00, 21.00, 10, 1, 14, NOW()),
('MKL7PN', 'PCNET Legit', 'Procesador: AMD Ryzen 7 5700X / RAM: Corsair Vengeance LPX DDR4 3200MHz PC4-25600 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 1TB Disco SSD / Tarjeta grafica: GeForce RTX 4060 2X BLACK 8GB / Placa base: ASUS PRIME B550M-A/CSM', 929.00, 30, 40.00, 21.00, 42.80, 14, 1, 14, NOW()),
('ZYX2BV', 'PCNET Work', 'Procesador: AMD Ryzen 7 5700G / RAM: DDR4 3200 MHz 32GB 2x16GB CL16 / Disco duro: Kioxia Exceria Plus G3 1TB Disco SSD / Tarjeta grafica: AMD Integrada / Placa base: Gigabyte B550M K Rev. 1.0', 459.00, 30, 40.00, 21.00, 42.80, 8, 1, 14, NOW()),
('DFG5HJ', 'PCNET Office', 'Procesador: Intel Core i3-12100 3.3 GHz / RAM: 3200 MHz 16GB 2x8GB CL16 / Disco duro: 500GB NVMe PCIe 3.0 / Tarjeta grafica: Gráficos HD Intel® 630 / Placa base: Gigabyte H610M S2H V3 DDR4', 375.00, 30, 33.00, 21.00, 43.90, 8, 1, 14, NOW()),  
('WSX4CV', 'PCNET Extreme', 'Procesador: AMD Ryzen 7 9800X3D / RAM: Biwin Black Opal DW100 DDR5 RGB 6000MHz 32GB 2x16GB CL30 Blanca / Disco duro:  Forgeon Nimbus PLUS Disco SSD 2TB / Tarjeta grafica: RTX 5080 Gaming 16GB / Placa base: ASUS ROG STRIX B850-A GAMING WIFI', 3350.00, 30, 44.50, 28.80, 38.605, 21, 1, 14, NOW());

/*-------------------------------------------------------------------------------------

/* Backups automáticos de la base de datos */

/* copia de seguridsd con: mysqldump -u [usuario] -p [nombre_base_datos] > backup.sql */
