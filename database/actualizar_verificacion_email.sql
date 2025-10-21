-- Script para agregar campos de verificación de email a la tabla usuarios
-- Ejecutar este script después de crear la base de datos inicial

USE farmacia;

-- Agregar columnas para verificación de email
ALTER TABLE usuarios 
ADD COLUMN email_verified TINYINT(1) DEFAULT 0 COMMENT 'Indica si el email está verificado',
ADD COLUMN verification_token VARCHAR(64) NULL COMMENT 'Token para verificación de email',
ADD COLUMN verification_expires DATETIME NULL COMMENT 'Fecha de expiración del token de verificación',
ADD COLUMN email_verified_at DATETIME NULL COMMENT 'Fecha y hora de verificación del email',
ADD COLUMN password_reset_token VARCHAR(64) NULL COMMENT 'Token para recuperación de contraseña',
ADD COLUMN password_reset_expires DATETIME NULL COMMENT 'Fecha de expiración del token de recuperación';

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_verification_token ON usuarios(verification_token);
CREATE INDEX idx_password_reset_token ON usuarios(password_reset_token);
CREATE INDEX idx_email_verified ON usuarios(email_verified);

-- Actualizar usuarios existentes para que estén verificados (para no romper el sistema actual)
UPDATE usuarios SET email_verified = 1 WHERE email_verified IS NULL;

-- Mostrar la estructura actualizada
DESCRIBE usuarios;
