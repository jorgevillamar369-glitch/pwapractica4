-- ============================================================
-- Práctica 4 - Sistema de venta de productos
-- Base de datos: pwapractica4
-- Importar desde phpMyAdmin dentro de la base ya creada.
-- (No incluye CREATE DATABASE para poder importarlo en el hosting)
-- ============================================================

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Tabla: vendedores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vendedores (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(120) NOT NULL,
  email     VARCHAR(150) NOT NULL UNIQUE,
  telefono  VARCHAR(30)  NOT NULL,
  ciudad    VARCHAR(80)  NOT NULL,
  creado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: productos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS productos (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(120)  NOT NULL,
  descripcion  VARCHAR(400)  NOT NULL,
  precio       DECIMAL(10,2) NOT NULL,
  categoria    VARCHAR(40)   NOT NULL,
  imagen       VARCHAR(160)  NOT NULL,
  stock        INT UNSIGNED  NOT NULL DEFAULT 0,
  id_vendedor  INT UNSIGNED  NOT NULL,
  CONSTRAINT fk_productos_vendedor
    FOREIGN KEY (id_vendedor) REFERENCES vendedores(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: clientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clientes (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(120) NOT NULL,
  email     VARCHAR(150) NOT NULL UNIQUE,
  direccion VARCHAR(200) NOT NULL,
  creado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tablas de apoyo: pedidos y su detalle
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo      CHAR(8)       NOT NULL UNIQUE,
  id_cliente  INT UNSIGNED  NOT NULL,
  total       DECIMAL(10,2) NOT NULL,
  estado      ENUM('pendiente','pagado','enviado') NOT NULL DEFAULT 'pendiente',
  creado_en   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pedidos_cliente
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedido_detalle (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pedido       INT UNSIGNED  NOT NULL,
  id_producto     INT UNSIGNED  NOT NULL,
  cantidad        INT UNSIGNED  NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_pedido
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detalle_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Datos de ejemplo
-- ------------------------------------------------------------
INSERT INTO vendedores (id, nombre, email, telefono, ciudad) VALUES
  (1, 'Tostaduría Altura', 'hola@tostaduriaaltura.ec', '+593 99 214 7788', 'Quito'),
  (2, 'Casa del Grano',   'pedidos@casadelgrano.ec',  '+593 98 330 1177', 'Cuenca'),
  (3, 'Taller de Gres',   'taller@tallerdegres.ec',   '+593 96 445 2290', 'Loja')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO productos (id, nombre, descripcion, precio, categoria, imagen, stock, id_vendedor) VALUES
  (1, 'Café Etiopía Yirgacheffe', 'Lavado, tueste medio. Notas de jazmín, durazno y miel. Bolsa de 250 g en grano.', 12.50, 'cafe', 'cafe-etiopia.jpg', 24, 1),
  (2, 'Café Colombia Huila',      'Tueste medio-alto, cuerpo redondo. Panela, naranja y cacao. Bolsa de 250 g en grano.', 10.90, 'cafe', 'cafe-colombia.jpg', 30, 2),
  (3, 'Café Brasil Cerrado',      'Tueste oscuro para espresso. Chocolate amargo y avellana. Bolsa de 500 g en grano.', 14.00, 'cafe', 'cafe-brasil.jpg', 18, 1),
  (4, 'Prensa francesa 600 ml',   'Vidrio borosilicato y acero inoxidable. Filtro de cuatro piezas, apta para 3 tazas.', 24.00, 'equipo', 'prensa.jpg', 9, 3),
  (5, 'Molinillo manual cónico',  'Fresas de acero, 38 mm. Molienda regulable para prensa, goteo y espresso.', 32.00, 'equipo', 'molino.jpg', 7, 2),
  (6, 'Cafetero V60 cerámica',    'Cerámica cocida a 1 200 °C, 02. Espiral interior para un flujo parejo.', 18.50, 'equipo', 'v60.jpg', 12, 2),
  (7, 'Taza de gres 220 ml',      'Torneada a mano, esmalte mate. Aguanta el calor sin quemar los dedos.', 9.00, 'ceramica', 'taza.jpg', 20, 3),
  (8, 'Filtros de papel V60',     'Cien filtros sin blanquear para cafetero 02. Papel grueso, sin sabor a papel.', 5.50, 'equipo', 'filtros.jpg', 40, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO clientes (id, nombre, email, direccion) VALUES
  (1, 'Ana Cedeño',      'ana.cedeno@correo.ec',   'Av. 9 de Octubre 1204, Guayaquil'),
  (2, 'Luis Andrade',    'luis.andrade@correo.ec', 'Calle Bolívar 5-42, Cuenca'),
  (3, 'María José Paz',  'mj.paz@correo.ec',       'Av. Amazonas N24-56, Quito')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
