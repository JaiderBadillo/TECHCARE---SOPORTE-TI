# 🎨 Mockups y Diseño de Interfaz de Usuario (UI/UX)
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA  
**Enfoque de Diseño:** Glassmorphism Moderno, Clean Dark/Light Mode & Responsive Design  
**Framework Frontend:** Bootstrap 5.3 + Bootstrap Icons + Chart.js 4.4 + Google Fonts (Inter)  
**Versión:** 2.2.0  

---

## 1. Guía de Estilos y Sistema de Diseño (Design System)

### 1.1. Paleta de Colores Corporativa

| Token de Color | Código Hex | Muestra Visual | Uso Principal |
| :--- | :---: | :---: | :--- |
| **`Primary Blue`** | `#4361ee` | 🟦 | Botones primarios, enlaces activos, badges de software. |
| **`Dark Indigo (Hub)`** | `#0f172a` ➔ `#1e1b4b` | ⬛ | Fondo del Hub de Decisiones IA, Navbar y Terminal de comandos. |
| **`Background Light`** | `#f8fafc` | ⬜ | Fondo general del portal y dashboard en modo diurno. |
| **`Card Glass Border`** | `rgba(255,255,255,0.12)` | ◽ | Bordes con efecto traslúcido y desenfoque (*backdrop-filter*). |
| **`Success Green`** | `#10b981` | 🟩 | Estado *Resuelto*, tasa de resolución, badges de impacto positivo. |
| **`Warning Orange`** | `#f59e0b` | 🟧 | Estado *En Proceso*, prioridad alta, avisos de infraestructura. |
| **`Danger Red`** | `#ef4444` | 🟥 | Estado *Pendiente*, prioridad crítica, alertas de ciberseguridad. |
| **`Info Cyan`** | `#06b6d4` | 🔷 | Badges de Redes/VPN, comandos de consola, dictamen de IA. |

