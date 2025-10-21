# FarmaXpress - Sistema de Gestión Farmacéutica Integral

## 🏥 Descripción del Proyecto

FarmaXpress es un sistema completo de gestión farmacéutica diseñado para optimizar las operaciones de una o múltiples farmacias. Permite un control exhaustivo del stock de medicamentos, gestión de ventas, administración de farmacias (incluyendo el estado de "de turno"), inventario detallado, reportes y gestión de usuarios administradores.

## 🚀 Características Principales

### 🏥 **Gestión de Farmacias**
- **Registro de Farmacias**: Añade y gestiona múltiples sucursales de farmacias.
- **Control de Turnos**: Actualiza fácilmente el estado de "de turno" (24hs) para cada farmacia.
- **Información Detallada**: Almacena dirección, teléfono, latitud, longitud y más.

### 💊 **Gestión de Medicamentos**
- **CRUD Completo**: Crea, lee, actualiza y elimina medicamentos.
- **Categorización**: Organiza medicamentos por categorías (ej. "Venta Libre", "Receta").
- **Proveedores**: Asocia medicamentos a proveedores específicos.
- **Precios y Stock**: Controla el precio y el stock disponible de cada medicamento.

### 📦 **Control de Stock e Inventario**
- **Stock por Farmacia**: Gestiona el inventario de forma independiente para cada sucursal.
- **Movimientos de Stock**: Registra entradas, salidas y ajustes de inventario.
- **Transferencias entre Farmacias**: Facilita el movimiento de medicamentos entre sucursales.
- **Alertas de Stock Bajo**: Notificaciones para reponer medicamentos antes de que se agoten.

### 💰 **Sistema de Ventas**
- **Registro de Ventas**: Documenta todas las transacciones de venta.
- **Historial de Ventas**: Consulta y filtra ventas pasadas.
- **Reportes de Ventas**: Analiza el rendimiento de ventas por farmacia y medicamento.

### 👥 **Gestión de Administradores**
- **Control de Usuarios**: Administra los usuarios con acceso al sistema.
- **Roles y Permisos**: Define diferentes niveles de acceso para administradores.
- **Autenticación Segura**: Sistema de login para proteger el acceso.

### 📊 **Reportes y Dashboard**
- **Dashboard Centralizado**: Vista general de las métricas clave del negocio.
- **Estadísticas en Tiempo Real**: Información sobre farmacias, stock, ventas y más.
- **Gráficos Interactivos**: Visualización de tendencias de ventas y stock.

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (con PDO para la base de datos)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, Tailwind CSS (para estilos), JavaScript ES6+
- **Gráficos**: Chart.js
- **Iconos**: Font Awesome

## ⚙️ Estructura del Proyecto

```
farmatest/
├── admin/                      # Panel de administración
│   ├── dashboard.php           # Dashboard principal
│   ├── farmacias.php           # Gestión de farmacias
│   ├── medicamentos.php        # Gestión de medicamentos
│   ├── stock.php               # Gestión de inventario y stock
│   ├── ventas.php              # Sistema de ventas
│   ├── reportes.php            # Reportes y analytics
│   ├── usuarios.php            # Gestión de usuarios
│   ├── login.php               # Página de login
│   └── logout.php              # Cerrar sesión
├── config/                     # Archivos de configuración
│   └── db.php                  # Conexión a la base de datos
├── database/                   # Scripts SQL
│   └── sistema_farmacias.sql   # Esquema de la base de datos y datos de ejemplo
├── public/                     # Archivos públicos (CSS, JS, imágenes)
│   └── css/
│       └── sidebar-admin.css   # Estilos del sidebar
├── instalar_sistema.php        # Script de instalación inicial
├── reparar_sistema.php         # Script de reparación automática
├── diagnostico_sistema.php     # Herramienta de diagnóstico
└── README.md                   # Documentación del proyecto
```

## 🚀 Instalación y Configuración

### **Opción 1: Instalación Automática (Recomendada)**

