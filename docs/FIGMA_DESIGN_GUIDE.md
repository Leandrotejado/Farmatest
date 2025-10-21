# 🎨 Guía de Diseño UI/UX en Figma - FarmaXpress

## 📋 Instrucciones para Crear el Diseño

### **PASO 1: Crear Cuenta en Figma**
1. Ir a [figma.com](https://figma.com)
2. Crear cuenta gratuita
3. Crear nuevo proyecto: "FarmaXpress - Sistema de Gestión Farmacéutica"

### **PASO 2: Configurar el Proyecto**
```
Nombre del archivo: FarmaXpress-UI-UX
Descripción: Diseño completo del sistema de gestión farmacéutica con IA
```

## 🎯 PÁGINAS A DISEÑAR

### **PÁGINA 1: WIREFRAMES (Estructura)**
- Layout general del sistema
- Navegación principal
- Estructura de componentes

### **PÁGINA 2: DESKTOP DESIGN (1920x1080)**
- Página principal con mapa
- Dashboard de usuario
- Dashboard administrativo
- Formularios y modales

### **PÁGINA 3: MOBILE DESIGN (375x812)**
- Versión móvil responsive
- Menú hamburguesa
- Formularios adaptados

### **PÁGINA 4: COMPONENTES**
- Botones y formularios
- Tarjetas de medicamentos
- Popups y modales
- Iconos personalizados

## 🎨 ESPECIFICACIONES DE DISEÑO

### **Paleta de Colores**
```
Primario: #2563eb (Azul)
Secundario: #10b981 (Verde)
Acento: #f59e0b (Naranja)
Peligro: #ef4444 (Rojo)
Gris: #6b7280
Fondo: #f9fafb
```

### **Tipografía**
```
Títulos: Inter Bold, 24px-48px
Subtítulos: Inter SemiBold, 18px-24px
Cuerpo: Inter Regular, 14px-16px
Pequeño: Inter Regular, 12px
```

### **Espaciado**
```
XS: 4px
SM: 8px
MD: 16px
LG: 24px
XL: 32px
XXL: 48px
```

## 📱 PANTALLAS A DISEÑAR

### **1. PÁGINA PRINCIPAL (Desktop)**
```
Elementos:
- Header con logo y navegación
- Hero section con mapa
- Mapa interactivo con marcadores
- Footer con información
- Botones de acción principales
```

### **2. REGISTRO DE USUARIO**
```
Elementos:
- Formulario de registro
- Validación en tiempo real
- Mensaje de éxito
- Página de verificación enviada
```

### **3. LOGIN**
```
Elementos:
- Formulario de login
- Opciones de recuperación
- Redes sociales (opcional)
- Mensajes de error
```

### **4. DASHBOARD DE USUARIO**
```
Elementos:
- Sidebar de navegación
- Tarjetas de estadísticas
- Lista de medicamentos
- Búsqueda y filtros
```

### **5. DASHBOARD DE IA**
```
Elementos:
- Estado del servicio
- Recomendaciones
- Predicciones
- Anomalías detectadas
```

### **6. DASHBOARD ADMINISTRATIVO**
```
Elementos:
- Panel de control
- Gráficos y estadísticas
- Tablas de datos
- Botones de acción
```

### **7. GESTIÓN DE MEDICAMENTOS**
```
Elementos:
- Lista de medicamentos
- Formulario CRUD
- Filtros avanzados
- Paginación
```

### **8. VERSIÓN MÓVIL**
```
Elementos:
- Menú hamburguesa
- Mapa adaptado
- Formularios responsive
- Navegación táctil
```

## 🎨 COMPONENTES PRINCIPALES

### **Botones**
```
Primario: Fondo azul, texto blanco, border-radius 8px
Secundario: Borde azul, texto azul, fondo blanco
Peligro: Fondo rojo, texto blanco
Tamaños: SM (32px), MD (40px), LG (48px)
```

### **Formularios**
```
Input: Borde gris, focus azul, padding 12px
Label: Texto gris oscuro, peso medium
Error: Borde rojo, texto rojo pequeño
Éxito: Borde verde, texto verde pequeño
```

### **Tarjetas**
```
Fondo: Blanco
Sombra: 0 1px 3px rgba(0,0,0,0.1)
Border-radius: 12px
Padding: 24px
```

### **Navegación**
```
Header: Fondo azul degradado
Links: Texto blanco, hover azul claro
Dropdown: Fondo blanco, sombra
Mobile: Menú hamburguesa
```

## 📐 ESPECIFICACIONES TÉCNICAS

### **Grid System**
```
Desktop: 12 columnas, gutter 24px
Tablet: 8 columnas, gutter 16px
Mobile: 4 columnas, gutter 12px
```

### **Breakpoints**
```
Desktop: 1920px, 1440px, 1024px
Tablet: 768px, 640px
Mobile: 375px, 320px
```

### **Componentes Reutilizables**
```
- Botones (primario, secundario, peligro)
- Inputs (texto, email, password, select)
- Tarjetas (medicamento, farmacia, estadística)
- Modales (confirmación, información)
- Navegación (header, sidebar, mobile)
```

## 🎯 CASOS DE USO ESPECÍFICOS

### **Caso 1: Usuario Busca Farmacia**
1. Página principal con mapa
2. Usuario permite ubicación
3. Farmacias aparecen en mapa
4. Popup de farmacia seleccionada
5. Botón "Cómo llegar"

### **Caso 2: Registro y Verificación**
1. Formulario de registro
2. Validación en tiempo real
3. Mensaje "Correo enviado"
4. Correo de verificación (mockup)
5. Página de éxito

### **Caso 3: Dashboard de IA**
1. Estado del servicio
2. Recomendaciones generadas
3. Predicción de demanda
4. Anomalías detectadas

### **Caso 4: Gestión Administrativa**
1. Dashboard con estadísticas
2. Lista de medicamentos
3. Formulario de nuevo medicamento
4. Alertas de stock

## 📱 RESPONSIVE DESIGN

### **Desktop (1920x1080)**
- Layout de 3 columnas
- Mapa en pantalla completa
- Navegación horizontal
- Formularios amplios

### **Tablet (768x1024)**
- Layout de 2 columnas
- Mapa adaptado
- Navegación colapsable
- Formularios medianos

### **Mobile (375x812)**
- Layout de 1 columna
- Mapa en pantalla completa
- Menú hamburguesa
- Formularios compactos

## 🎨 ESTILOS Y EFECTOS

### **Gradientes**
```
Header: linear-gradient(135deg, #2563eb, #1d4ed8)
Botones: linear-gradient(135deg, #10b981, #059669)
Cards: linear-gradient(135deg, #f9fafb, #f3f4f6)
```

### **Sombras**
```
Cards: 0 1px 3px rgba(0,0,0,0.1)
Buttons: 0 2px 4px rgba(0,0,0,0.1)
Modals: 0 10px 25px rgba(0,0,0,0.2)
```

### **Animaciones**
```
Hover: Transform scale(1.02)
Focus: Border color change
Loading: Spinner rotation
Transitions: 0.2s ease-in-out
```

## 📋 CHECKLIST DE DISEÑO

### **Wireframes**
- [ ] Layout general del sistema
- [ ] Navegación principal
- [ ] Estructura de componentes
- [ ] Flujo de usuario básico

### **Desktop Design**
- [ ] Página principal
- [ ] Formularios de registro/login
- [ ] Dashboard de usuario
- [ ] Dashboard de IA
- [ ] Dashboard administrativo
- [ ] Gestión de medicamentos

### **Mobile Design**
- [ ] Versión móvil de todas las pantallas
- [ ] Menú hamburguesa
- [ ] Formularios responsive
- [ ] Navegación táctil

### **Componentes**
- [ ] Botones (todos los estados)
- [ ] Formularios (todos los tipos)
- [ ] Tarjetas (medicamentos, farmacias)
- [ ] Modales y popups
- [ ] Navegación (header, sidebar, mobile)

### **Estados**
- [ ] Estados normales
- [ ] Estados hover
- [ ] Estados focus
- [ ] Estados disabled
- [ ] Estados de error
- [ ] Estados de éxito

## 🔗 ENLACES ÚTILES

### **Recursos de Figma**
- [Figma Community](https://figma.com/community)
- [Material Design Icons](https://figma.com/community/file/material-design-icons)
- [Inter Font](https://figma.com/community/file/inter-font)

### **Inspiración**
- [Dribbble Healthcare](https://dribbble.com/search/healthcare)
- [Behance Pharmacy](https://behance.net/search/projects?search=pharmacy)
- [UI Patterns](https://ui-patterns.com)

## 📤 EXPORTACIÓN

### **Formatos de Exportación**
- **PNG**: Para presentaciones (2x, 3x)
- **SVG**: Para iconos y gráficos
- **PDF**: Para documentación
- **CSS**: Para desarrollo

### **Nomenclatura de Archivos**
```
Desktop: farmaxpress-desktop-[pantalla].png
Mobile: farmaxpress-mobile-[pantalla].png
Components: farmaxpress-[componente].svg
```

---

**Esta guía te ayudará a crear un diseño profesional y completo en Figma que demuestre la calidad del sistema FarmaXpress.**