### 1.2. Tipografía Oficial
* **Familia Tipográfica:** [Inter (Google Fonts)](https://fonts.google.com/specimen/Inter)
* **Pesos Utilizados:** `300 (Light)`, `400 (Regular)`, `500 (Medium)`, `600 (SemiBold)`, `700 (Bold)`, `800 (ExtraBold)`.
* **Fuente Monospace:** `Consolas`, `'Courier New'`, `monospace` (para comandos de consola y scripts).

---

## 2. Catálogo de Pantallas y Wireframes Estructurados

```text
├── 1. Portal de Radicación de Incidentes (views/formulario.php)
├── 2. Portal de Historial "Mis Solicitudes" (views/formulario.php - Tab 2)
├── 3. Módulo de Autenticación / Login (views/login.php)
├── 4. Módulo de Registro de Usuarios Clientes (views/registro.php)
├── 5. Dashboard Gerencial & Hub de Decisiones IA (views/dashboard.php)
└── 6. Modal Copiloto de Diagnóstico Técnico con IA (views/partials/modal_ia.php)
```

---

### Pantalla 1: Portal de Radicación de Incidentes Técnicos (`views/formulario.php`)
Permite a los usuarios y empleados de las empresas cliente reportar fallas técnicas con autocompletado inteligente de sus datos corporativos.

```text
+-----------------------------------------------------------------------------------------------+
| [🛡️ TechCare Soporte TI]             [📝 Radicar Solicitud]  [👤 Carlos Mendoza (Logística)] [Salir] |
+-----------------------------------------------------------------------------------------------+
|                                                                                               |
|            [ (•) 1. Radicar Nueva Solicitud ]    [ ( ) 2. Mis Solicitudes Registradas (3) ]   |
|                                                                                               |
|   +---------------------------------------------------------------------------------------+   |
|   |                       MESA DE AYUDA TI — REPORTAR INCIDENTE TÉCNICO                   |   |
|   |       🏢 Empresa: Logística Global S.A.S   |   💼 Cargo: Líder de Contabilidad       |   |
|   +---------------------------------------------------------------------------------------+   |
|   |                                                                                       |   |
|   |   Nombre Completo (*):                      Correo Corporativo (*):                   |   |
|   |   [ 👤 Carlos Mendoza                     ] [ ✉️ cliente@empresa.com               ] |   |
|   |                                                                                       |   |
|   |   Empresa / Organización (*):               Categoría del Problema (*):               |   |
|   |   [ 🏢 Logística Global S.A.S             ] [ 💻 Software & Aplicaciones (Office) v ] |   |
|   |                                                                                       |   |
|   |   Nivel de Criticidad (*):                                                            |   |
|   |   [ 🟡 Media (Problema individual manejable)                                       v ] |   |
|   |                                                                                       |   |
|   |   Asunto del Problema (*):                                                            |   |
|   |   [ Ej. Aviso de expiración de licencia Microsoft 365 en módulo de facturación       ] |   |
|   |                                                                                       |   |
|   |   Descripción Detallada del Incidente (*):                                            |   |
|   |   +-------------------------------------------------------------------------------+   |
|   |   | Al abrir Excel y Word aparece un banner superior indicando que la suscripción |   |
|   |   | está en modo de funcionalidad reducida. No permite guardar archivos contables.|   |
|   |   +-------------------------------------------------------------------------------+   |
|   |                                                                                       |   |
|   |                  [ 🚀 ENVIAR SOLICITUD DE SOPORTE TÉCNICO ]                           |   |
|   +---------------------------------------------------------------------------------------+   |
|                                                                                               |
+-----------------------------------------------------------------------------------------------+
```

---

### Pantalla 2: Historial de "Mis Solicitudes" (`views/formulario.php` - Tab 2)
Permite al usuario cliente hacer seguimiento del estado de atención y ver el diagnóstico emitido por el equipo de TI y los motores de IA.

```text
+-----------------------------------------------------------------------------------------------+
|   +---------------------------------------------------------------------------------------+   |
|   | 📁 Historial de Solicitudes Registradas                              [ 🔄 Actualizar ] |   |
|   +---------------------------------------------------------------------------------------+   |
|   |                                                                                       |   |
|   |   +-------------------------------------------------------------------------------+   |
|   |   | [Ticket #14] [SOFTWARE]  📅 31/08/2026 15:30                   [🟡 EN REVISIÓN] |   |
|   |   | Asunto: Aviso de expiración de licencia Microsoft 365 en facturación          |   |
|   |   | Detalle: Al abrir Excel y Word aparece aviso de funcionalidad reducida...     |   |
|   |   |                                                                               |   |
|   |   | 🤖 Diagnóstico Preliminar de IA:                                              |   |
|   |   | Token OAuth de Entra ID desincronizado. Se requiere purgar caché de OSPP.VBS. |   |
|   |   +-------------------------------------------------------------------------------+   |
|   |                                                                                       |   |
|   |   +-------------------------------------------------------------------------------+   |
|   |   | [Ticket #09] [REDES / VPN]  📅 28/08/2026 09:15                   [🟢 RESUELTO] |   |
|   |   | Asunto: Corte en túnel VPN de sucursal norte                                  |   |
|   |   | Detalle: El enlace FortiClient no responde handshake en puerto 443...         |   |
|   |   +-------------------------------------------------------------------------------+   |
|   |                                                                                       |   |
+-----------------------------------------------------------------------------------------------+
```

---

### Pantalla 3: Módulo de Inicio de Sesión (`views/login.php`)
Control de acceso con tarjeta Glassmorphism flotante, alternador de visibilidad de contraseña y botones de acceso demo instantáneo.

```text
+-----------------------------------------------------------------------------------------------+
|                                                                                               |
|                               +-------------------------------+                               |
|                               |       [ 🛡️ TechCare TI ]       |                               |
|                               |     Acceso Administrativo     |                               |
|                               |   Mesa de Ayuda & Diagnóstico |                               |
|                               +-------------------------------+                               |
|                               |                               |                               |
|                               | Correo Electrónico:           |                               |
|                               | [ ✉️ cliente@empresa.com    ] |                               |
|                               |                               |                               |
|                               | Contraseña:                   |                               |
|                               | [ 🔑 ••••••••••••         👁️ ] |                               |
|                               |                               |                               |
|                               | 🪄 Accesos Demo Rápidos:      |                               |
|                               | [ 👤 Usuario Cliente ] [ 🛡️ Admin TI ]                        |
|                               |                               |                               |
|                               | [ 🔓 INGRESAR AL SISTEMA ]    |                               |
|                               |                               |                               |
|                               | ───────────────────────────── |                               |
|                               | ¿No tienes cuenta?            |                               |
|                               | 👉 [Regístrate gratis aquí]   |                               |
|                               +-------------------------------+                               |
|                                                                                               |
+-----------------------------------------------------------------------------------------------+
```

---

### Pantalla 4: Módulo de Registro de Usuarios Clientes (`views/registro.php`)
Formulario en 2 columnas para la incorporación (*onboarding*) de colaboradores y clientes empresariales.

```text
+-----------------------------------------------------------------------------------------------+
|                                                                                               |
|                   +-------------------------------------------------------+                   |
|                   |         [ 👤+ ] Crear Cuenta de Usuario               |                   |
|                   |   Regístrese para reportar y consultar incidentes TI  |                   |
|                   +-------------------------------------------------------+                   |
|                   |                                                       |                   |
|                   | Nombre Completo (*):        Correo Corporativo (*):   |                   |
|                   | [ Carlos Mendoza          ] [ cliente@empresa.com   ] |                   |
|                   |                                                       |                   |
|                   | Empresa / Organización (*): Cargo en la Empresa (*):  |                   |
|                   | [ Logística Global S.A.S  ] [ Líder de Contabilidad ] |                   |
|                   |                                                       |                   |
|                   | Contraseña (*):             Confirmar Contraseña (*): |                   |
|                   | [ ••••••••••••            ] [ ••••••••••••          ] |                   |
|                   |                                                       |                   |
|                   |       [ 🚀 REGISTRARME Y ACCEDER AL PORTAL ]          |                   |
|                   |                                                       |                   |
|                   | ¿Ya tienes una cuenta? 👉 [Inicia Sesión aquí]        |                   |
|                   +-------------------------------------------------------+                   |
|                                                                                               |
+-----------------------------------------------------------------------------------------------+
```

---

### Pantalla 5: Panel de Control Gerencial & Hub Estratégico IA (`views/dashboard.php`)
Panel central para el equipo de soporte e ingeniería TI con métricas en tiempo real, Hub Prescriptivo y tabla interactiva.

```text
+-----------------------------------------------------------------------------------------------+
| [🛡️ TechCare Soporte TI]  [Radicar Ticket]  [📊 Dashboard & IA]  [👤 Admin: Jaider B.] [Salir] |
+-----------------------------------------------------------------------------------------------+
|                                                                                               |
|  Panel de Control Gerencial & Decisiones IA    [✨ Análisis con Gemini] [⚡ Análisis Local]   |
|  Monitoreo en tiempo real de incidentes        (Modo Cloud Bajo Demanda) (0 Tokens Instant)   |
|                                                                                               |
|  +--------------+ +--------------+ +--------------+ +--------------+ +-------------+ +------+ |
|  | Total Tickets| |   Este Mes   | |  Pendientes  | |  En Proceso  | |  Resolución | | Top  | |
|  |     14       | |      14      | |   3 (Rojo)   | |  4 (Amarillo)| | 50.0% (Verde| | SOFT | |
|  +--------------+ +--------------+ +--------------+ +--------------+ +-------------+ +------+ |
|                                                                                               |
|  +--[ HUB DE TOMA DE DECISIONES ESTRATÉGICAS CON IA (CTO PRESCRIPTIVO) ]--------------------+  |
|  | 🤖 Dictamen Ejecutivo: "Demanda concentrada en Software (42.8%). Se recomienda reforzar  |  |
|  | capacitación en Entra ID y contratar 1 especialista L2 con certificación MD-102..."     |  |
|  |                                                                                          |  |
|  | [ (•) 1. Contratación ] [ ( ) 2. Infraestructura ] [ ( ) 3. Capacitación ] [ ( ) 4. Auto]|  |
|  |                                                                                          |  |
|  | +------------------------------------+  +------------------------------------+           |  |
|  | | 💼 Ing. Soporte Aplicaciones & ERP|  | 🔒 Especialista en Ciberseguridad |           |  |
|  | | Seniority: Semi-Senior (3+ años)   |  | Seniority: Senior (5+ años)        |           |  |
|  | | Prioridad: Alta Prioridad          |  | Prioridad: Urgente / Crítica       |           |  |
|  | | Certs: MD-102, ITIL 4, Azure Dev   |  | Certs: CompTIA Security+, CEH      |           |  |
|  | | 📈 Impacto: Reducción 45% en MTTR  |  | 📈 Impacto: Monitoreo SOC 24/7     |           |  |
|  | +------------------------------------+  +------------------------------------+           |  |
|  +------------------------------------------------------------------------------------------+  |
|                                                                                               |
|  +--[ GRÁFICAS ESTADÍSTICAS ]-----------------------+ +--[ ESPECIALIDADES ]-----------------+ |
|  | 📊 Evolución de Solicitudes por Mes (Chart.js)   | | 🍩 Distribución por Categoría       | |
|  |   [ █   █   █   █ ]                              | |    (Software, Redes, Hardware...)   | |
|  +--------------------------------------------------+ +-------------------------------------+ |
|                                                                                               |
|  +--[ FILTROS: [Buscar...] [Todos los Estados v] [Categorías v] [Prioridades v] [🔍 Filtrar] -+ |
|                                                                                               |
|  +--[ TABLA DE GESTIÓN DE TICKETS ]---------------------------------------------------------+ |
|  | #ID | Fecha        | Solicitante / Empresa | Asunto              | Prioridad | Estado    | IA| |
|  | #14 | 31/08 15:30  | Carlos Mendoza (Log.) | Expiración Lic. M365| 🟡 Media  |[En Proceso|💡]| |
|  | #13 | 31/08 14:10  | Andrea Díaz (Bancaria)| Caída túnel VPN IPsec| 🔴 Crítica|[Pendiente |💡]| |
|  | #12 | 30/08 11:20  | Pedro Gómez (Salud)   | Deadlock en base SQL| 🟠 Alta   |[Resuelto  |💡]| |
|  +------------------------------------------------------------------------------------------+ |
+-----------------------------------------------------------------------------------------------+
```

---

### Pantalla 6: Modal de Diagnóstico y Copiloto IA (`views/partials/modal_ia.php`)
Ventana emergente que entrega el diagnóstico forense, plan paso a paso y comandos listos para ejecutar.

```text
+-----------------------------------------------------------------------------------------------+
|  [🤖 Diagnóstico y Solución Técnica con IA — Ticket #14]                                  [X] |
+-----------------------------------------------------------------------------------------------+
|                                                                                               |
|  [✨ Enfoque #1: Mitigación Rápida N1]  [⚡ Motor Experto Local (0 Tokens)]  [⏱️ Est. 15-20 min]|
|                                                                                               |
|  🩺 Diagnóstico Detectado:                                                                    |
|  Alerta de expiración de suscripción o token de activación caducado en Microsoft 365...       |
|                                                                                               |
|  🔍 Causa Raíz Probable:                                                                      |
|  1. Desincronización del token OAuth de Entra ID en Credential Manager de Windows.            |
|  2. Licencia reasignada en admin.microsoft.com.                                               |
|                                                                                               |
|  📋 Plan de Acción Técnico (Paso a Paso):                                                     |
|  1. Cerrar procesos de Word y Excel desde el Administrador de Tareas.                         |
|  2. Abrir 'control keymgr.cpl' y purgar credenciales genéricas de Office16.                   |
|  3. Ejecutar script OSPP.VBS /dstatus para verificar estado del token.                        |
|  4. Revalidar inicio de sesión en Word con correo corporativo del usuario.                    |
|                                                                                               |
|  💻 Comandos / Herramientas Sugeridas (Terminal Oscura):                                      |
|  [ cscript "C:\Program Files\Microsoft Office\Office16\OSPP.VBS" /dstatus ] [ dsregcmd /status]|
|                                                                                               |
|  🛡️ Medida Preventiva a Largo Plazo:                                                         |
|  Configurar alertas automáticas de renovación con 30 días de anticipación en el portal M365.  |
|                                                                                               |
|  ─────────────────────────────────────────────────────────────────────────────────────────── |
|  [ Cerrar ]                          [ ⚡ Solución Local (0 Tokens) ] [ ✨ Diagnóstico Gemini ] |
+-----------------------------------------------------------------------------------------------+
```

---

## 3. Principios de Experiencia de Usuario (UX) Implementados

1. **Jerarquía Visual Clara:** Diferenciación cromática para prioridades y estados que permite a los técnicos identificar incidentes críticos en menos de 3 segundos.
2. **Retroalimentación Asíncrona (Sin Recargas):** El cambio de estado de tickets y las consultas de IA operan vía `fetch()` en segundo plano con *spinners* interactivos.
3. **Cero Fricción para el Cliente:** Autocompletado de datos empresariales que reduce el tiempo de radicación de tickets en un 60%.
4. **Optimización de Consumo de Tokens:** El sistema prioriza el motor local inmediato y delega las consultas a la nube de Google exclusivamente bajo demanda del usuario.
