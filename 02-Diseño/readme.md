# 🎨 Fase 02: Diseño de Software y Arquitectura

Esta carpeta contiene todos los diagramas, modelos, mockups y especificaciones técnicas de diseño de la plataforma **TechCare Soporte TI**:

---

## 📑 Catálogo de Documentación Técnica

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

5. **[Especificación de la API REST & Servicios Web](Especificacion_API.md):**
   * Catálogo completo de endpoints (`registro`, `login`, `ticket_guardar`, `ticket_estado`, `ia_solucion`, `ia_analisis`).
   * Parámetros de petición (Request), ejemplos de payload y códigos de estado HTTP.
   * Estructura de respuestas JSON exitosas y de error.
   * Integración externa con Google Gemini REST API.

6. **[Mockups y Diseño de Interfaz de Usuario (UI/UX)](Mockups_Diseno_Interfaz.md):**
   * Guía de estilos y sistema de diseño (Design Tokens, paleta de colores y tipografía Inter).
   * Wireframes estructurados de las 6 vistas principales (Radicación, Historial, Login, Registro, Dashboard y Modal IA).
   * Principios de experiencia de usuario (UX) implementados.

---

*Nota: Los diagramas Mermaid y wireframes se visualizan de forma interactiva y optimizada directamente en GitHub.*
