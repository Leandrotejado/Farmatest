-- Script para crear tabla de farmacias y relacionar medicamentos con farmacias
USE farmacia;

-- Tabla de farmacias
CREATE TABLE IF NOT EXISTS farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    horario_apertura TIME,
    horario_cierre TIME,
    de_turno BOOLEAN DEFAULT FALSE,
    obras_sociales TEXT,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para relacionar medicamentos con farmacias (stock por farmacia)
CREATE TABLE IF NOT EXISTS medicamentos_farmacias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_medicamento INT NOT NULL,
    id_farmacia INT NOT NULL,
    stock_disponible INT DEFAULT 0,
    precio_especial DECIMAL(10,2),
    descuento_obra_social DECIMAL(5,2) DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_farmacia) REFERENCES farmacias(id) ON DELETE CASCADE,
    UNIQUE KEY unique_medicamento_farmacia (id_medicamento, id_farmacia)
);

-- Insertar farmacias de ejemplo (las mismas del mapa)
INSERT INTO farmacias (nombre, direccion, telefono, latitud, longitud, de_turno, obras_sociales) VALUES 
('Farmacia San Isidro', 'Av. del Libertador 1234, San Isidro', '011 4743-5678', -34.4732, -58.5156, TRUE, 'OSDE, Swiss Medical, Galeno'),
('Farmacia Pilar', 'Ruta 8 Km 50, Pilar', '0230 442-3456', -34.4580, -58.9140, TRUE, 'OSDE, Medifé, Omint'),
('Farmacia Central La Plata', 'Av. 7 N° 1234, La Plata', '0221 456-7890', -34.9214, -57.9544, TRUE, 'OSDE, Swiss Medical, Galeno'),
('Farmacia Mar del Plata', 'Av. Colón 1234, Mar del Plata', '0223 456-7890', -38.0023, -57.5575, TRUE, 'OSDE, Medifé, Omint, Sancor Salud'),
('Farmacia Tandil', 'Av. España 1234, Tandil', '0249 442-7890', -37.3282, -59.1369, TRUE, 'OSDE, Swiss Medical, Galeno'),
('Farmacia Bahía Blanca', 'Av. Alem 567, Bahía Blanca', '0291 456-1234', -38.7183, -62.2663, TRUE, 'OSDE, Medifé, Swiss Medical'),
('Farmacia Pergamino', 'Av. de Mayo 567, Pergamino', '02477 42-9012', -33.8000, -60.0000, TRUE, 'OSDE, Galeno, Omint'),
('Farmacia Quilmes', 'Rivadavia 1234, Quilmes', '011 4256-7890', -34.7290, -58.2634, TRUE, 'OSDE, Swiss Medical, Medifé'),
('Farmacia Lomas de Zamora', 'Av. Hipólito Yrigoyen 567, Lomas de Zamora', '011 4289-1234', -34.7667, -58.4000, TRUE, 'OSDE, Galeno, Swiss Medical'),
('Farmacia Morón', 'Av. Rivadavia 1234, Morón', '011 4629-1234', -34.6000, -58.7000, TRUE, 'OSDE, Medifé, Omint');

-- Asignar algunos medicamentos a farmacias (ejemplo)
INSERT INTO medicamentos_farmacias (id_medicamento, id_farmacia, stock_disponible, precio_especial, descuento_obra_social) 
SELECT m.id, f.id, 
    CASE 
        WHEN f.id = 1 THEN 50  -- Farmacia San Isidro
        WHEN f.id = 2 THEN 30  -- Farmacia Pilar
        WHEN f.id = 3 THEN 40  -- Farmacia Central La Plata
        ELSE 25
    END,
    m.precio * 0.95, -- 5% de descuento
    10.00 -- 10% descuento obra social
FROM medicamentos m
CROSS JOIN farmacias f
WHERE m.id IN (1, 2, 3, 4, 5) -- Solo los primeros 5 medicamentos
AND f.id IN (1, 2, 3, 4, 5); -- Solo las primeras 5 farmacias
