# 📚 7. REPOSITORIO DOCUMENTAL PARA LAS PRUEBAS (WORD .DOCX)
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con Diagnóstico IA y Analítica Predictiva  
**Versión:** 2.2.0  
**Desarrollador y Responsable de QA:** Jaider Augusto Niño Badillo  
**Formato Principal de Entrega:** Microsoft Word (`.docx`) formal  
**Ubicación Física del Repositorio:** [`04-Pruebas/repositorio_documental/`](file:///c:/Users/Jaider/Documents/Proyectos_antigravity/Soporte/04-Pruebas/repositorio_documental/)  

---

## 1. INTRODUCCIÓN Y JUSTIFICACIÓN TÉCNICA

El **Repositorio Documental de Pruebas** constituye el corpus de datos de ensayo diseñado para someter a prueba las capacidades de ingesta, clasificación semántica (NLP), diagnóstico mediante Inteligencia Artificial (Google Gemini Cloud y Motor Heurístico Local) y validación documental del sistema **TechCare Soporte TI**.

Para garantizar una presentación profesional, formal y académica:
1. Se estructuró un conjunto de **30 documentos técnicos en formato Microsoft Word (`.docx`)**, los cuales pueden abrirse e inspeccionarse directamente en cualquier visor de documentos ofimáticos.
2. Cada documento cuenta con una **portada institucional**, **ficha de metadatos**, **una caja destacada con la explicación técnica de su función en las pruebas** y su respectivo **desarrollo temático**.
3. Los documentos están divididos en **tres (3) categorías estratégicas** del entorno de Mesa de Ayuda TI (10 documentos por categoría).
4. **Política de Privacidad y Anonimización:** Se garantiza que el 100% del contenido utiliza **datos sintéticos, ficticios y simulados** (direcciones IP privadas de laboratorio RFC 1918/RFC 5737, empresas ficticias como *"Distribuidora Central S.A."* o *"Soluciones Digitales Demo"*, y usuarios de prueba anonimizados como `user.alpha@ficticia.com`), asegurando la ausencia total de datos personales reales sin autorización conforme a normativas de protección de datos (Habeas Data / RGPD).

---

## 2. ESTRUCTURA Y DISTRIBUCIÓN DE CATEGORÍAS

```
04-Pruebas/repositorio_documental/
├── 01_reportes_incidentes_logs/    ➔ 10 Documentos en Word (.docx)
├── 02_guias_procedimientos_sop/     ➔ 10 Documentos en Word (.docx)
└── 03_politicas_acuerdos_sla/       ➔ 10 Documentos en Word (.docx)
```

---

## 3. CATÁLOGO DETALLADO DE LOS 30 DOCUMENTOS WORD (.DOCX)

### 📂 Categoría 1: Reportes de Incidentes y Logs del Sistema (10 Documentos Word)
*Ubicación:* `04-Pruebas/repositorio_documental/01_reportes_incidentes_logs/`

| Código | Nombre del Archivo Word (`.docx`) | Título y Ficha del Documento | Breve Explicación y Función en las Pruebas de TechCare |
| :---: | :--- | :--- | :--- |
| **DOC-01** | `DOC-01-Reporte_Falla_Tunel_VPN.docx` | Informe de Caída de Túnel IPsec en Gateway VPN | **Función:** Evalúa la capacidad del NLP y la IA para reconocer fallos de enlace de red, errores de fase IKE y sugerir comandos de mitigación (`ipconfig /flushdns`, reinicio de interfaz). |
| **DOC-02** | `DOC-02-Incidente_Deadlock_MySQL.docx` | Informe Técnico: Detección y Resolución de Deadlock en MySQL | **Función:** Verifica cómo la IA analiza consultas concurrentes bloqueantes en InnoDB y sugiere comandos de diagnóstico (`SHOW FULL PROCESSLIST`, `KILL`). |
| **DOC-03** | `DOC-03-Lote_Tickets_Soporte_Helpdesk.docx` | Lote Consolidado de Solicitudes Helpdesk de Prueba | **Función:** Se utiliza para pruebas de carga y para comprobar que los gráficos de Chart.js del Dashboard calculen adecuadamente los porcentajes y distribuciones. |
| **DOC-04** | `DOC-04-Alerta_Seguridad_Phishing.docx` | Reporte Forense: Detección de Campaña de Phishing | **Función:** Comprueba la clasificación de incidentes críticos de seguridad ante términos como phishing y la recomendación de aislamiento y revocación de tokens. |
| **DOC-05** | `DOC-05-Log_Expiracion_Licencias_M365.docx` | Registro de Auditoría: Caducidad de Tokens Microsoft 365 | **Función:** Valida que el motor local sugiera la limpieza de credenciales y la reactivación mediante el script oficial `OSPP.VBS`. |
| **DOC-06** | `DOC-06-Reporte_Corte_Fibra_Optica.docx` | Reporte de Conmutación por Falla: Corte de Fibra Óptica | **Función:** Alimenta el módulo de analítica estratégica para justificar decisiones de inversión en infraestructura de telecomunicaciones redundante. |
| **DOC-07** | `DOC-07-Auditoria_Accesos_Fallidos_AD.docx` | Auditoría de Intentos Fallidos y Bloqueo de Cuentas en AD | **Función:** Evalúa la detección de cuentas bloqueadas (Eventos 4625 y 4740) y el flujo de atención para incidentes de tipo `SEGURIDAD`. |
| **DOC-08** | `DOC-08-Volcado_Error_Memoria_ERP.docx` | Informe de Fuga de Memoria (OutOfMemory) en Software ERP | **Función:** Prueba las sugerencias de la IA orientadas a optimización de software, parametrización de buffers y paginación de consultas SQL. |
| **DOC-09** | `DOC-09-Simulacro_Contencion_Ransomware.docx` | Informe Forense: Simulacro de Contención de Malware | **Función:** Permite contrastar las respuestas generadas por la IA contra las mejores prácticas de aislamiento físico y recuperación de copias sombra (VSS). |
| **DOC-10** | `DOC-10-Telemetria_Rendimiento_Servidor.docx` | Reporte de Telemetría: Capacidad y Rendimiento en Servidor | **Función:** Valida las alertas predictivas de saturación de CPU y RAM antes de que los usuarios reporten caídas de servicio. |

---

### 📂 Categoría 2: Guías Técnicas y Procedimientos Operativos Estándar (10 Documentos Word)
*Ubicación:* `04-Pruebas/repositorio_documental/02_guias_procedimientos_sop/`

| Código | Nombre del Archivo Word (`.docx`) | Título y Ficha del Documento | Breve Explicación y Función en las Pruebas de TechCare |
| :---: | :--- | :--- | :--- |
| **DOC-11** | `DOC-11-SOP_Desbloqueo_Cuentas_AD.docx` | SOP-TI-001: Desbloqueo de Cuentas en Active Directory | **Función:** Valida que el copiloto guíe a técnicos de Nivel 1 en el uso de cmdlets seguros de PowerShell (`Unlock-ADUser`, `Set-ADAccountPassword`). |
| **DOC-12** | `DOC-12-Manual_Configuracion_VPN.docx` | Guía de Autoservicio: Configuración del Cliente VPN | **Función:** Evalúa las respuestas de autoservicio dirigidas a empleados en teletrabajo para resolver problemas de conectividad doméstica. |
| **DOC-13** | `DOC-13-Guia_Depuracion_Bloqueos_SQL.docx` | SOP-DB-004: Depuración de Consultas Lentas y Bloqueos | **Función:** Valida la coherencia técnica de los comandos SQL generados en el modal de diagnóstico cuando el problema es de base de datos. |
| **DOC-14** | `DOC-14-Procedimiento_Renovacion_M365.docx` | Procedimiento Técnico: Activación de Licencias Office 365 | **Función:** Corrobora la exactitud de los diagnósticos de la Variante 1 de mitigación rápida de licencias de software ofimático. |
| **DOC-15** | `DOC-15-Catalogo_Errores_Frecuentes_TI.docx` | Catálogo de Errores Frecuentes y Soluciones de Mesa de Ayuda | **Función:** Evalúa la precisión en búsquedas semánticas y la extracción de soluciones directas desde la base de conocimientos. |
| **DOC-16** | `DOC-16-SOP_Aislamiento_Equipo_Infectado.docx` | SOP-SEC-008: Aislamiento Inmediato de Estación Comprometida | **Función:** Certifica que el motor de IA recomiende la desconexión física de red y preservación de memoria RAM antes de cualquier formateo. |
| **DOC-17** | `DOC-17-Guia_Mantenimiento_Preventivo_PC.docx` | Checklist de Mantenimiento Preventivo de Equipos | **Función:** Comprueba los planes de acción recomendados para tickets clasificados como mantenimiento de hardware y sistema operativo. |
| **DOC-18** | `DOC-18-Matriz_Escalamiento_Incidentes.docx` | Matriz Operativa de Escalamiento: Niveles N1, N2 y N3 | **Función:** Valida las reglas de asignación, tiempos máximos (15 min en N1) y derivación en la gestión de solicitudes. |
| **DOC-19** | `DOC-19-SOP_Backup_Restauracion_MySQL.docx` | SOP-DB-008: Respaldo Lógico y Restauración con mysqldump | **Función:** Verifica los casos de prueba de integridad de datos y continuidad operativa del sistema. |
| **DOC-20** | `DOC-20-Arbol_Decision_Heuristica_IA.docx` | Especificación del Árbol de Decisión del Motor Local NLP | **Función:** Documenta y somete a prueba el funcionamiento interno de las reglas de expresiones regulares del modelo local offline. |

---

### 📂 Categoría 3: Políticas de Seguridad y Acuerdos SLA (10 Documentos Word)
*Ubicación:* `04-Pruebas/repositorio_documental/03_politicas_acuerdos_sla/`

| Código | Nombre del Archivo Word (`.docx`) | Título y Ficha del Documento | Breve Explicación y Función en las Pruebas de TechCare |
| :---: | :--- | :--- | :--- |
| **DOC-21** | `DOC-21-Politica_Contrasenas_MFA.docx` | POL-SEC-01: Política de Contraseñas y Autenticación MFA | **Función:** Respalda los requerimientos de seguridad y el almacenamiento de contraseñas mediante hash BCRYPT en TechCare. |
| **22** | `DOC-22-Matriz_Tiempos_SLA_Incidentes.docx` | Acuerdo de Nivel de Servicio (SLA): Tiempos Comprometidos | **Función:** Verifica que las alertas visuales en el Dashboard cambien de color oportunamente según la criticidad (Crítica: 15 min / 2 horas). |
| **23** | `DOC-23-Politica_Uso_Aceptable_Activos.docx` | POL-TI-003: Política de Uso Aceptable de Activos y Redes | **Función:** Proporciona el marco normativo institucional para justificar bloqueos y auditorías de seguridad en la empresa. |
| **24** | `DOC-24-Normativa_Control_Acceso_RBAC.docx` | POL-SEC-02: Normativa de Control de Acceso por Roles (RBAC) | **Función:** Valida directamente las pruebas de penetración y el comportamiento de los middlewares `requireAdmin()` y `requireAuth()`. |
| **25** | `DOC-25-Plan_Continuidad_Negocio_BCP.docx` | Plan de Continuidad del Negocio (BCP) y Recuperación ante Desastres | **Función:** Fundamenta las pruebas de resiliencia y tolerancia a fallos del sistema híbrido de IA con funcionamiento offline. |
| **26** | `DOC-26-Auditoria_Licencias_Software.docx` | Informe de Auditoría y Cumplimiento de Licenciamiento | **Función:** Valida los módulos de consulta y conciliación de software asignado vs comprado en la mesa de ayuda. |
| **27** | `DOC-27-Politica_Gestion_Parches_Seguridad.docx` | POL-SEC-04: Política de Gestión de Parches y Actualizaciones | **Función:** Da soporte a las sugerencias de actualización y mitigación preventiva que emite la IA ante vulnerabilidades. |
| **28** | `DOC-28-Convenio_Confidencialidad_Datos.docx` | Convenio de Confidencialidad y Tratamiento Ético de Datos | **Función:** Garantiza el cumplimiento normativo de protección de datos sensibles en el manejo de soporte técnico. |
| **29** | `DOC-29-Esquema_Clasificacion_Informacion.docx` | POL-SEC-05: Esquema de Clasificación de Activos de Información | **Función:** Justifica el aislamiento de variables de entorno y API Keys en archivos de configuración protegidos. |
| **30** | `DOC-30-Directrices_Cumplimiento_ISO27001.docx` | Guía de Cumplimiento ISO/IEC 27001:2022 en Mesa de Ayuda | **Función:** Certifica que los flujos de trabajo de TechCare se alinean con los estándares internacionales de ciberseguridad (A.5, A.8). |

---

## 4. INSTRUCCIONES PARA GENERAR LOS ARCHIVOS WORD (.DOCX) EN 1 CLIC

Para generar físicamente los 30 archivos `.docx` en tu máquina:

1. Abre tu terminal o PowerShell en la carpeta del proyecto:
   ```bash
   cd c:\Users\Jaider\Documents\Proyectos_antigravity\Soporte\04-Pruebas
   ```
2. Ejecuta el script generador nativo:
   ```bash
   python generar_documentos_docx.py
   ```
3. ¡Listo! En solo 2 segundos se crearán automáticamente los 30 archivos Word en las tres subcarpetas de `repositorio_documental/`, listos para abrir y editar con Microsoft Word.

---

*Documentación elaborada y catalogada por Jaider Augusto Niño Badillo — Desarrollador y Líder de Calidad de Software (QA).*
