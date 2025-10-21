# 📧 Configuración de Verificación por Email con Gmail

## 🚀 Pasos para Configurar Gmail

### 1. **Activar Verificación en 2 Pasos**
1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Selecciona "Seguridad" en el menú lateral
3. Busca "Verificación en 2 pasos" y actívala
4. Sigue las instrucciones para configurarla

### 2. **Generar Contraseña de Aplicación**
1. En la misma sección de "Seguridad"
2. Busca "Contraseñas de aplicaciones"
3. Selecciona "Aplicación" → "Otro (nombre personalizado)"
4. Escribe "FarmaXpress" como nombre
5. Copia la contraseña generada (16 caracteres)

### 3. **Configurar el Sistema**
1. Abre el archivo `config/email.php`
2. Reemplaza los siguientes valores:

```php
'smtp_username' => 'tu-email@gmail.com',     // Tu email de Gmail
'smtp_password' => 'tu-app-password',        // La contraseña de aplicación
'from_email' => 'tu-email@gmail.com',        // Tu email de Gmail
'from_name' => 'FarmaXpress',                // Nombre que aparecerá en los emails
```

### 4. **Ejemplo de Configuración**
```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'farmacia@gmail.com',
    'smtp_password' => 'abcd efgh ijkl mnop',  // Contraseña de aplicación
    'from_email' => 'farmacia@gmail.com',
    'from_name' => 'FarmaXpress',
    // ... resto de configuración
];
```

## 🔧 Instalación de PHPMailer (Opcional)

Para mejor compatibilidad, puedes instalar PHPMailer:

```bash
composer require phpmailer/phpmailer
```

## 📋 Funcionalidades Implementadas

### ✅ **Registro con Verificación**
- Los usuarios se registran pero no pueden iniciar sesión hasta verificar su email
- Se envía un email automático con enlace de verificación
- El enlace expira en 24 horas

### ✅ **Verificación de Email**
- Página dedicada para verificar cuentas (`public/verify-email.php`)
- Interfaz amigable con confirmación visual
- Manejo de errores (token expirado, inválido, etc.)

### ✅ **Reenvío de Verificación**
- Página para reenviar emails de verificación (`public/resend-verification.php`)
- Genera nuevos tokens si el anterior expiró
- Interfaz simple para ingresar email

### ✅ **Login con Verificación**
- El sistema verifica que el email esté confirmado antes de permitir login
- Mensajes claros si la cuenta no está verificada
- Enlaces directos para reenviar verificación

### ✅ **Templates de Email**
- Emails HTML profesionales con diseño responsive
- Versión de texto plano como fallback
- Branding consistente con FarmaXpress

## 🎨 Personalización de Emails

Los templates están en `services/EmailService.php`:

- **Verificación de cuenta**: Template azul con botón de verificación
- **Recuperación de contraseña**: Template rojo con botón de restablecimiento
- **Diseño responsive**: Se adapta a móviles y desktop

## 🔒 Seguridad

- **Tokens seguros**: Generados con `random_bytes(32)`
- **Expiración automática**: Tokens expiran en 24 horas
- **Validación de email**: Formato de email validado
- **Protección contra spam**: Rate limiting implícito

## 🧪 Testing

Para probar el sistema:

1. **Modo de prueba**: Cambia `test_mode` a `true` en `config/email.php`
2. **Email de prueba**: Configura `test_email` con tu email
3. **Debug**: Activa `debug` para ver errores detallados

## 📞 Soporte

Si tienes problemas:

1. **Verifica la configuración** en `config/email.php`
2. **Revisa los logs** de PHP para errores
3. **Prueba con modo debug** activado
4. **Verifica que Gmail** permita aplicaciones menos seguras (si es necesario)

## 🚨 Notas Importantes

- **Nunca compartas** tu contraseña de aplicación
- **Mantén seguro** el archivo `config/email.php`
- **Usa HTTPS** en producción para mayor seguridad
- **Monitorea** los logs de email para detectar problemas

---

¡Con esta configuración tendrás un sistema completo de verificación por email! 🎉