# 📱 Flujo de Usuario (User Flow) - FarmaXpress

## 🎯 Resumen del Sistema

FarmaXpress es un sistema de gestión farmacéutica que permite a los usuarios encontrar farmacias de turno, consultar medicamentos disponibles y recibir recomendaciones inteligentes basadas en IA.

## 👥 Tipos de Usuario

### 1. **Usuario No Registrado (Visitante)**
- Puede ver el mapa de farmacias
- Puede buscar farmacias por ubicación
- Acceso limitado a funcionalidades

### 2. **Usuario Cliente Registrado**
- Todas las funcionalidades del visitante
- Acceso a dashboard personal
- Recomendaciones de medicamentos
- Historial de búsquedas

### 3. **Administrador**
- Gestión completa del sistema
- CRUD de medicamentos
- Gestión de usuarios
- Dashboard con estadísticas
- Acceso a funcionalidades de IA

## 🔄 Flujos Principales

### **FLUJO 1: Registro y Verificación de Usuario**

```
1. Usuario accede a la página principal
   ↓
2. Hace clic en "Registrarse"
   ↓
3. Llena formulario de registro
   - Nombre completo
   - Email
   - Contraseña
   - Confirmar contraseña
   ↓
4. Sistema valida datos
   - Email único
   - Contraseñas coinciden
   - Formato válido
   ↓
5. Sistema crea cuenta NO VERIFICADA
   ↓
6. Sistema envía correo de verificación
   ↓
7. Usuario recibe correo
   ↓
8. Usuario hace clic en enlace de verificación
   ↓
9. Sistema verifica token y marca cuenta como VERIFICADA
   ↓
10. Sistema envía correo de bienvenida
    ↓
11. Usuario puede acceder a todas las funcionalidades
```

### **FLUJO 2: Inicio de Sesión**

```
1. Usuario accede a página de login
   ↓
2. Ingresa email y contraseña
   ↓
3. Sistema valida credenciales
   ↓
4. Sistema verifica si email está verificado
   ↓
5a. Si NO está verificado:
    - Muestra mensaje de verificación pendiente
    - Opción de reenviar correo
   ↓
5b. Si SÍ está verificado:
    - Inicia sesión
    - Redirige según rol:
      * Cliente → Dashboard público
      * Admin → Dashboard administrativo
```

### **FLUJO 3: Búsqueda de Farmacias**

```
1. Usuario accede al mapa
   ↓
2. Sistema solicita ubicación del usuario
   ↓
3a. Usuario permite ubicación:
    - Mapa se centra en ubicación actual
   ↓
3b. Usuario niega ubicación:
    - Mapa se centra en ubicación por defecto
   ↓
4. Sistema carga farmacias de turno
   ↓
5. Usuario puede:
   - Ver farmacias en el mapa
   - Hacer clic en farmacia para ver detalles
   - Usar "Cómo llegar" (abre Google Maps)
   - Ver medicamentos disponibles (requiere login)
```

### **FLUJO 4: Consulta de Medicamentos**

```
1. Usuario hace clic en "Ver medicamentos"
   ↓
2. Sistema verifica si está logueado
   ↓
3a. Si NO está logueado:
    - Muestra opción de login/registro
   ↓
3b. Si SÍ está logueado:
    - Muestra medicamentos de la farmacia
   ↓
4. Usuario puede:
   - Buscar medicamentos específicos
   - Ver detalles (precio, stock, vencimiento)
   - Recibir recomendaciones de IA
   - Agregar a favoritos
```

### **FLUJO 5: Recomendaciones de IA**

```
1. Usuario selecciona un medicamento
   ↓
2. Sistema envía ID del medicamento a servicio de IA
   ↓
3. Servicio de IA (Python) analiza:
   - Descripción del medicamento
   - Categoría
   - Patrones de uso
   ↓
4. IA genera recomendaciones similares
   ↓
5. Sistema muestra recomendaciones al usuario
   ↓
6. Usuario puede:
   - Ver medicamentos similares
   - Comparar precios
   - Ver disponibilidad en otras farmacias
```

