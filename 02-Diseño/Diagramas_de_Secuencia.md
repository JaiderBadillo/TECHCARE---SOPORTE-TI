# 🔄 Diagramas de Secuencia del Sistema TechCare Soporte TI
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA  
**Estándar:** UML 2.5 (Mermaid Sequence Diagrams)  
**Versión:** 2.2.0  

---

## 1. Introducción
Este documento especifica la dinámica temporal y el intercambio de mensajes entre los diferentes actores, capas del sistema (Vistas, Controladores, Modelos, Servicios de Inteligencia Artificial) y la base de datos MySQL para los 5 flujos operativos principales de la plataforma.

---

## 2. Catálogo de Diagramas de Secuencia

1. **[Flujo 1: Registro y Autenticación de Usuarios (Control de Acceso RBAC)](#flujo-1-registro-y-autenticaci%C3%B3n-de-usuarios-rbac)**
2. **[Flujo 2: Radicación de Incidente Técnico y Consulta de Historial](#flujo-2-radicaci%C3%B3n-de-incidente-t%C3%A9cnico-y-consulta-de-historial)**
3. **[Flujo 3: Diagnóstico Técnico Asistido por IA (Modo Híbrido: Local / Gemini Cloud)](#flujo-3-diagn%C3%B3stico-t%C3%A9cnico-asistido-por-ia-modo-h%C3%ADbrido)**
4. **[Flujo 4: Análisis Estratégico Directivo y Prescripción de Decisiones TI](#flujo-4-an%C3%A1lisis-estrat%C3%A9gico-directivo-y-toma-de-decisiones-ti)**
5. **[Flujo 5: Gestión Asíncrona de Estados de Tickets (AJAX)](#flujo-5-gesti%C3%B3n-as%C3%ADncrona-de-estados-de-tickets-ajax)**

---

## Flujo 1: Registro y Autenticación de Usuarios (RBAC)
Describe cómo un usuario cliente crea su cuenta con su empresa y cargo, y cómo el sistema valida las credenciales redirigiendo según su rol (`cliente` al formulario o `admin` al dashboard).

```mermaid
sequenceDiagram
    autonumber
    actor Usuario as 🧑‍💼 Usuario / Administrador
    participant UI as 🖥️ Interfaz Web (views/login.php / registro.php)
    participant Router as 🔀 index.php (Front Controller)
    participant AuthCtrl as ⚙️ AuthController
    participant UserMdl as 🗄️ User Model
    participant BD as 💾 MySQL (soporte_db)

    alt Caso A: Registro de Nuevo Usuario Cliente
        Usuario->>UI: Completa formulario (Nombre, Email, Empresa, Cargo, Password)
        UI->>Router: POST /index.php?action=registro
        Router->>AuthCtrl: registro()
        AuthCtrl->>UserMdl: register(nombre, email, empresa, cargo, password, 'cliente')
        UserMdl->>BD: SELECT id FROM usuarios WHERE email = ?
        BD-->>UserMdl: NULL (Email disponible)
        UserMdl->>UserMdl: password_hash(password, BCRYPT)
        UserMdl->>BD: INSERT INTO usuarios (nombre, email, password, empresa, cargo_empresa, rol) VALUES (...)
        BD-->>UserMdl: OK (ID asignado)
        UserMdl-->>AuthCtrl: Array User
        AuthCtrl->>AuthCtrl: session_start() + Guardar datos en $_SESSION
        AuthCtrl-->>UI: JSON { ok: true, redirect: 'index.php?route=formulario' }
        UI-->>Usuario: Redirección al Portal de Radicación con datos autocompletados
    else Caso B: Inicio de Sesión
        Usuario->>UI: Ingresa Email y Contraseña
        UI->>Router: POST /index.php?action=login
        Router->>AuthCtrl: login()
        AuthCtrl->>UserMdl: authenticate(email, password)
        UserMdl->>BD: SELECT * FROM usuarios WHERE email = ? AND activo = 1
        BD-->>UserMdl: Registro de Usuario
        UserMdl->>UserMdl: password_verify(password, hash_almacenado)
        alt Credenciales Válidas
            UserMdl-->>AuthCtrl: Datos de Usuario
            AuthCtrl->>AuthCtrl: Guardar ID, Nombre, Rol y Empresa en $_SESSION
            alt Rol = 'admin' o 'tecnico'
                AuthCtrl-->>UI: JSON { ok: true, redirect: 'index.php?route=dashboard' }
                UI-->>Usuario: Redirección al Dashboard Gerencial & KPIs
            else Rol = 'cliente'
                AuthCtrl-->>UI: JSON { ok: true, redirect: 'index.php?route=formulario' }
                UI-->>Usuario: Redirección al Portal de Radicación
            end
        else Credenciales Inválidas
            UserMdl-->>AuthCtrl: false
            AuthCtrl-->>UI: JSON { ok: false, error: 'Credenciales incorrectas' }
            UI-->>Usuario: Muestra alerta de error en pantalla
        end
    end
```

---

## Flujo 2: Radicación de Incidente Técnico y Consulta de Historial
Describe el proceso mediante el cual un usuario cliente envía una solicitud técnica, cómo se vincula a su cuenta y empresa, y cómo consulta el progreso en su historial.

```mermaid
sequenceDiagram
    autonumber
    actor Cliente as 🧑‍💼 Usuario Cliente
    participant UI as 🖥️ Portal de Radicación (views/formulario.php)
    participant Router as 🔀 index.php (Front Controller)
    participant TicketCtrl as ⚙️ TicketController
    participant TicketMdl as 🗄️ Ticket Model
    participant BD as 💾 MySQL (soporte_db)

    Cliente->>UI: Ingresa Asunto, Categoría, Criticidad y Descripción
    UI->>Router: POST /index.php?action=ticket_guardar
    Router->>TicketCtrl: guardar()
    TicketCtrl->>TicketCtrl: Obtener usuario_id y empresa de $_SESSION
    TicketCtrl->>TicketCtrl: Validar campos obligatorios y formato de email
    TicketCtrl->>TicketMdl: create(nombre, email, asunto, tipo, prioridad, mensaje, empresa, usuario_id)
    TicketMdl->>BD: INSERT INTO solicitudes (usuario_id, nombre, email, empresa, asunto, tipo_problema, prioridad, mensaje, estado) VALUES (..., 'pendiente')
    BD-->>TicketMdl: OK (ID Ticket generado)
    TicketMdl-->>TicketCtrl: { ok: true, id: #ID }
    TicketCtrl-->>UI: JSON { ok: true, id: #ID, mensaje: 'Solicitud radicada con éxito' }
    UI-->>Cliente: Muestra confirmación exitosa en pantalla

    opt Consulta de Historial de Tickets
        Cliente->>UI: Clic en pestaña "2. Mis Solicitudes Registradas"
        UI->>Router: GET /index.php?route=formulario
        Router->>TicketMdl: getByUser(usuario_id, email)
        TicketMdl->>BD: SELECT * FROM solicitudes WHERE usuario_id = ? OR email = ? ORDER BY fecha_creacion DESC
        BD-->>TicketMdl: Lista de Tickets del Usuario
        TicketMdl-->>UI: Renderiza tarjetas con estado (Pendiente / En Revisión / Resuelto) y Diagnóstico IA
        UI-->>Cliente: Visualiza el listado y retroalimentación de sus incidentes
    end
```

---

## Flujo 3: Diagnóstico Técnico Asistido por IA (Modo Híbrido)
Detalla la generación de soluciones técnicas paso a paso, alternando entre el **Motor Local Experto (0 Tokens / Instantáneo)** y **Google Gemini Cloud API (Bajo Demanda)**.

```mermaid
sequenceDiagram
    autonumber
    actor Tecnico as 👨‍💻 Ingeniero / Técnico TI
    participant UI as 🖥️ Dashboard (views/partials/modal_ia.php)
    participant Router as 🔀 index.php
    participant AuthCtrl as ⚙️ AuthController (Middleware)
    participant IACtrl as ⚙️ IAController
    participant TicketMdl as 🗄️ Ticket Model
    participant SrvLocal as 🧠 LocalExpertService (0 Tokens)
    participant SrvGemini as ☁️ GeminiService (Cloud)
    participant GoogleAPI as 🌐 Google Gemini API REST
    participant BD as 💾 MySQL (soporte_db)

    Tecnico->>UI: Clic en botón "Solución IA" (Ticket #ID)
    UI->>Router: POST /index.php?action=ia_solucion (id, motor='local')
    Router->>AuthCtrl: requireAdmin()
    AuthCtrl-->>Router: Sesión de Admin Válida
    Router->>IACtrl: solucionarTicket()
    IACtrl->>TicketMdl: getById(id)
    TicketMdl->>BD: SELECT * FROM solicitudes WHERE id = ?
    BD-->>TicketMdl: Registro del Ticket
    TicketMdl-->>IACtrl: Array Ticket

    alt Opción 1: Motor Local (Por Defecto - Instantáneo < 10ms)
        IACtrl->>SrvLocal: diagnoseTicket(tipo, prioridad, asunto, mensaje, variante)
        SrvLocal->>SrvLocal: Analizador Semántico NLP (M365, VPN, SQL, Seguridad...)
        SrvLocal-->>IACtrl: JSON { diagnostico, causa_probable, pasos_resolucion, comandos, accion_preventiva }
    else Opción 2: Consulta Google Gemini Cloud (Bajo Demanda)
        Tecnico->>UI: Clic en "Diagnóstico Gemini (Cloud)"
        UI->>Router: POST /index.php?action=ia_solucion (id, motor='gemini', force=1)
        Router->>IACtrl: solucionarTicket()
        IACtrl->>SrvGemini: generateTicketSolution(tipo, prioridad, asunto, mensaje, variante)
        SrvGemini->>GoogleAPI: HTTPS POST /v1beta/models/gemini-3.7-flash:generateContent
        alt API Responde OK (200)
            GoogleAPI-->>SrvGemini: JSON Respuesta con Diagnóstico Personalizado
            SrvGemini-->>IACtrl: Array Diagnóstico Cloud
        else API Falla (404 / 429 / Sin Conexión)
            GoogleAPI-->>SrvGemini: Error HTTP / Timeout
            SrvGemini-->>IACtrl: NULL + Captura error en lastError
            IACtrl->>SrvLocal: Fallback automático al Motor Local
            SrvLocal-->>IACtrl: Diagnóstico Local de Contingencia
        end
    end

    IACtrl->>TicketMdl: saveIASolution(id, solucionJSON)
    TicketMdl->>BD: UPDATE solicitudes SET solucion_ia = ? WHERE id = ?
    BD-->>TicketMdl: OK
    IACtrl-->>UI: JSON { ok: true, solucion: {...}, variante_index: 1 }
    UI-->>Tecnico: Despliega Causas Raíz, Plan Paso a Paso y Comandos en el Modal
```

---

## Flujo 4: Análisis Estratégico Directivo y Toma de Decisiones TI
Muestra cómo el sistema agrega las estadísticas de incidentes de toda la empresa y genera recomendaciones ejecutivas de contratación, inversión en infraestructura y capacitaciones.

```mermaid
sequenceDiagram
    autonumber
    actor Director as 👔 Director de TI / Administrador
    participant UI as 🖥️ Dashboard (Hub de Decisiones IA)
    participant Router as 🔀 index.php
    participant IACtrl as ⚙️ IAController
    participant TicketMdl as 🗄️ Ticket Model
    participant SrvLocal as 🧠 LocalExpertService
    participant SrvGemini as ☁️ GeminiService
    participant GoogleAPI as 🌐 Google Gemini API REST
    participant BD as 💾 MySQL (soporte_db)

    Director->>UI: Abre Dashboard o presiona "Análisis con Gemini" / "Análisis Local"
    UI->>Router: GET /index.php?action=ia_analisis&motor=local|gemini
    Router->>IACtrl: analizarEstrategia()
    IACtrl->>TicketMdl: getMetrics()
    TicketMdl->>BD: Consultas agregadas (COUNT, GROUP BY tipo_problema, estado, prioridad)
    BD-->>TicketMdl: Métricas de Demanda y Resolución
    TicketMdl-->>IACtrl: Array Métricas ($tipos, $prios, $estados, $total)

    alt Motor Local (Carga Inicial Automática - 0 Tokens)
        IACtrl->>SrvLocal: generateStrategicInsights(tipos, prios, estados, total)
        SrvLocal->>SrvLocal: Cálculo de Seniority, ROI, Costos USD y Carga Operativa
        SrvLocal-->>IACtrl: JSON Decisiones Estratégicas
    else Google Gemini Cloud (Bajo Demanda)
        IACtrl->>SrvGemini: generateStrategicAnalysis(tipos, prios, estados, total, muestraTickets)
        SrvGemini->>GoogleAPI: HTTPS POST generateContent (Prompt Directivo CTO)
        GoogleAPI-->>SrvGemini: JSON Estrategia Cloud
        SrvGemini-->>IACtrl: JSON Decisiones Cloud
    end

    IACtrl-->>UI: JSON { ok: true, insights: { resumen_ejecutivo, contratacion, infra, capacitacion, automatizacion } }
    UI-->>Director: Renderiza Dictamen Ejecutivo y tarjetas en las 4 pestañas interactivas
```

---

## Flujo 5: Gestión Asíncrona de Estados de Tickets (AJAX)
Describe la actualización en caliente del estado de un ticket (`pendiente` ➔ `en_proceso` ➔ `resuelto`) desde la tabla del dashboard sin recargar la página web.

```mermaid
sequenceDiagram
    autonumber
    actor Tecnico as 👨‍💻 Técnico de Soporte
    participant UI as 🖥️ Tabla de Tickets (views/dashboard.php)
    participant Script as 📜 public/js/dashboard.js
    participant Router as 🔀 index.php
    participant TicketCtrl as ⚙️ TicketController
    participant TicketMdl as 🗄️ Ticket Model
    participant BD as 💾 MySQL (soporte_db)

    Tecnico->>UI: Cambia el selector de estado de "Pendiente" a "En Proceso" / "Resuelto"
    UI->>Script: Evento 'change' en .select-estado
    Script->>Router: POST /index.php?action=ticket_estado (id: #ID, estado: 'en_proceso')
    Router->>TicketCtrl: actualizarEstado()
    TicketCtrl->>TicketCtrl: Validar estado permitido ('pendiente', 'en_proceso', 'resuelto')
    TicketCtrl->>TicketMdl: updateStatus(id, estado)
    TicketMdl->>BD: UPDATE solicitudes SET estado = ? WHERE id = ?
    BD-->>TicketMdl: OK (Rows affected: 1)
    TicketMdl-->>TicketCtrl: { ok: true }
    TicketCtrl-->>Script: JSON { ok: true, mensaje: 'Estado actualizado' }
    Script->>UI: Actualiza clase CSS dinámica del badge (Rojo / Amarillo / Verde)
    UI-->>Tecnico: Feedback visual instantáneo sin recarga de página
```
