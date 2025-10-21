# 🔐 Configuración de Login Social Multi-Proveedor

## 🚀 Proveedores Soportados

- ✅ **Google** - Login con cuenta de Google
- ✅ **Apple** - Sign in with Apple  
- ✅ **Microsoft** - Login con cuenta de Microsoft/Azure

## 📋 Configuración Paso a Paso

### 1. **Google OAuth**

#### Crear Aplicación en Google Cloud Console:
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Google Identity API**
4. Ve a **Credenciales** → **Crear credenciales** → **ID de cliente OAuth 2.0**
5. Configura las URIs de redirección autorizadas:
   ```
   http://localhost/farmatest/auth/google-callback.php
   https://tudominio.com/auth/google-callback.php
   ```
6. Copia el **Client ID** y **Client Secret**

#### Configurar en el sistema:
```php
// En config/oauth_providers.php
'google' => [
    'enabled' => true,
    'client_id' => 'tu-client-id.googleusercontent.com',
    'client_secret' => 'tu-client-secret',
    'redirect_uri' => 'http://localhost/farmatest/auth/google-callback.php',
    'scope' => 'email profile',
],
```

### 2. **Apple OAuth**

#### Crear Aplicación en Apple Developer Portal:
1. Ve a [Apple Developer Portal](https://developer.apple.com/)
2. Crea un **App ID** con **Sign in with Apple** habilitado
3. Crea un **Service ID** para web
4. Configura los dominios autorizados
5. Genera una **clave privada** (.p8)
6. Anota el **Team ID**, **Key ID** y **Service ID**

#### Configurar en el sistema:
```php
// En config/oauth_providers.php
'apple' => [
    'enabled' => true,
    'client_id' => 'com.tuapp.farmaxpress',  // Service ID
    'team_id' => 'tu-team-id',
    'key_id' => 'tu-key-id',
    'private_key' => 'tu-private-key',  // Contenido del archivo .p8
    'redirect_uri' => 'http://localhost/farmatest/auth/apple-callback.php',
    'scope' => 'name email',
],
```

### 3. **Microsoft OAuth**

#### Crear Aplicación en Azure Portal:
1. Ve a [Azure Portal](https://portal.azure.com/)
2. **Registro de aplicaciones** → **Nuevo registro**
3. Configura los URIs de redirección:
   ```
   http://localhost/farmatest/auth/microsoft-callback.php
   https://tudominio.com/auth/microsoft-callback.php
   ```
4. Ve a **Certificados y secretos** → **Nuevo secreto de cliente**
5. Copia el **Client ID** y **Client Secret**

#### Configurar en el sistema:
```php
// En config/oauth_providers.php
'microsoft' => [
    'enabled' => true,
    'client_id' => 'tu-client-id',
    'client_secret' => 'tu-client-secret',
    'tenant_id' => 'common',  // o tu tenant específico
    'redirect_uri' => 'http://localhost/farmatest/auth/microsoft-callback.php',
    'scope' => 'openid email profile',
],
```

## 🎯 Funcionalidades Implementadas

### ✅ **Login Social Completo:**
- **Botones elegantes** con iconos oficiales de cada proveedor
- **Colores de marca** (Google azul, Apple negro, Microsoft multicolor)
- **Hover effects** y transiciones suaves
- **Responsive design** para móviles y desktop

### ✅ **Gestión de Usuarios:**
- **Registro automático** de usuarios nuevos
- **Vinculación de cuentas** existentes por email
- **Avatares** desde los perfiles sociales
- **Verificación automática** de email

### ✅ **Seguridad:**
- **Tokens CSRF** para prevenir ataques
- **Validación de estado** en callbacks
- **Manejo seguro** de tokens de acceso
- **Expiración automática** de sesiones

### ✅ **Experiencia de Usuario:**
- **Separador visual** entre login social y tradicional
- **Mensajes claros** de éxito y error
- **Redirección automática** según rol de usuario
- **Fallback** si OAuth falla

## 🔧 Archivos del Sistema

### **Servicios:**
- `services/MultiOAuthService.php` - Servicio principal OAuth
- `services/EmailService.php` - Servicio de email (ya existente)

### **Configuración:**
- `config/oauth_providers.php` - Configuración de proveedores
- `config/email.php` - Configuración de email (ya existente)

### **Callbacks:**
- `auth/google-callback.php` - Callback de Google
- `auth/apple-callback.php` - Callback de Apple  
- `auth/microsoft-callback.php` - Callback de Microsoft

### **Interfaces:**
- `public/login.php` - Login público con OAuth
- `admin/login.php` - Login administrativo con OAuth

## 🎨 Diseño de Botones

### **Google:**
- **Fondo:** Blanco con borde gris
- **Icono:** Logo oficial de Google (multicolor)
- **Texto:** "Continuar con Google"
- **Hover:** Fondo gris claro

### **Apple:**
- **Fondo:** Negro
- **Icono:** Logo oficial de Apple (blanco)
- **Texto:** "Continuar con Apple" 
- **Hover:** Gris oscuro

### **Microsoft:**
- **Fondo:** Blanco con borde gris
- **Icono:** Logo oficial de Microsoft (multicolor)
- **Texto:** "Continuar con Microsoft"
- **Hover:** Fondo gris claro

## 🚨 Notas Importantes

### **Desarrollo vs Producción:**
- **Desarrollo:** Usa `http://localhost` en las URIs
- **Producción:** Cambia a `https://tudominio.com`
- **HTTPS:** Obligatorio en producción para Apple y Microsoft

### **Permisos de Dominio:**
- **Google:** Configura dominios autorizados
- **Apple:** Requiere dominio verificado
- **Microsoft:** Configura URIs de redirección exactas

### **Claves Privadas:**
- **Apple:** Mantén segura la clave privada (.p8)
- **Google/Microsoft:** Protege los client secrets
- **Nunca** subas credenciales a repositorios públicos

## 🧪 Testing

### **Modo de Prueba:**
```php
// En config/oauth_providers.php
'debug' => true,
'test_mode' => true,
```

### **Verificar Configuración:**
1. Revisa que las URIs de redirección coincidan exactamente
2. Verifica que las credenciales sean correctas
3. Prueba cada proveedor individualmente
4. Verifica el flujo completo de login

## 📞 Soporte

Si tienes problemas:

1. **Verifica la configuración** en `config/oauth_providers.php`
2. **Revisa los logs** de PHP para errores
3. **Confirma las URIs** de redirección en cada proveedor
4. **Prueba con modo debug** activado

---

¡Con esta configuración tendrás un sistema completo de login social con los principales proveedores! 🎉
