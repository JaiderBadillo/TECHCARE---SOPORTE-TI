# 🔌 Especificación de la API REST & Servicios Web
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA  
**Formato de Intercambio:** JSON (`application/json; charset=utf-8`)  
**Estilo Arquitectónico:** RESTful / Action-Based Front Controller  
**Versión de la API:** 2.2.0  

---

## 1. Introducción y Estándares Globales

La API de **TechCare Soporte TI** proporciona una interfaz para la radicación de solicitudes, control de estados, autenticación por roles (RBAC) y ejecución de diagnósticos y analítica prescriptiva asistida por Inteligencia Artificial (**Google Gemini Cloud** y **Motor Experto Local 0 Tokens**).

### URL Base del Servidor
* **Desarrollo Local:** `http://127.0.0.1:8000/index.php`

### Formato de Respuestas

#### Respuesta Exitosa (Estándar)
```json
{
  "ok": true,
  "mensaje": "Operación ejecutada correctamente.",
  "...datos": {}
}
```

#### Respuesta de Error (Estándar)
```json
{
  "ok": false,
  "error": "Descripción detallada del motivo del fallo.",
  "codigo": 400
}
```

### Códigos de Estado HTTP Utilizados

| Código HTTP | Significado | Contexto de Uso |
| :---: | :--- | :--- |
| **`200 OK`** | Petición exitosa | Operación procesada y respuesta JSON devuelta. |
| **`400 Bad Request`** | Parámetros inválidos | Faltan campos obligatorios o formatos de email erróneos. |
| **`401 Unauthorized`** | No autenticado | Se intentó acceder a un endpoint protegido sin sesión activa. |
| **`403 Forbidden`** | Acceso denegado | Usuario cliente intentando ejecutar acciones de Administrador/Técnico. |
| **`404 Not Found`** | Recurso no encontrado | El ID del ticket o la acción solicitada no existe. |
| **`503 Service Unavailable`** | Servicio no disponible | La base de datos MySQL o el servicio externo no responde. |

---

## 2. Catálogo de Endpoints

```text
├── Autenticación y Control de Acceso
│   ├── POST /index.php?action=registro       # Registro de usuario cliente
│   ├── POST /index.php?action=login          # Inicio de sesión (Cliente / Admin)
│   └── GET  /index.php?action=logout         # Cierre de sesión
│
├── Gestión de Tickets de Soporte
│   ├── POST /index.php?action=ticket_guardar # Radicación de nuevo ticket
│   └── POST /index.php?action=ticket_estado  # Actualización de estado (Admin)
│
└── Inteligencia Artificial y Diagnóstico
    ├── POST /index.php?action=ia_solucion    # Diagnóstico técnico por ticket
    └── GET  /index.php?action=ia_analisis    # Analítica estratégica y decisiones TI
```

---

## 3. Especificación Detallada de Endpoints

### 3.1. Módulo de Autenticación & Usuarios

---

#### `POST /index.php?action=registro`
Registra una nueva cuenta de usuario cliente asociada a una empresa y cargo.

* **Autenticación requerida:** Ninguna (Público).
* **Content-Type:** `multipart/form-data` o `application/x-www-form-urlencoded`.

##### Parámetros del Body (Request)
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
| :--- | :---: | :---: | :--- | :--- |
| `nombre` | `string` | **SÍ** | Nombre completo del usuario. | `"Carlos Mendoza"` |
| `email` | `string` | **SÍ** | Correo corporativo válido (único). | `"cliente@empresa.com"` |
| `empresa` | `string` | **SÍ** | Razón social de la empresa. | `"Logística Global S.A.S"` |
| `cargo_empresa` | `string` | **SÍ** | Cargo o rol dentro de la empresa. | `"Líder de Contabilidad"` |
| `password` | `string` | **SÍ** | Contraseña (mínimo 6 caracteres). | `"cliente123"` |
| `password_confirm` | `string` | **SÍ** | Confirmación idéntica de contraseña. | `"cliente123"` |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "mensaje": "¡Cuenta creada con éxito! Bienvenido a TechCare Soporte TI.",
  "redirect": "index.php?route=formulario"
}
```

##### Respuesta de Error (`200 OK` con `ok: false`)
```json
{
  "ok": false,
  "error": "Ya existe una cuenta registrada con el correo: cliente@empresa.com"
}
```

---

#### `POST /index.php?action=login`
Autentica a un usuario cliente o administrador e inicia su sesión en el servidor.

* **Autenticación requerida:** Ninguna (Público).

##### Parámetros del Body (Request)
| Campo | Tipo | Obligatorio | Descripción | Ejemplo |
| :--- | :---: | :---: | :--- | :--- |
| `email` | `string` | **SÍ** | Correo corporativo registrado. | `"admin@techcare.com"` |
| `password` | `string` | **SÍ** | Contraseña de acceso. | `"admin123"` |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "mensaje": "Inicio de sesión exitoso. Redirigiendo...",
  "user": {
    "nombre": "Administrador TI",
    "rol": "admin",
    "empresa": "TechCare Corp"
  },
  "redirect": "index.php?route=dashboard"
}
```

