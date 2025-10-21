-- Datos iniciales para el sistema de farmacia
USE farmacia;

-- Insertar categorías de medicamentos
INSERT INTO categorias (nombre) VALUES 
-- Categorías por tipo de venta
('Venta Libre'),
('Con Receta'),
-- Categorías por tipo de medicamento
('Analgésicos'),
('Antibióticos'),
('Antiinflamatorios'),
('Antihistamínicos'),
('Vitaminas'),
('Medicamentos para la presión'),
('Medicamentos para la diabetes'),
('Cremas y ungüentos'),
('Antipiréticos'),
('Antitusivos'),
('Expectorantes'),
('Laxantes'),
('Antidiarreicos'),
('Antiácidos'),
('Antiespasmódicos'),
('Sedantes'),
('Antidepresivos'),
('Anticonvulsivos'),
('Hormonas'),
('Vacunas'),
('Suplementos nutricionales');

-- Insertar categorías de proveedores
INSERT INTO categorias_proveedores (nombre, descripcion) VALUES 
('Laboratorios Multinacionales', 'Grandes laboratorios farmacéuticos internacionales'),
('Laboratorios Nacionales', 'Laboratorios farmacéuticos argentinos'),
('Distribuidoras', 'Empresas especializadas en distribución farmacéutica'),
('Importadores', 'Empresas que importan medicamentos del exterior'),
('Laboratorios Genéricos', 'Especializados en medicamentos genéricos'),
('Suplementos y Vitaminas', 'Proveedores de suplementos nutricionales y vitaminas'),
('Equipos Médicos', 'Proveedores de equipos e insumos médicos');

-- Insertar proveedores con sus categorías
INSERT INTO proveedores (nombre, telefono, email, direccion, id_categoria) VALUES 
('Laboratorio Bayer', '011-4567-8900', 'ventas@bayer.com', 'Av. Corrientes 1234, CABA', 1),
('Laboratorio Pfizer', '011-4567-8901', 'ventas@pfizer.com', 'Av. Santa Fe 5678, CABA', 1),
('Laboratorio Roche', '011-4567-8902', 'ventas@roche.com', 'Av. Córdoba 9012, CABA', 1),
('Laboratorio Novartis', '011-4567-8903', 'ventas@novartis.com', 'Av. Callao 3456, CABA', 1),
('Laboratorio Sanofi', '011-4567-8904', 'ventas@sanofi.com', 'Av. 9 de Julio 7890, CABA', 1),
('Laboratorio Elea', '011-4567-8905', 'ventas@elea.com', 'Av. Rivadavia 2000, CABA', 2),
('Laboratorio Beta', '011-4567-8906', 'ventas@beta.com', 'Av. Cabildo 3000, CABA', 2),
('Distribuidora Farmacéutica Central', '011-4567-8907', 'ventas@dfc.com', 'Av. Corrientes 4000, CABA', 3),
('Importadora Medicamentos SA', '011-4567-8908', 'ventas@importadora.com', 'Av. Santa Fe 5000, CABA', 4),
('Genéricos del Sur', '011-4567-8909', 'ventas@genericos.com', 'Av. Córdoba 6000, CABA', 5),
('Suplementos Naturales', '011-4567-8910', 'ventas@suplementos.com', 'Av. Callao 7000, CABA', 6),
('Equipos Médicos Pro', '011-4567-8911', 'ventas@equipos.com', 'Av. 9 de Julio 8000, CABA', 7);

-- Insertar medicamentos de ejemplo
INSERT INTO medicamentos (nombre, descripcion, stock, precio, id_categoria, id_proveedor, fecha_vencimiento) VALUES 
-- Medicamentos de Venta Libre (categoría 1)
('Paracetamol 500mg', 'Analgésico y antipirético', 100, 250.00, 1, 1, '2025-12-31'),
('Ibuprofeno 600mg', 'Antiinflamatorio no esteroideo', 75, 320.00, 1, 2, '2025-11-30'),
('Aspirina 100mg', 'Analgésico y antiagregante plaquetario', 110, 120.00, 1, 5, '2025-04-10'),
('Vitamina C 1000mg', 'Suplemento vitamínico', 120, 150.00, 1, 6, '2026-01-15'),
('Diclofenac Gel', 'Gel antiinflamatorio tópico', 90, 220.00, 1, 4, '2025-05-15'),

-- Medicamentos Con Receta (categoría 2)
('Amoxicilina 500mg', 'Antibiótico de amplio espectro', 50, 450.00, 2, 3, '2025-10-15'),
('Metformina 850mg', 'Antidiabético oral', 40, 380.00, 2, 2, '2025-07-25'),
('Losartán 50mg', 'Antihipertensivo', 35, 420.00, 2, 3, '2025-06-20'),
('Omeprazol 20mg', 'Inhibidor de bomba de protones', 60, 280.00, 2, 1, '2025-08-30'),

