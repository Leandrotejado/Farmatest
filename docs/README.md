# Sistema de Gestión de Farmacia - FarmaXpress

Sistema completo de gestión farmacéutica desarrollado en PHP con MySQL, que incluye gestión de inventario, ventas, empleados y un mapa interactivo de farmacias de turno.

## Características

- **Sistema de Autenticación**: Login y registro de usuarios con roles (admin/empleado)
- **Gestión de Inventario**: CRUD completo de medicamentos con categorías y proveedores
- **Sistema de Ventas**: Registro de ventas con control de stock automático
- **Gestión de Empleados**: Administración de usuarios del sistema
- **Mapa Interactivo**: Localización de farmacias de turno con Leaflet
- **Dashboard**: Panel de control con estadísticas y acceso rápido
- **Historial de Stock**: Seguimiento de entradas y salidas de medicamentos

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- XAMPP (recomendado para desarrollo)

## Instalación

### 1. Configurar Base de Datos

1. Inicia XAMPP y asegúrate de que MySQL esté ejecutándose
2. Abre phpMyAdmin (http://localhost/phpmyadmin)
3. Importa el archivo `farmacia.sql` para crear la base de datos y tablas
4. Importa el archivo `datos_iniciales.sql` para insertar datos de prueba

### 2. Configurar Conexión

El archivo `db.php` ya está configurado para XAMPP con las siguientes credenciales:
- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: farmacia

### 3. Acceder al Sistema

1. Coloca todos los archivos en la carpeta `htdocs` de XAMPP
2. **IMPORTANTE**: Ejecuta `setup_completo.php` para configurar automáticamente el sistema
3. Si tienes problemas con el login, ejecuta `verificar_login.php` para diagnosticar
4. Abre tu navegador y ve a `http://localhost/farma4`
5. Usa las siguientes credenciales para iniciar sesión:

**Administradores por Defecto:**
- Email: admin@farmacia.com / Contraseña: password
- Email: admin.mañana@farmacia.com / Contraseña: password  
- Email: admin.noche@farmacia.com / Contraseña: password

**Usuarios Cliente de Prueba:**
- Email: juan.perez@email.com / Contraseña: password
- Email: maria.gonzalez@email.com / Contraseña: password
- Email: carlos.lopez@email.com / Contraseña: password

*Nota: Los administradores manejan el sistema por turnos, mientras que los usuarios clientes pueden registrarse libremente para acceder a servicios personalizados.*

## Solución de Problemas

### ❌ Problema: No puedo iniciar sesión

**Solución paso a paso:**

1. **Diagnóstico rápido:**
   - Ve a `http://localhost/farma4/verificar_errores.php`
   - Esto te mostrará todos los errores del sistema

2. **Test de login:**
   - Ve a `http://localhost/farma4/test_login_simple.php`
   - Esto probará el login paso a paso

3. **Ejecuta la configuración inicial:**
   - Ve a `http://localhost/farma4/setup_completo.php`
   - Esto creará la base de datos y usuarios de prueba

4. **Verifica el sistema completo:**
   - Ve a `http://localhost/farma4/diagnostico_completo.php`
   - Esto verificará todo el sistema
   - Esto probará las credenciales directamente

4. **Usa las credenciales correctas:**
   - Email: `admin@farmacia.com`
   - Contraseña: `password`

### ❌ Problema: Errores después del login

**Solución paso a paso:**

1. **Diagnóstico rápido de errores:**
   - Ve a `http://localhost/farma4/verificar_errores.php`
   - Esto te mostrará exactamente qué está fallando

2. **Test específico del login:**
   - Ve a `http://localhost/farma4/test_login_simple.php`
   - Esto probará cada paso del proceso de login

3. **Diagnóstico completo:**
   - Ve a `http://localhost/farma4/diagnostico_completo.php`
   - Esto identificará todos los problemas del sistema

4. **Configuración automática:**
   - Si faltan tablas, ejecuta `setup_completo.php`
   - Esto creará todas las tablas y datos necesarios

### 🔧 Errores comunes y soluciones:

- **"Variable $pdo no está disponible"**: Ejecuta `setup_completo.php`
- **"Tabla no existe"**: Ejecuta `setup_completo.php`
- **"Error de conexión"**: Verifica que XAMPP esté ejecutándose
- **"Sintaxis incorrecta"**: Los archivos han sido corregidos, recarga la página
- **"No se puede iniciar sesión"**: Usa `test_login_simple.php` para diagnosticar

### ❌ Problema: Error de conexión a la base de datos

**Solución:**
- Verifica que XAMPP esté ejecutándose
- Asegúrate de que MySQL esté activo
- Verifica que la base de datos `farmacia` exista
- Ejecuta `setup_completo.php` para crear todo automáticamente

## Estructura del Proyecto

```
farma4/
├── public/                 # Sección pública para usuarios
│   ├── index.php          # Página principal con mapa
│   ├── login.php          # Login para usuarios clientes
│   ├── register.php       # Registro de usuarios clientes
│   ├── dashboard.php      # Panel de usuario cliente
│   └── logout.php         # Cerrar sesión
├── admin/                 # Sección administrativa
│   ├── login.php          # Login para administradores
│   ├── dashboard.php      # Panel de control mejorado
│   ├── stock.php          # Gestión de inventario avanzada
│   ├── ventas.php         # Sistema de ventas con carrito
│   ├── medicamentos_farmacias.php # Asignar medicamentos a farmacias
│   ├── empleados.php      # Gestión de administradores
│   └── logout.php         # Cerrar sesión
├── index.php              # Redirección a sección pública
├── db.php                 # Conexión a base de datos
├── style.css              # Estilos CSS
├── farmacia.sql           # Esquema de base de datos
├── datos_iniciales.sql    # Datos iniciales
├── actualizar_roles.sql   # Script para actualizar roles
├── medicamentos_farmacias.sql # Script para medicamentos por farmacia
├── setup_completo.php     # Configuración automática del sistema
├── verificar_login.php    # Diagnóstico de problemas de login
├── test_login.php         # Prueba directa del sistema de login
├── diagnostico_completo.php # Diagnóstico completo del sistema
└── README.md              # Este archivo
```

## Funcionalidades Principales

### 1. Sección Pública (Usuarios Clientes)
- **Registro e Inicio de Sesión**: Los usuarios pueden registrarse libremente
- **Mapa Interactivo**: Visualización de farmacias de turno en toda la provincia de Buenos Aires
- **Búsqueda de Farmacias**: Encuentra farmacias de guardia cerca de tu ubicación
- **Medicamentos por Farmacia**: Consulta qué medicamentos están disponibles en cada farmacia
- **Panel de Usuario**: Dashboard personalizado con servicios exclusivos
- **Información de Farmacias**: Detalles de contacto, horarios y obras sociales

### 2. Sección Administrativa
- **Dashboard Mejorado**: Estadísticas en tiempo real, resumen de ventas y alertas
- **Gestión de Inventario**: Sistema completo con filtros, alertas de stock bajo y vencimientos
- **Sistema de Ventas**: Carrito funcional con control de stock automático
- **Asignación de Medicamentos**: Distribuye medicamentos a farmacias con precios especiales
- **Gestión de Administradores**: Sistema simplificado para múltiples administradores por turno

### 3. Gestión de Inventario
- Agregar medicamentos con categorías y proveedores
- Control de stock con alertas de stock bajo
- Fechas de vencimiento con alertas
- Historial de movimientos de stock

### 4. Sistema de Ventas
- Registro de ventas con control automático de stock
- Cálculo automático de totales
- Historial de ventas por usuario
- Transacciones seguras con rollback

### 5. Gestión de Usuarios
- Roles diferenciados (admin/empleado/cliente)
- CRUD completo de empleados
- Protección de rutas por rol
- Cambio de contraseñas

### 6. Mapa de Farmacias
- Integración con Leaflet para mapas interactivos
- Geolocalización del usuario
- Información de farmacias de turno
- Enlaces a Google Maps para direcciones

## Seguridad

- Contraseñas hasheadas con `password_hash()`
- Protección contra SQL injection con `mysqli_real_escape_string()`
- Validación de sesiones en todas las páginas protegidas
- Control de acceso basado en roles

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Tailwind CSS
- **Mapas**: Leaflet.js
- **Servidor**: Apache (XAMPP)

## Notas de Desarrollo

- El sistema está optimizado para XAMPP en Windows
- Todas las consultas SQL están preparadas para evitar inyecciones
- El diseño es responsive y compatible con dispositivos móviles
- Se incluyen datos de prueba para facilitar las pruebas

## Soporte

Para soporte técnico o consultas sobre el sistema, contacta al administrador del sistema.

---

**Desarrollado para FarmaXpress - Sistema de Gestión Farmacéutica**
