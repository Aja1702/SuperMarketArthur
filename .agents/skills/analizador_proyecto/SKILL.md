---
name: Analizador del Proyecto
description: Instrucciones sobre cómo analizar el estado general del código y la estructura.
---

# Instrucciones para el Agente

Cuando el usuario pida "revisar el proyecto" o "analizar el código", debes seguir estos pasos estrictamente:

1. **Revisar estructura:** Verifica que existan las carpetas principales esperadas (`assets`, `app`, `views`, `models`).
2. **Auditoría de Logs:**
   - Revisa el archivo `logs/app.log` si existe para detectar errores recientes de PHP o base de datos.
3. **Reporte:** Devuelve siempre un resumen claro en formato Markdown (Markdown estándar).

# Notas adicionales
- Siempre prioriza la estética en las recomendaciones de UI/UX, siguiendo la paleta de colores principal de la tienda.
