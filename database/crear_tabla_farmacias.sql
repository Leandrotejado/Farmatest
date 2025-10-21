-- Crear tabla de farmacias para gestión de turnos
CREATE TABLE IF NOT EXISTS farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion TEXT NOT NULL,
    telefono VARCHAR(20),
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
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

-- Insertar farmacias de ejemplo
INSERT INTO farmacias (nombre, direccion, telefono, latitud, longitud, distrito, de_turno, horario_apertura, horario_cierre, servicios) VALUES
('Farmacia Central', 'Av. Corrientes 1234, CABA', '(011) 1234-5678', -34.6037, -58.3816, 'CABA', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery, Vacunas'),
('Farmacia San Telmo', 'Defensa 1200, San Telmo, CABA', '(011) 2345-6789', -34.6208, -58.3731, 'CABA', FALSE, '08:00:00', '20:00:00', 'Atención diurna, Vacunas'),
('Farmacia Palermo', 'Av. Santa Fe 2000, Palermo, CABA', '(011) 3456-7890', -34.5889, -58.3974, 'CABA', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery'),
('Farmacia Escobar', 'Av. Tapia de Cruz 1500, Escobar', '(03488) 456-789', -34.3500, -58.7833, 'Escobar', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery'),
('Farmacia Centro Escobar', 'Belgrano 600, Escobar', '(03488) 567-890', -34.3450, -58.7800, 'Escobar', FALSE, '08:00:00', '20:00:00', 'Atención diurna'),
('Farmacia Tigre', 'Av. Liniers 1000, Tigre', '(011) 4749-0000', -34.4167, -58.5833, 'Tigre', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery'),
('Farmacia Centro Tigre', 'Av. Cazón 800, Tigre', '(011) 4749-1111', -34.4100, -58.5800, 'Tigre', FALSE, '08:00:00', '20:00:00', 'Atención diurna'),
('Farmacia Pilar', 'Av. Tratado del Pilar 2000, Pilar', '(0230) 456-7890', -34.4667, -58.9000, 'Pilar', FALSE, '08:00:00', '20:00:00', 'Atención diurna'),
('Farmacia Centro Pilar', 'San Martín 800, Pilar', '(0230) 567-8901', -34.4600, -58.8950, 'Pilar', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery'),
('Farmacia La Plata', 'Av. 7 1200, La Plata', '(0221) 123-4567', -34.9214, -57.9544, 'La Plata', TRUE, '00:00:00', '23:59:59', 'Atención 24hs, Delivery, Vacunas');

-- Crear índices para optimizar consultas
CREATE INDEX idx_farmacias_activa ON farmacias(activa);
CREATE INDEX idx_farmacias_turno ON farmacias(de_turno);
CREATE INDEX idx_farmacias_distrito ON farmacias(distrito);
CREATE INDEX idx_farmacias_coordenadas ON farmacias(latitud, longitud);
