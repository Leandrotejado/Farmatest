-- Script para crear todas las tablas necesarias del sistema administrativo
-- Ejecutar este script en la base de datos 'farmacia'

-- Tabla de movimientos de stock
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_medicamento INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de transferencias de stock
CREATE TABLE IF NOT EXISTS transferencias_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_medicamento_origen INT NOT NULL,
    id_medicamento_destino INT NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_medicamento_origen) REFERENCES medicamentos(id),
    FOREIGN KEY (id_medicamento_destino) REFERENCES medicamentos(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_finalizacion TIMESTAMP NULL,
    estado ENUM('en_proceso', 'completada', 'cancelada') DEFAULT 'en_proceso',
    total DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de detalles de venta
CREATE TABLE IF NOT EXISTS venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_medicamento INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id)
);

-- Actualizar tabla medicamentos para agregar campos faltantes
ALTER TABLE medicamentos 
ADD COLUMN IF NOT EXISTS activo BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS fecha_vencimiento DATE NULL,
ADD COLUMN IF NOT EXISTS requiere_receta BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS stock_minimo INT DEFAULT 10;

-- Actualizar tabla usuarios para agregar campos faltantes
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS activo BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Insertar datos de ejemplo si no existen
INSERT IGNORE INTO categorias_medicamentos (nombre, tipo_venta, activa) VALUES
('Analgésicos', 'Venta libre', TRUE),
('Antibióticos', 'Con receta', TRUE),
('Antiinflamatorios', 'Venta libre', TRUE),
('Antihistamínicos', 'Venta libre', TRUE),
('Antipiréticos', 'Venta libre', TRUE),
('Vitaminas', 'Venta libre', TRUE),
('Antiacidos', 'Venta libre', TRUE),
('Cremas y Pomadas', 'Venta libre', TRUE);

INSERT IGNORE INTO proveedores (nombre, contacto, telefono, email, direccion, activo) VALUES
('Laboratorios Pfizer', 'Juan Pérez', '011-4567-8901', 'contacto@pfizer.com', 'Av. Corrientes 1234', TRUE),
('Roche Argentina', 'María González', '011-4567-8902', 'contacto@roche.com', 'Av. Santa Fe 5678', TRUE),
('Bayer Argentina', 'Carlos López', '011-4567-8903', 'contacto@bayer.com', 'Av. Córdoba 9012', TRUE),
('Novartis Argentina', 'Ana Martínez', '011-4567-8904', 'contacto@novartis.com', 'Av. Rivadavia 3456', TRUE),
('GSK Argentina', 'Roberto Silva', '011-4567-8905', 'contacto@gsk.com', 'Av. Callao 7890', TRUE);

-- Insertar medicamentos de ejemplo
INSERT IGNORE INTO medicamentos (nombre, descripcion, precio, stock, stock_minimo, id_categoria, id_proveedor, codigo_barras, fecha_vencimiento, requiere_receta, activo) VALUES
('Ibuprofeno 400mg', 'Antiinflamatorio y analgésico', 850.00, 50, 10, 3, 1, '7891234567890', '2025-12-31', FALSE, TRUE),
('Paracetamol 500mg', 'Analgésico y antipirético', 650.00, 75, 15, 1, 2, '7891234567891', '2025-10-15', FALSE, TRUE),
('Amoxicilina 500mg', 'Antibiótico de amplio espectro', 1200.00, 30, 5, 2, 3, '7891234567892', '2025-08-20', TRUE, TRUE),
('Loratadina 10mg', 'Antihistamínico', 950.00, 40, 8, 4, 4, '7891234567893', '2025-11-30', FALSE, TRUE),
('Vitamina C 1000mg', 'Suplemento vitamínico', 750.00, 60, 12, 6, 5, '7891234567894', '2025-09-25', FALSE, TRUE),
('Omeprazol 20mg', 'Protector gástrico', 1100.00, 25, 5, 7, 1, '7891234567895', '2025-07-18', FALSE, TRUE),
('Hidrocortisona 1%', 'Crema antiinflamatoria', 800.00, 35, 7, 8, 2, '7891234567896', '2025-06-12', FALSE, TRUE),
('Aspirina 500mg', 'Analgésico y antiagregante', 600.00, 80, 20, 1, 3, '7891234567897', '2025-05-30', FALSE, TRUE);

-- Crear usuario administrador por defecto si no existe
INSERT IGNORE INTO usuarios (usuario_nombre, email, password, rol, activo) VALUES
('admin', 'admin@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE);

-- Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_movimientos_fecha ON movimientos_stock(fecha);
CREATE INDEX IF NOT EXISTS idx_movimientos_medicamento ON movimientos_stock(id_medicamento);
CREATE INDEX IF NOT EXISTS idx_ventas_fecha ON ventas(fecha);
CREATE INDEX IF NOT EXISTS idx_ventas_usuario ON ventas(id_usuario);
CREATE INDEX IF NOT EXISTS idx_ventas_estado ON ventas(estado);
CREATE INDEX IF NOT EXISTS idx_venta_detalles_venta ON venta_detalles(id_venta);
CREATE INDEX IF NOT EXISTS idx_medicamentos_activo ON medicamentos(activo);
CREATE INDEX IF NOT EXISTS idx_medicamentos_stock ON medicamentos(stock);
CREATE INDEX IF NOT EXISTS idx_usuarios_activo ON usuarios(activo);
CREATE INDEX IF NOT EXISTS idx_usuarios_rol ON usuarios(rol);

-- Mostrar mensaje de éxito
SELECT 'Sistema administrativo configurado exitosamente' as mensaje;
