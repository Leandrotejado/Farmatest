-- Sistema de Gestión Farmacéutica - Base de Datos Completa
-- Crear base de datos desde cero

-- Tabla de farmacias
CREATE TABLE IF NOT EXISTS farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion TEXT NOT NULL,
    telefono VARCHAR(50),
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    distrito VARCHAR(100),
    activa BOOLEAN DEFAULT TRUE,
    de_turno BOOLEAN DEFAULT FALSE,
    horario_apertura TIME,
    horario_cierre TIME,
    servicios TEXT,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías de medicamentos
CREATE TABLE IF NOT EXISTS categorias_medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    tipo_venta ENUM('venta_libre', 'con_receta') DEFAULT 'venta_libre',
    descripcion TEXT,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de proveedores
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    contacto VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    direccion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de medicamentos
CREATE TABLE IF NOT EXISTS medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    codigo_barras VARCHAR(50) UNIQUE,
    precio DECIMAL(10, 2) NOT NULL,
    id_categoria INT,
    id_proveedor INT,
    stock_general INT DEFAULT 0,
    stock_minimo INT DEFAULT 10,
    fecha_vencimiento DATE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_medicamentos(id),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

-- Tabla de usuarios/administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_nombre VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'farmacia') DEFAULT 'farmacia',
    id_farmacia INT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id)
);

-- Tabla de stock por farmacia
CREATE TABLE IF NOT EXISTS stock_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_medicamento INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    precio_especial DECIMAL(10, 2) NULL,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    UNIQUE KEY unique_farmacia_medicamento (id_farmacia, id_medicamento)
);

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_usuario INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de detalles de ventas
CREATE TABLE IF NOT EXISTS venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_medicamento INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id)
);

-- Tabla de movimientos de stock
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_medicamento INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'transferencia') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de transferencias entre farmacias
CREATE TABLE IF NOT EXISTS transferencias_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia_origen INT NOT NULL,
    id_farmacia_destino INT NOT NULL,
    id_medicamento INT NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_farmacia_origen) REFERENCES farmacias(id),
    FOREIGN KEY (id_farmacia_destino) REFERENCES farmacias(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Insertar datos de ejemplo
INSERT INTO categorias_medicamentos (nombre, tipo_venta, descripcion) VALUES
('Analgésicos', 'venta_libre', 'Medicamentos para el dolor'),
('Antibióticos', 'con_receta', 'Medicamentos antibióticos'),
('Antihistamínicos', 'venta_libre', 'Medicamentos para alergias'),
('Vitaminas', 'venta_libre', 'Suplementos vitamínicos'),
('Antihipertensivos', 'con_receta', 'Medicamentos para presión arterial');

INSERT INTO proveedores (nombre, contacto, telefono, email) VALUES
('Laboratorio Central', 'Dr. Juan Pérez', '011-4567-8901', 'ventas@labcentral.com'),
('Farmacéutica Norte', 'Lic. María García', '011-4567-8902', 'contacto@farmnorte.com'),
('Distribuidora Sur', 'Sr. Carlos López', '011-4567-8903', 'info@distsur.com'),
('Medicamentos Plus', 'Dra. Ana Martínez', '011-4567-8904', 'ventas@medplus.com');

INSERT INTO farmacias (nombre, direccion, telefono, latitud, longitud, distrito, de_turno, horario_apertura, horario_cierre, servicios) VALUES
('Farmacia Central', 'Av. Corrientes 1234, CABA', '011-4567-8901', -34.6037, -58.3816, 'Centro', TRUE, '00:00:00', '23:59:59', 'Delivery 24hs, Vacunas, Análisis clínicos'),
('Farmacia Norte', 'Av. Santa Fe 5678, CABA', '011-4567-8902', -34.5895, -58.3974, 'Palermo', FALSE, '08:00:00', '22:00:00', 'Delivery, Vacunas'),
('Farmacia Sur', 'Av. Rivadavia 9012, CABA', '011-4567-8903', -34.6227, -58.3731, 'San Telmo', FALSE, '08:00:00', '20:00:00', 'Delivery'),
('Farmacia Oeste', 'Av. Córdoba 3456, CABA', '011-4567-8904', -34.6158, -58.4333, 'Villa Crespo', TRUE, '00:00:00', '23:59:59', 'Delivery 24hs, Vacunas'),
('Farmacia Este', 'Av. Callao 7890, CABA', '011-4567-8905', -34.6097, -58.3731, 'Recoleta', FALSE, '09:00:00', '21:00:00', 'Vacunas, Análisis clínicos');

INSERT INTO medicamentos (nombre, descripcion, codigo_barras, precio, id_categoria, id_proveedor, stock_general, stock_minimo) VALUES
('Ibuprofeno 400mg', 'Analgésico y antiinflamatorio', '7891234567890', 850.00, 1, 1, 100, 20),
('Paracetamol 500mg', 'Analgésico y antipirético', '7891234567891', 650.00, 1, 1, 150, 25),
('Amoxicilina 500mg', 'Antibiótico de amplio espectro', '7891234567892', 1200.00, 2, 2, 50, 15),
('Loratadina 10mg', 'Antihistamínico', '7891234567893', 750.00, 3, 3, 80, 20),
('Vitamina C 1000mg', 'Suplemento vitamínico', '7891234567894', 450.00, 4, 4, 200, 30),
('Enalapril 10mg', 'Antihipertensivo', '7891234567895', 950.00, 5, 1, 60, 15);

-- Crear usuario administrador principal
INSERT INTO usuarios (usuario_nombre, email, password, rol) VALUES
('admin', 'admin@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar stock inicial para todas las farmacias
INSERT INTO stock_farmacias (id_farmacia, id_medicamento, stock, stock_minimo)
SELECT f.id, m.id, FLOOR(RAND() * 50) + 10, m.stock_minimo
FROM farmacias f
CROSS JOIN medicamentos m
WHERE f.activa = 1 AND m.activo = 1;

-- Crear índices para mejorar rendimiento
CREATE INDEX idx_farmacias_turno ON farmacias(de_turno);
CREATE INDEX idx_farmacias_distrito ON farmacias(distrito);
CREATE INDEX idx_stock_farmacias_farmacia ON stock_farmacias(id_farmacia);
CREATE INDEX idx_stock_farmacias_medicamento ON stock_farmacias(id_medicamento);
CREATE INDEX idx_ventas_farmacia ON ventas(id_farmacia);
CREATE INDEX idx_ventas_fecha ON ventas(fecha);
CREATE INDEX idx_movimientos_fecha ON movimientos_stock(fecha);
CREATE INDEX idx_usuarios_farmacia ON usuarios(id_farmacia);

SELECT 'Sistema de farmacias creado exitosamente' as mensaje;
