# 🏛️ Diagrama de Arquitectura del Sistema TechCare Soporte TI
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA Generativa y Analítica Predictiva  
**Patrón Arquitectónico:** MVC (Modelo - Vista - Controlador) + Capa de Servicios Híbrida (Cloud / Local)  
**Versión:** 2.1.0  

---

## 1. Diagrama de Arquitectura de Capas (Mermaid)

Este diagrama representa cómo interactúan las capas de la aplicación, desde la interfaz de usuario hasta los motores de inteligencia artificial y la base de datos:

```mermaid
flowchart TB
    subgraph CLIENTE["🌐 Capa de Presentación (Frontend)"]
        UI_USER["Portal de Radicación<br/>(views/formulario.php)"]
        UI_LOGIN["Módulo de Acceso<br/>(views/login.php)"]
        UI_ADMIN["Dashboard Gerencial & KPIs<br/>(views/dashboard.php)"]
        UI_MODAL["Modal Copiloto IA<br/>(views/partials/modal_ia.php)"]
        ASSETS["Assets & Scripts<br/>(CSS Glassmorphism / JS Fetch / Chart.js)"]
    end

    subgraph ROUTER["🔀 Capa de Enrutamiento (Front Controller)"]
        INDEX["index.php<br/>(Router / Dispatcher)"]
    end

    subgraph CONTROLLERS["⚙️ Capa de Controladores (Application Logic)"]
        AUTH_CTRL["AuthController.php<br/>• Login / Logout<br/>• Sesiones & Middleware"]
        TICKET_CTRL["TicketController.php<br/>• Crear Solicitudes<br/>• Cambiar Estados"]
        IA_CTRL["IAController.php<br/>• Despacho Diagnóstico<br/>• Analítica Estratégica"]
    end

    subgraph SERVICES["🧠 Capa de Servicios de Inteligencia Artificial"]
        GEMINI_SRV["GeminiService.php<br/>• Google Gemini 3.x Flash<br/>• Auto-Recuperación Modelos"]
        LOCAL_SRV["LocalExpertService.php<br/>• Motor Heurístico Semántico<br/>• 0 Tokens (Offline)"]
    end

    subgraph MODELS["🗄️ Capa de Datos (Modelos DAO / Active Record)"]
        USER_MDL["User.php<br/>• Autenticación BCRYPT<br/>• Gestión de Técnicos"]
        TICKET_MDL["Ticket.php<br/>• CRUD Solicitudes<br/>• Agregación de Métricas SQL"]
    end

    subgraph STORAGE["💾 Capa de Persistencia & Servicios Externos"]
        MYSQL[("MySQL / MariaDB<br/>soporte_db<br/>(solicitudes / usuarios)")]
        GEMINI_CLOUD["☁️ Google AI Studio<br/>Gemini REST API (Cloud)"]
    end

    %% Relaciones de Flujo
    CLIENTE -->|HTTP GET / POST| INDEX
    INDEX -->|Rutas de Auth| AUTH_CTRL
    INDEX -->|Rutas de Tickets| TICKET_CTRL
    INDEX -->|Rutas de IA| IA_CTRL

    AUTH_CTRL -->|Verificar / Crear| USER_MDL
    TICKET_CTRL -->|Guardar / Listar| TICKET_MDL
    IA_CTRL -->|Consultas IA Cloud| GEMINI_SRV
    IA_CTRL -->|Consultas IA Local| LOCAL_SRV
    IA_CTRL -->|Guardar Solución| TICKET_MDL

    GEMINI_SRV -->|HTTPS REST cURL| GEMINI_CLOUD
    GEMINI_SRV -.->|Fallback si falla API| LOCAL_SRV

    USER_MDL -->|SQL Prepared Stmt| MYSQL
    TICKET_MDL -->|SQL Prepared Stmt| MYSQL
```

---

## 2. Descripción de Componentes por Capa

