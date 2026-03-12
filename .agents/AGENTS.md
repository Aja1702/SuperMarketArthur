# 📚 Guía de Uso: Carpeta `.agents`

La carpeta `.agents` contiene instrucciones personalizadas para automatizar tareas comunes en el proyecto.

## 📂 Estructura

```
.agents/
├── workflows/                    # Procedimientos paso a paso
│   ├── crear_pagina_template.md
│   └── agregar_feature_psr4.md
└── skills/                       # Instrucciones especializadas
    └── analizador_proyecto/
        └── SKILL.md
```

---

## 🔄 Workflows

Los workflows son **instrucciones paso a paso** para realizar tareas comunes.

### Crear un nuevo workflow

1. Crea un archivo `.md` en `.agents/workflows/`:
```bash
.agents/workflows/nombre_workflow.md
```

2. Estructura mínima:
```markdown
---
description: Descripción corta de qué hace este workflow
---

# Nombre del Workflow

Explicación detallada con pasos numerados...

1. Paso 1
2. Paso 2
3. Paso 3
```

### Workflows disponibles:

| Workflow | Uso |
|----------|-----|
| `crear_pagina_template.md` | Crear nuevas páginas/vistas |
| `agregar_feature_psr4.md` | Agregar nuevas funcionalidades |

---

## 🎯 Skills

Los skills son **conjuntos de instrucciones especializadas** para análisis, revisión o tareas complejas.

### Crear un nuevo skill

1. Crea una carpeta en `.agents/skills/`:
```bash
.agents/skills/nombre_skill/
```

2. Crea un archivo `SKILL.md` dentro:
```markdown
---
name: Nombre del Skill
description: Descripción del skill
---

# Instrucciones

Define qué debe hacer el agente cuando se invoque este skill...

## Pasos:
1. Primer paso
2. Segundo paso

## Notas:
- Información importante
- Consideraciones especiales
```

3. **Opcional:** Agrega archivos de referencia:
```
.agents/skills/nombre_skill/
├── SKILL.md
├── ejemplos/
│   └── archivo_ejemplo.md
└── referencias/
    └── doc_referencia.md
```

### Skills disponibles:

| Skill | Uso |
|-------|-----|
| `analizador_proyecto` | Revisar estado general del código |

---

## 💡 Cuándo usar cada uno

### Usar Workflows cuando:
- Necesitas pasos **repetibles y predecibles**
- Es una tarea **común** que haces frecuentemente
- Necesitas **consistencia** en la implementación
- Ejemplos: crear página, agregar feature, hacer deploy

### Usar Skills cuando:
- Necesitas **análisis profundo** o investigación
- Es una tarea **compleja** con muchas variables
- Necesitas **validación** o auditoría
- Ejemplos: revisar proyecto, auditar código, diagnóstico de errores

---

## 🚀 Cómo invocar desde el chat

**Para workflows:**
```
"Crea una nueva página siguiendo el workflow crear_pagina_template"
```

**Para skills:**
```
"Revisa el proyecto usando el skill analizador_proyecto"
```

---

## 📖 Ejemplos de Skills a crear en el futuro

- **testing_skill** - Validar y ejecutar tests
- **refactoring_skill** - Sugerencias de refactorización
- **database_skill** - Auditar esquema de BD
- **performance_skill** - Análisis de rendimiento
- **security_skill** - Revisión de seguridad

---

## ✅ Checklist para crear nuevos Workflows/Skills

### Para Workflows:
- [ ] Archivo `.md` en `.agents/workflows/`
- [ ] Frontmatter con `description`
- [ ] Pasos claros y numerados
- [ ] Ejemplos de código si aplica
- [ ] Checklist final de verificación

### Para Skills:
- [ ] Carpeta en `.agents/skills/nombre_skill/`
- [ ] Archivo `SKILL.md` con frontmatter
- [ ] Sección "Instrucciones para el Agente"
- [ ] Pasos claramente definidos
- [ ] Notas y consideraciones
- [ ] (Opcional) Carpetas `ejemplos/` y `referencias/`

---

## 📝 Ejemplo: Crear un Skill para Testing

```markdown
---
name: Validador de Tests
description: Ejecutar y validar que todos los tests pasen
---

# Validador de Tests

Cuando el usuario pida "ejecutar tests" o "validar tests":

## 1. Verificar configuración:
   - Revisa `phpunit.xml`
   - Verifica que PHPUnit está instalado

## 2. Ejecutar tests:
   ```bash
   vendor/bin/phpunit
   ```

## 3. Reportar resultados:
   - Formato: Tests/Failures/Skipped
   - Detalle de fallos si existen

## 4. Recomendaciones:
   - Sugiere qué falló y por qué
   - Propone correcciones
```

---

**Última actualización:** Marzo 2026 (Post-refactor PSR-4)
