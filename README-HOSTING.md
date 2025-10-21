# 🏥 FarmaXpress - Sistema de Gestión Farmacéutica

## 📋 Descripción
FarmaXpress es un sistema completo de gestión farmacéutica que incluye:
- ✅ Gestión de inventario y stock
- ✅ Sistema de ventas con descuento automático
- ✅ Reportes y análisis
- ✅ Autenticación con verificación por email
- ✅ Login social (Google, Apple, Microsoft)
- ✅ Modo oscuro/claro
- ✅ Interfaz responsive
- ✅ Seguridad avanzada

## 🚀 Instalación en Hosting

### 1. Requisitos del Servidor
- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Extensiones PHP**: PDO, PDO_MySQL, cURL, OpenSSL, JSON
- **Memoria**: 128MB mínimo
- **Espacio**: 50MB mínimo

### 2. Pasos de Instalación

#### Paso 1: Subir Archivos
1. Sube todos los archivos a tu directorio web
2. Mantén la estructura de carpetas intacta

#### Paso 2: Configurar Base de Datos
1. Crea una base de datos MySQL en tu hosting
2. Edita `config/db.php` con tus credenciales:
```php
$host = 'tu_host';
$dbname = 'tu_base_de_datos';
$username = 'tu_usuario';
$password = 'tu_contraseña';
```

#### Paso 3: Configurar Email
1. Edita `config/email.php` con tus datos SMTP:
```php
define('SMTP_HOST', 'smtp.tu-proveedor.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu_email@tu-dominio.com');
define('SMTP_PASSWORD', 'tu_contraseña_email');
```

#### Paso 4: Ejecutar Instalación
1. Visita `tu-dominio.com/install-hosting.php`
2. Sigue las instrucciones del instalador
3. Elimina el archivo `install-hosting.php` después de usar

#### Paso 5: Configurar OAuth (Opcional)
1. Edita `config/oauth_providers.php`
2. Configura tus credenciales de Google, Apple, Microsoft

### 3. Configuración Recomendada del Hosting

#### PHP Settings
```
memory_limit = 128M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
session.gc_maxlifetime = 3600
```

#### Apache Settings (.htaccess incluido)
- Compresión GZIP habilitada
- Cache de archivos estáticos
- Headers de seguridad
- Protección de archivos sensibles

### 4. Acceso al Sistema

#### Panel de Administración
- URL: `tu-dominio.com/admin/login.php`
- Usuario por defecto: Crear desde el registro
- Funciones: Gestión completa del sistema

#### Sitio Público
- URL: `tu-dominio.com/public/index.php`
- Funciones: Búsqueda de farmacias y medicamentos

### 5. Seguridad

#### Archivos Protegidos
- `config/` - Configuraciones sensibles
- `services/` - Servicios internos
- `app/` - Lógica de aplicación
- `database/` - Scripts de BD
- `docs/` - Documentación

#### Recomendaciones
- ✅ Cambiar todas las contraseñas por defecto
- ✅ Configurar HTTPS
- ✅ Mantener PHP actualizado
- ✅ Hacer backups regulares
- ✅ Monitorear logs de seguridad

### 6. Estructura del Proyecto

```
farmatest/
├── admin/                 # Panel administrativo
├── app/                   # Lógica de aplicación
├── auth/                  # Callbacks OAuth
├── config/                # Configuraciones
├── database/              # Scripts de BD
├── docs/                  # Documentación
├── public/                # Sitio público
├── services/              # Servicios
├── utils/                 # Utilidades
├── .htaccess             # Configuración Apache
└── install-hosting.php   # Instalador
```

### 7. Funcionalidades Principales

#### Gestión de Stock
- ✅ Entrada de medicamentos
- ✅ Control de inventario
- ✅ Alertas de stock bajo
- ✅ Historial de movimientos

#### Sistema de Ventas
- ✅ Procesamiento de ventas
- ✅ Descuento automático de stock
- ✅ Historial de transacciones
- ✅ Reportes de ventas

#### Reportes
- ✅ Ventas por período
- ✅ Movimientos de stock
- ✅ Medicamentos más vendidos
- ✅ Estadísticas en tiempo real

#### Autenticación
- ✅ Registro con verificación por email
- ✅ Login social (Google, Apple, Microsoft)
- ✅ Recuperación de contraseña
- ✅ Rate limiting

### 8. Soporte y Mantenimiento

#### Logs del Sistema
- `security.log` - Eventos de seguridad
- `app.log` - Logs generales
- Logs de PHP en `/tmp/`

#### Monitoreo
- Verificar logs regularmente
- Monitorear uso de recursos
- Revisar errores de PHP

#### Backups
- Base de datos: Exportar regularmente
- Archivos: Backup completo del directorio
- Configuraciones: Guardar copias de seguridad

### 9. Actualizaciones

#### Actualizar el Sistema
1. Hacer backup completo
2. Subir nuevos archivos
3. Ejecutar scripts de migración si los hay
4. Verificar funcionamiento

#### Mantenimiento
- Limpiar logs antiguos
- Optimizar base de datos
- Actualizar dependencias

### 10. Troubleshooting

#### Problemas Comunes
- **Error de conexión BD**: Verificar credenciales en `config/db.php`
- **Email no funciona**: Verificar configuración SMTP
- **Modo oscuro roto**: Verificar archivos CSS/JS
- **Permisos**: Verificar permisos de directorios

#### Contacto
Para soporte técnico o consultas sobre el sistema.

---

## 🎉 ¡Sistema Listo para Producción!

Tu sistema FarmaXpress está completamente configurado y listo para usar en producción. Todas las funcionalidades están implementadas y probadas.

**¡Disfruta de tu nuevo sistema de gestión farmacéutica!** 🏥💊
