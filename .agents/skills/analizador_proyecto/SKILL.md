---
name: Analizador del Proyecto
description: Instrucciones sobre cómo analizar el estado general del código y la estructura.
---

# Instrucciones para el Agente

Cuando el usuario pida "revisar el proyecto" o "analizar el código", debes seguir estos pasos estrictamente:

1. **Revisar estructura:** Verifica que existan las carpetas principales esperadas (`css`, `js`, `img`, etc.).
2. **Ejecutar script de análisis:**
   - Puedes ejecutar el script de ayuda ubicado en `./scripts/analisis_basico.php` si necesitas información rápida sobre los archivos. (Este es solo un ejemplo).
3. **Reporte:** Devuelve siempre un resumen claro en formato Markdown (Markdown estándar).

# Notas adicionales
- Siempre prioriza la estética en las recomendaciones de UI/UX, siguiendo la paleta de colores principal de la tienda.
