# 🏥 FarmaXpress - Sistema Administrativo Completo

## 📋 Descripción General

FarmaXpress es un sistema completo de gestión farmacéutica que incluye un panel administrativo robusto con todas las funcionalidades necesarias para administrar una farmacia moderna.

## 🚀 Características Principales

### 💊 Gestión de Medicamentos
- **CRUD Completo**: Crear, leer, actualizar y eliminar medicamentos
- **Categorización**: Organización por categorías (venta libre, con receta)
- **Control de Stock**: Gestión de inventario con alertas de stock bajo
- **Fechas de Vencimiento**: Control de medicamentos próximos a vencer
- **Códigos de Barras**: Soporte para códigos de barras
- **Autocompletado**: Sistema inteligente de búsqueda y selección

### 📦 Gestión de Inventario
- **Movimientos de Stock**: Registro de entradas y salidas
- **Transferencias**: Transferencia de stock entre medicamentos
- **Alertas Automáticas**: Notificaciones de stock bajo y vencimientos
- **Historial Completo**: Seguimiento de todos los movimientos
- **Valoración**: Cálculo automático del valor del inventario

### 💰 Sistema de Ventas
- **Punto de Venta**: Interfaz intuitiva para ventas
- **Carrito de Compras**: Gestión de productos en venta
- **Control de Stock**: Verificación automática de disponibilidad
- **Facturación**: Generación automática de totales
- **Historial de Ventas**: Registro completo de transacciones

### 📈 Reportes y Estadísticas
- **Dashboard en Tiempo Real**: Métricas actualizadas constantemente
- **Reportes de Ventas**: Análisis por día, semana, mes
- **Reportes de Inventario**: Estado del stock y valoración
- **Reportes Financieros**: Ingresos vs gastos
- **Gráficos Interactivos**: Visualización de datos con Chart.js
- **Exportación**: Capacidad de exportar datos a Excel

### 👥 Gestión de Usuarios
- **Roles y Permisos**: Administrador y empleado
- **Control de Acceso**: Autenticación segura
- **Gestión de Contraseñas**: Cambio seguro de contraseñas
- **Actividad de Usuarios**: Seguimiento de acciones
- **Estadísticas por Usuario**: Rendimiento individual

### 🏥 Gestión de Farmacias
- **Múltiples Sucursales**: Gestión de varias farmacias
- **Stock por Farmacia**: Control individual de inventario
- **Ubicación**: Integración con mapas para direcciones
- **Horarios**: Gestión de turnos y horarios de atención

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+**: Lenguaje principal del servidor
- **MySQL**: Base de datos relacional
- **PDO**: Interfaz de acceso a datos
- **Sessions**: Gestión de sesiones de usuario

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos modernos y responsivos
- **JavaScript ES6+**: Interactividad del cliente
- **Tailwind CSS**: Framework de CSS utilitario
- **Font Awesome**: Iconografía profesional
- **Chart.js**: Gráficos interactivos

### Patrones de Diseño
- **MVC**: Arquitectura Modelo-Vista-Controlador
- **RESTful**: API REST para comunicación
- **Responsive Design**: Adaptable a todos los dispositivos

## 📁 Estructura del Proyecto

```
farmatest/
├── admin/                          # Panel administrativo
│   ├── dashboard.php               # Dashboard principal
│   ├── medicamentos.php           # Gestión de medicamentos
│   ├── stock.php                  # Gestión de inventario
│   ├── ventas.php                 # Sistema de ventas
│   ├── reportes.php               # Reportes y estadísticas
│   ├── empleados.php              # Gestión de usuarios
│   ├── farmacias.php              # Gestión de farmacias
│   ├── categorias_proveedores.php  # Categorías y proveedores
│   └── api_autocomplete_*.php     # APIs de autocompletado
├── config/
│   ├── db.php                     # Configuración de base de datos
│   └── email.php                  # Configuración de email
├── database/
│   ├── setup_sistema_admin.sql    # Script de configuración
│   └── *.sql                      # Otros scripts SQL
├── public/
│   ├── css/
│   │   └── sidebar-admin.css      # Estilos del sidebar
│   └── js/
│       └── autocomplete.js         # Sistema de autocompletado
├── setup_sistema_admin.php        # Configuración automática
└── README.md                      # Este archivo
```

