<?php
/**
 * Configuración de OAuth para múltiples proveedores - FarmaXpress
 * 
 * INSTRUCCIONES PARA CONFIGURAR:
 * 
 * GOOGLE:
 * 1. Google Cloud Console: https://console.cloud.google.com/
 * 2. Crear proyecto → Habilitar Google Identity API
 * 3. Crear credenciales OAuth 2.0
 * 
 * APPLE:
 * 1. Apple Developer Portal: https://developer.apple.com/
 * 2. Crear App ID → Configurar Sign in with Apple
 * 3. Crear Service ID y configurar dominios
 * 
 * MICROSOFT:
 * 1. Azure Portal: https://portal.azure.com/
 * 2. Registro de aplicaciones → Crear nueva aplicación
 * 3. Configurar permisos y URIs de redirección
 */

return [
    // Configuración de Google OAuth
    'google' => [
        'enabled' => true,
        'client_id' => 'tu-client-id.googleusercontent.com',
        'client_secret' => 'tu-client-secret',
        'redirect_uri' => 'http://localhost/farmatest/auth/google-callback.php',
        'scope' => 'email profile',
    ],
    
    // Configuración de Apple OAuth
    'apple' => [
        'enabled' => true,
        'client_id' => 'com.tuapp.farmaxpress',  // Service ID de Apple
        'team_id' => 'tu-team-id',
        'key_id' => 'tu-key-id',
        'private_key' => 'tu-private-key',  // Archivo .p8 convertido a string
        'redirect_uri' => 'http://localhost/farmatest/auth/apple-callback.php',
        'scope' => 'name email',
    ],
    
    // Configuración de Microsoft OAuth
    'microsoft' => [
        'enabled' => true,
        'client_id' => 'tu-client-id',
        'client_secret' => 'tu-client-secret',
        'tenant_id' => 'common',  // o tu tenant específico
        'redirect_uri' => 'http://localhost/farmatest/auth/microsoft-callback.php',
        'scope' => 'openid email profile',
    ],
    
    // Configuración general
    'base_url' => 'http://localhost/farmatest',
    'auto_register' => true,
    'default_role' => 'cliente',
    'debug' => false,
    'test_mode' => false,
];
