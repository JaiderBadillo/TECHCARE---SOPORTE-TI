# 🧪 Carpeta 04 - Pruebas y Calidad de Software (QA)
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente  
**Fase del Ciclo de Vida:** Verificación, Validación y Pruebas (Testing)  
**Versión:** 2.2.0  

---

## 📂 Contenido del Directorio

En esta carpeta se encuentra toda la documentación técnica, casos de prueba, matrices de trazabilidad y scripts ejecutables de pruebas automatizadas del proyecto **TechCare**:

| Archivo | Descripción |
| :--- | :--- |
| 📄 **[Plan_de_Pruebas_y_Calidad_Software.md](Plan_de_Pruebas_y_Calidad_Software.md)** | **Documento Principal del Apartado 6.4 PRUEBAS:** Contiene el plan de pruebas, estrategias, 12 casos de prueba formales, pruebas de IA y NLP, seguridad básica, manejo de casos límite, evidencias de ejecución, registro de defectos (Bug Tracking Log), Matriz de Trazabilidad Requisito-Prueba (RTM) y conclusiones. |
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
* **Tasa de Aprobación:** **100% PASS**
* **Defectos Críticos Pendientes:** **0**
* **Estado de Calidad:** ✅ **Aprobado para Producción**