-- Medicamentos por tipo específico
('Loratadina 10mg', 'Antihistamínico para alergias', 80, 180.00, 4, 4, '2025-09-20');

-- Insertar farmacias de ejemplo
INSERT INTO farmacias (nombre, direccion, telefono, lat, lng, horario, obras_sociales) VALUES 
('Farmacia Central', 'Av. Corrientes 1234, CABA', '011-4567-8900', -34.6037, -58.3816, 'Lunes a Viernes: 8:00-20:00, Sábados: 8:00-18:00', 'OSDE, Swiss Medical, Galeno'),
('Farmacia del Sol', 'Av. Santa Fe 5678, CABA', '011-4567-8901', -34.5895, -58.3974, '24 horas', 'OSDE, Swiss Medical, Galeno, Medicus'),
('Farmacia San Martín', 'Av. Córdoba 9012, CABA', '011-4567-8902', -34.5950, -58.3830, 'Lunes a Viernes: 9:00-19:00, Sábados: 9:00-17:00', 'OSDE, Swiss Medical'),
('Farmacia Libertad', 'Av. Callao 3456, CABA', '011-4567-8903', -34.6037, -58.3816, 'Lunes a Viernes: 8:30-20:30', 'Galeno, Medicus'),
('Farmacia 24hs Norte', 'Av. 9 de Julio 7890, CABA', '011-4567-8904', -34.6037, -58.3816, '24 horas', 'OSDE, Swiss Medical, Galeno, Medicus');

-- Insertar stock de medicamentos por farmacia
INSERT INTO medicamentos_farmacias (medicamento_id, farmacia_id, stock, precio_especial) VALUES 
-- Farmacia Central (ID: 1)
(1, 1, 50, 250.00),   -- Paracetamol
(2, 1, 30, 320.00),   -- Ibuprofeno
(3, 1, 25, 120.00),   -- Aspirina
(4, 1, 40, 150.00),   -- Vitamina C
(5, 1, 20, 220.00),   -- Diclofenac Gel
(6, 1, 15, 450.00),   -- Amoxicilina
(7, 1, 10, 380.00),   -- Metformina

-- Farmacia del Sol (ID: 2)
(1, 2, 80, 245.00),   -- Paracetamol
(2, 2, 45, 315.00),   -- Ibuprofeno
(3, 2, 60, 115.00),   -- Aspirina
(4, 2, 70, 145.00),   -- Vitamina C
(5, 2, 35, 215.00),   -- Diclofenac Gel
(6, 2, 25, 445.00),   -- Amoxicilina
(8, 2, 20, 420.00),   -- Losartán
(9, 2, 15, 280.00),   -- Omeprazol

-- Farmacia San Martín (ID: 3)
(1, 3, 35, 255.00),   -- Paracetamol
(2, 3, 25, 325.00),   -- Ibuprofeno
(4, 3, 50, 155.00),   -- Vitamina C
(5, 3, 15, 225.00),   -- Diclofenac Gel
(6, 3, 10, 455.00),   -- Amoxicilina
(7, 3, 8, 385.00),    -- Metformina
(10, 3, 25, 180.00),  -- Loratadina

-- Farmacia Libertad (ID: 4)
(1, 4, 40, 248.00),   -- Paracetamol
(3, 4, 30, 118.00),   -- Aspirina
(4, 4, 45, 148.00),   -- Vitamina C
(5, 4, 25, 218.00),   -- Diclofenac Gel
(8, 4, 12, 418.00),   -- Losartán
(9, 4, 18, 278.00),   -- Omeprazol
(10, 4, 20, 175.00),  -- Loratadina

-- Farmacia 24hs Norte (ID: 5)
(1, 5, 100, 240.00),  -- Paracetamol
(2, 5, 60, 310.00),   -- Ibuprofeno
(3, 5, 80, 110.00),   -- Aspirina
(4, 5, 90, 140.00),   -- Vitamina C
(5, 5, 50, 210.00),   -- Diclofenac Gel
(6, 5, 30, 440.00),   -- Amoxicilina
(7, 5, 25, 375.00),   -- Metformina
(8, 5, 20, 415.00),   -- Losartán
(9, 5, 30, 275.00),   -- Omeprazol
(10, 5, 35, 170.00);  -- Loratadina

-- Insertar usuarios administradores por defecto
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador Principal', 'admin@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Administrador Turno Mañana', 'admin.mañana@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Administrador Turno Noche', 'admin.noche@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Nota: Las contraseñas por defecto son "password" para todos los usuarios
-- Es recomendable cambiar estas contraseñas después del primer inicio de sesión
