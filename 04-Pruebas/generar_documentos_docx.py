import os
import sys
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = OxmlElement('w:shd')
    shd.set(qn('w:val'), 'clear')
    shd.set(qn('w:color'), 'auto')
    shd.set(qn('w:fill'), fill_hex)
    tcPr.append(shd)

def create_docx(file_path, title, category, code_doc, explanation, full_content_sections):
    """
    Genera un archivo Microsoft Word .docx oficial y 100% válido utilizando python-docx.
    """
    doc = Document()
    
    for section in doc.sections:
        section.top_margin = Inches(1)
        section.bottom_margin = Inches(1)
        section.left_margin = Inches(1)
        section.right_margin = Inches(1)
        
    # Encabezado institucional
    header_p = doc.add_paragraph()
    header_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run_h1 = header_p.add_run("TECHCARE SOPORTE TI")
    run_h1.bold = True
    run_h1.font.size = Pt(13)
    run_h1.font.color.rgb = RGBColor(0, 86, 179) # #0056B3
    
    run_h2 = header_p.add_run("  |  Repositorio Documental de Pruebas (QA)")
    run_h2.font.size = Pt(10)
    run_h2.font.color.rgb = RGBColor(100, 116, 139)
    
    # Título del Documento
    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(10)
    title_p.paragraph_format.space_after = Pt(4)
    title_run = title_p.add_run(title)
    title_run.bold = True
    title_run.font.size = Pt(15)
    title_run.font.color.rgb = RGBColor(15, 23, 42)
    
    # Metadatos del Documento
    meta_p = doc.add_paragraph()
    meta_p.paragraph_format.space_after = Pt(10)
    
    r1 = meta_p.add_run("Código: ")
    r1.bold = True
    r1.font.size = Pt(10)
    r1.font.color.rgb = RGBColor(0, 86, 179)
    r_c = meta_p.add_run(f"{code_doc}   |   ")
    r_c.font.size = Pt(10)
    
    r2 = meta_p.add_run("Categoría: ")
    r2.bold = True
    r2.font.size = Pt(10)
    r2.font.color.rgb = RGBColor(0, 86, 179)
    r_cat = meta_p.add_run(f"{category}   |   ")
    r_cat.font.size = Pt(10)
    
    r3 = meta_p.add_run("Autor/QA: ")
    r3.bold = True
    r3.font.size = Pt(10)
    r3.font.color.rgb = RGBColor(0, 86, 179)
    r_aut = meta_p.add_run("Jaider Augusto Niño Badillo")
    r_aut.font.size = Pt(10)
    
    # Caja destacada de Explicación y Función en Pruebas
    callout_table = doc.add_table(rows=1, cols=1)
    callout_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    callout_table.autofit = False
    callout_table.columns[0].width = Inches(6.5)
    
    cell = callout_table.cell(0, 0)
    set_cell_background(cell, "F0F7FF")
    
    cp = cell.paragraphs[0]
    cp.paragraph_format.space_before = Pt(4)
    cp.paragraph_format.space_after = Pt(4)
    c_title = cp.add_run("📌 Breve Explicación y Función en las Pruebas:")
    c_title.bold = True
    c_title.font.size = Pt(10.5)
    c_title.font.color.rgb = RGBColor(0, 86, 179)
    
    cp_body = cell.add_paragraph()
    cp_body.paragraph_format.space_after = Pt(4)
    c_text = cp_body.add_run(explanation)
    c_text.font.size = Pt(10)
    c_text.font.color.rgb = RGBColor(30, 41, 59)
    
    # Espacio tras la tabla
    spacer = doc.add_paragraph()
    spacer.paragraph_format.space_before = Pt(6)
    spacer.paragraph_format.space_after = Pt(0)
    
    # Secciones del contenido técnico
    for sec_title, sec_paragraphs in full_content_sections:
        sp = doc.add_paragraph()
        sp.paragraph_format.space_before = Pt(10)
        sp.paragraph_format.space_after = Pt(3)
        s_run = sp.add_run(sec_title)
        s_run.bold = True
        s_run.font.size = Pt(11.5)
        s_run.font.color.rgb = RGBColor(15, 23, 42)
        
        for p in sec_paragraphs:
            pp = doc.add_paragraph()
            pp.paragraph_format.space_after = Pt(3)
            p_run = pp.add_run(p)
            p_run.font.size = Pt(10)
            p_run.font.color.rgb = RGBColor(51, 65, 85)
            
    # Pie de página institucional
    footer_p = doc.add_paragraph()
    footer_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    footer_p.paragraph_format.space_before = Pt(16)
    f_run = footer_p.add_run("TechCare Soporte TI — Repositorio Documental Oficial | Datos 100% Sintéticos y Anonimizados para QA")
    f_run.font.size = Pt(8.5)
    f_run.font.color.rgb = RGBColor(148, 163, 184)
    
    doc.save(file_path)


