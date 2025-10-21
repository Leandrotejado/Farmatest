<?php
/**
 * Configuración de Google OAuth para FarmaXpress
 * 
 * INSTRUCCIONES PARA CONFIGURAR GOOGLE OAUTH:
 * 
 * 1. Ir a Google Cloud Console: https://console.cloud.google.com/
 * 2. Crear un nuevo proyecto o seleccionar uno existente
 * 3. Habilitar la API de Google+ (Google Identity)
 * 4. Crear credenciales OAuth 2.0
 * 5. Configurar URIs de redirección autorizadas
 * 6. Reemplazar los valores de abajo con tu información real
 */

return [
    // Configuración de Google OAuth
    'google_client_id' => 'tu-client-id.googleusercontent.com',     // Cambiar por tu Client ID
    'google_client_secret' => 'tu-client-secret',                    // Cambiar por tu Client Secret
    'google_redirect_uri' => 'http://localhost/farmatest/auth/google-callback.php', // Cambiar por tu dominio
    
    // URLs del sitio (se detectan automáticamente, pero puedes sobrescribir)
    'base_url' => 'http://localhost/farmatest',                      // Cambiar por tu dominio
    
    // Configuración adicional
    'google_scope' => 'email profile',                              // Permisos solicitados
    'auto_register' => true,                                        // Registrar automáticamente usuarios nuevos
    'default_role' => 'cliente',                                   // Rol por defecto para usuarios de Google
    
    // Configuración de desarrollo
    'debug' => false,                                               // true para mostrar errores detallados
    'test_mode' => false,                                           // true para modo de prueba
];
