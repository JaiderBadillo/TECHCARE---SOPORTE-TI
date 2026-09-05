# 📚 7. REPOSITORIO DOCUMENTAL PARA LAS PRUEBAS
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con Diagnóstico IA y Analítica Predictiva  
**Versión:** 2.2.0  
**Desarrollador y Responsable de QA:** Jaider Augusto Niño Badillo  
**Ubicación Física del Repositorio:** [`04-Pruebas/repositorio_documental/`](file:///c:/Users/Jaider/Documents/Proyectos_antigravity/Soporte/04-Pruebas/repositorio_documental/)  

---

## 1. INTRODUCCIÓN Y JUSTIFICACIÓN TÉCNICA

El **Repositorio Documental de Pruebas** constituye el corpus de datos de ensayo diseñado para someter a prueba las capacidades de ingesta, clasificación semántica (NLP), diagnóstico mediante Inteligencia Artificial (Google Gemini Cloud y Motor Heurístico Local) y validación de archivos del sistema **TechCare Soporte TI**.

Para dar cumplimiento estricto a las directrices de aseguramiento de calidad del software:
1. Se estructuró un conjunto de **30 documentos de prueba técnicos**.
2. Los documentos se encuentran distribuidos equilibradamente en **tres (3) categorías operativas** clave del dominio de TI.
3. Se utilizaron **seis (6) formatos de archivo diferentes** (`.log`, `.json`, `.csv`, `.txt`, `.md`, `.html`) para certificar la versatilidad de procesamiento documental.
4. **Política de Privacidad y Anonimización:** Se garantiza que el 100% del contenido utiliza **datos sintéticos, ficticios y simulados** (direcciones IP privadas de laboratorio RFC 1918/RFC 5737, empresas ficticias como *"Distribuidora Central S.A."* o *"Soluciones Digitales Demo"*, y usuarios de prueba anonimizados como `user.alpha@ficticia.com`), asegurando la ausencia total de datos personales reales sin autorización conforme a normativas de protección de datos (Habeas Data / RGPD).

---

## 2. ESTRUCTURA Y DISTRIBUCIÓN DE CATEGORÍAS Y FORMATOS

```
04-Pruebas/repositorio_documental/
├── 01_reportes_incidentes_logs/    ➔ 10 Documentos (.log, .json, .csv, .txt, .md)
├── 02_guias_procedimientos_sop/     ➔ 10 Documentos (.md, .html, .txt, .json)
└── 03_politicas_acuerdos_sla/       ➔ 10 Documentos (.md, .csv, .txt, .json, .html)
```

### Resumen de Formatos Implementados
| Formato | Cantidad | Justificación en el Entorno de Soporte TI |
| :---: | :---: | :--- |
| **`.json`** | 7 | Representación estructurada de eventos, volcados de bases de datos, catálogos de errores y esquemas RBAC. |
| **`.md`** | 8 | Procedimientos operativos estándar (SOP), informes forenses y políticas documentadas en Markdown. |
| **`.txt`** | 6 | Alertas de texto plano, cabeceras MIME de correo electrónico, acuerdos y matrices de escalamiento. |
| **`.csv`** | 4 | Tablas estructuradas de tickets masivos, logs de auditoría AD, acuerdos SLA e inventario de licencias. |
| **`.log`** | 2 | Trazas reales de eventos de red (túneles VPN, IKE) y bitácoras de sincronización de licencias Office 365. |
| **`.html`** | 3 | Guías interactivas de autoservicio para usuarios y checklists técnicos con marcado web semántico. |
| **Total** | **30** | **Conjunto exhaustivo multiformato** |

---

## 3. INVENTARIO COMPLETO DE LOS 30 DOCUMENTOS DE PRUEBA

### 📂 Categoría 1: Reportes de Incidentes y Logs del Sistema (10 Documentos)
*Ubicación:* `04-Pruebas/repositorio_documental/01_reportes_incidentes_logs/`

| ID Documento | Nombre del Archivo | Formato | Propósito en las Pruebas | Casos de Prueba Vinculados |
| :---: | :--- | :---: | :--- | :---: |
| **DOC-01** | `DOC-01-log_falla_vpn_gateway.log` | `.log` | Simulación de caída de enlace VPN y renegociación fallida de Phase 1 para extracción semántica de patrones de red. | CP-07, CP-09, CP-NLP-02 |
| **DOC-02** | `DOC-02-incidente_deadlock_mysql.json` | `.json` | Volcado de transacción InnoDB bloqueante (`LOCK IN SHARE MODE`) para prueba de diagnósticos de base de datos. | CP-06, CP-09, CP-NLP-04 |
| **DOC-03** | `DOC-03-lote_solicitudes_helpdesk.csv` | `.csv` | Lote de 8 tickets de prueba para radicación masiva, cálculo de KPIs en Dashboard y métricas analíticas. | CP-04, CP-10 |
| **DOC-04** | `DOC-04-alerta_intento_phishing.txt` | `.txt` | Análisis de cabeceras de correo malicioso simulado (`SPF fail`) para detección heurística de ciberseguridad. | CP-09, CP-NLP-03 |
| **DOC-05** | `DOC-05-log_expiracion_licencias_m365.log` | `.log` | Eventos de expiración de token de activación de Office y PRT en Entra ID para validación de diagnósticos M365. | CP-08, CP-NLP-01 |
| **DOC-06** | `DOC-06-reporte_caida_enlace_fibra.json` | `.json` | Telemetría de corte de fibra óptica y conmutación a enlace 4G de respaldo para análisis de infraestructura. | CP-07, CP-10 |
| **DOC-07** | `DOC-07-registro_accesos_fallidos_ad.csv` | `.csv` | Auditoría de intentos fallidos (Evento 4625) y bloqueo de cuenta en Directorio Activo para triaje de seguridad. | CP-09, CP-10 |
| **DOC-08** | `DOC-08-dump_error_erp_sap.txt` | `.txt` | Volcado de excepción por desbordamiento de memoria (`Out of Memory`) en ERP para solución asistida por IA. | CP-06, CP-08 |
| **DOC-09** | `DOC-09-incidente_ransomware_simulado.md` | `.md` | Informe forense de prueba de intrusión controlada y aislamiento de red en estación de trabajo. | CP-09, CP-NLP-03 |
| **DOC-10** | `DOC-10-metricas_rendimiento_servidor.json` | `.json` | Registro de picos de consumo de CPU al 94% para pruebas del motor de recomendaciones estratégicas. | CP-10, CP-IA-03 |

---

### 📂 Categoría 2: Procedimientos Operativos Estándar y Guías Técnicas (SOP) (10 Documentos)
*Ubicación:* `04-Pruebas/repositorio_documental/02_guias_procedimientos_sop/`

| ID Documento | Nombre del Archivo | Formato | Propósito en las Pruebas | Casos de Prueba Vinculados |
| :---: | :--- | :---: | :--- | :---: |
| **DOC-11** | `DOC-11-sop_desbloqueo_cuentas_ad.md` | `.md` | Procedimiento estándar de validación de identidad y comandos PowerShell de desbloqueo en Active Directory. | CP-04, CP-06 |
| **DOC-12** | `DOC-12-manual_configuracion_vpn_remoto.html` | `.html` | Guía técnica interactiva de conexión SSL-VPN con comandos `ipconfig /flushdns` para soporte de autoservicio. | CP-07, CP-09 |
| **DOC-13** | `DOC-13-guia_depuracion_consultas_sql.md` | `.md` | Instrucciones paso a paso para análisis de consultas bloqueantes con `SHOW FULL PROCESSLIST` y terminación con `KILL`. | CP-06, CP-09, CP-NLP-04 |
| **DOC-14** | `DOC-14-procedimiento_renovacion_m365.txt` | `.txt` | Guía de depuración de licencias de Office 16 mediante script `OSPP.VBS` para cotejo de respuestas de IA. | CP-08, CP-NLP-01 |
| **DOC-15** | `DOC-15-catalogo_errores_frecuentes_ti.json` | `.json` | Diccionario estructurado de códigos de falla corporativos (`ERR_NET_VPN_AUTH_TIMEOUT`, etc.) para consultas. | CP-09, CP-10 |
| **DOC-16** | `DOC-16-sop_aislamiento_equipo_infectado.md` | `.md` | Protocolo de acción inmediata ante intrusión (desconexión de cable de red y preservación de RAM). | CP-06, CP-09 |
| **DOC-17** | `DOC-17-guia_mantenimiento_preventivo_pc.html` | `.html` | Lista de chequeo trimestral de depuración de temporales, `sfc /scannow` y firmas de antivirus corporativo. | CP-04, CP-08 |
| **DOC-18** | `DOC-18-protocolo_escalamiento_incidentes.txt` | `.txt` | Tiempos y criterios de derivación técnica entre Mesa de Ayuda N1, Soporte Especializado N2 e Infraestructura N3. | CP-05, CP-10 |
| **DOC-19** | `DOC-19-sop_backup_restauracion_bd.md` | `.md` | Rutina de respaldo lógico con `mysqldump` y verificación de integridad de datos para pruebas de continuidad. | CP-ARC-01, CP-ARC-02 |
| **DOC-20** | `DOC-20-arbol_decision_diagnostico_ia.json` | `.json` | Reglas de inferencia léxica y prioridad del motor local heurístico para contrastación algorítmica. | CP-08, CP-NLP-01..04 |

---

### 📂 Categoría 3: Políticas de Seguridad, Acuerdos SLA y Cumplimiento (10 Documentos)
*Ubicación:* `04-Pruebas/repositorio_documental/03_politicas_acuerdos_sla/`

| ID Documento | Nombre del Archivo | Formato | Propósito en las Pruebas | Casos de Prueba Vinculados |
| :---: | :--- | :---: | :--- | :---: |
| **DOC-21** | `DOC-21-politica_contrasenas_mfa.md` | `.md` | Estándares de complejidad (12+ caracteres, MFA obligatorio, rotación 90 días) para validación de seguridad. | CP-01, CP-02, CP-SEC-01 |
| **DOC-22** | `DOC-22-matriz_tiempos_sla_incidentes.csv` | `.csv` | Tiempos máximos de respuesta y resolución según criticidad para verificación de alarmas en el Dashboard. | CP-04, CP-05, CP-10 |
| **DOC-23** | `DOC-23-politica_uso_aceptable_activos.txt` | `.txt` | Reglas de uso legítimo de equipos, prohibición de software no autorizado y auditoría de terminales. | CP-03, CP-11 |
| **DOC-24** | `DOC-24-normativa_control_acceso_rbac.json` | `.json` | Definición formal de privilegios por perfil (`cliente`, `tecnico`, `admin`) para pruebas de control de acceso. | CP-02, CP-03, CP-SEC-01 |
| **DOC-25** | `DOC-25-plan_continuidad_negocio_bcp.md` | `.md` | Objetivos RTO (2 horas) y RPO (15 min) y especificación de contingencia con el motor local offline. | CP-07, RNF-02 |
| **DOC-26** | `DOC-26-auditoria_licenciamiento_software.csv` | `.csv` | Matriz de licencias compradas vs asignadas (M365, FortiClient, Windows Server) para auditoría de cumplimiento. | CP-08, CP-10 |
| **DOC-27** | `DOC-27-politica_gestion_parches_seguridad.html` | `.html` | Calendario oficial de despliegue de parches de seguridad según severidad (Zero-day en 24h, mensual). | CP-10, CP-IA-03 |
| **DOC-28** | `DOC-28-convenio_confidencialidad_datos.txt` | `.txt` | Compromiso formal de reserva de información y tratamiento ético de datos sensibles para administradores. | CP-03, CP-11, CP-12 |
| **DOC-29** | `DOC-29-esquema_clasificacion_informacion.json` | `.json` | Niveles de confidencialidad de la información (Pública, Interna, Confidencial, Restringida/BCRYPT). | CP-03, CP-SEC-01, CP-SEC-02 |
| **DOC-30** | `DOC-30-manual_cumplimiento_iso27001.md` | `.md` | Mapeo de controles de seguridad ISO/IEC 27001:2022 aplicados en la mesa de ayuda inteligente. | CP-03, CP-11, CP-12 |

---

## 4. INTEGRACIÓN DEL REPOSITORIO CON EL PLAN DE PRUEBAS DE TECHCARE

Este repositorio documental interactúa de forma directa con los módulos evaluados en el [Plan_de_Pruebas_y_Calidad_Software.md](file:///c:/Users/Jaider/Documents/Proyectos_antigravity/Soporte/04-Pruebas/Plan_de_Pruebas_y_Calidad_Software.md):

1. **Pruebas de Clasificación y Extracción Semántica (NLP):**
   Los documentos `DOC-01`, `DOC-02`, `DOC-04` y `DOC-05` proporcionan el texto de prueba exacto con el que el motor heurístico [`LocalExpertService.php`](file:///c:/Users/Jaider/Documents/Proyectos_antigravity/Soporte/03-Desarrollo/src/Services/LocalExpertService.php) reconoce patrones léxicos de redes, bases de datos, seguridad y licenciamiento.
2. **Pruebas de Procesamiento y Diagnóstico con IA (Cloud y Local):**
   Los incidentes documentados en `DOC-08` (fuga de memoria ERP) y `DOC-09` (ransomware simulado) sirven de insumo para evaluar si las recomendaciones devueltas por Google Gemini y el motor local coinciden con los procedimientos oficiales estandarizados en los manuales `DOC-13`, `DOC-14` y `DOC-16`.
3. **Pruebas de Analítica y Búsqueda en el Dashboard:**
   Los lotes tabulares `DOC-03`, `DOC-07` y `DOC-22` se emplearon para alimentar la base de datos de pruebas y comprobar que las gráficas de distribución, métricas de resolución y recomendaciones estratégicas de contratación e infraestructura reflejen fielmente la realidad operativa del helpdesk.

---

## 5. POLÍTICA DE PROTECCIÓN Y ANONIMIZACIÓN DE DATOS

* **Cero Datos Reales:** Ningún número de cédula, teléfono, cuenta bancaria, correo corporativo real ni nombre de persona física viva ha sido empleado en los archivos.
* **Espacios de Red Reservados para Pruebas:** Las direcciones IP utilizadas pertenecen a los bloques estándar para documentación y pruebas de laboratorio (RFC 5737: `198.51.100.0/24` y RFC 1918: `10.0.0.0/8`, `192.168.0.0/16`).
* **Cumplimiento Ético:** Toda la información técnica corresponde a simulaciones metodológicas para fines exclusivos de aseguramiento de calidad de software (QA).

---

*Repositorio estructurado, verificado y documentado por Jaider Augusto Niño Badillo — Desarrollador y Líder de Calidad de Software (QA).*
