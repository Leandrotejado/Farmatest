# 📸 Guía de Capturas de Pantalla - FarmaXpress

## 🎯 Casos de Uso para Capturar

### **1. PÁGINA PRINCIPAL (Desktop)**
**URL**: `http://localhost/farmatest/public/index.php`
**Casos de uso**:
- ✅ Mapa de farmacias cargado
- ✅ Marcadores de farmacias visibles
- ✅ Navegación completa
- ✅ Hero section con logo

**Capturas necesarias**:
1. **Vista completa de la página principal**
2. **Mapa con farmacias marcadas**
3. **Popup de farmacia seleccionada**
4. **Menú de navegación desplegado**

### **2. REGISTRO DE USUARIO**
**URL**: `http://localhost/farmatest/public/register.php`
**Casos de uso**:
- ✅ Formulario de registro
- ✅ Validación de campos
- ✅ Mensaje de correo enviado

**Capturas necesarias**:
1. **Formulario de registro vacío**
2. **Formulario con datos ingresados**
3. **Mensaje de éxito "Correo enviado"**
4. **Página de verificación enviada**

### **3. VERIFICACIÓN DE EMAIL**
**URL**: `http://localhost/farmatest/verify-email?token=...`
**Casos de uso**:
- ✅ Correo de verificación recibido
- ✅ Enlace de verificación funcional
- ✅ Página de éxito de verificación

**Capturas necesarias**:
1. **Correo de verificación en Gmail**
2. **Página "Email verificado exitosamente"**
3. **Correo de bienvenida recibido**

### **4. INICIO DE SESIÓN**
**URL**: `http://localhost/farmatest/public/login.php`
**Casos de uso**:
- ✅ Login exitoso
- ✅ Redirección según rol
- ✅ Dashboard personal

**Capturas necesarias**:
1. **Formulario de login**
2. **Dashboard de usuario cliente**
3. **Dashboard de administrador**

### **5. BÚSQUEDA DE MEDICAMENTOS**
**URL**: `http://localhost/farmatest/public/medicamentos.php`
**Casos de uso**:
- ✅ Lista de medicamentos
- ✅ Búsqueda por nombre
- ✅ Filtros por categoría
- ✅ Detalles de medicamento

**Capturas necesarias**:
1. **Lista completa de medicamentos**
2. **Búsqueda en tiempo real**
3. **Detalle de medicamento específico**
4. **Filtros aplicados**

### **6. DASHBOARD DE IA**
**URL**: `http://localhost/farmatest/ai/dashboard`
**Casos de uso**:
- ✅ Estado del servicio de IA
- ✅ Recomendaciones generadas
- ✅ Predicciones de demanda
- ✅ Anomalías detectadas

**Capturas necesarias**:
1. **Dashboard completo de IA**
2. **Recomendaciones de medicamentos**
3. **Predicción de demanda**
4. **Lista de anomalías**

### **7. GESTIÓN ADMINISTRATIVA**
**URL**: `http://localhost/farmatest/admin/dashboard.php`
**Casos de uso**:
- ✅ CRUD de medicamentos
- ✅ Gestión de usuarios
- ✅ Estadísticas del sistema
- ✅ Alertas de stock

**Capturas necesarias**:
1. **Dashboard administrativo**
2. **Formulario de nuevo medicamento**
3. **Lista de usuarios**
4. **Alertas de stock bajo**

### **8. VERSIÓN MÓVIL (Responsive)**
**Casos de uso**:
- ✅ Menú hamburguesa
- ✅ Mapa adaptado
- ✅ Formularios responsive
- ✅ Navegación táctil

**Capturas necesarias**:
1. **Página principal en móvil**
2. **Menú móvil desplegado**
3. **Formulario de registro en móvil**
4. **Mapa en pantalla móvil**

## 📱 Instrucciones para Capturas

### **Herramientas Recomendadas**
- **Desktop**: Snipping Tool (Windows) o Screenshot (Mac)
- **Móvil**: Herramientas de desarrollador del navegador
- **Edición**: Paint, GIMP, o herramientas online

### **Configuración del Navegador**
1. **Abrir DevTools** (F12)
2. **Toggle device toolbar** (Ctrl+Shift+M)
3. **Seleccionar dispositivo**:
   - iPhone 12 Pro (375x812)
   - iPad (768x1024)
   - Desktop (1920x1080)

### **Calidad de Capturas**
- **Resolución**: Mínimo 1920x1080 para desktop
- **Formato**: PNG para mejor calidad
- **Tamaño**: Optimizar para web (< 500KB)

