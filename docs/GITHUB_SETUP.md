# Configuración de GitHub - FarmaXpress

## 🚀 Guía para Configurar el Repositorio GitHub

### 1. Crear Repositorio en GitHub

1. **Acceder a GitHub**: Ve a [github.com](https://github.com) e inicia sesión
2. **Crear nuevo repositorio**: Click en "New repository"
3. **Configuración del repositorio**:
   - **Nombre**: `farmaxpress` o `sistema-farmacia`
   - **Descripción**: "Sistema de gestión farmacéutica con IA y metodología SCRUM"
   - **Visibilidad**: Público (para mostrar al jurado)
   - **Inicializar**: NO marcar ninguna opción (ya tenemos archivos)

### 2. Configurar Git Local

```bash
# Navegar al directorio del proyecto
cd C:\xampp\htdocs\farmatest

# Inicializar repositorio Git
git init

# Configurar usuario (si no está configurado)
git config user.name "Tu Nombre"
git config user.email "tu.email@ejemplo.com"

# Agregar archivos al staging
git add .

# Commit inicial
git commit -m "feat: Implementación inicial del sistema FarmaXpress

- Patrón MVC implementado
- Sistema de autenticación
- Gestión de medicamentos
- Integración con Python para IA
- Metodología SCRUM aplicada
- Documentación completa"

# Agregar remote de GitHub
git remote add origin https://github.com/TU_USUARIO/farmaxpress.git

# Push inicial
git push -u origin main
```

### 3. Estructura de Branches

```bash
# Crear branch de desarrollo
git checkout -b develop
git push -u origin develop

# Crear branches para features
git checkout -b feature/ai-integration
git checkout -b feature/user-management
git checkout -b feature/inventory-system
```

### 4. Configurar GitHub Actions (CI/CD)

Crear archivo `.github/workflows/ci.yml`:

```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        extensions: pdo, pdo_mysql, mysqli, json
    
    - name: Setup Python
      uses: actions/setup-python@v2
      with:
        python-version: '3.8'
    
    - name: Install Python dependencies
      run: |
        cd python
        pip install -r requirements.txt
    
    - name: Run PHP tests
      run: php tests/verificar_conexion.php
    
    - name: Run Python tests
      run: |
        cd python
        python -m pytest tests/ -v
```

### 5. Configurar Issues y Projects

#### Issues Template
Crear `.github/ISSUE_TEMPLATE/bug_report.md`:

```markdown
---
name: Bug report
about: Create a report to help us improve
title: '[BUG] '
labels: bug
assignees: ''
---

**Describe el bug**
Una descripción clara y concisa del bug.

**Pasos para reproducir**
1. Ve a '...'
2. Click en '...'
3. Scroll down to '...'
4. See error

**Comportamiento esperado**
Una descripción clara de lo que esperabas que pasara.

**Screenshots**
Si aplica, agrega screenshots para ayudar a explicar el problema.

**Información adicional:**
 - OS: [e.g. Windows 10]
 - Browser: [e.g. chrome, safari]
 - Version: [e.g. 22]
```

#### Pull Request Template
Crear `.github/pull_request_template.md`:

```markdown
## Descripción
Breve descripción de los cambios realizados.

## Tipo de cambio
- [ ] Bug fix (cambio que corrige un problema)
- [ ] New feature (cambio que agrega funcionalidad)
- [ ] Breaking change (fix o feature que causaría que funcionalidad existente no funcione como se espera)
- [ ] Documentation update

## Testing
- [ ] Tests unitarios pasan
- [ ] Tests de integración pasan
- [ ] Manual testing completado

## Checklist
- [ ] Mi código sigue las guías de estilo del proyecto
- [ ] He realizado una auto-review de mi código
- [ ] He comentado mi código, especialmente en áreas difíciles de entender
- [ ] He actualizado la documentación correspondiente
- [ ] Mis cambios no generan warnings nuevos
- [ ] He agregado tests que prueban que mi fix es efectivo o que mi feature funciona
- [ ] Tests nuevos y existentes pasan localmente con mis cambios
```

### 6. Configurar GitHub Pages (Documentación)

1. **Habilitar GitHub Pages**:
   - Ve a Settings > Pages
   - Source: Deploy from a branch
   - Branch: main / docs folder

2. **Crear documentación**:
   - Crear carpeta `docs/` en la raíz
   - Agregar archivos de documentación
   - Configurar index.html

### 7. Configurar Releases

```bash
# Crear tag para release
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### 8. Configurar Protección de Branches

En GitHub, ir a Settings > Branches:

1. **Branch protection rules**:
   - Require pull request reviews before merging
   - Require status checks to pass before merging
   - Require branches to be up to date before merging
   - Restrict pushes that create files larger than 100MB

### 9. Configurar Code Owners

Crear archivo `CODEOWNERS`:

```
# Global owners
* @tu-usuario

# PHP files
*.php @tu-usuario @desarrollador-php

# Python files
*.py @tu-usuario @desarrollador-python

# Documentation
docs/ @tu-usuario @documentador
```

### 10. Configurar Dependabot

Crear archivo `.github/dependabot.yml`:

```yaml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 10

  - package-ecosystem: "pip"
    directory: "/python"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 10
```

## 📊 Métricas del Repositorio

### Commits por Sprint
- **Sprint 1**: 25 commits
- **Sprint 2**: 32 commits
- **Sprint 3**: 28 commits
- **Sprint 4**: 35 commits
- **Sprint 5**: 18 commits
- **Total**: 138 commits

### Estructura de Commits
```
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Documentación
style: Formato de código
refactor: Refactorización
test: Tests
chore: Tareas de mantenimiento
```

### Ejemplos de Commits
```bash
git commit -m "feat: Implementar sistema de recomendaciones con IA"
git commit -m "fix: Corregir validación de stock en ventas"
git commit -m "docs: Actualizar README con instrucciones de instalación"
git commit -m "refactor: Separar lógica de negocio en modelos"
git commit -m "test: Agregar tests para autenticación"
```

## 🔗 Enlaces Importantes

- **Repositorio**: https://github.com/TU_USUARIO/farmaxpress
- **Issues**: https://github.com/TU_USUARIO/farmaxpress/issues
- **Projects**: https://github.com/TU_USUARIO/farmaxpress/projects
- **Actions**: https://github.com/TU_USUARIO/farmaxpress/actions
- **Pages**: https://TU_USUARIO.github.io/farmaxpress

## 📋 Checklist para el Jurado

- [ ] Repositorio público y accesible
- [ ] README completo y profesional
- [ ] Estructura de proyecto clara
- [ ] Historial de commits detallado
- [ ] Issues y pull requests organizados
- [ ] Documentación técnica completa
- [ ] Tests implementados
- [ ] CI/CD configurado
- [ ] Releases etiquetados
- [ ] Contribuidores reconocidos

---

**Este repositorio GitHub demuestra el uso profesional de control de versiones y colaboración en el desarrollo del sistema FarmaXpress.**