---

#### `GET /index.php?action=logout`
Destruye la sesión activa del usuario y elimina las cookies de autenticación.

* **Autenticación requerida:** Ninguna.
* **Respuesta:** Redirección HTTP 302 hacia `index.php?route=login`.

---

### 3.2. Módulo de Gestión de Tickets

---

#### `POST /index.php?action=ticket_guardar`
Radica una nueva solicitud de soporte técnico. Si el usuario tiene sesión iniciada, se vincula automáticamente su `usuario_id` y `empresa`.

* **Autenticación requerida:** Opcional (Permite usuarios anónimos o registrados).

##### Parámetros del Body (Request)
| Campo | Tipo | Obligatorio | Descripción | Valores Permitidos |
| :--- | :---: | :---: | :--- | :--- |
| `nombre` | `string` | **SÍ** | Nombre del solicitante. | Texto libre |
| `email` | `string` | **SÍ** | Correo de contacto. | Formato email |
| `empresa` | `string` | **SÍ** | Empresa del solicitante. | Texto libre |
| `tipo_problema` | `string` | **SÍ** | Categoría técnica del fallo. | `'SOFTWARE'`, `'RED'`, `'HARDWARE'`, `'SEGURIDAD'`, `'CLOUD_SERVIDORES'`, `'BASE_DE_DATOS'` |
| `prioridad` | `string` | **SÍ** | Criticidad de la falla. | `'baja'`, `'media'`, `'alta'`, `'critica'` |
| `asunto` | `string` | **SÍ** | Título breve del problema. | Texto libre (máx 150 caracteres) |
| `mensaje` | `string` | **SÍ** | Descripción detallada de síntomas. | Texto libre |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "id": 14,
  "mensaje": "Solicitud de soporte #14 registrada correctamente."
}
```

---

#### `POST /index.php?action=ticket_estado`
Actualiza en tiempo real el estado de atención de un ticket de soporte.

* **Autenticación requerida:** **SÍ (Rol: `admin` o `tecnico`)**.

##### Parámetros del Body (Request)
| Campo | Tipo | Obligatorio | Descripción | Valores Permitidos |
| :--- | :---: | :---: | :--- | :--- |
| `id` | `integer` | **SÍ** | ID numérico del ticket. | `> 0` |
| `estado` | `string` | **SÍ** | Nuevo estado del ticket. | `'pendiente'`, `'en_proceso'`, `'resuelto'` |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "mensaje": "Estado actualizado a: en_proceso"
}
```

---

### 3.3. Módulo de Inteligencia Artificial

---

#### `POST /index.php?action=ia_solucion`
Genera o recupera de caché un diagnóstico técnico paso a paso con causas raíz y comandos de consola.

* **Autenticación requerida:** **SÍ (Rol: `admin` o `tecnico`)**.

