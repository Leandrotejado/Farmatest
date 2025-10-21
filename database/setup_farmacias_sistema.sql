-- Script para crear las tablas específicas del sistema de farmacias
-- Ejecutar este script en la base de datos 'farmacia'

-- Tabla de stock por farmacia
CREATE TABLE IF NOT EXISTS stock_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_medicamento INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    UNIQUE KEY unique_farmacia_medicamento (id_farmacia, id_medicamento)
);

-- Tabla de movimientos de stock por farmacia
CREATE TABLE IF NOT EXISTS movimientos_stock_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_medicamento INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
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

-- Tabla de medicamentos por farmacia (configuración específica)
CREATE TABLE IF NOT EXISTS medicamentos_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_farmacia INT NOT NULL,
    id_medicamento INT NOT NULL,
    precio_especial DECIMAL(10, 2) NULL,
    descuento_porcentaje DECIMAL(5, 2) DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id),
    UNIQUE KEY unique_farmacia_medicamento_config (id_farmacia, id_medicamento)
);

-- Tabla de ventas por farmacia
CREATE TABLE IF NOT EXISTS ventas_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_farmacia INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id)
);

-- Actualizar tabla usuarios para incluir farmacia asignada
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS id_farmacia INT NULL,
ADD FOREIGN KEY (id_farmacia) REFERENCES farmacias(id);

-- Actualizar tabla farmacias si no tiene todos los campos
ALTER TABLE farmacias 
ADD COLUMN IF NOT EXISTS servicios TEXT NULL,
ADD COLUMN IF NOT EXISTS observaciones TEXT NULL,
ADD COLUMN IF NOT EXISTS fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Insertar farmacias de ejemplo si no existen
INSERT IGNORE INTO farmacias (nombre, direccion, telefono, latitud, longitud, distrito, activa, de_turno, horario_apertura, horario_cierre, servicios, observaciones) VALUES
('Farmacia Central', 'Av. Corrientes 1234, CABA', '011-4567-8901', -34.6037, -58.3816, 'Centro', TRUE, TRUE, '00:00:00', '23:59:59', 'Delivery 24hs, Vacunas, Análisis clínicos', 'Farmacia principal del sistema'),
('Farmacia Norte', 'Av. Santa Fe 5678, CABA', '011-4567-8902', -34.5895, -58.3974, 'Palermo', TRUE, FALSE, '08:00:00', '22:00:00', 'Delivery, Vacunas', 'Sucursal norte'),
('Farmacia Sur', 'Av. Rivadavia 9012, CABA', '011-4567-8903', -34.6227, -58.3731, 'San Telmo', TRUE, FALSE, '08:00:00', '20:00:00', 'Delivery', 'Sucursal sur'),
('Farmacia Oeste', 'Av. Córdoba 3456, CABA', '011-4567-8904', -34.6158, -58.4333, 'Villa Crespo', TRUE, TRUE, '00:00:00', '23:59:59', 'Delivery 24hs, Vacunas', 'Farmacia de turno oeste'),
('Farmacia Este', 'Av. Callao 7890, CABA', '011-4567-8905', -34.6097, -58.3731, 'Recoleta', TRUE, FALSE, '09:00:00', '21:00:00', 'Vacunas, Análisis clínicos', 'Sucursal este');

-- Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_stock_farmacias_farmacia ON stock_farmacias(id_farmacia);
CREATE INDEX IF NOT EXISTS idx_stock_farmacias_medicamento ON stock_farmacias(id_medicamento);
CREATE INDEX IF NOT EXISTS idx_stock_farmacias_stock ON stock_farmacias(stock);
CREATE INDEX IF NOT EXISTS idx_movimientos_farmacias_fecha ON movimientos_stock_farmacias(fecha);
CREATE INDEX IF NOT EXISTS idx_movimientos_farmacias_farmacia ON movimientos_stock_farmacias(id_farmacia);
CREATE INDEX IF NOT EXISTS idx_transferencias_farmacias_fecha ON transferencias_farmacias(fecha);
CREATE INDEX IF NOT EXISTS idx_usuarios_farmacia ON usuarios(id_farmacia);
CREATE INDEX IF NOT EXISTS idx_farmacias_turno ON farmacias(de_turno);
CREATE INDEX IF NOT EXISTS idx_farmacias_distrito ON farmacias(distrito);

-- Insertar stock inicial para las farmacias de ejemplo
INSERT IGNORE INTO stock_farmacias (id_farmacia, id_medicamento, stock, stock_minimo) 
SELECT f.id, m.id, FLOOR(RAND() * 50) + 10, 10
FROM farmacias f
CROSS JOIN medicamentos m
WHERE f.activa = 1 AND m.activo = 1;

-- Mostrar mensaje de éxito
SELECT 'Sistema de farmacias configurado exitosamente' as mensaje;
