# 🚨 Solución al Error de Email SMTP

## ❌ **Error Actual:**
```
Warning: mail(): Failed to connect to mailserver at "localhost" port 25
```

## 🔍 **Causa del Problema:**
XAMPP no tiene configurado un servidor SMTP local, y PHP está intentando usar `localhost:25` que no existe.

## ✅ **Solución Rápida:**

### **1. Configurar Gmail SMTP:**

Edita el archivo `config/email.php` con tus credenciales reales:

```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'tu-email@gmail.com',     // ← CAMBIAR
    'smtp_password' => 'tu-app-password',         // ← CAMBIAR  
    'from_email' => 'tu-email@gmail.com',        // ← CAMBIAR
    'from_name' => 'FarmaXpress',
    // ... resto de configuración
];
```

### **2. Obtener Contraseña de Aplicación de Gmail:**

1. Ve a [Google Account](https://myaccount.google.com/)
2. **Seguridad** → **Verificación en 2 pasos** → **Activar**
3. **Seguridad** → **Contraseñas de aplicaciones** → **Crear nueva**
4. Selecciona **"Otro"** y escribe **"FarmaXpress"**
5. Copia la contraseña de **16 caracteres** generada
6. Úsala en `smtp_password` del archivo de configuración

### **3. Probar la Configuración:**

Ejecuta el script de prueba:
```
http://localhost/farmatest/test-email.php
```

## 🔧 **Métodos de Envío Implementados:**

El sistema ahora usa **3 métodos** en orden de prioridad:

1. **✅ cURL SMTP** - Más confiable para Gmail
2. **✅ PHPMailer** - Si está instalado
3. **❌ mail() nativo** - Deshabilitado (causa el error)

## 🎯 **Resultado Esperado:**

Después de configurar Gmail correctamente:
- ✅ Los emails se envían sin errores
- ✅ Los códigos de verificación llegan al usuario
- ✅ El registro funciona completamente

## 🚨 **Si Sigue Fallando:**

### **Verificar Configuración:**
```php
// En config/email.php - ejemplo correcto:
'smtp_username' => 'farmacia@gmail.com',
'smtp_password' => 'abcd efgh ijkl mnop',  // Contraseña de aplicación
'from_email' => 'farmacia@gmail.com',
```

### **Verificar Logs:**
- Revisa los logs de PHP en XAMPP
- Verifica que cURL esté habilitado
- Confirma que OpenSSL esté disponible

### **Probar Manualmente:**
Usa el script `test-email.php` para probar paso a paso.

---

**¡Con esta configuración el sistema de verificación por código funcionará perfectamente!** 🎉