### A. Capa de Presentación (Frontend)
* **Tecnologías:** HTML5 semántico, CSS3 personalizado con efectos Glassmorphism, Bootstrap 5.3, Bootstrap Icons y Chart.js 4.4.
* **Componentes:**
  * `formulario.php`: Portal público y accesible para que cualquier usuario reporte una incidencia técnica.
  * `login.php`: Control de acceso administrativo con autenticación asíncrona.
  * `dashboard.php`: Panel directivo con 6 tarjetas KPIs, gráficos dinámicos y tabla interactiva de solicitudes.
  * `modal_ia.php`: Ventana modal para ver planes de acción técnicos, comandos sugeridos y alternar entre modo Cloud y Local.

### B. Capa de Enrutamiento y Controladores (Backend)
* **`index.php` (Front Controller):** Punto único de entrada. Centraliza la recepción de todas las peticiones web y las despacha al controlador correspondiente según el parámetro `route` o `action`.
* **`AuthController`:** Valida credenciales, inicializa sesiones seguras de PHP (`$_SESSION`) y actúa como **Middleware** (`requireAuth()`) para bloquear accesos no autorizados al dashboard y a la API.
* **`TicketController`:** Valida la integridad de los datos de entrada, previene inyecciones y coordina el ciclo de vida de los tickets (`pendiente` ➔ `en_proceso` ➔ `resuelto`).
* **`IAController`:** Determina si la petición de diagnóstico o análisis directivo debe ejecutarse a través de Google Gemini Cloud o el Motor Heurístico Local.

### C. Capa de Servicios de IA (Arquitectura Híbrida)
* **`GeminiService` (Cloud):** Se comunica vía cURL con la API de Google Generative Language (`gemini-3.7-flash` / `gemini-3.1-pro-preview`) enviando prompts contextualizados y procesando respuestas estrictas en JSON.
* **`LocalExpertService` (Offline / 0 Tokens):** Motor de reglas expertas con analizador semántico de palabras clave (NLP) que reconoce 7 sub-dominios (M365, VPN, Seguridad, Bases de Datos, Servidores, Hardware, Software). Provee respuestas en < 10 ms sin costo de API.
* **Mecanismo de Resiliencia (*Fallback*):** Si la API de Google presenta latencia o cuota agotada, el sistema conmuta automáticamente al motor local sin interrumpir la operación.

### D. Capa de Modelos y Datos
* **`Ticket` & `User`:** Encapsulan todas las consultas SQL mediante sentencias preparadas (`prepared statements`) con `mysqli` sobre la base de datos `soporte_db`.

---

## 3. Diagrama de Secuencia: Diagnóstico de Ticket con IA

```mermaid
sequenceDiagram
    autonumber
    actor Tecnico as 👨‍💻 Técnico TI
    participant UI as 🖥️ Dashboard (modal_ia.php)
    participant Router as 🔀 index.php (Router)
    participant Ctrl as ⚙️ IAController
    participant Model as 🗄️ Ticket Model
    participant SrvGemini as ☁️ GeminiService
    participant SrvLocal as 🧠 LocalExpertService
    participant BD as 💾 MySQL (soporte_db)

    Tecnico->>UI: Clic en "Solución IA" (Ticket #ID)
    UI->>Router: POST /index.php?action=ia_solucion (id, motor='local')
    Router->>Ctrl: solucionarTicket()
    Ctrl->>Model: getById(id)
    Model->>BD: SELECT * FROM solicitudes WHERE id = ?
    BD-->>Model: Datos del Ticket
    Model-->>Ctrl: Array Ticket

    alt Motor Local (Por Defecto - 0 Tokens)
        Ctrl->>SrvLocal: diagnoseTicket(tipo, prioridad, asunto, mensaje)
        SrvLocal-->>Ctrl: JSON Diagnóstico Semántico (<10ms)
    else Motor Gemini (Cloud)
        Ctrl->>SrvGemini: generateTicketSolution(tipo, prioridad, asunto, mensaje)
        SrvGemini->>GoogleAPI: POST https://generativelanguage.googleapis.com/...
        GoogleAPI-->>SrvGemini: Respuesta JSON Gemini
        SrvGemini-->>Ctrl: JSON Diagnóstico Cloud
    end

    Ctrl->>Model: saveIASolution(id, solucion)
    Model->>BD: UPDATE solicitudes SET solucion_ia = ? WHERE id = ?
    Ctrl-->>UI: JSON { ok: true, solucion: {...} }
    UI-->>Tecnico: Muestra Diagnóstico, Causas y Comandos en pantalla
```
