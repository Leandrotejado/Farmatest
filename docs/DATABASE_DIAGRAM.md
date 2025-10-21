# 🗄️ Diagrama de Base de Datos - FarmaXpress

## 📊 Estructura de la Base de Datos

### **Diagrama de Relaciones**

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     USUARIOS    │    │   CATEGORIAS    │    │   MEDICAMENTOS  │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ nombre          │    │ nombre          │    │ nombre          │
│ email           │    │ descripcion     │    │ descripcion     │
│ password        │    │ created_at      │    │ precio          │
│ rol             │    └─────────────────┘    │ stock           │
│ email_verified  │           │               │ categoria_id(FK)│
│ verification_   │           │               │ fecha_vencimiento│
│ token           │           │               │ proveedor       │
│ verification_   │           │               │ created_at      │
│ expires         │           │               │ updated_at      │
│ email_verified_ │           │               └─────────────────┘
│ at              │           │                        │
│ password_reset_ │           │                        │
│ token           │           │                        │
│ password_reset_ │           │                        │
│ expires         │           │                        │
│ ultimo_acceso   │           │                        │
│ created_at      │           │                        │
│ updated_at      │           │                        │
└─────────────────┘           │                        │
                              │                        │
                              │ 1:N                   │ 1:N
                              │                        │
┌─────────────────┐           │                        │
│     FARMACIAS   │           │                        │
├─────────────────┤           │                        │
│ id (PK)         │           │                        │
│ nombre          │           │                        │
│ direccion       │           │                        │
│ telefono        │           │                        │
│ lat             │           │                        │
│ lng             │           │                        │
│ horario         │           │                        │
│ obras_sociales  │           │                        │
│ created_at      │           │                        │
│ updated_at      │           │                        │
└─────────────────┘           │                        │
                              │                        │
                              │                        │
┌─────────────────┐           │                        │
│ MEDICAMENTOS_   │           │                        │
│ FARMACIAS       │           │                        │
├─────────────────┤           │                        │
│ id (PK)         │           │                        │
│ medicamento_id  │           │                        │
│ (FK)            │           │                        │
│ farmacia_id (FK)│           │                        │
│ stock           │           │                        │
│ precio_especial │           │                        │
│ created_at      │           │                        │
│ updated_at      │           │                        │
└─────────────────┘           │                        │
                              │                        │
                              │                        │
┌─────────────────┐           │                        │
│     VENTAS      │           │                        │
├─────────────────┤           │                        │
│ id (PK)         │           │                        │
│ usuario_id (FK) │           │                        │
│ farmacia_id (FK)│           │                        │
│ total           │           │                        │
│ fecha_venta     │           │                        │
│ created_at      │           │                        │
│ updated_at      │           │                        │
└─────────────────┘           │                        │
                              │                        │
                              │                        │
┌─────────────────┐           │                        │
│ VENTAS_DETALLE  │           │                        │
├─────────────────┤           │                        │
│ id (PK)         │           │                        │
│ venta_id (FK)   │           │                        │
│ medicamento_id  │           │                        │
│ (FK)            │           │                        │
│ cantidad        │           │                        │
│ precio_unitario │           │                        │
│ subtotal        │           │                        │
│ created_at      │           │                        │
│ updated_at      │           │                        │
└─────────────────┘           │                        │
```

## 📋 Descripción de Tablas

### **1. USUARIOS**
```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado', 'cliente') DEFAULT 'cliente',
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(64) NULL,
    verification_expires DATETIME NULL,
    email_verified_at DATETIME NULL,
    password_reset_token VARCHAR(64) NULL,
    password_reset_expires DATETIME NULL,
    ultimo_acceso DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Relaciones:**
- 1:N con VENTAS (un usuario puede tener múltiples ventas)

### **2. CATEGORIAS**
```sql
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Relaciones:**
- 1:N con MEDICAMENTOS (una categoría puede tener múltiples medicamentos)

### **3. MEDICAMENTOS**
```sql
CREATE TABLE medicamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    categoria_id INT,
    fecha_vencimiento DATE,
    proveedor VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);
```

**Relaciones:**
- N:1 con CATEGORIAS (muchos medicamentos pertenecen a una categoría)
- 1:N con MEDICAMENTOS_FARMACIAS (un medicamento puede estar en múltiples farmacias)
- 1:N con VENTAS_DETALLE (un medicamento puede estar en múltiples ventas)

### **4. FARMACIAS**
```sql
CREATE TABLE farmacias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    telefono VARCHAR(20),
    lat DECIMAL(10,8) NOT NULL,
    lng DECIMAL(11,8) NOT NULL,
    horario TEXT,
    obras_sociales TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Relaciones:**
- 1:N con MEDICAMENTOS_FARMACIAS (una farmacia puede tener múltiples medicamentos)
- 1:N con VENTAS (una farmacia puede tener múltiples ventas)

### **5. MEDICAMENTOS_FARMACIAS (Tabla de Relación)**
```sql
CREATE TABLE medicamentos_farmacias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medicamento_id INT NOT NULL,
    farmacia_id INT NOT NULL,
    stock INT DEFAULT 0,
    precio_especial DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (farmacia_id) REFERENCES farmacias(id) ON DELETE CASCADE,
    UNIQUE KEY unique_medicamento_farmacia (medicamento_id, farmacia_id)
);
```