##### Parámetros del Body (Request)
| Campo | Tipo | Obligatorio | Por Defecto | Descripción |
| :--- | :---: | :---: | :---: | :--- |
| `id` | `integer` | **SÍ** | &mdash; | ID del ticket a diagnosticar. |
| `motor` | `string` | NO | `'local'` | Motor a utilizar: `'local'` (0 Tokens / Heurístico) o `'gemini'` (Cloud). |
| `force` | `integer` | NO | `0` | `1` para forzar regeneración ignorando la caché. |
| `variant` | `integer` | NO | `0` | `1` (Mitigación N1), `2` (Forense N3), `3` (Endurecimiento). |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "cached": false,
  "variante_index": 1,
  "solucion": {
    "enfoque_nombre": "Solución Rápida en Caliente & Mitigación Inmediata (N1/N2)",
    "categoria": "Microsoft 365 & Licenciamiento Cloud",
    "diagnostico": "Alerta de expiración de suscripción o token de activación caducado en la suite Microsoft 365...",
    "causa_probable": "1. Desincronización del token OAuth de Microsoft Entra ID en el perfil de Windows. 2. Licencia no renovada en el portal. 3. Conflicto de cuentas en Credential Manager.",
    "tiempo_estimado": "10 - 20 minutos",
    "pasos_resolucion": [
      "Paso 1: Cerrar todas las aplicaciones de Office desde el Administrador de Tareas.",
      "Paso 2: Abrir el Administrador de Credenciales (control keymgr.cpl) y purgar credenciales de Office16.",
      "Paso 3: Ejecutar cscript OSPP.VBS /dstatus para verificar estado de activación.",
      "Paso 4: Comprobar licencia activa asignada en admin.microsoft.com.",
      "Paso 5: Abrir Word y revalidar inicio de sesión corporativo."
    ],
    "comandos_herramientas": [
      "cscript \"C:\\Program Files\\Microsoft Office\\Office16\\OSPP.VBS\" /dstatus",
      "dsregcmd /status",
      "control keymgr.cpl"
    ],
    "accion_preventiva": "Configurar alertas automáticas de renovación con 30 días de anticipación.",
    "motor_ia": "Motor Experto TI (Local - 0 Tokens)",
    "fecha_generacion": "15:24:02"
  },
  "mensaje": "Diagnóstico generado con [Motor Experto TI (Local - 0 Tokens) - Enfoque #1]"
}
```

---

#### `GET /index.php?action=ia_analisis`
Calcula la agregación de métricas de todos los tickets registrados y genera un dictamen ejecutivo con recomendaciones prescriptivas de contratación, infraestructura, capacitación y automatización.

* **Autenticación requerida:** **SÍ (Rol: `admin` o `tecnico`)**.

##### Parámetros Query (URL)
| Parámetro | Tipo | Obligatorio | Por Defecto | Descripción |
| :--- | :---: | :---: | :---: | :--- |
| `motor` | `string` | NO | `'local'` | `'local'` (Instantáneo / 0 Tokens) o `'gemini'` (Cloud). |

##### Respuesta Exitosa (`200 OK`)
```json
{
  "ok": true,
  "insights": {
    "resumen_ejecutivo": "El análisis integral sobre las solicitudes revela que la mayor carga operativa recae en el área de SOFTWARE (42.8%). Se mantienen 3 tickets pendientes y 2 de alta criticidad...",
    "metricas_clave": {
      "total_tickets": 14,
      "pendientes": 3,
      "en_proceso": 4,
      "resueltos": 7,
      "tasa_resolucion": "50.0%",
      "categoria_mayor_demanda": "SOFTWARE",
      "tickets_criticos_altos": 2
    },
    "decision_contratacion": [
      {
        "perfil": "Ingeniero de Soporte de Aplicaciones & ERP (L2/L3)",
        "especialidad": "SOFTWARE",
        "seniority": "Semi-Senior (3+ años exp.)",
        "prioridad_contratacion": "Alta Prioridad",
        "certificaciones_clave": ["Microsoft MD-102", "ITIL 4 Specialist", "Azure Fundamentals"],
        "justificacion": "El software corporativo y SaaS concentran el 42.8% de los incidentes.",
        "impacto_esperado": "Despacho ágil en primer contacto y reducción del 45% en el MTTR."
      }
    ],
    "decision_infraestructura": [
      {
        "inversion": "Plataforma de Monitoreo Proactivo de Red & Servidores (Zabbix / PRTG)",
        "categoria": "Monitoreo & NOC",
        "costo_estimado": "USD $1,200 - $2,500 / año",
        "justificacion": "Permite detectar degradaciones de enlace antes de que los usuarios reporten caídas.",
        "retorno_inversion": "Disponibilidad de red superior al 99.9%."
      }
    ],
    "decision_capacitacion": [
      {
        "tema": "Administración de Identidades en Microsoft Entra ID (Azure AD) y MFA",
        "audiencia": "Técnicos de Soporte N1/N2",
        "duracion": "16 Horas",
        "objetivo": "Capacitar en la resolución rápida de fallas de autenticación y tokens PRT."
      }
    ],
    "decision_automatizacion": [
      {
        "iniciativa": "Script de Auto-Reparación y Limpieza (TechCare QuickFix PowerShell)",
        "alcance": "Puestos de trabajo y software ofimático",
        "ahorro_tiempo": "~35 horas/mes del equipo técnico",
        "descripcion": "Herramienta ejecutable en 1 clic para limpieza de caché y certificados."
      }
    ],
    "motor_ia": "Motor de Inteligencia de Negocio IT (Local - 0 Tokens)"
  }
}
```

---

## 4. Integración Externa con Google Gemini REST API

El servicio `GeminiService.php` interactúa con la API oficial de Google AI Studio mediante HTTPS POST:

### Endpoint Externo de Google
```http
POST https://generativelanguage.googleapis.com/v1beta/models/gemini-3.7-flash:generateContent?key={GEMINI_API_KEY}
Content-Type: application/json
```

### Payload de Envío a Gemini
```json
{
  "contents": [
    {
      "role": "user",
      "parts": [{ "text": "Analiza el siguiente incidente de soporte técnico..." }]
    }
  ],
  "generationConfig": {
    "temperature": 0.3,
    "responseMimeType": "application/json"
  },
  "systemInstruction": {
    "parts": [{ "text": "Eres un Ingeniero Principal de Soporte TI y Ciberseguridad. Responde estrictamente en JSON." }]
  }
}
```
