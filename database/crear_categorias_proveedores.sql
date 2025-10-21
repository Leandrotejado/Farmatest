-- Crear tabla de categorías de medicamentos
CREATE TABLE IF NOT EXISTS categorias_medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    tipo_venta ENUM('venta_libre', 'receta_simple', 'receta_archivada') NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de proveedores
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    ciudad VARCHAR(100),
    provincia VARCHAR(100),
    codigo_postal VARCHAR(10),
    cuit VARCHAR(15),
    observaciones TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar categorías de ejemplo
INSERT INTO categorias_medicamentos (nombre, descripcion, tipo_venta) VALUES
('Analgésicos', 'Medicamentos para el dolor', 'venta_libre'),
('Antibióticos', 'Medicamentos antibacterianos', 'receta_archivada'),
('Antiinflamatorios', 'Medicamentos para reducir inflamación', 'venta_libre'),
('Antihistamínicos', 'Medicamentos para alergias', 'venta_libre'),
('Antitusivos', 'Medicamentos para la tos', 'venta_libre'),
('Broncodilatadores', 'Medicamentos para problemas respiratorios', 'receta_simple'),
('Cardiovasculares', 'Medicamentos para el corazón', 'receta_archivada'),
('Dermatológicos', 'Medicamentos para la piel', 'venta_libre'),
('Digestivos', 'Medicamentos para problemas digestivos', 'venta_libre'),
('Diuréticos', 'Medicamentos para eliminar líquidos', 'receta_simple'),
('Hormonales', 'Medicamentos hormonales', 'receta_archivada'),
('Neurológicos', 'Medicamentos para el sistema nervioso', 'receta_archivada'),
('Oftalmológicos', 'Medicamentos para los ojos', 'venta_libre'),
('Otorrinolaringológicos', 'Medicamentos para oído, nariz y garganta', 'venta_libre'),
('Psiquiátricos', 'Medicamentos para salud mental', 'receta_archivada'),
('Vitaminas y Suplementos', 'Suplementos vitamínicos y minerales', 'venta_libre');

-- Insertar proveedores de ejemplo
INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, ciudad, provincia, cuit) VALUES
('Laboratorios Pfizer Argentina', 'Juan Pérez', '(011) 1234-5678', 'ventas@pfizer.com.ar', 'Av. Corrientes 1234', 'Buenos Aires', 'CABA', '30-12345678-9'),
('Roche Argentina S.A.', 'María González', '(011) 2345-6789', 'contacto@roche.com.ar', 'Av. Santa Fe 2000', 'Buenos Aires', 'CABA', '30-23456789-0'),
('Novartis Argentina', 'Carlos Rodríguez', '(011) 3456-7890', 'info@novartis.com.ar', 'Av. 9 de Julio 1500', 'Buenos Aires', 'CABA', '30-34567890-1'),
('GSK Argentina', 'Ana Martínez', '(011) 4567-8901', 'ventas@gsk.com.ar', 'Av. Rivadavia 3000', 'Buenos Aires', 'CABA', '30-45678901-2'),
('Sanofi Argentina', 'Luis Fernández', '(011) 5678-9012', 'contacto@sanofi.com.ar', 'Av. Córdoba 2500', 'Buenos Aires', 'CABA', '30-56789012-3'),
('Bayer Argentina', 'Sofia López', '(011) 6789-0123', 'info@bayer.com.ar', 'Av. Callao 1800', 'Buenos Aires', 'CABA', '30-67890123-4'),
('Merck Argentina', 'Diego Sánchez', '(011) 7890-1234', 'ventas@merck.com.ar', 'Av. Corrientes 3000', 'Buenos Aires', 'CABA', '30-78901234-5'),
('Abbott Argentina', 'Laura Torres', '(011) 8901-2345', 'contacto@abbott.com.ar', 'Av. Santa Fe 4000', 'Buenos Aires', 'CABA', '30-89012345-6'),
('Johnson & Johnson', 'Roberto Morales', '(011) 9012-3456', 'info@jnj.com.ar', 'Av. 9 de Julio 2500', 'Buenos Aires', 'CABA', '30-90123456-7'),
('Boehringer Ingelheim', 'Carmen Ruiz', '(011) 0123-4567', 'ventas@boehringer.com.ar', 'Av. Rivadavia 4000', 'Buenos Aires', 'CABA', '30-01234567-8');

-- Actualizar tabla medicamentos para incluir categoría y proveedor
ALTER TABLE medicamentos 
ADD COLUMN IF NOT EXISTS id_categoria INT,
ADD COLUMN IF NOT EXISTS id_proveedor INT,
ADD COLUMN IF NOT EXISTS tipo_venta ENUM('venta_libre', 'receta_simple', 'receta_archivada') DEFAULT 'venta_libre',
ADD FOREIGN KEY (id_categoria) REFERENCES categorias_medicamentos(id),
ADD FOREIGN KEY (id_proveedor) REFERENCES proveedores(id);

-- Crear índices para optimizar búsquedas de autocompletado
CREATE INDEX idx_categorias_nombre ON categorias_medicamentos(nombre);
CREATE INDEX idx_proveedores_nombre ON proveedores(nombre);
CREATE INDEX idx_medicamentos_nombre ON medicamentos(nombre);
CREATE INDEX idx_categorias_activa ON categorias_medicamentos(activa);
CREATE INDEX idx_proveedores_activo ON proveedores(activo);