**Relaciones:**
- N:1 con MEDICAMENTOS (muchos registros pueden referenciar un medicamento)
- N:1 con FARMACIAS (muchos registros pueden referenciar una farmacia)

### **6. VENTAS**
```sql
CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    farmacia_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha_venta DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (farmacia_id) REFERENCES farmacias(id)
);
```

**Relaciones:**
- N:1 con USUARIOS (muchas ventas pueden pertenecer a un usuario)
- N:1 con FARMACIAS (muchas ventas pueden pertenecer a una farmacia)
- 1:N con VENTAS_DETALLE (una venta puede tener múltiples detalles)

### **7. VENTAS_DETALLE**
```sql
CREATE TABLE ventas_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
);
```

**Relaciones:**
- N:1 con VENTAS (muchos detalles pueden pertenecer a una venta)
- N:1 con MEDICAMENTOS (muchos detalles pueden referenciar un medicamento)

## 🔍 Índices y Optimizaciones

### **Índices Principales**
```sql
-- Índices para búsquedas frecuentes
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_verification_token ON usuarios(verification_token);
CREATE INDEX idx_usuarios_password_reset_token ON usuarios(password_reset_token);
CREATE INDEX idx_medicamentos_nombre ON medicamentos(nombre);
CREATE INDEX idx_medicamentos_categoria ON medicamentos(categoria_id);
CREATE INDEX idx_medicamentos_stock ON medicamentos(stock);
CREATE INDEX idx_farmacias_ubicacion ON farmacias(lat, lng);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
```

### **Índices Compuestos**
```sql
-- Para consultas complejas
CREATE INDEX idx_medicamentos_farmacias_stock ON medicamentos_farmacias(farmacia_id, stock);
CREATE INDEX idx_ventas_detalle_medicamento ON ventas_detalle(medicamento_id, venta_id);
```

## 📊 Estados de las Entidades

### **Estados de Usuario**
```
REGISTRADO → EMAIL_VERIFICADO → ACTIVO → INACTIVO
     ↓              ↓              ↓         ↓
   Token         Verificado     Último    Bloqueado
   pendiente     por email      acceso    por admin
```

### **Estados de Medicamento**
```
CREADO → DISPONIBLE → STOCK_BAJO → AGOTADO → VENCIDO
   ↓         ↓           ↓           ↓         ↓
 Nuevo    Stock > 10   Stock ≤ 10  Stock = 0  Fecha < hoy
```

### **Estados de Venta**
```
INICIADA → PROCESANDO → COMPLETADA → CANCELADA
    ↓          ↓            ↓           ↓
 Carrito    Validando    Pagada     Rechazada
           stock        exitosa    por error
```

## 🔄 Flujos de Datos

### **Flujo de Registro de Usuario**
```
1. Usuario llena formulario
2. Sistema valida datos
3. Se crea registro en USUARIOS (email_verified = 0)
4. Se genera verification_token
5. Se envía correo con token
6. Usuario hace clic en enlace
7. Sistema verifica token
8. Se actualiza email_verified = 1
9. Se limpia verification_token
```

### **Flujo de Venta**
```
1. Usuario selecciona medicamentos
2. Sistema verifica stock en MEDICAMENTOS_FARMACIAS
3. Se crea registro en VENTAS
4. Se crean registros en VENTAS_DETALLE
5. Se actualiza stock en MEDICAMENTOS_FARMACIAS
6. Se actualiza stock en MEDICAMENTOS
```

### **Flujo de Recomendaciones IA**
```
1. Usuario selecciona medicamento
2. Sistema consulta MEDICAMENTOS
3. Se envía información a servicio Python
4. IA analiza descripciones y categorías
5. Se devuelven IDs de medicamentos similares
6. Sistema consulta MEDICAMENTOS_FARMACIAS
7. Se muestran recomendaciones al usuario
```

## 📈 Métricas y Reportes

### **Consultas Frecuentes**
```sql
-- Medicamentos con stock bajo
SELECT m.nombre, m.stock, c.nombre as categoria
FROM medicamentos m
JOIN categorias c ON m.categoria_id = c.id
WHERE m.stock <= 10;

-- Ventas por usuario
SELECT u.nombre, COUNT(v.id) as total_ventas, SUM(v.total) as monto_total
FROM usuarios u
LEFT JOIN ventas v ON u.id = v.usuario_id
GROUP BY u.id, u.nombre;

-- Medicamentos más vendidos
SELECT m.nombre, SUM(vd.cantidad) as total_vendido
FROM medicamentos m
JOIN ventas_detalle vd ON m.id = vd.medicamento_id
GROUP BY m.id, m.nombre
ORDER BY total_vendido DESC;
```

## 🔒 Seguridad de Datos

### **Datos Sensibles**
- **Contraseñas**: Hasheadas con `password_hash()`
- **Tokens**: Generados con `random_bytes()`
- **Emails**: Validados con filtros PHP

### **Integridad Referencial**
- **Foreign Keys**: Todas las relaciones tienen FK
- **Cascade Delete**: Eliminación en cascada donde corresponde
- **Unique Constraints**: Emails únicos, combinaciones únicas

### **Backup y Recuperación**
```sql
-- Backup completo
mysqldump -u root -p farmacia > backup_farmacia.sql

-- Restaurar backup
mysql -u root -p farmacia < backup_farmacia.sql
```

---

**Esta estructura de base de datos garantiza la integridad, escalabilidad y rendimiento del sistema FarmaXpress.**

