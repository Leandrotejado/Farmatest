-- Script para actualizar la tabla usuarios y agregar el rol 'cliente'
USE farmacia;

-- Agregar el rol 'cliente' al ENUM existente
ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'empleado', 'cliente') DEFAULT 'cliente';

-- Insertar algunos usuarios de prueba para clientes
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Juan Pérez', 'juan.perez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('María González', 'maria.gonzalez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('Carlos López', 'carlos.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

-- Nota: Las contraseñas por defecto son "password" para todos los usuarios