1. **Configurar la Base de Datos:**
   - Asegúrate de tener MySQL (o MariaDB) instalado y funcionando.
   - Crea una base de datos llamada `farmacia`.
   - Abre `config/db.php` y ajusta las credenciales si es necesario (usuario, contraseña).

2. **Ejecutar el Script de Instalación:**
   - Abre tu navegador y ve a: `http://localhost/farmatest/instalar_sistema.php`
   - Este script creará todas las tablas necesarias y cargará datos de ejemplo.

3. **Acceder al Panel de Administración:**
   - Ve a: `http://localhost/farmatest/admin/login.php`
   - **Credenciales por defecto:**
     - **Usuario:** `admin`
     - **Contraseña:** `password`

### **Opción 2: Reparación Automática**

Si encuentras errores en el sistema:

1. **Ejecutar Diagnóstico:**
   - Ve a: `http://localhost/farmatest/diagnostico_sistema.php`
   - Este script verificará la integridad del sistema.

2. **Ejecutar Reparación:**
   - Ve a: `http://localhost/farmatest/reparar_sistema.php`
   - Este script reparará automáticamente los errores más comunes.

## 🔧 Solución de Problemas

### **Errores Comunes y Soluciones:**

#### **Error: "Undefined array key"**
- **Causa**: Tablas de base de datos faltantes o vacías
- **Solución**: Ejecutar `reparar_sistema.php`

#### **Error: "Table doesn't exist"**
- **Causa**: Base de datos no inicializada
- **Solución**: Ejecutar `instalar_sistema.php`

#### **Error: "Access denied"**
- **Causa**: Credenciales incorrectas en `config/db.php`
- **Solución**: Verificar usuario y contraseña de MySQL

#### **Error: "Connection failed"**
- **Causa**: MySQL no está ejecutándose
- **Solución**: Iniciar el servicio MySQL

### **Herramientas de Diagnóstico:**

1. **`diagnostico_sistema.php`**: Verifica la integridad del sistema
2. **`reparar_sistema.php`**: Repara errores automáticamente
3. **`instalar_sistema.php`**: Instalación inicial completa

## 🔑 Credenciales por Defecto

- **Usuario Administrador:**
  - **Usuario:** `admin`
  - **Contraseña:** `password`

## 📱 Funcionalidades del Sistema

### **Dashboard Principal:**
- Estadísticas generales del sistema
- Alertas de stock bajo
- Ventas recientes
- Gráficos de rendimiento

### **Gestión de Farmacias:**
- CRUD completo de farmacias
- Control de turnos (24hs)
- Información de contacto y ubicación
- Estado activo/inactivo

### **Gestión de Medicamentos:**
- Catálogo completo de medicamentos
- Categorización por tipo de venta
- Control de stock y fechas de vencimiento
- Gestión de proveedores

### **Sistema de Ventas:**
- Punto de venta con carrito dinámico
- Registro automático de ventas
- Actualización de stock en tiempo real
- Historial de transacciones

### **Reportes Avanzados:**
- Filtros por fecha y farmacia
- Gráficos interactivos
- Top medicamentos vendidos
- Análisis de rendimiento

### **Gestión de Usuarios:**
- Roles y permisos
- Asignación a farmacias
- Control de acceso
- Cambio de contraseñas

## 🎯 Características Específicas para Farmacias

- **Sistema Multi-Farmacia**: Gestión independiente de múltiples sucursales
- **Control de Turnos**: Indicador visual de farmacias de turno (24hs)
- **Stock Específico**: Inventario independiente por farmacia
- **Transferencias**: Movimiento de medicamentos entre farmacias
- **Alertas Inteligentes**: Notificaciones de stock bajo y vencimientos
- **Reportes Especializados**: Análisis específico del sector farmacéutico

## 📞 Soporte

Si encuentras problemas:

1. **Ejecuta el diagnóstico**: `diagnostico_sistema.php`
2. **Intenta la reparación automática**: `reparar_sistema.php`
3. **Revisa los logs de error** en tu servidor web
4. **Verifica la configuración** de la base de datos

## 📄 Licencia

Este proyecto está desarrollado para fines académicos y de demostración.

---

**¡El sistema está listo para tu tesis!** 🎓✨