### **FLUJO 6: Gestión Administrativa**

```
1. Administrador inicia sesión
   ↓
2. Accede al dashboard administrativo
   ↓
3. Puede realizar:
   - Gestión de medicamentos (CRUD)
   - Gestión de usuarios
   - Ver estadísticas de ventas
   - Configurar alertas de stock
   - Acceder a insights de IA
   ↓
4. Dashboard muestra:
   - Medicamentos con stock bajo
   - Medicamentos próximos a vencer
   - Predicciones de demanda
   - Anomalías detectadas por IA
```

## 🎨 Estados de la Interfaz

### **Estado 1: Carga Inicial**
- Logo de FarmaXpress
- Spinner de carga
- Mensaje "Cargando farmacias..."

### **Estado 2: Mapa Cargado**
- Mapa interactivo visible
- Marcadores de farmacias
- Controles de zoom
- Botón de ubicación

### **Estado 3: Farmacia Seleccionada**
- Popup con información de farmacia
- Botones de acción
- Lista de medicamentos (si está logueado)

### **Estado 4: Usuario No Logueado**
- Acceso limitado
- Botones de login/registro
- Mensajes informativos

### **Estado 5: Usuario Logueado**
- Acceso completo
- Dashboard personal
- Recomendaciones personalizadas

## 🔐 Flujos de Seguridad

### **Verificación de Email**
```
Registro → Token generado → Email enviado → Usuario verifica → Cuenta activada
```

### **Recuperación de Contraseña**
```
Login fallido → "¿Olvidaste contraseña?" → Email de recuperación → Token temporal → Nueva contraseña
```

### **Control de Acceso**
```
Cada página → Verificar sesión → Verificar rol → Permitir/Denegar acceso
```

## 📱 Responsive Design

### **Desktop (≥1024px)**
- Mapa completo visible
- Sidebar con información
- Navegación horizontal
- Múltiples columnas

### **Tablet (768px - 1023px)**
- Mapa adaptado
- Navegación colapsable
- Layout de 2 columnas

### **Mobile (<768px)**
- Mapa en pantalla completa
- Menú hamburguesa
- Botones grandes
- Scroll vertical

## 🎯 Casos de Uso Principales

### **Caso 1: Usuario busca farmacia de turno**
1. Abre la app
2. Permite ubicación
3. Ve farmacias cercanas
4. Selecciona una farmacia
5. Obtiene direcciones

### **Caso 2: Usuario consulta medicamentos**
1. Se registra en la app
2. Verifica su email
3. Busca medicamento específico
4. Ve disponibilidad en farmacias
5. Recibe recomendaciones

### **Caso 3: Administrador gestiona inventario**
1. Inicia sesión como admin
2. Ve alertas de stock bajo
3. Actualiza inventario
4. Revisa predicciones de IA
5. Toma decisiones informadas

## 📊 Métricas de Usuario

### **Tiempos Objetivo**
- Carga inicial: < 3 segundos
- Búsqueda de farmacias: < 2 segundos
- Recomendaciones de IA: < 5 segundos
- Login: < 1 segundo

### **Tasas de Conversión**
- Registro → Verificación: 80%
- Verificación → Primer uso: 70%
- Primer uso → Uso regular: 60%

## 🔄 Flujos de Error

### **Error de Conexión**
```
Error detectado → Mensaje informativo → Opción de reintentar → Fallback a datos locales
```

### **Error de Verificación**
```
Token inválido → Página de error → Opción de reenviar → Nuevo token generado
```

### **Error de IA**
```
Servicio no disponible → Mensaje informativo → Funcionalidad básica disponible
```

---

**Este flujo de usuario garantiza una experiencia intuitiva y eficiente para todos los tipos de usuarios del sistema FarmaXpress.**

