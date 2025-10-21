# Estructura de CSS - FarmaXpress

Esta carpeta contiene los archivos CSS organizados por funcionalidad para facilitar el mantenimiento y la modificación de los estilos.

## Archivos CSS

### 1. `base.css`
**Estilos comunes que se aplican a todos los temas**
- Fuentes base (Inter)
- Estilos para menú desplegable
- Estilos para el mapa (Leaflet)
- Animaciones y efectos base
- Estilos para logos
- Barra de progreso de notificaciones

### 2. `light-theme.css`
**Estilos específicos para el tema claro**
- Colores del menú desplegable en tema claro
- Colores del encabezado y leyenda en tema claro
- Colores de texto en tema claro
- Estilos para el mapa en tema claro
- Estilos para contador de farmacias en tema claro
- Estilos para el loading del mapa en tema claro

### 3. `dark-theme.css`
**Estilos específicos para el tema oscuro**
- Colores del menú desplegable en tema oscuro
- Colores del encabezado y leyenda en tema oscuro
- Colores de texto en tema oscuro
- Estilos para el mapa en tema oscuro
- Estilos para contador de farmacias en tema oscuro
- Estilos para el loading del mapa en tema oscuro
- Estilos para header, body, contenedores, cards, botones, enlaces e inputs en tema oscuro

### 4. `responsive.css`
**Estilos específicos para dispositivos móviles y tablets**
- **Móviles (max-width: 768px)**: Navegación, tamaños de texto, espaciado, mapa
- **Móviles pequeños (max-width: 480px)**: Tamaños de texto más pequeños, mapa más pequeño
- **Tablets (769px - 1024px)**: Tamaño de mapa intermedio

### 5. `desktop.css`
**Estilos específicos para dispositivos de escritorio**
- **Escritorio (min-width: 1025px)**: Navegación horizontal, tamaños de texto grandes, espaciado amplio
- **Pantallas grandes (min-width: 1440px)**: Contenedor más ancho, mapa más grande

## Cómo modificar los estilos

### Para cambiar colores del tema claro:
Edita el archivo `light-theme.css`

### Para cambiar colores del tema oscuro:
Edita el archivo `dark-theme.css`

### Para cambiar estilos en móviles:
Edita el archivo `responsive.css` en las secciones `@media (max-width: 768px)` o `@media (max-width: 480px)`

### Para cambiar estilos en escritorio:
Edita el archivo `desktop.css` en las secciones `@media (min-width: 1025px)` o `@media (min-width: 1440px)`

### Para cambiar estilos comunes:
Edita el archivo `base.css`

## Orden de carga en el HTML

Los archivos se cargan en el siguiente orden:
1. `base.css` - Estilos base
2. `light-theme.css` - Tema claro (por defecto)
3. `dark-theme.css` - Tema oscuro (se activa con media query o JavaScript)
4. `responsive.css` - Estilos responsive
5. `desktop.css` - Estilos de escritorio

## Notas importantes

- Los estilos de tema oscuro se activan automáticamente con `media="(prefers-color-scheme: dark)"` o mediante JavaScript
- Los estilos responsive y de escritorio se aplican automáticamente según el tamaño de pantalla
- Todos los archivos usan `!important` donde es necesario para sobrescribir Tailwind CSS
- Los estilos están organizados de manera que los más específicos sobrescriben a los generales
