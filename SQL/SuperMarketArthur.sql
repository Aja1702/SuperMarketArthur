CREATE DATABASE if NOT exists supermarketarthur;
USE supermarketarthur;

CREATE TABLE supermarketarthur.usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    apellido1 VARCHAR(150) NOT NULL,
    apellido2 VARCHAR(150),
    provincia VARCHAR(50) NOT NULL,
    localidad VARCHAR(100) NOT NULL,
    cp VARCHAR(10) NOT NULL,
    calle VARCHAR(150) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(80) NOT NULL UNIQUE,
    tipo_doc VARCHAR(3) NOT NULL,         -- 'DNI' o 'NIE'
    num_doc VARCHAR(15) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_usu VARCHAR(1) NOT NULL     -- 'a' para administrador, 'u' para usuario normal
);

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `pass`, `apellido1`, `apellido2`, `provincia`, `localidad`, `cp`, `calle`, `numero`, `telefono`, `email`, `tipo_doc`, `num_doc`, `fecha_nacimiento`, `fecha_registro`, `tipo_usu`) 
	VALUES (NULL, 'root', '$2y$10$.x4pBBCYOsl17wKgdZyX0erdcZmm6D7iBqytg6vDh.1l/z7sYMVTC', 'root1', 'root2', 'toledo', 'villacañas', '45860', 'Arturo', '01', '600000001', 'root@mysql.es', 'DNI', '83727913R', '2002-01-04', '2025-07-28 17:07:26', 'a');

-- Tabla de usuarios
CREATE TABLE supermarketarthur.direcciones (
  id_direccion INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  tipo ENUM('envío','facturación'),
  calle VARCHAR(100),
  ciudad VARCHAR(50),
  provincia VARCHAR(50),
  cp VARCHAR(10),
  pais VARCHAR(50),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla de categorias
CREATE TABLE supermarketarthur.categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre_categoria VARCHAR(50),
  descripcion TEXT
);

-- Tabla de productos
CREATE TABLE supermarketarthur.productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre_producto VARCHAR(100),
  descripcion TEXT,
  precio DECIMAL(10,2),
  stock INT,
  id_categoria INT,
  url_imagen VARCHAR(255),
  FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Tabla de carrito temporal
CREATE TABLE supermarketarthur.carrito_temp (
  id_carrito INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
-- Tabla de items del carrito
CREATE TABLE supermarketarthur.carrito_items (
  id_item INT AUTO_INCREMENT PRIMARY KEY,
  id_carrito INT,
  id_producto INT,
  cantidad INT,
  FOREIGN KEY (id_carrito) REFERENCES carrito_temp(id_carrito),
  FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
-- Tabla de pedidos
CREATE TABLE supermarketarthur.pedidos (
  id_pedido INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2),
  estado ENUM('pendiente','pagado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  id_direccion INT,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
  FOREIGN KEY (id_direccion) REFERENCES direcciones(id_direccion)
);

-- Tabla de items del pedido
CREATE TABLE supermarketarthur.pedido_items (
  id_pedido_item INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT,
  id_producto INT,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
  FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Tabla de pagos
CREATE TABLE supermarketarthur.pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    metodo_pago VARCHAR(50), -- ejemplo: 'tarjeta', 'paypal', 'transferencia'
    estado_pago ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido)
);

-- Tabla de cupones
CREATE TABLE supermarketarthur.cupones (
    id_cupon INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    descripcion VARCHAR(255),
    descuento DECIMAL(5,2), -- porcentaje o cantidad fija
    tipo_descuento ENUM('porcentaje','cantidad') DEFAULT 'porcentaje',
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de cupones aplicados a pedidos
CREATE TABLE supermarketarthur.cupones_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cupon INT,
    id_pedido INT,
    id_usuario INT,
    fecha_uso DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cupon) REFERENCES cupones(id_cupon),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla de historial de stock
CREATE TABLE supermarketarthur.historial_stock (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    cantidad INT,
    tipo_movimiento ENUM('entrada','salida','ajuste'),
    fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
-- Tabla de valoraciones de productos
CREATE TABLE supermarketarthur.valoraciones (
    id_valoracion INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    id_usuario INT,
    puntuacion INT CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- tabla de imagenes de productos
CREATE TABLE supermarketarthur.imagenes_producto (
    id_imagen INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    orden INT DEFAULT 1,
    es_principal BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Tabla de favoritos
CREATE TABLE supermarketarthur.favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    UNIQUE (id_usuario, id_producto)  -- Evita duplicados
);

-- tabla de password resets
-- Esta tabla se utiliza para gestionar los restablecimientos de contraseña
CREATE TABLE supermarketarthur.password_resets (
    id_reset INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    utilizado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
