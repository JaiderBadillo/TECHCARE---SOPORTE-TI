# 🧪 Carpeta 04 - Pruebas y Calidad de Software (QA)
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con Diagnóstico IA y Analítica Predictiva  
**Fase del Ciclo de Vida:** Verificación, Validación y Pruebas (Testing)  
**Desarrollador y Responsable de QA:** Jaider Augusto Niño Badillo  
**Versión:** 2.2.0  

---

## 📂 Contenido del Directorio

En esta carpeta se encuentra toda la documentación técnica, casos de prueba, matrices de trazabilidad, scripts ejecutables de pruebas automatizadas y el repositorio documental de prueba del proyecto **TechCare**:

| Archivo / Carpeta | Descripción |
| :--- | :--- |
| 📄 **[Plan_de_Pruebas_y_Calidad_Software.md](Plan_de_Pruebas_y_Calidad_Software.md)** | **Apartado 6.4 PRUEBAS:** Contiene el plan de pruebas, estrategias, 12 casos de prueba formales, pruebas funcionales, de IA y NLP, seguridad básica, casos límite, evidencias de ejecución, registro de defectos (Bug Tracking Log), Matriz de Trazabilidad Requisito-Prueba (RTM) y conclusiones. |
| 📚 **[7_Repositorio_Documental_Pruebas.md](7_Repositorio_Documental_Pruebas.md)** | **Apartado 7 REPOSITORIO DOCUMENTAL:** Especificación, justificación técnica, inventario completo y mapeo de los 30 documentos de prueba técnicos con datos sintéticos y libres de datos personales reales. |
| 📁 **[repositorio_documental/](repositorio_documental/)** | **Directorio Físico con 30 Documentos de Prueba:** Distribuidos en 3 categorías (`01_reportes_incidentes_logs`, `02_guias_procedimientos_sop`, `03_politicas_acuerdos_sla`) en 6 formatos diferentes (`.log`, `.json`, `.csv`, `.txt`, `.md`, `.html`). |
| ⚙️ **[test_runner.php](test_runner.php)** | **Suite Automatizada de Testing CLI:** Script ejecutable en PHP que valida automáticamente la integridad de archivos, la taxonomía y extracción de patrones semánticos NLP, el procesamiento de variantes de IA, la seguridad criptográfica BCRYPT y el comportamiento ante casos límite. |

---

## 🚀 Cómo Ejecutar las Pruebas Automatizadas

Para correr la batería de pruebas automatizadas localmente en la consola o terminal:

```bash
# 1. Posicionarse en la carpeta de pruebas
cd c:\Users\Jaider\Documents\Proyectos_antigravity\Soporte\04-Pruebas

# 2. Ejecutar el Test Runner
php test_runner.php
```

### 📊 Resumen de Resultados de las Pruebas
* **Total de Pruebas Ejecutadas:** 15 pruebas automatizadas + 12 casos funcionales documentados.
* **Documentos de Prueba Disponibles:** 30 documentos técnicos en 3 categorías y 6 formatos.
* **Tasa de Aprobación:** **100% PASS**
* **Defectos Críticos Pendientes:** **0**
* **Estado de Calidad:** ✅ **Aprobado para Producción**
