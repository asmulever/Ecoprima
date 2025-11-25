-- EcoPrima Marketplace - esquema base
-- Ejecutar este script en una base vacía para regenerar las tablas críticas.

DROP TABLE IF EXISTS producto_imagenes;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS empresas;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    empresa VARCHAR(190) DEFAULT NULL,
    rol ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
    estado ENUM('pendiente','activo','inactivo') NOT NULL DEFAULT 'pendiente',
    token VARCHAR(64) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE empresas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(190) NOT NULL,
    cuit VARCHAR(20) NOT NULL,
    email VARCHAR(190) NOT NULL,
    telefono VARCHAR(50) DEFAULT NULL,
    direccion VARCHAR(190) DEFAULT NULL,
    rubro VARCHAR(120) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    sitio_web VARCHAR(190) DEFAULT NULL,
    cuenta_bancaria VARCHAR(80) DEFAULT NULL,
    logo LONGBLOB DEFAULT NULL,
    logo_tipo VARCHAR(60) DEFAULT NULL,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_empresas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE productos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(190) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    precio DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ubicacion VARCHAR(190) NOT NULL,
    estado ENUM('activo','pausado') NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE producto_imagenes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id INT UNSIGNED NOT NULL,
    imagen LONGBLOB NOT NULL,
    mime_type VARCHAR(60) NOT NULL,
    orden INT UNSIGNED NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_imagenes_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin inicial (clave: admin123) - cambiar antes de usar en producción.
INSERT INTO usuarios (email, password_hash, empresa, rol, estado)
VALUES (
    'admin@ecoprima.local',
    '$2y$10$1TH8yM7GWqFaN2.chlTsPe4LEF8YB8x2FsUzXuoBIyLPlFDjY4Lv.',
    'EcoPrima',
    'admin',
    'activo'
);
