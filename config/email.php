<?php
/**
 * Configuración de Email para FarmaXpress
 * 
 * INSTRUCCIONES PARA CONFIGURAR GMAIL:
 * 
 * 1. Activar la verificación en 2 pasos en tu cuenta de Google
 * 2. Generar una "Contraseña de aplicación" específica para FarmaXpress
 * 3. Reemplazar los valores de abajo con tu información real
 * 4. Mantener este archivo seguro y no compartirlo
 */

return [
    // Configuración SMTP de Gmail
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'tu-email@gmail.com', // Cambiar por tu email de Gmail
    'smtp_password' => 'tu-app-password',     // Cambiar por tu contraseña de aplicación
    'from_email' => 'tu-email@gmail.com',    // Cambiar por tu email de Gmail
    'from_name' => 'FarmaXpress',
    
    // Configuración adicional
    'verification_expires_hours' => 24,      // Horas para expirar token de verificación
    'password_reset_expires_hours' => 1,     // Horas para expirar token de recuperación
    
    // URLs del sitio (se detectan automáticamente, pero puedes sobrescribir)
    'base_url' => null, // null = detección automática
    
    // Configuración de desarrollo (para testing)
    'debug' => false,                       // true para mostrar errores detallados
    'test_mode' => false,                   // true para no enviar emails reales
    'test_email' => 'test@example.com',     // Email para pruebas
];