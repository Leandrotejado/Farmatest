# Metodología SCRUM - FarmaXpress

## 📋 Resumen Ejecutivo

FarmaXpress ha sido desarrollado siguiendo la metodología ágil SCRUM, garantizando un desarrollo iterativo, colaborativo y centrado en el usuario. Este documento detalla la implementación de SCRUM en nuestro proyecto.

## 🎯 Roles SCRUM

### Product Owner
- **Responsabilidad**: Definir y priorizar el backlog del producto
- **Tareas**:
  - Crear y mantener user stories
  - Definir criterios de aceptación
  - Priorizar funcionalidades
  - Validar entregables

### Scrum Master
- **Responsabilidad**: Facilitar el proceso SCRUM y remover impedimentos
- **Tareas**:
  - Organizar ceremonias SCRUM
  - Facilitar retrospectivas
  - Remover bloqueos del equipo
  - Asegurar seguimiento de la metodología

### Development Team
- **Responsabilidad**: Desarrollar el producto de manera colaborativa
- **Tareas**:
  - Implementar user stories
  - Realizar testing
  - Mantener calidad del código
  - Participar en ceremonias

## 📅 Ceremonias SCRUM

### Sprint Planning
- **Frecuencia**: Al inicio de cada sprint (2 semanas)
- **Duración**: 2 horas
- **Objetivo**: Planificar el trabajo del sprint
- **Participantes**: Todo el equipo SCRUM

### Daily Standup
- **Frecuencia**: Diaria
- **Duración**: 15 minutos
- **Objetivo**: Sincronización del equipo
- **Preguntas**:
  - ¿Qué hice ayer?
  - ¿Qué haré hoy?
  - ¿Tengo algún impedimento?

### Sprint Review
- **Frecuencia**: Al final de cada sprint
- **Duración**: 1 hora
- **Objetivo**: Demostrar funcionalidades completadas
- **Participantes**: Todo el equipo + stakeholders

### Sprint Retrospective
- **Frecuencia**: Al final de cada sprint
- **Duración**: 1 hora
- **Objetivo**: Mejorar el proceso
- **Preguntas**:
  - ¿Qué funcionó bien?
  - ¿Qué se puede mejorar?
  - ¿Qué acciones tomaremos?

## 📊 Artefactos SCRUM

### Product Backlog
Lista priorizada de funcionalidades del producto:

#### Epic 1: Sistema de Autenticación
- **User Story 1.1**: Como usuario, quiero registrarme para acceder al sistema
- **User Story 1.2**: Como usuario, quiero iniciar sesión para acceder a mis datos
- **User Story 1.3**: Como administrador, quiero gestionar roles de usuario

#### Epic 2: Gestión de Medicamentos
- **User Story 2.1**: Como administrador, quiero agregar medicamentos al inventario
- **User Story 2.2**: Como usuario, quiero buscar medicamentos por nombre
- **User Story 2.3**: Como administrador, quiero actualizar stock de medicamentos

#### Epic 3: Sistema de Ventas
- **User Story 3.1**: Como empleado, quiero registrar ventas
- **User Story 3.2**: Como administrador, quiero ver reportes de ventas
- **User Story 3.3**: Como sistema, quiero actualizar stock automáticamente

#### Epic 4: Inteligencia Artificial
- **User Story 4.1**: Como usuario, quiero recibir recomendaciones de medicamentos
- **User Story 4.2**: Como administrador, quiero predecir demanda de stock
- **User Story 4.3**: Como sistema, quiero detectar anomalías automáticamente

### Sprint Backlog
Tareas específicas para el sprint actual:

#### Sprint 1 (Semanas 1-2): Fundación
- [x] Configurar entorno de desarrollo
- [x] Implementar estructura MVC
- [x] Crear sistema de autenticación
- [x] Configurar base de datos

#### Sprint 2 (Semanas 3-4): Core Features
- [x] CRUD de medicamentos
- [x] Sistema de búsqueda
- [x] Gestión de inventario
- [x] Interfaz de usuario

#### Sprint 3 (Semanas 5-6): Ventas y Reportes
- [x] Sistema de ventas
- [x] Reportes básicos
- [x] Control de stock
- [x] Dashboard administrativo

#### Sprint 4 (Semanas 7-8): Inteligencia Artificial
- [x] Integración con Python
- [x] Sistema de recomendaciones
- [x] Predicción de demanda
- [x] Detección de anomalías

#### Sprint 5 (Semanas 9-10): Optimización
- [x] Mejoras de rendimiento
- [x] Testing completo
- [x] Documentación
- [x] Preparación para producción

### Incremento
Funcionalidad potencialmente entregable al final de cada sprint:

