# 🎨 Fase 02: Diseño de Software y Arquitectura

Esta carpeta contiene todos los diagramas, modelos y especificaciones técnicas de diseño de la plataforma **TechCare Soporte TI**:

---

## 📑 Documentos y Diagramas Disponibles

1. **[Diagrama de Arquitectura del Sistema](Diagrama_Arquitectura.md):**
   * Diagrama de flujo de capas en Mermaid (Frontend, Enrutamiento, Controladores, Servicios IA, Modelos y Persistencia).
   * Especificación de componentes y mecanismo de resiliencia (*Fallback*).

2. **[Diagrama Entidad - Relación (DER)](Diagrama_Entidad_Relacion.md):**
   * Diagrama conceptual y lógico ERD en Mermaid.
   * Cardinalidades y relaciones entre `USUARIOS` y `SOLICITUDES`.
   * Esquema relacional con llaves primarias (`PK`) y foráneas (`FK`).

3. **[Diccionario de Datos Detallado](Diccionario_de_Datos.md):**
   * Catálogo completo de tablas (`usuarios`, `solicitudes`).
   * Tipos de datos SQL, longitudes, restricciones de nulidad (`NULL` / `NOT NULL`) y valores por defecto.
   * Estructura detallada del objeto JSON de diagnósticos en `solucion_ia`.
   * Reglas de integridad y seguridad de datos.

4. **[Diagramas de Secuencia del Sistema](Diagramas_de_Secuencia.md):**
   * **Flujo 1:** Registro y Autenticación de Usuarios (Control de Acceso RBAC).
   * **Flujo 2:** Radicación de Incidente Técnico y Consulta de Historial (Portal Cliente).
   * **Flujo 3:** Diagnóstico Técnico Asistido por IA (Modo Híbrido: Local 0 Tokens / Gemini Cloud).
   * **Flujo 4:** Análisis Estratégico Directivo y Prescripción de Decisiones TI.
   * **Flujo 5:** Gestión Asíncrona de Estados de Tickets (AJAX).

---

*Nota: Los diagramas Mermaid se renderizan de forma interactiva y visual directamente en la interfaz web de GitHub.*
