# Configuración de Trello - FarmaXpress

## 📋 Guía para Configurar el Tablero Trello

### 1. Crear Tablero en Trello

1. **Acceder a Trello**: Ve a [trello.com](https://trello.com) e inicia sesión
2. **Crear nuevo tablero**: Click en "Create new board"
3. **Configuración del tablero**:
   - **Nombre**: "FarmaXpress - Sistema de Gestión Farmacéutica"
   - **Visibilidad**: Público (para mostrar al jurado)
   - **Descripción**: "Desarrollo de sistema farmacéutico con metodología SCRUM"

### 2. Configurar Columnas del Tablero

#### Columnas Principales:
1. **📋 Product Backlog**
2. **🎯 Sprint Backlog**
3. **🔄 En Progreso**
4. **🧪 Testing**
5. **✅ Completado**
6. **📚 Documentación**

### 3. Configurar Listas de Tarjetas

#### Epic 1: Sistema de Autenticación
**Tarjeta**: "🔐 Sistema de Autenticación y Usuarios"

**Descripción**:
```
Como administrador del sistema
Quiero gestionar usuarios y autenticación
Para controlar el acceso al sistema

Criterios de Aceptación:
- [ ] Login de usuarios con email y contraseña
- [ ] Registro de nuevos usuarios
- [ ] Gestión de roles (admin, empleado, cliente)
- [ ] Protección de rutas por rol
- [ ] Sistema de sesiones seguro
```

**Subtareas**:
- [ ] Crear modelo User
- [ ] Implementar AuthController
- [ ] Crear vistas de login/registro
- [ ] Configurar middleware de autenticación
- [ ] Implementar hash de contraseñas
- [ ] Crear sistema de roles

**Estimación**: 8 story points
**Prioridad**: Alta
**Sprint**: 1

---

#### Epic 2: Gestión de Medicamentos
**Tarjeta**: "💊 Sistema de Gestión de Medicamentos"

**Descripción**:
```
Como administrador de farmacia
Quiero gestionar el inventario de medicamentos
Para controlar stock y precios

Criterios de Aceptación:
- [ ] CRUD completo de medicamentos
- [ ] Categorización de medicamentos
- [ ] Control de stock y alertas
- [ ] Fechas de vencimiento
- [ ] Búsqueda y filtros
```

**Subtareas**:
- [ ] Crear modelo Medicamento
- [ ] Implementar MedicamentoController
- [ ] Crear vistas de gestión
- [ ] Sistema de categorías
- [ ] Alertas de stock bajo
- [ ] Búsqueda avanzada

**Estimación**: 12 story points
**Prioridad**: Alta
**Sprint**: 2

---

#### Epic 3: Sistema de Ventas
**Tarjeta**: "💰 Sistema de Ventas y Transacciones"

**Descripción**:
```
Como empleado de farmacia
Quiero registrar ventas de medicamentos
Para procesar transacciones y actualizar stock

Criterios de Aceptación:
- [ ] Registro de ventas
- [ ] Carrito de compras
- [ ] Cálculo automático de totales
- [ ] Actualización automática de stock
- [ ] Historial de ventas
```

**Subtareas**:
- [ ] Crear modelo Venta
- [ ] Implementar VentaController
- [ ] Sistema de carrito
- [ ] Cálculo de totales
- [ ] Control de stock automático
- [ ] Reportes de ventas

**Estimación**: 10 story points
**Prioridad**: Alta
**Sprint**: 3

---

#### Epic 4: Inteligencia Artificial
**Tarjeta**: "🤖 Integración de IA y Análisis Predictivo"

**Descripción**:
```
Como administrador
Quiero utilizar IA para optimizar el negocio
Para tomar decisiones basadas en datos

Criterios de Aceptación:
- [ ] Recomendaciones de medicamentos
- [ ] Predicción de demanda
- [ ] Detección de anomalías
- [ ] Dashboard de IA
- [ ] Integración con Python
```

**Subtareas**:
- [ ] Configurar servicio Python
- [ ] Implementar recomendaciones
- [ ] Sistema de predicción
- [ ] Detección de anomalías
- [ ] Dashboard de IA
- [ ] API de integración

**Estimación**: 14 story points
**Prioridad**: Media
**Sprint**: 4

---

#### Epic 5: Optimización y Documentación
**Tarjeta**: "📚 Documentación y Optimización Final"

**Descripción**:
```
Como desarrollador
Quiero documentar y optimizar el sistema
Para facilitar mantenimiento y escalabilidad

Criterios de Aceptación:
- [ ] Documentación técnica completa
- [ ] Optimización de rendimiento
- [ ] Tests automatizados
- [ ] Guías de instalación
- [ ] Preparación para producción
```

**Subtareas**:
- [ ] Documentar API
- [ ] Crear guías de usuario
- [ ] Optimizar consultas SQL
- [ ] Implementar caché
- [ ] Tests de integración
- [ ] Configuración de producción

**Estimación**: 6 story points
**Prioridad**: Media
**Sprint**: 5

### 4. Configurar Etiquetas (Labels)

#### Por Prioridad:
- 🔴 **Alta**: Funcionalidades críticas
- 🟡 **Media**: Funcionalidades importantes
- 🟢 **Baja**: Mejoras y optimizaciones

#### Por Tipo:
- 🐛 **Bug**: Corrección de errores
- ✨ **Feature**: Nueva funcionalidad
- 📚 **Documentation**: Documentación
- 🔧 **Technical**: Tarea técnica
- 🎨 **UI/UX**: Interfaz de usuario

#### Por Sprint:
- **Sprint 1**: Fundación
- **Sprint 2**: Core Features
- **Sprint 3**: Ventas y Reportes
- **Sprint 4**: IA y Análisis
- **Sprint 5**: Optimización

### 5. Configurar Power-Ups

#### Power-Ups Recomendados:
1. **Calendar**: Para fechas de entrega
2. **Custom Fields**: Para story points y estimaciones
3. **Voting**: Para priorización
4. **Time Tracking**: Para seguimiento de tiempo
5. **GitHub**: Para integración con repositorio

### 6. Configurar Automatizaciones

#### Reglas Automáticas:
1. **Mover tarjeta a "Completado"** cuando todas las subtareas estén marcadas
2. **Agregar etiqueta "Testing"** cuando se mueva a columna Testing
3. **Notificar al equipo** cuando se agregue una nueva tarjeta crítica
4. **Archivar tarjetas** después de 30 días en "Completado"

### 7. Configurar Plantillas de Tarjetas

#### Plantilla de User Story:
```
**Como** [tipo de usuario]
**Quiero** [funcionalidad]
**Para** [beneficio/objetivo]

**Criterios de Aceptación:**
- [ ] Criterio 1
- [ ] Criterio 2
- [ ] Criterio 3

**Estimación:** [X] story points
**Prioridad:** [Alta/Media/Baja]
**Sprint:** [Número]
```

#### Plantilla de Bug:
```
**Descripción del Bug:**
[Descripción detallada]

**Pasos para Reproducir:**
1. [Paso 1]
2. [Paso 2]
3. [Paso 3]

**Comportamiento Esperado:**
[Lo que debería pasar]

**Comportamiento Actual:**
[Lo que está pasando]

**Severidad:** [Crítica/Alta/Media/Baja]
```

### 8. Configurar Métricas y Reportes

#### Métricas a Seguir:
- **Velocity**: Story points completados por sprint
- **Burndown**: Progreso diario del sprint
- **Lead Time**: Tiempo desde creación hasta completado
- **Cycle Time**: Tiempo en cada columna
- **Throughput**: Tarjetas completadas por período

#### Reportes Semanales:
- Resumen de progreso del sprint
- Tarjetas bloqueadas o con impedimentos
- Métricas de velocidad y calidad
- Planificación del siguiente sprint

### 9. Configurar Integración con GitHub

#### GitHub Power-Up:
1. Conectar repositorio GitHub
2. Vincular commits con tarjetas
3. Sincronizar pull requests
4. Automatizar movimientos de tarjetas

#### Formato de Commits:
```
feat: Implementar sistema de autenticación

Closes #123
```

### 10. Configurar Permisos y Miembros

#### Roles del Equipo:
- **Product Owner**: Puede editar todas las tarjetas
- **Scrum Master**: Puede mover tarjetas entre columnas
- **Developers**: Pueden editar tarjetas asignadas
- **Testers**: Pueden mover a Testing y Completado

### 11. Configurar Plantillas de Sprint

#### Sprint Planning:
- [ ] Revisar backlog priorizado
- [ ] Seleccionar user stories para el sprint
- [ ] Estimar story points
- [ ] Crear subtareas detalladas
- [ ] Asignar responsables
- [ ] Definir Definition of Done

#### Daily Standup:
- [ ] Revisar progreso del día anterior
- [ ] Planificar trabajo del día
- [ ] Identificar impedimentos
- [ ] Actualizar estado de tarjetas

#### Sprint Review:
- [ ] Demostrar funcionalidades completadas
- [ ] Recopilar feedback
- [ ] Actualizar backlog
- [ ] Planificar siguiente sprint

#### Sprint Retrospective:
- [ ] Revisar qué funcionó bien
- [ ] Identificar áreas de mejora
- [ ] Definir acciones de mejora
- [ ] Actualizar proceso SCRUM

### 12. Configurar Dashboard para el Jurado

#### Vista Ejecutiva:
- Resumen de progreso por sprint
- Métricas de velocidad y calidad
- Estado actual del proyecto
- Próximos hitos importantes

#### Vista Técnica:
- Detalles de implementación
- Arquitectura del sistema
- Tecnologías utilizadas
- Métricas de código

## 📊 Ejemplo de Tablero Completo

### Columna: Product Backlog
- 🔐 Sistema de Autenticación (8 pts) - Alta
- 💊 Gestión de Medicamentos (12 pts) - Alta
- 💰 Sistema de Ventas (10 pts) - Alta
- 🤖 Integración de IA (14 pts) - Media
- 📚 Documentación (6 pts) - Media

### Columna: Sprint Backlog (Sprint 1)
- 🔐 Sistema de Autenticación (8 pts)
- 📋 Configuración inicial (2 pts)
- 🗄️ Base de datos (3 pts)

### Columna: En Progreso
- 🔐 Login de usuarios (3 pts) - Asignado a: Dev1
- 🔐 Registro de usuarios (2 pts) - Asignado a: Dev2

### Columna: Testing
- 🗄️ Configuración BD (3 pts) - Asignado a: Tester1

### Columna: Completado
- 📋 Estructura MVC (2 pts) ✅
- 📋 Configuración entorno (1 pt) ✅

## 🔗 Enlaces Importantes

- **Tablero Principal**: https://trello.com/b/farmaxpress
- **Vista de Calendario**: https://trello.com/b/farmaxpress/calendar
- **Vista de Actividad**: https://trello.com/b/farmaxpress/activity
- **Exportar Datos**: https://trello.com/b/farmaxpress/export

## 📋 Checklist para el Jurado

- [ ] Tablero público y accesible
- [ ] Estructura SCRUM implementada
- [ ] User stories bien definidas
- [ ] Estimaciones en story points
- [ ] Progreso visible por sprint
- [ ] Integración con GitHub
- [ ] Métricas y reportes
- [ ] Documentación del proceso
- [ ] Retrospectivas registradas
- [ ] Mejoras implementadas

---

**Este tablero Trello demuestra la aplicación práctica de la metodología SCRUM en el desarrollo del sistema FarmaXpress.**