def main():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    repo_dir = os.path.join(base_dir, "repositorio_documental")
    cat1 = os.path.join(repo_dir, "01_reportes_incidentes_logs")
    cat2 = os.path.join(repo_dir, "02_guias_procedimientos_sop")
    cat3 = os.path.join(repo_dir, "03_politicas_acuerdos_sla")

    os.makedirs(cat1, exist_ok=True)
    os.makedirs(cat2, exist_ok=True)
    os.makedirs(cat3, exist_ok=True)

    docs_data = [
        # CATEGORÍA 1
        {
            "folder": cat1,
            "filename": "DOC-01-Reporte_Falla_Tunel_VPN.docx",
            "code": "DOC-01",
            "category": "Reportes de Incidentes y Logs",
            "title": "Informe de Incidencia Crítica: Caída de Túnel IPsec en Gateway VPN",
            "explanation": "Este documento registra la bitácora técnica y el análisis de la caída del túnel de comunicación cifrada IPsec/SSL entre la sede central y la sucursal remota. En las pruebas de TechCare, sirve para evaluar la capacidad del motor de NLP y de IA para extraer entidades críticas de red (IKE Phase 1, DPD, SA negotiation, Gateway IP) y generar de forma automática los comandos de mitigación (ipconfig /flushdns, netsh interface reset).",
            "sections": [
                ("1. Resumen Ejecutivo del Incidente", [
                    "Fecha de Ocurrencia: 01 de Septiembre de 2026 a las 08:14:22 COT.",
                    "Dispositivo Afectado: Gateway de Borde Perimetral VPN-GW-01 (FortiGate / Cisco ASA).",
                    "Impacto en la Operación: 45 colaboradores de la sede remota perdieron acceso temporal a los sistemas ERP corporativos y carpetas compartidas en el DataCenter central.",
                    "Severidad Asignada: Alta (P2)."
                ]),
                ("2. Registro Cronológico de Eventos (Trazas del Sistema)", [
                    "[08:14:22] [IKE] Se inicia negociación de Fase 1 con el par remoto 198.51.100.45 en el puerto UDP 500.",
                    "[08:14:23] [WARNING] Discrepancia detectada en el hash de la clave precompartida (PSK) desde el gateway remoto.",
                    "[08:14:24] [ERROR] Falló la renegociación de la Asociación de Seguridad (SA): NO_PROPOSAL_CHOSEN para el túnel ID #TUN-8821.",
                    "[08:15:01] [CRITICAL] El túnel principal se reporta DOWN. El tráfico activo se desvía automáticamente a la conexión WAN de respaldo 4G.",
                    "[08:15:10] [ALERT] Dead Peer Detection (DPD) perdió 3 paquetes de sondeo de latencia consecutivos."
                ]),
                ("3. Diagnóstico y Causa Raíz Identificada", [
                    "La causa raíz correspondió a una desincronización en la clave criptográfica tras la actualización nocturna de firmware en el router de la sucursal, lo cual impidió completar el handshake IKE de fase 1."
                ]),
                ("4. Plan de Acción y Solución Aplicada", [
                    "Paso 1: Se forzó el restablecimiento de la clave compartida de 256 bits en ambos extremos.",
                    "Paso 2: Se reinició el daemon de IPsec en el gateway perimetral.",
                    "Paso 3: Se validó la conectividad punto a punto mediante comando Test-NetConnection -Port 443 obteniendo respuesta exitosa en 18 ms."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-02-Incidente_Deadlock_MySQL.docx",
            "code": "DOC-02",
            "category": "Reportes de Incidentes y Logs",
            "title": "Informe Técnico: Detección y Resolución de Deadlock en Motor MySQL InnoDB",
            "explanation": "Documento que detalla un evento de bloqueo mutuo transaccional (deadlock) ocurrido en la base de datos de soporte. En las pruebas de software, este documento se utiliza para verificar cómo el modelo local heurístico y la IA Cloud analizan consultas SQL concurrentes, identifican la transacción causante del bloqueo y sugieren comandos de diagnóstico de alto nivel como SHOW FULL PROCESSLIST y KILL thread_id.",
            "sections": [
                ("1. Ficha del Incidente de Base de Datos", [
                    "Identificador: INC-DB-2026-0941.",
                    "Motor Afectado: MySQL 8.0.35 InnoDB Transaccional (Base de Datos: soporte_db).",
                    "Síntoma Reportado: Los usuarios experimentaron tiempos de espera superiores a 30 segundos al intentar radicar y actualizar solicitudes simultáneamente."
                ]),
                ("2. Transacciones Concurrentes en Conflicto", [
                    "Transacción 1 (Hilo 4210): UPDATE solicitudes SET estado = 'en_proceso', asignado_a = 'tecnico_demo' WHERE id = 1045 LOCK IN SHARE MODE; reteniendo cerrojos de registro.",
                    "Transacción 2 (Hilo 4218): UPDATE solicitudes SET solucion_ia = '{JSON_DATA}' WHERE id = 1045; esperando liberación del cerrojo exclusivo.",
                    "Resolución Automática del Motor: El motor InnoDB detectó el ciclo de bloqueo y ejecutó ROLLBACK automático sobre la transacción 2 por tener menor footprint de deshacer."
                ]),
                ("3. Medidas Correctivas Adoptadas", [
                    "1. Se optimizó el nivel de aislamiento de transacciones de REPEATABLE READ a READ COMMITTED para reducir bloqueos de rango.",
                    "2. Se creó un índice compuesto sobre las columnas (estado, fecha_creacion) para acelerar consultas de actualización.",
                    "3. Se configuró el parámetro innodb_lock_wait_timeout en 15 segundos en el archivo my.cnf."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-03-Lote_Tickets_Soporte_Helpdesk.docx",
            "code": "DOC-03",
            "category": "Reportes de Incidentes y Logs",
            "title": "Registro de Lote Masivo de Solicitudes de Soporte (Dataset de Prueba)",
            "explanation": "Contiene un conjunto consolidado de solicitudes de mesa de ayuda simuladas de diversas empresas ficticias y prioridades. Se utiliza en las pruebas para comprobar la ingesta por lotes, el cálculo de métricas en el Dashboard de TechCare (tasa de resolución, distribución por tipo de problema) y la categorización automática por NLP en dominios como RED, SOFTWARE y SEGURIDAD.",
            "sections": [
                ("1. Descripción del Lote de Datos", [
                    "Este conjunto de datos reúne 8 solicitudes representativas del entorno corporativo para someter a prueba la cola de despacho y los filtros del panel gerencial.",
                    "Todos los usuarios son cuentas de prueba sintéticas pertenecientes a empresas ficticias (Distribuidora Central S.A., Almacenes Andinos Ltda., Soluciones Digitales Demo)."
                ]),
                ("2. Detalle de los Casos de Prueba Incluidos", [
                    "Caso 1 [TCK-1001]: Tipo RED | Prioridad Alta | Asunto: Imposibilidad de conexión a la VPN corporativa desde casa.",
                    "Caso 2 [TCK-1002]: Tipo SOFTWARE | Prioridad Media | Asunto: Alerta en Word sobre expiración inminente de suscripción Office 365.",
                    "Caso 3 [TCK-1003]: Tipo SEGURIDAD | Prioridad Crítica | Asunto: Recepción de correo sospechoso solicitando contraseña corporativa.",
                    "Caso 4 [TCK-1004]: Tipo BASE DE DATOS | Prioridad Alta | Asunto: Timeout al generar el reporte mensual de transacciones.",
                    "Caso 5 [TCK-1005]: Tipo HARDWARE | Prioridad Baja | Asunto: Monitor secundario no enciende tras corte eléctrico.",
                    "Caso 6 [TCK-1006]: Tipo SOFTWARE | Prioridad Alta | Asunto: Falla de sincronización en OneDrive for Business con archivos bloqueados."
                ]),
                ("3. Utilidad en las Pruebas de Calidad", [
                    "Permite validar que las gráficas generadas con Chart.js en views/dashboard.php calculen correctamente las proporciones porcentuales y alimenten la analítica prescriptiva de negocio."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-04-Alerta_Seguridad_Phishing.docx",
            "code": "DOC-04",
            "category": "Reportes de Incidentes y Logs",
            "title": "Reporte Forense: Detección y Neutralización de Campaña de Phishing",
            "explanation": "Informe técnico que documenta el análisis de un correo fraudulento simulado que intentaba suplantar la identidad de Microsoft 365 para robar credenciales. En las pruebas de TechCare, sirve para evaluar la precisión del clasificador de seguridad ante términos como phishing, suplantación, contraseña, 2FA y la recomendación de medidas urgentes como revocación de tokens y aislamiento del endpoint.",
            "sections": [
                ("1. Información General del Incidente CSIRT-2026-0082", [
                    "Fecha de Detección: 02 de Septiembre de 2026 a las 09:35 UTC.",
                    "Vector de Entrada: Correo electrónico suplantando el Centro de Seguridad de Microsoft 365.",
                    "Remitente Simulado: notification-secure-auth@external-fake-portal.net.",
                    "Destinatario Afectado: buzon_contabilidad@empresa-demo.com."
                ]),
                ("2. Análisis de Cabeceras Técnicas del Correo", [
                    "SPF Check: FAIL (La dirección IP de origen 198.51.100.89 no está autorizada en el registro SPF del dominio remitente).",
                    "Firma DKIM: Ausente.",
                    "Validación DMARC: FAIL con política de cuarentena activada.",
                    "Enlace Fraudulento Detectado: http://portal-login-verify-auth.fake-security-check.ru/login."
                ]),
                ("3. Acciones de Contención Ejecutadas", [
                    "1. Bloqueo inmediato de la IP de origen y del dominio malicioso en el Firewall y filtro DNS corporativo.",
                    "2. Purgado automatizado de todos los buzones de correo para eliminar mensajes idénticos recibidos.",
                    "3. Forzado de cambio de contraseña y revocación de sesiones OAuth del usuario mediante script de PowerShell."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-05-Log_Expiracion_Licencias_M365.docx",
            "code": "DOC-05",
            "category": "Reportes de Incidentes y Logs",
            "title": "Registro de Auditoría: Caducidad de Tokens y Licencias Microsoft 365",
            "explanation": "Bitácora de eventos que documenta los errores de autenticación MSAL y caducidad del Primary Refresh Token (PRT) en Windows cuando una licencia de Office entra en modo de funcionalidad reducida. En las pruebas de TechCare, este archivo valida que el motor de IA local reconozca con exactitud los códigos de error de activación (0xC004C003) y sugiera el uso de herramientas como OSPP.VBS y Credential Manager.",
            "sections": [
                ("1. Contexto Operativo", [
                    "El presente registro captura los eventos generados por el cliente de identidad de Microsoft (MSAL) en una estación de trabajo con Windows 11 al intentar renovar el token de activación de Office Apps for Enterprise."
                ]),
                ("2. Trazas Relevantes del Visor de Eventos", [
                    "Evento 1: [Microsoft.Identity.Client] [Warning] AcquireTokenSilent failed: MsalUiRequiredException.",
                    "Evento 2: [Win32.Office.Activation] Error 0xC004C003: La clave de producto especificada superó el límite de activaciones autorizadas.",
                    "Evento 3: [Microsoft.Entra.PRT] El Primary Refresh Token se encuentra vencido. Se requiere interacción del usuario.",
                    "Evento 4: [Office16.Licensing] Aplicaciones de Office pasan a modo de solo lectura (Viewer Mode)."
                ]),
                ("3. Solución Técnica Estandarizada", [
                    "Eliminación de credenciales genéricas obsoletas en el Administrador de Credenciales de Windows.",
                    "Ejecución del script cscript OSPP.VBS /unpkey para remover la clave temporal.",
                    "Reinicio de sesión con la cuenta de Microsoft Entra ID verificando asignación de licencia Business Standard activa."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-06-Reporte_Corte_Fibra_Optica.docx",
            "code": "DOC-06",
            "category": "Reportes de Incidentes y Logs",
            "title": "Reporte de Conmutación por Falla: Corte de Enlace Principal de Fibra Óptica",
            "explanation": "Documenta la pérdida del enlace de datos principal suministrado por el proveedor de telecomunicaciones y la conmutación exitosa al radioenlace de contingencia. Se utiliza para probar el módulo de recomendaciones de infraestructura de TechCare (inversión en enlaces redundantes y monitoreo proactivo NOC).",
            "sections": [
                ("1. Resumen de la Falla del Proveedor ISP", [
                    "Dispositivo: Router-Core-Borde-01 (Interfaz GigabitEthernet0/0/1).",
                    "Duración del Corte: 70.5 minutos (de 03:15 a 04:25 UTC).",
                    "Causa Externa: Ruptura física de fibra óptica aérea por trabajos viales de terceros."
                ]),
                ("2. Comportamiento del Mecanismo de Alta Disponibilidad", [
                    "El protocolo de enrutamiento dinámico conmutó el tráfico al radioenlace secundario de microondas en 4.2 segundos.",
                    "Durante la contingencia se registró una pérdida de paquetes promedio del 2.1%, garantizando la operación continua de la mesa de ayuda TechCare."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-07-Auditoria_Accesos_Fallidos_AD.docx",
            "code": "DOC-07",
            "category": "Reportes de Incidentes y Logs",
            "title": "Informe de Auditoría: Intentos Fallidos de Inicio de Sesión y Bloqueos de Cuenta",
            "explanation": "Registro de eventos de seguridad de Windows (Event ID 4625 y 4740) asociados a intentos repetidos de autenticación fallida. En las pruebas de software, valida el flujo de atención para incidentes de tipo SEGURIDAD y las alertas de políticas de contraseñas.",
            "sections": [
                ("1. Objetivo del Monitoreo", [
                    "Supervisar los eventos de autenticación en los controladores de dominio DC01 y DC02 para detectar ataques de fuerza bruta (Password Spraying) o bloqueos legítimos por olvido de credenciales."
                ]),
                ("2. Registro de Eventos Analizados", [
                    "Usuario j.perez.demo: 3 intentos fallidos consecutivos en menos de 30 segundos (Evento 4625).",
                    "Evento 4740 registrado inmediatamente: Cuenta bloqueada automáticamente por directiva de seguridad corporativa.",
                    "IP de origen identificada: 10.0.1.102 (estación interna del área comercial)."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-08-Volcado_Error_Memoria_ERP.docx",
            "code": "DOC-08",
            "category": "Reportes de Incidentes y Logs",
            "title": "Informe de Fuga de Memoria y Crash en Sistema ERP Empresarial",
            "explanation": "Volcado del error OutOfMemoryException ocurrido durante la exportación masiva de balances contables anuales. Se utiliza para probar las sugerencias de la IA orientadas a depuración de software, parametrización de buffers y migración de procesos a arquitecturas de 64 bits.",
            "sections": [
                ("1. Datos Técnicos del Error", [
                    "Proceso: ERP_Accounting_Engine.exe (PID: 8140, Arquitectura 32 bits).",
                    "Excepción Lanzada: System.OutOfMemoryException al intentar alocar un búfer contiguo de 3.8 GB de memoria RAM.",
                    "Ruta del Código: ERP.Module.Billing.ExportManager.BuildAnnualConsolidatedReport()."
                ]),
                ("2. Resolución Definitiva", [
                    "Se finalizó el proceso colgado con taskkill /f /im ERP_Accounting_Engine.exe /t.",
                    "Se dividió la consulta SQL en bloques paginados de 5,000 registros para evitar sobrecarga en la memoria del cliente."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-09-Simulacro_Contencion_Ransomware.docx",
            "code": "DOC-09",
            "category": "Reportes de Incidentes y Logs",
            "title": "Informe Forense: Simulacro Controlado de Contención de Malware",
            "explanation": "Reporte de ejercicio de ciberseguridad donde se evaluaron los protocolos de respuesta ante malware tipo ransomware. Se utiliza para contrastar las recomendaciones emitidas por la IA contra las mejores prácticas de aislamiento físico y recuperación de copias sombra (VSS).",
            "sections": [
                ("1. Objetivos del Simulacro", [
                    "Medir el tiempo de respuesta del equipo de soporte TI para contener una amenaza activa en un endpoint de pruebas sin conexión a la red de producción."
                ]),
                ("2. Resultados Obtenidos", [
                    "Tiempo de aislamiento de red: 1.8 minutos mediante desconexión de interfaz.",
                    "Tiempo de identificación del proceso malicioso: 3 minutos.",
                    "Tiempo total de recuperación del equipo desde backup limpio: 14 minutos."
                ])
            ]
        },
        {
            "folder": cat1,
            "filename": "DOC-10-Telemetria_Rendimiento_Servidor.docx",
            "code": "DOC-10",
            "category": "Reportes de Incidentes y Logs",
            "title": "Reporte de Telemetría: Análisis de Capacidad y Rendimiento en Servidor Cloud",
            "explanation": "Informe con métricas de consumo de CPU, RAM e I/O de disco del servidor de aplicaciones. En las pruebas de TechCare, alimenta el motor de Analítica Estratégica para justificar decisiones de inversión en escalamiento de infraestructura.",
            "sections": [
                ("1. Parámetros de Monitoreo", [
                    "Servidor SRV-PROD-APP-01 (8 vCPUs, 32 GB RAM, SSD NVMe 500 GB).",
                    "Pico de CPU registrado: 94.2% durante la franja horaria de cierre contable mensual (14:30 COT)."
                ]),
                ("2. Recomendación Generada por el Sistema", [
                    "Habilitar auto-escalado horizontal de instancias y balanceo de carga para distribuir el tráfico en picos de demanda."
                ])
            ]
        },

        # CATEGORÍA 2
        {
            "folder": cat2,
            "filename": "DOC-11-SOP_Desbloqueo_Cuentas_AD.docx",
            "code": "DOC-11",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "SOP-TI-001: Procedimiento para Desbloqueo de Cuentas en Active Directory",
            "explanation": "Manual operativo que estandariza los pasos obligatorios para validar la identidad de un colaborador antes de desbloquear su cuenta o restablecer su clave. En las pruebas de software, se utiliza para comprobar que el asistente virtual de TechCare guíe correctamente a los técnicos N1 en el uso de los cmdlets de PowerShell (Unlock-ADUser, Set-ADAccountPassword).",
            "sections": [
                ("1. Propósito y Alcance", [
                    "Garantizar que todo restablecimiento de credenciales cumpla con los controles de validación de identidad para prevenir ataques de ingeniería social."
                ]),
                ("2. Comandos PowerShell Oficiales", [
                    "1. Verificar estado: Get-ADUser -Identity 'usuario.demo' -Properties LockedOut",
                    "2. Desbloquear cuenta: Unlock-ADUser -Identity 'usuario.demo'",
                    "3. Asignar clave temporal forzando cambio al iniciar sesión: Set-ADAccountPassword -Identity 'usuario.demo' -NewPassword $securePass -Force"
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-12-Manual_Configuracion_VPN.docx",
            "code": "DOC-12",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Guía de Autoservicio: Instalación y Configuración del Cliente VPN Corporativo",
            "explanation": "Guía orientada a usuarios finales y técnicos de mesa de ayuda para configurar conexiones remotas seguras mediante FortiClient y Cisco AnyConnect. Se utiliza en las pruebas para validar la generación de respuestas explicativas de autoservicio.",
            "sections": [
                ("1. Parámetros de Conexión Oficiales", [
                    "Nombre de la Conexión: VPN Corporativa TechCare.",
                    "Gateway Remoto: vpn.empresa-demo.com | Puerto: 443 (SSL-VPN).",
                    "Método de Autenticación: Credenciales corporativas + Notificación Push en la app de autenticación móvil (MFA)."
                ]),
                ("2. Comprobación Rápida de Conectividad", [
                    "En caso de falla de resolución DNS, ejecutar en CMD: ipconfig /flushdns",
                    "Probar conectividad de red con el gateway: Test-NetConnection -ComputerName vpn.empresa-demo.com -Port 443"
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-13-Guia_Depuracion_Bloqueos_SQL.docx",
            "code": "DOC-13",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "SOP-DB-004: Guía de Depuración de Consultas Lentas y Bloqueos en MySQL",
            "explanation": "Instrucciones de soporte N2/N3 para diagnosticar cuellos de botella en la base de datos relacional. Valida que el motor de IA proporcione comandos SQL precisos como SHOW PROCESSLIST y revisión de tablas sys.innodb_lock_waits.",
            "sections": [
                ("1. Identificación de Hilos Bloqueantes", [
                    "Ejecutar SHOW FULL PROCESSLIST para listar todas las conexiones activas.",
                    "Filtrar consultas con estado 'Locked' o tiempo de ejecución superior a 30 segundos."
                ]),
                ("2. Terminación Segura de la Conexión Problemática", [
                    "Una vez verificado el hilo responsable, finalizarlo con el comando: KILL <thread_id>;",
                    "Verificar la normalización del pool de conexiones en views/dashboard.php."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-14-Procedimiento_Renovacion_M365.docx",
            "code": "DOC-14",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Procedimiento Técnico: Depuración y Activación de Licencias Office 365",
            "explanation": "Manual paso a paso para resolver advertencias de producto sin licencia en Word y Excel. Valida la recomendación del script OSPP.VBS en la Variante 1 de diagnóstico del motor local.",
            "sections": [
                ("1. Limpieza de Claves Residuales", [
                    "Navegar en consola CMD a: C:\\Program Files\\Microsoft Office\\Office16",
                    "Consultar licencias instaladas: cscript ospp.vbs /dstatus",
                    "Desinstalar claves obsoletas: cscript ospp.vbs /unpkey:<ultimos_5_caracteres>"
                ]),
                ("2. Reactivación y Validación", [
                    "Abrir Word, autenticarse con el correo corporativo y comprobar que el estado figure como 'Producto Activado'."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-15-Catalogo_Errores_Frecuentes_TI.docx",
            "code": "DOC-15",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Catálogo Estructurado de Errores Frecuentes y Soluciones de Mesa de Ayuda",
            "explanation": "Diccionario de incidentes típicos y códigos de error (VPN timeout, token dañado, max_connections). Se utiliza para probar la precisión de búsqueda semántica y respuestas guiadas en la base de conocimientos.",
            "sections": [
                ("1. Código ERR_NET_001 (Falla de Handshake VPN)", [
                    "Causa: Discrepancia en parámetros de encriptación o bloqueo de puerto 443 en red doméstica.",
                    "Solución: Modificar MTU a 1400 y reiniciar el adaptador virtual de red."
                ]),
                ("2. Código ERR_AUTH_002 (Token de Sesión JWT Corrupto)", [
                    "Causa: Desfase de reloj local mayor a 5 minutos respecto al servidor NTP.",
                    "Solución: Sincronizar hora del sistema con w32tm /resync y limpiar cookies del navegador."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-16-SOP_Aislamiento_Equipo_Infectado.docx",
            "code": "DOC-16",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "SOP-SEC-008: Protocolo de Aislamiento Inmediato de Estación de Trabajo Comprometida",
            "explanation": "Procedimiento de contención urgente ante sospecha de intrusión o malware. Sirve para validar que la IA recomiende acciones preventivas como desconexión física de red y preservación de memoria volátil.",
            "sections": [
                ("1. Acciones Inmediatas (Primeros 120 Segundos)", [
                    "1. Desconectar el cable de red RJ-45 y apagar interruptor WiFi.",
                    "2. No apagar el equipo para permitir la adquisición forense de la memoria RAM.",
                    "3. Aislar la cuenta de Active Directory en el controlador de dominio."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-17-Guia_Mantenimiento_Preventivo_PC.docx",
            "code": "DOC-17",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Checklist de Mantenimiento Preventivo Trimestral de Equipos de Cómputo",
            "explanation": "Lista de chequeo para soporte en sitio que incluye purgado de archivos temporales (%temp%), ejecución de sfc /scannow y verificación de actualizaciones de seguridad.",
            "sections": [
                ("1. Tareas de Optimización de Software", [
                    "Eliminar temporales del sistema y purgar la caché de actualizaciones de Windows.",
                    "Comprobar integridad del sistema operativo con DISM /Online /Cleanup-Image /RestoreHealth."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-18-Matriz_Escalamiento_Incidentes.docx",
            "code": "DOC-18",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Matriz Operativa de Escalamiento Técnico: Niveles N1, N2 y N3",
            "explanation": "Define los tiempos y criterios para transferir incidentes entre la mesa de ayuda (N1), soporte especializado (N2) e ingeniería de infraestructura (N3).",
            "sections": [
                ("1. Criterios de Asignación y Escalamiento", [
                    "Nivel 1: Tiempo máximo de análisis inicial de 15 minutos (atención de primer contacto).",
                    "Nivel 2: Incidentes de configuración de software, licencias complejas o conectividad local.",
                    "Nivel 3: Incidentes que comprometen servidores de bases de datos, firewalls perimetrales o enlaces de fibra."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-19-SOP_Backup_Restauracion_MySQL.docx",
            "code": "DOC-19",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "SOP-DB-008: Rutina de Respaldo Lógico y Restauración de Base de Datos",
            "explanation": "Procedimiento para ejecutar respaldos consistentes con mysqldump y pruebas periódicas de restauración en entornos de laboratorio.",
            "sections": [
                ("1. Comando de Respaldo Transaccional Seguro", [
                    "mysqldump -u root -p --single-transaction --routines --triggers soporte_db > soporte_db_backup.sql"
                ]),
                ("2. Validación de Integridad", [
                    "Restaurar la copia en una base de datos de pruebas para verificar que no existan tablas truncadas ni llaves foráneas rotas."
                ])
            ]
        },
        {
            "folder": cat2,
            "filename": "DOC-20-Arbol_Decision_Heuristica_IA.docx",
            "code": "DOC-20",
            "category": "Procedimientos Operativos Estándar (SOP)",
            "title": "Especificación Técnica: Árbol de Decisión y Reglas Heurísticas del Motor Local",
            "explanation": "Documenta la lógica de evaluación por expresiones regulares del LocalExpertService.php para asignar prioridades y categorías técnicas en modo 100% offline.",
            "sections": [
                ("1. Taxonomía de Clasificación Semántica", [
                    "Patrón (vpn|proxy|gateway) => Asignar dominio RED y prioridad Alta.",
                    "Patrón (sql|deadlock|timeout) => Asignar dominio BASE_DE_DATOS y prioridad Alta.",
                    "Patrón (phishing|virus|ransomware) => Asignar dominio SEGURIDAD y prioridad Crítica."
                ])
            ]
        },

        # CATEGORÍA 3
        {
            "folder": cat3,
            "filename": "DOC-21-Politica_Contrasenas_MFA.docx",
            "code": "DOC-21",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "POL-SEC-01: Política Corporativa de Contraseñas y Autenticación Multifactor",
            "explanation": "Normativa de seguridad que define los requisitos de robustez de contraseñas (12+ caracteres, mayúsculas, símbolos) y la obligatoriedad de MFA. Valida los requisitos no funcionales de seguridad y el uso de BCRYPT en TechCare.",
            "sections": [
                ("1. Directrices Obligatorias de Autenticación", [
                    "Longitud mínima de 12 caracteres con combinación de números, letras y símbolos especiales.",
                    "Rotación obligatoria cada 90 días calendario con restricción de reutilización de las últimas 10 claves.",
                    "Autenticación Multifactor (MFA) obligatoria para acceso a correo corporativo y sistemas internos."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-22-Matriz_Tiempos_SLA_Incidentes.docx",
            "code": "DOC-22",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "Acuerdo de Nivel de Servicio (SLA): Tiempos de Respuesta y Resolución",
            "explanation": "Define los compromisos formales de tiempo para cada nivel de prioridad (Crítica: 15 min respuesta / 2 horas solución). Se utiliza para comprobar que el Dashboard alerte oportunamente sobre tickets próximos a vencer.",
            "sections": [
                ("1. Tiempos Máximos Comprometidos", [
                    "Prioridad Crítica: Primera respuesta en 15 minutos | Resolución en 2 horas (Disponibilidad 24/7).",
                    "Prioridad Alta: Primera respuesta en 30 minutos | Resolución en 6 horas.",
                    "Prioridad Media: Primera respuesta en 2 horas | Resolución en 24 horas laborables.",
                    "Prioridad Baja: Primera respuesta en 4 horas | Resolución en 48 horas laborables."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-23-Politica_Uso_Aceptable_Activos.docx",
            "code": "DOC-23",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "POL-TI-003: Política de Uso Aceptable de Activos Tecnológicos y Redes",
            "explanation": "Establece las directrices para el uso adecuado de computadores, redes y correo empresarial, prohibiendo software no licenciado o minería de criptomonedas.",
            "sections": [
                ("1. Obligaciones de los Colaboradores", [
                    "Los equipos de cómputo son propiedad exclusiva de la empresa para actividades netamente laborales.",
                    "Queda prohibida la descarga e instalación de software sin autorización previa del área de TI."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-24-Normativa_Control_Acceso_RBAC.docx",
            "code": "DOC-24",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "POL-SEC-02: Normativa de Control de Acceso Basado en Roles (RBAC)",
            "explanation": "Define formalmente los perfiles de usuario ('cliente', 'tecnico', 'admin') y las rutas autorizadas en la plataforma TechCare. Valida los middlewares de autenticación requireAdmin() y requireAuth().",
            "sections": [
                ("1. Definición de Perfiles y Privilegios", [
                    "Rol Cliente: Radicación de solicitudes y consulta de historial de tickets propios en views/formulario.php.",
                    "Rol Técnico: Visualización de incidentes globales, cambio de estados y ejecución del copiloto IA.",
                    "Rol Administrador: Acceso total al Dashboard gerencial, auditoría y módulo de analítica estratégica."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-25-Plan_Continuidad_Negocio_BCP.docx",
            "code": "DOC-25",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "Plan de Continuidad del Negocio (BCP) y Recuperación ante Desastres de TI",
            "explanation": "Estrategia para garantizar la operación continua de la mesa de ayuda ante fallos mayores, estableciendo un RTO de 2 horas y RPO de 15 minutos, y destacando el papel del motor local offline.",
            "sections": [
                ("1. Objetivos de Recuperación", [
                    "RTO (Recovery Time Objective): Restablecimiento de servicios de soporte en menos de 120 minutos.",
                    "RPO (Recovery Point Objective): Pérdida máxima de datos de 15 minutos mediante respaldos continuos.",
                    "Contingencia Offline: Funcionamiento del motor heurístico local sin requerir conexión a la nube de Google."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-26-Auditoria_Licencias_Software.docx",
            "code": "DOC-26",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "Informe de Auditoría y Cumplimiento de Licenciamiento Corporativo",
            "explanation": "Matriz de conciliación entre licencias de software adquiridas y asignadas (M365, FortiClient, Windows Server) para evitar infracciones de propiedad intelectual.",
            "sections": [
                ("1. Balance de Cumplimiento", [
                    "Microsoft 365 Business Standard: 120 adquiridas / 118 en uso activo (2 disponibles).",
                    "FortiClient ZTNA / VPN: 150 adquiridas / 135 en uso activo (15 disponibles).",
                    "Cumplimiento normativo global: 100% de software legal debidamente amparado por contratos."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-27-Politica_Gestion_Parches_Seguridad.docx",
            "code": "DOC-27",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "POL-SEC-04: Política de Gestión de Parches y Actualizaciones Críticas",
            "explanation": "Regula el calendario de instalación de parches en servidores y terminales, estableciendo un plazo de 24 a 48 horas para vulnerabilidades críticas de día cero.",
            "sections": [
                ("1. Ventanas de Mantenimiento", [
                    "Parches Críticos (Zero-Day): Aplicación urgente en menos de 48 horas bajo aprobación de emergencia.",
                    "Parches Regulares: Despliegue el tercer jueves de cada mes en horario nocturno no hábil."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-28-Convenio_Confidencialidad_Datos.docx",
            "code": "DOC-28",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "Convenio de Confidencialidad y Tratamiento Ético de la Información de TI",
            "explanation": "Acuerdo legal y ético suscrito por el personal con acceso privilegiado a bases de datos y contraseñas, asegurando la no divulgación de información corporativa.",
            "sections": [
                ("1. Compromisos del Personal de Soporte", [
                    "Mantener estricta confidencialidad sobre datos de tickets, claves y registros de auditoría de los usuarios.",
                    "No realizar copias no autorizadas ni alterar los registros de la base de datos de soporte."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-29-Esquema_Clasificacion_Informacion.docx",
            "code": "DOC-29",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "POL-SEC-05: Esquema de Clasificación y Manipulación de Activos de Información",
            "explanation": "Clasifica los datos corporativos en cuatro niveles (Pública, Interna, Confidencial, Restringida), justificando el cifrado de credenciales en BCRYPT y protección de API Keys.",
            "sections": [
                ("1. Taxonomía de Niveles", [
                    "Nivel Pública: Portal web y formularios de contacto.",
                    "Nivel Interna: Manuales de procedimientos y guías de soporte.",
                    "Nivel Confidencial: Métricas gerenciales del Dashboard y registros de incidentes.",
                    "Nivel Restringida: Contraseñas encriptadas, llaves criptográficas y tokens de APIs de IA."
                ])
            ]
        },
        {
            "folder": cat3,
            "filename": "DOC-30-Directrices_Cumplimiento_ISO27001.docx",
            "code": "DOC-30",
            "category": "Políticas de Seguridad y Acuerdos SLA",
            "title": "Guía de Cumplimiento ISO/IEC 27001:2022 para Mesas de Ayuda de TI",
            "explanation": "Mapeo de los controles de seguridad de la información de la norma ISO 27001 (Control A.5.15 Control de Accesos, A.8.7 Protección contra Malware, A.8.20 Seguridad en Redes) integrados en el sistema TechCare.",
            "sections": [
                ("1. Controles ISO 27001 Aplicados en TechCare", [
                    "Control A.5.15: Identificación unívoca y trazabilidad completa de acciones técnicas.",
                    "Control A.8.7: Diagnóstico asistido y recomendaciones preventivas para neutralizar vectores de malware.",
                    "Control A.8.20: Aislamiento de redes y monitoreo de túneles VPN corporativos."
                ])
            ]
        }
    ]

    total = 0
    for d in docs_data:
        file_path = os.path.join(d["folder"], d["filename"])
        create_docx(file_path, d["title"], d["category"], d["code"], d["explanation"], d["sections"])
        total += 1
        print(f"[{total}/30] Creado archivo Word 100% nativo: {d['filename']}")

    print(f"\n¡Éxito! Se generaron los {total} documentos en formato Microsoft Word (.docx) nativos.")

if __name__ == "__main__":
    main()