## 🚀 Instalación y Configuración

### Requisitos Previos
- **XAMPP/WAMP/LAMP**: Servidor web local
- **PHP 7.4+**: Versión compatible
- **MySQL 5.7+**: Base de datos
- **Navegador Web**: Chrome, Firefox, Safari, Edge

### Pasos de Instalación

1. **Clonar el Proyecto**
   ```bash
   git clone [url-del-repositorio]
   cd farmatest
   ```

2. **Configurar Base de Datos**
   - Crear base de datos `farmacia` en MySQL
   - Ejecutar el script de configuración automática:
   ```
   http://localhost/farmatest/setup_sistema_admin.php
   ```

3. **Configurar Credenciales**
   - Editar `config/db.php` con tus credenciales de MySQL
   - Verificar permisos de usuario de base de datos

4. **Acceder al Sistema**
   - URL: `http://localhost/farmatest/admin/login.php`
   - Usuario por defecto: `admin`
   - Contraseña por defecto: `password`

## 👤 Usuarios y Roles

### Administrador
- Acceso completo a todas las funcionalidades
- Gestión de usuarios y permisos
- Configuración del sistema
- Reportes avanzados

### Empleado
- Gestión de ventas
- Consulta de inventario
- Reportes básicos
- Gestión de medicamentos

## 🔧 Funcionalidades Avanzadas

### Sistema de Autocompletado
- Búsqueda inteligente de medicamentos
- Sugerencias en tiempo real
- Filtrado por categorías
- Integración con base de datos

### Dashboard Inteligente
- Métricas en tiempo real
- Alertas automáticas
- Gráficos interactivos
- Acciones rápidas

### Sistema de Alertas
- Stock bajo
- Medicamentos próximos a vencer
- Sin ventas del día
- Errores del sistema

### Reportes Dinámicos
- Filtros por fecha
- Múltiples tipos de reporte
- Exportación a Excel
- Gráficos personalizables

## 📊 Métricas y KPIs

### Inventario
- Total de medicamentos
- Valor del inventario
- Stock bajo
- Próximos a vencer

### Ventas
- Ventas del día
- Facturación diaria
- Promedio por venta
- Top medicamentos

### Usuarios
- Total de usuarios
- Actividad reciente
- Rendimiento por usuario
- Estadísticas de acceso

## 🔒 Seguridad

### Autenticación
- Contraseñas encriptadas con `password_hash()`
- Sesiones seguras
- Control de acceso por roles
- Timeout de sesión

### Validación de Datos
- Sanitización de entradas
- Validación de tipos de datos
- Prevención de SQL injection
- Escape de caracteres especiales

### Auditoría
- Log de movimientos de stock
- Historial de ventas
- Registro de cambios de usuarios
- Trazabilidad completa

## 🚀 Próximas Mejoras

### Funcionalidades Planificadas
- [ ] Sistema de notificaciones push
- [ ] Respaldo automático de base de datos
- [ ] Integración con proveedores
- [ ] App móvil
- [ ] Sistema de puntos de fidelidad
- [ ] Integración con sistemas contables

### Mejoras Técnicas
- [ ] API REST completa
- [ ] Microservicios
- [ ] Cache Redis
- [ ] Docker containers
- [ ] CI/CD pipeline

## 📞 Soporte y Contacto

### Documentación
- Manual de usuario disponible
- Documentación técnica completa
- Videos tutoriales
- FAQ frecuentes

### Soporte Técnico
- Email: soporte@farmacia.com
- Teléfono: +54 11 1234-5678
- Horario: Lunes a Viernes 9:00-18:00

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📈 Changelog

### v1.0.0 (2024-01-XX)
- ✅ Sistema administrativo completo
- ✅ Gestión de medicamentos
- ✅ Sistema de ventas
- ✅ Reportes avanzados
- ✅ Dashboard en tiempo real
- ✅ Gestión de usuarios
- ✅ Sistema de inventario

---

**¡Gracias por usar FarmaXpress! 🏥💊**