#### Incremento 1: Sistema Base
- Autenticación funcional
- Estructura MVC implementada
- Base de datos configurada

#### Incremento 2: Gestión de Medicamentos
- CRUD completo de medicamentos
- Sistema de búsqueda
- Interfaz responsive

#### Incremento 3: Sistema de Ventas
- Registro de ventas
- Control automático de stock
- Reportes básicos

#### Incremento 4: Inteligencia Artificial
- Recomendaciones de medicamentos
- Predicción de demanda
- Dashboard de IA

#### Incremento 5: Producto Final
- Sistema completo y optimizado
- Documentación completa
- Listo para producción

## 📈 Métricas SCRUM

### Velocity
- **Sprint 1**: 8 story points
- **Sprint 2**: 12 story points
- **Sprint 3**: 10 story points
- **Sprint 4**: 14 story points
- **Sprint 5**: 6 story points
- **Promedio**: 10 story points/sprint

### Burndown Chart
Seguimiento del progreso diario del sprint:
- **Día 1**: 10 story points restantes
- **Día 3**: 8 story points restantes
- **Día 5**: 6 story points restantes
- **Día 7**: 4 story points restantes
- **Día 9**: 2 story points restantes
- **Día 10**: 0 story points restantes

### Definition of Done
Un elemento está "Done" cuando:
- [ ] Código implementado y probado
- [ ] Tests unitarios pasando
- [ ] Code review completado
- [ ] Documentación actualizada
- [ ] Deploy en ambiente de testing
- [ ] Aceptado por Product Owner

## 🛠️ Herramientas SCRUM

### Trello
- **URL**: [Tablero FarmaXpress](https://trello.com/b/farmaxpress)
- **Uso**: Gestión visual de tareas y progreso
- **Columnas**:
  - Backlog
  - Sprint Backlog
  - En Progreso
  - Testing
  - Completado

### GitHub
- **Repositorio**: [FarmaXpress GitHub](https://github.com/usuario/farmaxpress)
- **Uso**: Control de versiones y colaboración
- **Branches**:
  - `main`: Código de producción
  - `develop`: Código de desarrollo
  - `feature/*`: Nuevas funcionalidades
  - `hotfix/*`: Correcciones urgentes

### Herramientas de Comunicación
- **Slack**: Comunicación diaria del equipo
- **Zoom**: Ceremonias SCRUM remotas
- **Google Docs**: Documentación colaborativa

## 📋 User Stories Template

```
Como [tipo de usuario]
Quiero [funcionalidad]
Para [beneficio/objetivo]

Criterios de Aceptación:
- [ ] Criterio 1
- [ ] Criterio 2
- [ ] Criterio 3

Estimación: [X] story points
Prioridad: [Alta/Media/Baja]
```

## 🎯 Beneficios de SCRUM en FarmaXpress

### Para el Equipo
- **Transparencia**: Visibilidad completa del progreso
- **Colaboración**: Trabajo en equipo mejorado
- **Adaptabilidad**: Respuesta rápida a cambios
- **Calidad**: Testing continuo y code reviews

### Para el Producto
- **Valor**: Entregas frecuentes de valor
- **Feedback**: Retroalimentación temprana
- **Flexibilidad**: Adaptación a requisitos cambiantes
- **Calidad**: Producto robusto y bien probado

### Para el Negocio
- **Time-to-Market**: Entregas más rápidas
- **ROI**: Retorno de inversión más temprano
- **Riesgo**: Reducción de riesgos de proyecto
- **Satisfacción**: Mayor satisfacción del cliente

## 📚 Lecciones Aprendidas

### Lo que Funcionó Bien
- Daily standups mantuvieron al equipo sincronizado
- Sprint reviews facilitaron feedback temprano
- Retrospectivas mejoraron continuamente el proceso
- User stories clarificaron requisitos

### Áreas de Mejora
- Estimación inicial de story points fue conservadora
- Testing automatizado se implementó tarde
- Documentación se dejó para el final
- Comunicación con stakeholders podría ser más frecuente

### Acciones de Mejora
- Implementar testing desde el primer sprint
- Documentar durante el desarrollo
- Sesiones de estimación más frecuentes
- Reuniones semanales con stakeholders

## 🚀 Próximos Pasos

### Mejoras del Proceso
- Implementar CI/CD pipeline
- Automatizar más tests
- Mejorar métricas de calidad
- Implementar pair programming

### Escalabilidad
- Preparar para equipos más grandes
- Implementar Scrum of Scrums
- Definir roles adicionales si es necesario
- Establecer guías de contribución

---

**Este documento es un registro vivo del proceso SCRUM implementado en FarmaXpress y se actualiza continuamente basado en retrospectivas y mejoras del proceso.**

