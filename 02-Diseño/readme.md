# 🎨 Fase 02: Diseño de Software y Arquitectura

Esta carpeta contiene todos los diagramas, modelos y especificaciones técnicas de diseño de la plataforma **TechCare Soporte TI**:

---

## 📑 Documentos y Diagramas Disponibles

1. **[Diagrama de Arquitectura del Sistema](Diagrama_Arquitectura.md):**
   * Diagrama de flujo de capas en Mermaid (Frontend, Enrutamiento, Controladores, Servicios IA, Modelos y Persistencia).
   * Diagrama de secuencia de Registro, Autenticación y Radicación de Tickets.
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

---

*Nota: Los diagramas Mermaid se renderizan de forma interactiva y visual directamente en la interfaz web de GitHub.*