## 🎨 Casos de Uso Específicos

### **Caso 1: Usuario Nuevo Completa Registro**
```
1. Captura: Página principal
2. Captura: Clic en "Registrarse"
3. Captura: Formulario lleno
4. Captura: Mensaje "Correo enviado"
5. Captura: Correo en Gmail
6. Captura: Clic en enlace de verificación
7. Captura: Página "Email verificado"
8. Captura: Correo de bienvenida
```

### **Caso 2: Usuario Busca Farmacia de Turno**
```
1. Captura: Mapa cargado con ubicación
2. Captura: Lista de farmacias cercanas
3. Captura: Popup de farmacia seleccionada
4. Captura: Botón "Cómo llegar" (Google Maps)
5. Captura: Información de contacto
```

### **Caso 3: Usuario Consulta Medicamentos**
```
1. Captura: Login exitoso
2. Captura: Lista de medicamentos
3. Captura: Búsqueda "ibuprofeno"
4. Captura: Resultados filtrados
5. Captura: Detalle del medicamento
6. Captura: Recomendaciones de IA
```

### **Caso 4: Administrador Gestiona Sistema**
```
1. Captura: Dashboard administrativo
2. Captura: Alertas de stock bajo
3. Captura: Formulario nuevo medicamento
4. Captura: Lista de usuarios
5. Captura: Estadísticas de ventas
6. Captura: Dashboard de IA
```

### **Caso 5: Sistema de IA en Acción**
```
1. Captura: Estado del servicio Python
2. Captura: Generando recomendaciones
3. Captura: Resultados de recomendaciones
4. Captura: Predicción de demanda
5. Captura: Anomalías detectadas
6. Captura: Insights del negocio
```

## 📋 Checklist de Capturas

### **Desktop (1920x1080)**
- [ ] Página principal con mapa
- [ ] Formulario de registro
- [ ] Login exitoso
- [ ] Dashboard de usuario
- [ ] Lista de medicamentos
- [ ] Búsqueda de medicamentos
- [ ] Dashboard de IA
- [ ] Dashboard administrativo
- [ ] CRUD de medicamentos
- [ ] Gestión de usuarios

### **Móvil (375x812)**
- [ ] Página principal responsive
- [ ] Menú hamburguesa
- [ ] Formulario de registro móvil
- [ ] Mapa en móvil
- [ ] Navegación táctil
- [ ] Dashboard móvil

### **Funcionalidades Específicas**
- [ ] Verificación de email (correo + página)
- [ ] Recomendaciones de IA
- [ ] Predicciones de demanda
- [ ] Detección de anomalías
- [ ] Sistema de alertas
- [ ] Responsive design

## 🎬 Secuencias de Video

### **Secuencia 1: Registro Completo (30 segundos)**
1. Página principal → Registro → Formulario → Envío → Correo → Verificación

### **Secuencia 2: Búsqueda de Farmacia (20 segundos)**
1. Mapa → Ubicación → Farmacias → Selección → Información

### **Secuencia 3: IA en Acción (25 segundos)**
1. Dashboard IA → Recomendaciones → Predicción → Anomalías

### **Secuencia 4: Responsive Design (15 segundos)**
1. Desktop → Redimensionar → Móvil → Menú → Navegación

## 📁 Organización de Archivos

### **Estructura de Carpetas**
```
screenshots/
├── desktop/
│   ├── 01-homepage.png
│   ├── 02-registration.png
│   ├── 03-login.png
│   └── ...
├── mobile/
│   ├── 01-homepage-mobile.png
│   ├── 02-menu-mobile.png
│   └── ...
├── email/
│   ├── verification-email.png
│   ├── welcome-email.png
│   └── ...
└── ai/
    ├── dashboard-ai.png
    ├── recommendations.png
    └── ...
```

### **Nomenclatura de Archivos**
- **Formato**: `[número]-[descripción]-[dispositivo].png`
- **Ejemplos**:
  - `01-homepage-desktop.png`
  - `02-registration-mobile.png`
  - `03-ai-recommendations.png`

## 🎯 Casos de Error (Opcional)

### **Capturas de Manejo de Errores**
- [ ] Token de verificación expirado
- [ ] Error de conexión a IA
- [ ] Stock insuficiente
- [ ] Usuario no autorizado
- [ ] Error de validación

---

**Esta guía asegura que tengas todas las capturas necesarias para demostrar el funcionamiento completo del sistema FarmaXpress.**

